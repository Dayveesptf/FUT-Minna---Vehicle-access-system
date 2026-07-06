<x-app-layout>
    <x-slot name="header">
        <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · User Management</p>
        <h1 class="font-display text-2xl font-semibold">Register User</h1>
        <div class="barrier-divider"></div>
    </x-slot>

    <div class="card p-8 max-w-2xl">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="field-input">
                    @error('first_name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="field-input">
                    @error('last_name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="field-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="field-input">
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="field-input">
                    @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label">Category</label>
                    <select name="user_category" class="field-input">
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                        <option value="visitor">Visitor</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="field-label">ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}" class="field-input">
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="btn btn-primary">Register User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
