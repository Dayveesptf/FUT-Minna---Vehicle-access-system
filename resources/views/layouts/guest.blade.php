<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Vehicle Access') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4" style="background-color: var(--paper);">
        <div class="mb-8 flex items-center gap-3">
            <span class="barrier-mark" aria-hidden="true" style="width: 36px; height: 36px;"></span>
            <div class="flex flex-col leading-tight" style="font-family: 'Space Grotesk', sans-serif;">
                <strong class="text-lg">VAMS</strong>
                <small class="text-xs text-black/40">FUT Minna Gate Control</small>
            </div>
        </div>

        <div class="card w-full max-w-md p-8">
            {{ $slot }}
        </div>

        <p class="text-xs text-black/30 mt-6">Vehicle Access Monitoring System</p>
    </div>
</body>
</html>
