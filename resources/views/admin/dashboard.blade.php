<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Overview</p>
        <h1 class="font-display text-2xl font-semibold">Dashboard</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="grid grid-cols-5 gap-4 mb-8">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Registered Users</p>
            <p class="font-display text-3xl font-semibold">{{ $totalRegisteredUsers }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Vehicles</p>
            <p class="font-display text-3xl font-semibold">{{ $totalVehicles }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Active QR Codes</p>
            <p class="font-display text-3xl font-semibold" style="color: var(--blue);">{{ $activeQrCodes }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Today's Events</p>
            <p class="font-display text-3xl font-semibold" style="color: var(--green);">{{ $todayEvents }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Today's Denied</p>
            <p class="font-display text-3xl font-semibold" style="color: var(--red);">{{ $todayDenied }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="card p-6">
            <p class="text-xs uppercase tracking-wide text-black/40 mb-2">Total Officers</p>
            <p class="font-display text-3xl font-semibold">{{ $totalOfficers }}</p>
        </div>

        <div class="card overflow-hidden col-span-2">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--line);">
                <p class="font-display font-semibold text-sm">Recent Activity</p>
            </div>
            <table class="vas-table">
                <thead>
                    <tr>
                        <th>Plate</th>
                        <th>Decision</th>
                        <th>Gate</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivities as $log)
                        <tr>
                            <td class="plate-mono">{{ $log->qrCode->vehicle->plate_number ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $log->access_decision === 'granted' ? 'badge-active' : 'badge-suspended' }}">
                                    <span class="badge-dot"></span>
                                    {{ ucfirst($log->access_decision) }}{{ $log->direction ? ' · ' . strtoupper($log->direction) : '' }}
                                </span>
                            </td>
                            <td class="text-sm">{{ $log->gatePoint->gate_name ?? '—' }}</td>
                            <td class="text-sm text-black/50">{{ $log->scan_timestamp->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-8 text-black/40">No activity yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
