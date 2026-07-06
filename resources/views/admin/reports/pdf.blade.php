<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #14161C; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        p.meta { color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f4f4f2; text-transform: uppercase; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Vehicle Access Report — {{ ucfirst($period) }}</h1>
    <p class="meta">{{ $start->format('d M Y, H:i') }} to {{ $end->format('d M Y, H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Plate</th><th>Owner</th><th>Gate</th><th>Time</th><th>Decision</th><th>Officer</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->qrCode->vehicle->plate_number ?? '—' }}</td>
                    <td>{{ $log->qrCode->vehicle->registeredUser->first_name ?? '—' }} {{ $log->qrCode->vehicle->registeredUser->last_name ?? '' }}</td>
                    <td>{{ $log->gatePoint->gate_name ?? '—' }}</td>
                    <td>{{ $log->scan_timestamp->format('d M, H:i') }}</td>
                    <td>{{ ucfirst($log->access_decision) }}</td>
                    <td>{{ $log->operator->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No records for this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
