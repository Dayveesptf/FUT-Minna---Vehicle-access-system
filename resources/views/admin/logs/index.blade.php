<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Records</p>
        <h1 class="font-display text-2xl font-semibold">Access Logs</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <form method="GET" class="card p-5 mb-6 grid grid-cols-6 gap-3 items-end">
        <div>
            <label class="field-label">Plate Number</label>
            <input type="text" name="plate" value="{{ request('plate') }}" class="field-input" placeholder="Search plate">
        </div>
        <div>
            <label class="field-label">Officer</label>
            <select name="operator_id" class="field-input">
                <option value="">All</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->id }}" {{ request('operator_id') == $officer->id ? 'selected' : '' }}>{{ $officer->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label">Gate</label>
            <select name="gate_point_id" class="field-input">
                <option value="">All</option>
                @foreach($gates as $gate)
                    <option value="{{ $gate->id }}" {{ request('gate_point_id') == $gate->id ? 'selected' : '' }}>{{ $gate->gate_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="field-input">
        </div>
        <div>
            <label class="field-label">Decision</label>
            <select name="decision" class="field-input">
                <option value="">All</option>
                <option value="granted" {{ request('decision') === 'granted' ? 'selected' : '' }}>Granted</option>
                <option value="denied" {{ request('decision') === 'denied' ? 'selected' : '' }}>Denied</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="card overflow-hidden">
        <table class="vas-table">
            <thead>
                <tr>
                    <th>Plate</th>
                    <th>Owner</th>
                    <th>Gate</th>
                    <th>Time</th>
                    <th>Decision</th>
                    <th>Direction</th>
                    <th>Officer</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="plate-mono">{{ $log->qrCode->vehicle->plate_number ?? '—' }}</td>
                        <td>{{ $log->qrCode->vehicle->registeredUser->first_name ?? '—' }} {{ $log->qrCode->vehicle->registeredUser->last_name ?? '' }}</td>
                        <td class="text-sm">{{ $log->gatePoint->gate_name ?? '—' }}</td>
                        <td class="text-sm">{{ $log->scan_timestamp->format('d M, H:i') }}</td>
                        <td>
                            <span class="badge {{ $log->access_decision === 'granted' ? 'badge-active' : 'badge-suspended' }}">
                                <span class="badge-dot"></span>{{ ucfirst($log->access_decision) }}
                            </span>
                        </td>
                        <td class="text-sm">{{ $log->direction ? strtoupper($log->direction) : '—' }}</td>
                        <td class="text-sm">{{ $log->operator->name ?? '—' }}</td>
                        <td class="text-sm text-black/50">{{ $log->denial_reason ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-12 text-black/40">No logs match this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $logs->links() }}</div>
</x-app-layout>
