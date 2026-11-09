<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\AuditTrail;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Generate Math CAPTCHA numbers
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        session(['math_captcha_result' => $num1 + $num2]);

        return view('auth.login', compact('num1', 'num2'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'captcha_answer' => 'required|numeric',
        ]);

        // Verify Math CAPTCHA
        $expectedCaptcha = session('math_captcha_result');
        if (!$expectedCaptcha || (int) $request->captcha_answer !== (int) $expectedCaptcha) {
            return back()->withErrors(['captcha_answer' => 'Incorrect Math CAPTCHA answer. Please try again.'])->withInput();
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            AuditTrail::create([
                'user_id' => $user ? $user->id : null,
                'action_type' => 'Failed Login',
                'description' => 'Failed login attempt for username: ' . $request->username,
                'ip_address' => $request->ip(),
            ]);

            return back()->withErrors(['username' => 'Invalid credentials provided.'])->withInput();
        }

        // Check if account is active
        if (!$user->is_active) {
            return back()->withErrors(['username' => 'Your account has been deactivated. Please contact an administrator.']);
        }

        // Check if 2FA is enabled
        if (!empty($user->google2fa_secret)) {
            session(['auth_user_id' => $user->id]);
            return redirect()->route('2fa.verify');
        }

        // Log user in if 2FA is not enabled
        Auth::login($user);
        session(['2fa_verified' => true]);

        AuditTrail::create([
            'user_id' => $user->id,
            'action_type' => 'Login',
            'description' => 'User successfully logged in.',
            'ip_address' => $request->ip(),
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    public function showRegister()
    {
        // Generate Math CAPTCHA numbers for register view
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        session(['math_captcha_result' => $num1 + $num2]);

        return view('auth.register', compact('num1', 'num2'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required', 
                'string', 
                'confirmed', 
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'captcha_answer' => 'required|numeric',
        ]);

        // Verify Math CAPTCHA
        $expectedCaptcha = session('math_captcha_result');
        if (!$expectedCaptcha || (int) $request->captcha_answer !== (int) $expectedCaptcha) {
            return back()->withErrors(['captcha_answer' => 'Incorrect Math CAPTCHA answer. Please try again.'])->withInput();
        }

        $user = User::create([
            'name' => $request->username, // Satisfies the database 'name' column constraint
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true,
        ]);

        AuditTrail::create([
            'user_id' => $user->id,
            'action_type' => 'Register',
            'description' => 'New user registered: ' . $user->username,
            'ip_address' => $request->ip(),
        ]);

        Auth::login($user);

        return redirect()->route('2fa.enable')->with('success', 'Registration successful! Please set up Two-Factor Authentication.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action_type' => 'Logout',
                'description' => 'User logged out.',
                'ip_address' => $request->ip(),
            ]);
        }

        // Force logout and fully clear and flush session data
        Auth::guard('web')->logout();
        
        $request->session()->flush();
        $request->session()->regenerate(true);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return with explicit cache-busting headers directly on the redirect response
        return redirect('/login')
            ->with('success', 'You have been successfully logged out.')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && file_exists(public_path('storage/' . $user->profile_picture))) {
                @unlink(public_path('storage/' . $user->profile_picture));
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $user->save();

            AuditTrail::create([
                'user_id' => $user->id,
                'action_type' => 'Profile Updated',
                'description' => 'User updated their profile picture.',
                'ip_address' => $request->ip(),
            ]);
        }

        return back()->with('success', 'Profile picture updated successfully!');
    }
}