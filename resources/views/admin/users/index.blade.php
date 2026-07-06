<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Admin · User Management</p>
                <h1 class="font-display text-2xl font-semibold">Registered Users</h1>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Register User</a>
        </div>
        <div class="barrier-divider"></div>
    </x-slot>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm" style="background: rgba(27,122,77,0.08); color: #1B7A4D; border: 1px solid rgba(27,122,77,0.2);">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="card p-5 mb-6 flex gap-3 items-end">
        <div class="flex-1">
            <label class="field-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email" class="field-input">
        </div>
        <div>
            <label class="field-label">Category</label>
            <select name="category" class="field-input">
                <option value="">All</option>
                <option value="student" {{ request('category') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="staff" {{ request('category') === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="visitor" {{ request('category') === 'visitor' ? 'selected' : '' }}>Visitor</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    <div class="card overflow-hidden">
        <table class="vas-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Vehicles</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td class="text-sm">{{ $user->email }}</td>
                        <td>{{ ucfirst($user->user_category) }}</td>
                        <td>{{ $user->vehicles_count }}</td>
                        <td>
                            <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                                <span class="badge-dot"></span>{{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-4 justify-end text-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-black/60 hover:text-black">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-black/60 hover:text-black">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user and their vehicles?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: rgba(194,59,59,0.7);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-12 text-black/40">No registered users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
</x-app-layout>
