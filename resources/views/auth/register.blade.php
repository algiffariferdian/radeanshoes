<x-guest-layout>
    <div class="space-y-4">
        <div>
            <h1 class="text-[clamp(1.5rem,2.2vw,2rem)] font-semibold tracking-tight text-[var(--text-primary)]">
                Daftar
            </h1>
            <p class="mt-2 text-sm text-[var(--text-secondary)]">Buat akun untuk mulai belanja.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <input id="name" class="input-field" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama lengkap" />
                @error('name')
                    <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Email" />
                    @error('email')
                        <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input id="phone" class="input-field" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="Nomor telepon" />
                    @error('phone')
                        <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <input id="password" class="input-field" type="password" name="password" required autocomplete="new-password" placeholder="Kata sandi" />
                    @error('password')
                        <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input id="password_confirmation" class="input-field" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi kata sandi" />
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-[var(--error)]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-primary w-full">Daftar</button>

            <p class="text-center text-sm text-[var(--text-secondary)]">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-[var(--accent-primary)]">Masuk</a>
            </p>
        </form>
    </div>
</x-guest-layout>
