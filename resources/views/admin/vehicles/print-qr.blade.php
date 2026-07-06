<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Label — {{ $vehicle->plate_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=IBM+Plex+Mono:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: #F6F5F1;
        }
        .label {
            border: 2px solid #14161C;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            background: #fff;
        }
        .plate {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 22px;
            font-weight: 600;
            margin-top: 16px;
            letter-spacing: 0.04em;
        }
        .owner {
            font-size: 13px;
            color: #6b6b6b;
            margin-top: 4px;
        }
        .print-btn {
            margin-top: 20px;
            padding: 8px 18px;
            border-radius: 8px;
            border: none;
            background: #1D4ED8;
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer;
        }
        @media print {
            .print-btn { display: none; }
            body { background: #fff; }
            .label { border: none; }
        }
    </style>
</head>
<body>
    <div class="label">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->margin(1)->generate($vehicle->activeQrCode->encrypted_payload) !!}
        <div class="plate">{{ $vehicle->plate_number }}</div>
        <div class="owner">{{ $vehicle->registeredUser->first_name }} {{ $vehicle->registeredUser->last_name }} — FUT Minna</div>
        <br>
        <button class="print-btn" onclick="window.print()">Print Label</button>
    </div>
</body>
</html>
