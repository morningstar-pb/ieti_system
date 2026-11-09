<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use App\Models\AuditTrail;
use App\Models\User;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class TwoFactorController extends Controller
{
    public function showEnable(Request $request)
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $secret = $user->google2fa_secret;
        if (!$secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        }

        // Generate provisioning URL for Google Authenticator
        $g2faUrl = $google2fa->getQRCodeUrl(
            'IETI System',
            $user->email,
            $secret
        );

        // Generate native local SVG QR code (No external API calls needed)
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($g2faUrl);

        return view('auth.enable-2fa', compact('qrCodeSvg', 'secret'));
    }

    public function enable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        AuditTrail::create([
            'user_id' => $user->id,
            'action_type' => '2FA Enabled',
            'description' => 'User enabled Google Authenticator 2FA.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Google Authenticator 2FA successfully enabled!');
    }

    public function showVerify()
    {
        if (!session('auth_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify-2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('auth_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid 2FA code. Please check your authenticator app.']);
        }

        Auth::login($user);
        session()->forget('auth_user_id');
        session(['2fa_verified' => true]);

        AuditTrail::create([
            'user_id' => $user->id,
            'action_type' => 'Access',
            'description' => 'User successfully logged in with 2FA verification.',
            'ip_address' => $request->ip(),
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended('/dashboard');
    }
}