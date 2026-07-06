<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Fleet Registry</p>
                <h1 class="font-display text-2xl font-semibold">Registered Vehicles</h1>
            </div>
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary">+ Register Vehicle</a>
        </div>
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
                    <th>Plate Number</th>
                    <th>Owner</th>
                    <th>Vehicle</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr>
                        <td class="plate-mono">{{ $vehicle->plate_number }}</td>
                        <td>{{ $vehicle->registeredUser->first_name }} {{ $vehicle->registeredUser->last_name }}</td>
                        <td>{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }}</td>
                        <td>
                            <span class="badge {{ $vehicle->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                                <span class="badge-dot"></span>{{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-4 justify-end text-sm">
                                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="text-black/60 hover:text-black">View</a>
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-black/60 hover:text-black">Edit</a>
                                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Delete this vehicle?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: rgba(194,59,59,0.7);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-black/40">
                            No vehicles registered yet. Click <strong>+ Register Vehicle</strong> to add the first one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $vehicles->links() }}</div>
</x-app-layout>
