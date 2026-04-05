<x-guest-layout>
    <div class="space-y-[var(--space-sm)]">
        <div>
            <h1 class="text-[clamp(1.5rem,2.2vw,2rem)] font-semibold tracking-tight text-[var(--text-primary)]">
                Atur ulang kata sandi
            </h1>
            <p class="mt-2 text-sm text-[var(--text-secondary)]">Masukkan email dan kata sandi baru untuk akunmu.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]" for="email">Email</label>
                <input id="email" class="input-field mt-2" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                @error('email')
                    <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]" for="password">Kata sandi baru</label>
                    <input id="password" class="input-field mt-2" type="password" name="password" required autocomplete="new-password" />
                    @error('password')
                        <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]" for="password_confirmation">Konfirmasi</label>
                    <input id="password_confirmation" class="input-field mt-2" type="password" name="password_confirmation" required autocomplete="new-password" />
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-[var(--text-secondary)]">Kembali ke login</a>
                <button type="submit" class="btn-primary px-6">Reset Kata Sandi</button>
            </div>
        </form>
    </div>
</x-guest-layout>
