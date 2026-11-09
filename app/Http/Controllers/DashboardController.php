<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditTrail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $todayLogins = AuditTrail::where('action_type', 'Access')
            ->where('description', 'like', '%logged in%')
            ->whereDate('created_at', Carbon::today())
            ->count();
        $totalAudit = AuditTrail::count();

        // Chart data
        $usersByRole = User::selectRaw("role, count(*) as count")
            ->groupBy('role')
            ->pluck('count', 'role');

        $auditByType = AuditTrail::selectRaw("action_type, count(*) as count")
            ->groupBy('action_type')
            ->pluck('count', 'action_type');

        return view('dashboard', compact(
            'totalUsers', 'activeUsers', 'todayLogins', 'totalAudit',
            'usersByRole', 'auditByType'
        ));
    }
}
