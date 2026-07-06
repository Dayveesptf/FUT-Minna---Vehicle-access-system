<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Analytics</p>
        <h1 class="font-display text-2xl font-semibold">Reports</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="flex gap-2 mb-6">
        @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $key => $label)
            <a href="{{ route('admin.reports.index', ['period' => $key]) }}"
               class="btn {{ $period === $key ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="card p-6">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Total Entries</p>
            <p class="font-display text-3xl font-semibold" style="color: var(--green);">{{ $totalEntries }}</p>
        </div>
        <div class="card p-6">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Total Exits</p>
            <p class="font-display text-3xl font-semibold" style="color: var(--amber);">{{ $totalExits }}</p>
        </div>
        <div class="card p-6">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Total Denied</p>
            <p class="font-display text-3xl font-semibold" style="color: var(--red);">{{ $totalDenied }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="card overflow-hidden">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--line);">
                <p class="font-display font-semibold text-sm">Frequently Visited Vehicles</p>
            </div>
            <table class="vas-table">
                <thead><tr><th>Plate</th><th>Visits</th></tr></thead>
                <tbody>
                    @forelse($frequentVehicles as $entry)
                        <tr>
                            <td class="plate-mono">{{ $entry->qrCode->vehicle->plate_number ?? '—' }}</td>
                            <td>{{ $entry->visits }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-8 text-black/40">No data for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--line);">
                <p class="font-display font-semibold text-sm">Recently Revoked QR Codes</p>
            </div>
            <table class="vas-table">
                <thead><tr><th>Plate</th><th>Token</th></tr></thead>
                <tbody>
                    @forelse($revokedQrCodes as $qr)
                        <tr>
                            <td class="plate-mono">{{ $qr->vehicle->plate_number ?? '—' }}</td>
                            <td class="font-mono-id text-sm">{{ $qr->token }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-8 text-black/40">No revoked QR codes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.reports.export.csv', ['period' => $period]) }}" class="btn btn-secondary">Export Excel (CSV)</a>
        <a href="{{ route('admin.reports.export.pdf', ['period' => $period]) }}" class="btn btn-secondary">Export PDF</a>
    </div>
</x-app-layout>
