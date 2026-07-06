<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; background: #F6F5F1; padding: 32px; color: #14161C; }
        .card { background: #fff; border-radius: 12px; padding: 32px; max-width: 480px; margin: 0 auto; border: 1px solid #E4E2DC; }
        .barrier { height: 4px; border-radius: 2px; background: repeating-linear-gradient(45deg, #E8A63C, #E8A63C 8px, #14161C 8px, #14161C 16px); margin: 16px 0 24px; }
        .plate { font-family: monospace; font-size: 18px; font-weight: 700; letter-spacing: 0.04em; }
        .meta { font-size: 13px; color: #6b6b6b; margin-top: 4px; }
        .footer { font-size: 12px; color: #999; margin-top: 24px; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <strong style="font-size: 15px;">VAMS — FUT Minna Gate Control</strong>
        <div class="barrier"></div>

        <p>Hello {{ $vehicle->registeredUser->first_name }},</p>

        <p>Your vehicle access QR code has been issued. Please find it attached to this email as an SVG file — present it (printed or on your phone) at any campus gate for scanning.</p>

        <p class="plate">{{ $vehicle->plate_number }}</p>
        <p class="meta">{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }} ({{ $vehicle->vehicle_color }})</p>
        <p class="meta">
            Expires: {{ $vehicle->activeQrCode->expiry_date?->format('d M Y') ?? 'Permanent pass' }}
        </p>

        <p style="margin-top: 24px; font-size: 13px; color: #6b6b6b;">
            If you did not request this or believe it was issued in error, please contact campus security immediately.
        </p>

        <div class="footer">Federal University of Technology, Minna — Vehicle Access Monitoring System</div>
    </div>
</body>
</html>
