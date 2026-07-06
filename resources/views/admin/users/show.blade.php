<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · User Management</p>
                <h1 class="font-display text-2xl font-semibold">{{ $user->first_name }} {{ $user->last_name }}</h1>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-black/50 hover:text-black">&larr; Back to list</a>
        </div>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-8 max-w-3xl mb-6">
        <div class="grid grid-cols-3 gap-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Email</p>
                <p class="font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Phone</p>
                <p class="font-medium">{{ $user->phone }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Category</p>
                <p class="font-medium">{{ ucfirst($user->user_category) }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">ID Number</p>
                <p class="font-medium">{{ $user->id_number ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Department</p>
                <p class="font-medium">{{ $user->department ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Status</p>
                <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                    <span class="badge-dot"></span>{{ ucfirst($user->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center" style="border-bottom: 1px solid var(--line);">
            <p class="font-display font-semibold text-sm">Vehicles ({{ $user->vehicles->count() }})</p>
            <a href="{{ route('admin.vehicles.create') }}" class="text-sm" style="color: var(--blue);">+ Add Vehicle</a>
        </div>
        <table class="vas-table">
            <thead>
                <tr><th>Plate</th><th>Vehicle</th><th>QR Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($user->vehicles as $vehicle)
                    <tr>
                        <td class="plate-mono">{{ $vehicle->plate_number }}</td>
                        <td>{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }}</td>
                        <td>
                            <span class="badge {{ $vehicle->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                                <span class="badge-dot"></span>{{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td><a href="{{ route('admin.vehicles.show', $vehicle) }}" class="text-sm text-black/60 hover:text-black">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-8 text-black/40">No vehicles registered for this user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
