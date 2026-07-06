<?php

namespace App\Http\Controllers;

use App\Models\RegisteredUser;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function index(Request $request)
    {
        $query = RegisteredUser::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('user_category', $request->category);
        }

        $users = $query->withCount('vehicles')->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:registered_users,email',
            'phone'         => 'required|string|max:20',
            'user_category' => 'required|in:student,staff,visitor',
            'id_number'     => 'nullable|string|max:50',
            'department'    => 'nullable|string|max:255',
        ]);

        RegisteredUser::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User registered successfully.');
    }

    public function show(RegisteredUser $user)
    {
        $user->load('vehicles.activeQrCode');
        return view('admin.users.show', compact('user'));
    }

    public function edit(RegisteredUser $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, RegisteredUser $user)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:registered_users,email,' . $user->id,
            'phone'         => 'required|string|max:20',
            'user_category' => 'required|in:student,staff,visitor',
            'id_number'     => 'nullable|string|max:50',
            'department'    => 'nullable|string|max:255',
            'status'        => 'required|in:active,suspended',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(RegisteredUser $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
