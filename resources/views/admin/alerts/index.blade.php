<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Security</p>
        <h1 class="font-display text-2xl font-semibold">Access Alerts</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm" style="background: rgba(27,122,77,0.08); color: #1B7A4D; border: 1px solid rgba(27,122,77,0.2);">
            {{ session('success') }}
        </div>
    @endif

    <div class="card overflow-hidden">
        <table class="vas-table">
            <thead>
                <tr>
                    <th>Plate</th>
                    <th>Owner</th>
                    <th>Gate</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $log)
                    <tr>
                        <td class="plate-mono">{{ $log->qrCode->vehicle->plate_number ?? '—' }}</td>
                        <td>{{ $log->qrCode->vehicle->registeredUser->first_name ?? '—' }} {{ $log->qrCode->vehicle->registeredUser->last_name ?? '' }}</td>
                        <td class="text-sm">{{ $log->gatePoint->gate_name ?? '—' }}</td>
                        <td class="text-sm">{{ $log->scan_timestamp->format('d M, H:i') }}</td>
                        <td class="text-sm text-black/50">{{ $log->denial_reason ?? '—' }}</td>
                        <td>
                            @if($log->is_acknowledged)
                                <span class="badge badge-active"><span class="badge-dot"></span>Acknowledged</span>
                            @else
                                <span class="badge badge-suspended"><span class="badge-dot"></span>New</span>
                            @endif
                        </td>
                        <td>
                            @unless($log->is_acknowledged)
                                <form action="{{ route('admin.alerts.acknowledge', $log) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm" style="color: var(--blue);">Acknowledge</button>
                                </form>
                            @else
                                <span class="text-xs text-black/40">by {{ $log->acknowledgedBy->name ?? '—' }}</span>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-12 text-black/40">No denied access attempts recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $alerts->links() }}</div>
</x-app-layout>
