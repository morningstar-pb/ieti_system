<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    // Return just the table partial for AJAX reload
    public function table()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users._table', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,standard',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ]);

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Transaction',
            'description' => 'Created user: ' . $user->username,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'User created successfully.']);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role' => 'required|in:admin,standard',
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Transaction',
            'description' => 'Updated user: ' . $user->username,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    public function destroy(User $user)
    {
        // Prevent deleting active accounts
        if ($user->is_active) {
            return response()->json(['success' => false, 'message' => 'Cannot delete an active account. Deactivate it first.'], 422);
        }

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 422);
        }

        $username = $user->username;
        $user->delete();

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Transaction',
            'description' => 'Deleted user: ' . $username,
            'ip_address' => request()->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
