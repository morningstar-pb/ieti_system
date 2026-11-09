<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditTrail;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old picture if not default
        if ($user->profile_picture !== 'default.png') {
            $oldPath = public_path('profile_pictures/' . $user->profile_picture);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('profile_picture');
        $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('profile_pictures'), $filename);

        $user->update(['profile_picture' => $filename]);

        AuditTrail::create([
            'user_id' => $user->id,
            'action_type' => 'Transaction',
            'description' => 'Updated profile picture',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Profile picture updated successfully.');
    }

    public function toggleTheme(Request $request)
    {
        $user = Auth::user();
        $newTheme = $user->theme === 'light' ? 'dark' : 'light';
        $user->update(['theme' => $newTheme]);

        return response()->json(['success' => true, 'theme' => $newTheme]);
    }
}
