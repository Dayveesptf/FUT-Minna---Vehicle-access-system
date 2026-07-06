<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\AccessLog;
use App\Models\QrCode;
use App\Models\RegisteredUser;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('officer.scan');
    }

    public function admin()
    {
        $totalRegisteredUsers = RegisteredUser::count();
        $totalVehicles = Vehicle::count();
        $activeQrCodes = QrCode::where('status', 'active')->count();
        $totalOfficers = User::where('role', 'officer')->count();

        $todayEvents = AccessLog::whereDate('scan_timestamp', today())->count();
        $todayDenied = AccessLog::whereDate('scan_timestamp', today())
            ->where('access_decision', 'denied')
            ->count();

        $recentActivities = AccessLog::with(['qrCode.vehicle.registeredUser', 'gatePoint', 'operator'])
            ->latest('scan_timestamp')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalRegisteredUsers', 'totalVehicles', 'activeQrCodes',
            'totalOfficers', 'todayEvents', 'todayDenied', 'recentActivities'
        ));
    }
}
