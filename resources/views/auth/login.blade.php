<x-guest-layout>
    <div class="space-y-4">
        <div>
            <h1 class="text-[clamp(1.5rem,2.2vw,2rem)] font-semibold tracking-tight text-[var(--text-primary)]">
                Masuk
            </h1>
            <p class="mt-2 text-sm text-[var(--text-secondary)]">Gunakan akun untuk melanjutkan belanja.</p>
        </div>

        @if (session('status'))
            <div class="rounded-[0.7rem] border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Email" />
                @error('email')
                    <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input id="password" class="input-field" type="password" name="password" required autocomplete="current-password" placeholder="Kata sandi" />
                @error('password')
                    <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-4 text-sm">
                <label for="remember_me" class="inline-flex items-center gap-2 text-[var(--text-secondary)]">
                    <input id="remember_me" type="checkbox" class="rounded border-[var(--border-strong)] text-[var(--accent-primary)] focus:ring-[var(--accent-primary)]" name="remember">
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-[var(--accent-primary)]" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full">Masuk</button>

            <p class="text-center text-sm text-[var(--text-secondary)]">
                Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-[var(--accent-primary)]">Daftar</a>
            </p>
        </form>
    </div>
</x-guest-layout>
