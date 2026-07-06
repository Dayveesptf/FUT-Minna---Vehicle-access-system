<x-guest-layout>
    <p class="text-xs uppercase tracking-wide text-black/40 mb-1">Sign in</p>
    <h1 class="font-display text-xl font-semibold mb-6">Welcome back</h1>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label class="field-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field-input">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="field-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="field-input">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-black/60">
                <input type="checkbox" name="remember" class="rounded border-gray-300">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-black/50 hover:text-black">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-full justify-center">Sign in</button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-black/50 pt-2">
                No account? <a href="{{ route('register') }}" style="color: var(--blue);">Register here</a>
            </p>
        @endif
    </form>
</x-guest-layout>
