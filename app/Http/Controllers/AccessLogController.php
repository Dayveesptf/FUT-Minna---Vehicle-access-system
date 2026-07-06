<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\User;
use App\Models\GatePoint;
use Illuminate\Http\Request;

class AccessLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AccessLog::with(['qrCode.vehicle.registeredUser', 'gatePoint', 'operator']);

        if ($request->filled('plate')) {
            $query->whereHas('qrCode.vehicle', function ($q) use ($request) {
                $q->where('plate_number', 'like', '%' . $request->plate . '%');
            });
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        if ($request->filled('gate_point_id')) {
            $query->where('gate_point_id', $request->gate_point_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('scan_timestamp', $request->date);
        }

        if ($request->filled('decision')) {
            $query->where('access_decision', $request->decision);
        }

        $logs = $query->latest('scan_timestamp')->paginate(15)->withQueryString();
        $officers = User::where('role', 'officer')->get();
        $gates = GatePoint::orderBy('gate_name')->get();

        return view('admin.logs.index', compact('logs', 'officers', 'gates'));
    }
}
