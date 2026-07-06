<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · Infrastructure</p>
        <h1 class="font-display text-2xl font-semibold">Gate Points</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm" style="background: rgba(27,122,77,0.08); color: #1B7A4D; border: 1px solid rgba(27,122,77,0.2);">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">
        <div class="card p-6">
            <p class="font-display font-semibold text-sm mb-4">Add Gate Point</p>
            <form action="{{ route('admin.gates.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="field-label">Gate Name</label>
                    <input type="text" name="gate_name" class="field-input">
                    @error('gate_name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label">Location</label>
                    <input type="text" name="location" class="field-input">
                    @error('location') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center">Add Gate</button>
            </form>
        </div>

        <div class="card overflow-hidden col-span-2">
            <table class="vas-table">
                <thead>
                    <tr><th>Gate Name</th><th>Location</th><th>Status</th><th>Scans</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($gates as $gate)
                        <tr>
                            <form action="{{ route('admin.gates.update', $gate) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td><input type="text" name="gate_name" value="{{ $gate->gate_name }}" class="field-input" style="border:none; background:transparent; padding:2px;"></td>
                                <td><input type="text" name="location" value="{{ $gate->location }}" class="field-input" style="border:none; background:transparent; padding:2px;"></td>
                                <td>
                                    <select name="status" class="field-input" style="border:none; background:transparent; padding:2px;">
                                        <option value="active" {{ $gate->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $gate->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>
                                <td class="text-sm text-black/50">{{ $gate->access_logs_count }}</td>
                                <td>
                                    <div class="flex gap-3 justify-end text-sm">
                                        <button type="submit" style="color: var(--blue);">Save</button>
                            </form>
                                        <form action="{{ route('admin.gates.destroy', $gate) }}" method="POST" onsubmit="return confirm('Delete this gate point?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: rgba(194,59,59,0.7);">Delete</button>
                                        </form>
                                    </div>
                                </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-12 text-black/40">No gate points configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
