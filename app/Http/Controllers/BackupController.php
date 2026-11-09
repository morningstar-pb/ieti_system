<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditTrail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index()
    {
        $backupDir = 'backups';
        if (!Storage::disk('local')->exists($backupDir)) {
            Storage::disk('local')->makeDirectory($backupDir);
        }

        $files = Storage::disk('local')->files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => Storage::disk('local')->size($file),
                'date' => Storage::disk('local')->lastModified($file),
                'path' => $file,
            ];
        }

        // Sort backups by latest date
        usort($backups, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('admin.backups', compact('backups'));
    }

    public function createDatabaseBackup(Request $request)
    {
        $filename = 'db-backup-' . date('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path('app/backups/' . $filename);

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > "%s"',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            $backupPath
        );

        $result = null;
        $output = null;
        exec($command, $output, $result);

        if ($result === 0) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action_type' => 'Database Backup',
                'description' => 'Manually generated database backup: ' . $filename,
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Database backup created successfully!');
        }

        return back()->withErrors(['error' => 'Failed to create database backup. Ensure mysqldump is accessible in your system PATH.']);
    }

    public function download($fileName): BinaryFileResponse
    {
        $filePath = storage_path('app/backups/' . basename($fileName));
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        abort(404, 'Backup file not found.');
    }

    public function delete(Request $request, $fileName)
    {
        $filePath = 'backups/' . basename($fileName);

        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);

            AuditTrail::create([
                'user_id' => Auth::id(),
                'action_type' => 'Backup Deleted',
                'description' => 'Deleted backup file: ' . $fileName,
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Backup deleted successfully.');
        }

        return back()->withErrors(['error' => 'Backup file not found.']);
    }
}