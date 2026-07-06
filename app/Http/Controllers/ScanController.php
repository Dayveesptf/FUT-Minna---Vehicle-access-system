<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\GatePoint;
use App\Models\QrCode;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function screen()
    {
        $gates = GatePoint::where('status', 'active')->orderBy('gate_name')->get();
        return view('officer.scan', compact('gates'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'gate_point_id' => 'required|exists:gate_points,id',
        ]);

        $result = QrCode::resolveFromScan($request->qr_code);

        // Malformed payload, or token doesn't match any record — nothing to log against.
        if (!$result['qr']) {
            return response()->json([
                'success' => false,
                'message' => $result['reason'] ?? 'QR code not recognized.',
            ], 404);
        }

        $qr = $result['qr'];
        $qr->load('vehicle.registeredUser');

        // Invalid: revoked or expired. Log the denial against this QR code.
        if (!$result['valid']) {
            AccessLog::create([
                'qr_code_id' => $qr->id,
                'gate_point_id' => $request->gate_point_id,
                'operator_id' => $request->user()->id,
                'scan_timestamp' => now(),
                'access_decision' => 'denied',
                'direction' => null,
                'denial_reason' => $result['reason'],
            ]);

            return response()->json([
                'success' => true,
                'access' => 'denied',
                'message' => $result['reason'],
                'vehicle' => $qr->vehicle,
                'owner' => $qr->vehicle->registeredUser,
            ]);
        }

        // Valid QR — determine direction by checking the last logged direction for this QR.
        $lastLog = AccessLog::where('qr_code_id', $qr->id)
            ->where('access_decision', 'granted')
            ->latest('scan_timestamp')
            ->first();

        $direction = (!$lastLog || $lastLog->direction === 'out') ? 'in' : 'out';

        AccessLog::create([
            'qr_code_id' => $qr->id,
            'gate_point_id' => $request->gate_point_id,
            'operator_id' => $request->user()->id,
            'scan_timestamp' => now(),
            'access_decision' => 'granted',
            'direction' => $direction,
        ]);

        return response()->json([
            'success' => true,
            'access' => $direction === 'in' ? 'entry' : 'exit',
            'message' => $direction === 'in' ? 'Entry granted.' : 'Exit recorded.',
            'vehicle' => $qr->vehicle,
            'owner' => $qr->vehicle->registeredUser,
        ]);
    }
}
