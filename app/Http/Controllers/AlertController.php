<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = AccessLog::with(['qrCode.vehicle.registeredUser', 'gatePoint'])
            ->where('access_decision', 'denied')
            ->orderByDesc('scan_timestamp')
            ->paginate(15);

        return view('admin.alerts.index', compact('alerts'));
    }

    public function acknowledge(Request $request, AccessLog $log)
    {
        $log->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
            'acknowledged_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Alert acknowledged.');
    }

    public function unacknowledgedCount()
    {
        $count = AccessLog::where('access_decision', 'denied')
            ->where('is_acknowledged', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
