<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Fleet Registry</p>
        <h1 class="font-display text-2xl font-semibold">Register Vehicle</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-8 max-w-2xl">
        @if($registeredUsers->isEmpty())
            <div class="mb-5 px-4 py-3 rounded-lg text-sm" style="background: rgba(232,166,60,0.1); color: #8a6417; border: 1px solid rgba(232,166,60,0.3);">
                No registered users exist yet. <a href="{{ route('admin.users.create') }}" style="text-decoration: underline;">Register a user first</a>, then come back to assign a vehicle to them.
            </div>
        @endif

        <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="field-label">Registered User (Owner)</label>
                <select name="registered_user_id" class="field-input">
                    <option value="">Select a user…</option>
                    @foreach($registeredUsers as $user)
                        <option value="{{ $user->id }}" {{ old('registered_user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }} — {{ ucfirst($user->user_category) }}
                        </option>
                    @endforeach
                </select>
                @error('registered_user_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label">Plate Number</label>
                <input type="text" name="plate_number" value="{{ old('plate_number') }}" class="field-input font-mono-id">
                @error('plate_number') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="field-label">Brand</label>
                    <input type="text" name="vehicle_brand" value="{{ old('vehicle_brand') }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Model</label>
                    <input type="text" name="vehicle_model" value="{{ old('vehicle_model') }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Color</label>
                    <input type="text" name="vehicle_color" value="{{ old('vehicle_color') }}" class="field-input">
                </div>
            </div>

            <div>
                <label class="field-label">Vehicle Type</label>
                <input type="text" name="vehicle_type" value="{{ old('vehicle_type') }}" placeholder="e.g. Car, Bike, Van" class="field-input">
            </div>

            <div>
                <label class="field-label">QR Expiry (optional — for temporary/visitor passes)</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="field-input">
                <p class="text-xs text-black/40 mt-1">Leave blank for a permanent pass (students/staff). Set a date for visitor passes.</p>
                @error('expiry_date') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="btn btn-primary">Register Vehicle &amp; Issue QR</button>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
