<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\AuditTrail;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        $recentUsers = $users;
        $auditTrails = AuditTrail::orderBy('created_at', 'desc')->take(50)->get();

        return view('admin.dashboard', compact('totalUsers', 'users', 'recentUsers', 'auditTrails'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own active account.']);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Toggle User Status',
            'description' => 'User ' . $user->username . ' status changed to ' . ($user->is_active ? 'Active' : 'Deactivated'),
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'User status updated successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->withErrors(['error' => 'You cannot strip your own admin role.']);
        }

        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Update Role',
            'description' => 'Updated role for user ' . $user->username . ' from ' . $oldRole . ' to ' . $request->role,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $username = $user->username;
        $user->delete();

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Delete User',
            'description' => 'Deleted user account: ' . $username,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'User deleted successfully.');
    }

    public function auditTrail(Request $request)
    {
        $query = AuditTrail::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        $auditTrails = $query->paginate(20);
        return view('admin.audit', compact('auditTrails'));
    }

    public function backups()
    {
        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => number_format($file->getSize() / 1024 / 1024, 2) . ' MB',
                'created_at' => Carbon::createFromTimestamp($file->getMTime())->toDateTimeString(),
            ];
        }

        usort($backups, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return view('admin.backups', compact('backups'));
    }

    public function createBackup()
    {
        try {
            Artisan::call('backup:run');

            AuditTrail::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create Backup',
                'description' => 'Manual database backup generated.',
                'ip_address' => request()->ip(),
            ]);

            return back()->with('success', 'Database backup created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    public function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!File::exists($filePath)) {
            abort(404, 'Backup file not found.');
        }

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action_type' => 'Download Backup',
            'description' => 'Downloaded backup file: ' . $filename,
            'ip_address' => request()->ip(),
        ]);

        return response()->download($filePath);
    }

    public function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (File::exists($filePath)) {
            File::delete($filePath);

            AuditTrail::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete Backup',
                'description' => 'Deleted backup file: ' . $filename,
                'ip_address' => request()->ip(),
            ]);

            return back()->with('success', 'Backup deleted successfully.');
        }

        return back()->withErrors(['error' => 'Backup file not found.']);
    }
}