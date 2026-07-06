<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Fleet Registry</p>
        <h1 class="font-display text-2xl font-semibold">Edit Vehicle</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-8 max-w-2xl">
        <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="field-label">Registered User (Owner)</label>
                <select name="registered_user_id" class="field-input">
                    @foreach($registeredUsers as $user)
                        <option value="{{ $user->id }}" {{ $vehicle->registered_user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }} — {{ ucfirst($user->user_category) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label">Plate Number</label>
                <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" class="field-input font-mono-id">
                @error('plate_number') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="field-label">Brand</label>
                    <input type="text" name="vehicle_brand" value="{{ old('vehicle_brand', $vehicle->vehicle_brand) }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Model</label>
                    <input type="text" name="vehicle_model" value="{{ old('vehicle_model', $vehicle->vehicle_model) }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Color</label>
                    <input type="text" name="vehicle_color" value="{{ old('vehicle_color', $vehicle->vehicle_color) }}" class="field-input">
                </div>
            </div>

            <div>
                <label class="field-label">Vehicle Type</label>
                <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $vehicle->vehicle_type) }}" class="field-input">
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
