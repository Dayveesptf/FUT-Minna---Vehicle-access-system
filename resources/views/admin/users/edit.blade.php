<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · User Management</p>
        <h1 class="font-display text-2xl font-semibold">Edit User</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-8 max-w-2xl">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="field-input">
                </div>
            </div>

            <div>
                <label class="field-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="field-input">
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Category</label>
                    <select name="user_category" class="field-input">
                        <option value="student" {{ $user->user_category === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ $user->user_category === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="visitor" {{ $user->user_category === 'visitor' ? 'selected' : '' }}>Visitor</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number', $user->id_number) }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}" class="field-input">
                </div>
            </div>

            <div>
                <label class="field-label">Status</label>
                <select name="status" class="field-input">
                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
