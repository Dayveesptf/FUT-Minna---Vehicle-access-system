<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Vehicle;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        [$start, $end] = $this->resolvePeriod($period);

        $totalEntries = AccessLog::where('access_decision', 'granted')
            ->where('direction', 'in')
            ->whereBetween('scan_timestamp', [$start, $end])
            ->count();

        $totalExits = AccessLog::where('access_decision', 'granted')
            ->where('direction', 'out')
            ->whereBetween('scan_timestamp', [$start, $end])
            ->count();

        $totalDenied = AccessLog::where('access_decision', 'denied')
            ->whereBetween('scan_timestamp', [$start, $end])
            ->count();

        $frequentVehicles = AccessLog::select('qr_code_id', DB::raw('count(*) as visits'))
            ->whereBetween('scan_timestamp', [$start, $end])
            ->groupBy('qr_code_id')
            ->orderByDesc('visits')
            ->with('qrCode.vehicle')
            ->take(5)
            ->get();

        $revokedQrCodes = QrCode::where('status', 'revoked')->with('vehicle')->latest()->take(10)->get();

        return view('admin.reports.index', compact(
            'period', 'totalEntries', 'totalExits', 'totalDenied',
            'frequentVehicles', 'revokedQrCodes', 'start', 'end'
        ));
    }

    public function exportCsv(Request $request)
    {
        $period = $request->get('period', 'daily');
        [$start, $end] = $this->resolvePeriod($period);

        $logs = AccessLog::with(['qrCode.vehicle.registeredUser', 'gatePoint', 'operator'])
            ->whereBetween('scan_timestamp', [$start, $end])
            ->get();

        $filename = 'access-report-' . $period . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Plate Number', 'Owner', 'Gate', 'Scan Time', 'Decision', 'Direction', 'Officer', 'Denial Reason']);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->qrCode->vehicle->plate_number ?? '—',
                    trim(($log->qrCode->vehicle->registeredUser->first_name ?? '') . ' ' . ($log->qrCode->vehicle->registeredUser->last_name ?? '')),
                    $log->gatePoint->gate_name ?? '—',
                    $log->scan_timestamp->format('Y-m-d H:i'),
                    $log->access_decision,
                    $log->direction ?? '—',
                    $log->operator->name ?? '—',
                    $log->denial_reason ?? '—',
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'daily');
        [$start, $end] = $this->resolvePeriod($period);

        $logs = AccessLog::with(['qrCode.vehicle.registeredUser', 'gatePoint', 'operator'])
            ->whereBetween('scan_timestamp', [$start, $end])
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('logs', 'period', 'start', 'end'));

        return $pdf->download('access-report-' . $period . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
