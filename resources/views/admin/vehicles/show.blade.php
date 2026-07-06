<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Fleet Registry</p>
                <h1 class="font-display text-2xl font-semibold">Vehicle Details</h1>
            </div>
            <a href="{{ route('admin.vehicles.index') }}" class="text-sm text-black/50 hover:text-black">&larr; Back to list</a>
        </div>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-8 max-w-2xl mb-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Owner</p>
                <p class="font-medium">
                    <a href="{{ route('admin.users.show', $vehicle->registeredUser) }}" style="color: var(--blue);">
                        {{ $vehicle->registeredUser->first_name }} {{ $vehicle->registeredUser->last_name }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Owner Category</p>
                <p class="font-medium">{{ ucfirst($vehicle->registeredUser->user_category) }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Plate Number</p>
                <p class="plate-mono">{{ $vehicle->plate_number }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Vehicle</p>
                <p class="font-medium">{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }} ({{ $vehicle->vehicle_color }})</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Type</p>
                <p class="font-medium">{{ $vehicle->vehicle_type }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Status</p>
                <span class="badge {{ $vehicle->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                    <span class="badge-dot"></span>{{ ucfirst($vehicle->status) }}
                </span>
            </div>
        </div>

        <div class="pt-6 mt-6 flex gap-3" style="border-top: 1px solid var(--line);">
            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-secondary">Edit</a>
            <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Delete this vehicle?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <div class="card p-8 max-w-2xl">
        <p class="font-display font-semibold text-sm mb-4">QR Code Credential</p>

        @if($vehicle->activeQrCode)
            <div class="flex items-start gap-6">
                <div class="p-4 rounded-lg" style="background: var(--paper); border: 1px solid var(--line);">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->margin(1)->generate($vehicle->activeQrCode->encrypted_payload) !!}
                </div>
                <div class="flex-1">
                    <p class="text-sm text-black/50 mb-1">Token: <span class="font-mono-id">{{ $vehicle->activeQrCode->token }}</span></p>
                    <p class="text-sm text-black/50 mb-1">Issued: {{ $vehicle->activeQrCode->generation_date->format('d M Y, H:i') }}</p>
                    <p class="text-sm text-black/50 mb-4">
                        Expires: {{ $vehicle->activeQrCode->expiry_date?->format('d M Y, H:i') ?? 'Never (permanent pass)' }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.vehicles.qr.download', $vehicle) }}" class="btn btn-secondary">Download SVG</a>
                        <a href="{{ route('admin.vehicles.qr.print', $vehicle) }}" target="_blank" class="btn btn-secondary">Print</a>
                        <form action="{{ route('admin.vehicles.qr.email', $vehicle) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Email to Owner</button>
                        </form>
                        <form action="{{ route('admin.vehicles.qr.reissue', $vehicle) }}" method="POST" onsubmit="return confirm('This will revoke the current QR permanently and issue a new one. Continue?')">
                            @csrf
                            <button type="submit" class="btn btn-danger">Revoke &amp; Reissue</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <p class="text-black/40 text-sm">No active QR code. This may mean it was revoked and not yet reissued.</p>
            <form action="{{ route('admin.vehicles.qr.reissue', $vehicle) }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-primary">Issue New QR Code</button>
            </form>
        @endif

        @if($vehicle->qrCodes->where('status', 'revoked')->count())
            <div class="mt-6 pt-6" style="border-top: 1px solid var(--line);">
                <p class="text-xs uppercase tracking-wide text-black/40 mb-2">QR History</p>
                <table class="vas-table">
                    <thead><tr><th>Token</th><th>Issued</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($vehicle->qrCodes->sortByDesc('created_at') as $qr)
                            <tr>
                                <td class="font-mono-id text-sm">{{ $qr->token }}</td>
                                <td class="text-sm">{{ $qr->generation_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $qr->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                                        <span class="badge-dot"></span>{{ ucfirst($qr->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
