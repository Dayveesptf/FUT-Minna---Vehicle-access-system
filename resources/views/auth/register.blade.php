<x-guest-layout>
    <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Create account</p>
    <h1 class="font-display text-xl font-semibold mb-6">Register</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label class="field-label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="field-input">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="field-input">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="field-input">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field-input">
            @error('password_confirmation') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label">Role</label>
            <select name="role" class="field-input">
                <option value="officer">Security Officer</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-full justify-center">Create account</button>

        <p class="text-center text-sm text-black/50 pt-2">
            Already registered? <a href="{{ route('login') }}" style="color: var(--blue);">Sign in</a>
        </p>
    </form>
</x-guest-layout>
