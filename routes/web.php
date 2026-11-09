<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AdminController;

// Public Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Two-Factor Authentication & Dashboard Routes (Protected)
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/2fa/enable', [TwoFactorController::class, 'showEnable'])->name('2fa.enable');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable2fa'])->name('2fa.enable.submit');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::put('/profile/picture', [AuthController::class, 'updateProfile'])->name('profile.picture.update');
});

// 2FA Verification Challenge Routes
Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify2fa'])->name('2fa.verify.submit');

// Admin Routes (Protected by role, auth, and back-history prevention)
Route::middleware(['auth', 'role:admin', 'prevent-back-history'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleStatus'])->name('users.toggle');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
    
    // User Deletion Routes (Supporting both 'destroy' and 'delete' names for view compatibility)
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::delete('/users/{user}/delete', [AdminController::class, 'destroy'])->name('users.delete');

    Route::get('/audit-trail', [AdminController::class, 'auditTrail'])->name('audit');
    Route::get('/backups', [AdminController::class, 'backups'])->name('backups');
    Route::post('/backups/create', [AdminController::class, 'createBackup'])->name('backups.create');
    Route::get('/backups/download/{filename}', [AdminController::class, 'downloadBackup'])->name('backups.download');
    Route::delete('/backups/{filename}', [AdminController::class, 'deleteBackup'])->name('backups.delete');
});