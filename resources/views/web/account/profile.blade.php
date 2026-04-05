<x-layouts.store :title="'Akun Saya - RadeanShoes'">
    @php
        $memberSince = $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-';
        $ordersCount = (int) ($user->orders_count ?? 0);
        $addressesCount = (int) ($user->addresses_count ?? 0);
        $profilePhotoUrl = $user->profilePhotoUrl();
        $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))
            ->filter()
            ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->join('');
    @endphp

    <div class="space-y-[var(--space-lg)]" x-data>
        <x-store.breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Akun Saya'],
    ]" />

        <div class="grid gap-[var(--space-md)] lg:grid-cols-[minmax(0,1fr)_240px] lg:items-start">
            <section class="rounded-[0.95rem] border border-[var(--border-soft)] bg-white">
                <div class="px-[var(--space-md)] py-[var(--space-md)]">
                    <div class="space-y-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[var(--text-muted)]">Akun
                            Saya</p>
                        <h1
                            class="text-[clamp(1.65rem,2.6vw,2.35rem)] font-semibold tracking-tight text-[var(--text-primary)]">
                            {{ $user->name }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--text-secondary)]">
                            <span>{{ $user->email }}</span>
                            <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                            <span data-live-clock>Sinkronisasi...</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-sm)]">
                    <div class="grid gap-[var(--space-sm)] text-center sm:grid-cols-3 sm:justify-items-center">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Total
                                pesanan</p>
                            <p class="mt-2 text-lg font-semibold text-[var(--text-primary)]">
                                {{ number_format($ordersCount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Alamat
                                tersimpan</p>
                            <p class="mt-2 text-lg font-semibold text-[var(--text-primary)]">
                                {{ number_format($addressesCount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Member
                                sejak</p>
                            <p class="mt-2 text-sm font-semibold text-[var(--text-primary)]">{{ $memberSince }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex items-start justify-center lg:justify-end">
                <div class="h-60 w-96 overflow-hidden rounded-[1rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]"
                    data-profile-avatar>
                    @if ($profilePhotoUrl)
                        <img src="{{ $profilePhotoUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-2xl font-semibold text-[var(--text-muted)]">
                            {{ $initials ?: 'RS' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <section class="rounded-[0.95rem] border border-[var(--border-soft)] bg-white">
            <div class="grid gap-[var(--space-md)] lg:grid-cols-[minmax(0,1fr)_minmax(0,0.62fr)]">
                <div class="border-b border-[var(--border-soft)] lg:border-b-0 lg:border-r">
                    <section class="px-[var(--space-md)] py-[var(--space-md)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                                    Profil</p>
                                <h2 class="text-base font-semibold text-[var(--text-primary)]">Informasi akun</h2>
                            </div>
                            <span class="text-xs text-[var(--text-secondary)]">Member sejak {{ $memberSince }}</span>
                        </div>

                        <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data"
                            class="mt-[var(--space-sm)] grid gap-[var(--space-sm)]" data-ajax-form="profile">
                            @csrf
                            @method('PATCH')

                            <div>
                                <p class="sr-only">Foto profil</p>
                                <div class="mt-3 flex flex-wrap items-center gap-4">
                                    <div class="h-16 w-16 overflow-hidden rounded-[0.75rem] border border-[var(--border-soft)] bg-[var(--surface-soft)]"
                                        data-profile-avatar>
                                        @if ($profilePhotoUrl)
                                            <img src="{{ $profilePhotoUrl }}" alt="{{ $user->name }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div
                                                class="flex h-full items-center justify-center text-lg font-semibold text-[var(--text-muted)]">
                                                {{ $initials ?: 'RS' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="inline-flex items-center gap-2 rounded-[0.75rem] border border-[var(--border-strong)] bg-white px-4 py-2 text-sm font-semibold text-[var(--text-primary)] transition hover:bg-[var(--surface-soft)]">
                                            Unggah foto
                                            <input type="file" name="photo" accept="image/*" class="sr-only"
                                                data-profile-photo-input>
                                        </label>
                                        <p class="text-xs text-[var(--text-muted)]">JPG/PNG/WEBP. Maks 2MB.</p>
                                        <p class="text-xs text-[var(--accent-primary)] hidden" data-photo-note>
                                            Foto profil sudah diunggah. Tidak perlu Simpan Profil lagi.
                                        </p>
                                        <p class="text-xs text-[var(--error)] hidden" data-error="photo"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]"
                                        for="name">Nama lengkap</label>
                                    <input id="name" name="name" value="{{ old('name', $user->name) }}"
                                        class="input-field mt-2" required>
                                    <p class="mt-1 text-xs text-[var(--error)] hidden" data-error="name"></p>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]"
                                        for="phone">Nomor telepon</label>
                                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                        class="input-field mt-2" placeholder="08xxxxxxxxxx">
                                    <p class="mt-1 text-xs text-[var(--error)] hidden" data-error="phone"></p>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]"
                                    for="email">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="input-field mt-2" required>
                                <p class="mt-1 text-xs text-[var(--error)] hidden" data-error="email"></p>
                            </div>
                            <div class="flex flex-wrap gap-2 pt-2">
                                <button type="submit" class="btn-primary">Simpan Profil</button>
                            </div>
                        </form>
                    </section>

                    <section class="border-t border-[var(--border-soft)] px-[var(--space-md)] py-[var(--space-md)]">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                                Keamanan</p>
                            <h2 class="mt-2 text-base font-semibold text-[var(--text-primary)]">Reset kata sandi</h2>
                        </div>
                        <form method="POST" action="{{ route('password.update') }}"
                            class="mt-[var(--space-sm)] grid gap-3" data-ajax-form="password">
                            @csrf
                            @method('PUT')
                            <div>
                                <label
                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]"
                                    for="current_password">Kata sandi saat ini</label>
                                <input id="current_password" name="current_password" type="password"
                                    class="input-field mt-2" autocomplete="current-password">
                                <p class="mt-1 text-xs text-[var(--error)] hidden" data-error="current_password"></p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]"
                                        for="password">Kata sandi baru</label>
                                    <input id="password" name="password" type="password" class="input-field mt-2"
                                        autocomplete="new-password">
                                    <p class="mt-1 text-xs text-[var(--error)] hidden" data-error="password"></p>
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]"
                                        for="password_confirmation">Konfirmasi</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="input-field mt-2" autocomplete="new-password">
                                    <p class="mt-1 text-xs text-[var(--error)] hidden"
                                        data-error="password_confirmation"></p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 pt-2">
                                <button type="submit" class="btn-primary">Perbarui Kata Sandi</button>
                            </div>
                        </form>
                    </section>
                </div>

                <aside class="divide-y divide-[var(--border-soft)]">
                    <section class="px-[var(--space-md)] py-[var(--space-md)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                                    Pesanan</p>
                                <h2 class="text-base font-semibold text-[var(--text-primary)]">Order terbaru</h2>
                            </div>
                            <a href="{{ route('orders.index') }}"
                                class="text-sm font-semibold text-[var(--accent-primary)]">Lihat semua</a>
                        </div>

                        <div class="mt-[var(--space-sm)] divide-y divide-[var(--border-soft)]">
                            @forelse ($recentOrders as $order)
                                <article class="py-3">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="space-y-1">
                                            <p class="text-sm font-semibold text-[var(--text-primary)]">
                                                {{ $order->order_number }}</p>
                                            <div
                                                class="flex flex-wrap items-center gap-2 text-xs text-[var(--text-secondary)]">
                                                <span>{{ optional($order->placed_at ?? $order->created_at)->translatedFormat('d M Y, H:i') }}
                                                    WIB</span>
                                                <span class="h-1 w-1 rounded-full bg-[var(--border-strong)]"></span>
                                                <span>{{ number_format((int) ($order->items_count ?? 0), 0, ',', '.') }}
                                                    produk</span>
                                            </div>
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <p class="text-sm font-semibold text-[var(--text-primary)]">
                                                Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                                            <div class="mt-2 flex flex-wrap gap-2 sm:justify-end">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $order->order_status->badgeClasses() }}">
                                                    {{ $order->order_status->label() }}
                                                </span>
                                                <a href="{{ route('orders.show', $order->order_number) }}"
                                                    class="btn-secondary px-3 py-2 text-xs">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="px-4 py-6 text-center">
                                    <x-store.empty-state icon="package" title="Belum ada pesanan"
                                        body="Riwayat pembelian akan muncul di sini setelah checkout." />
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="px-[var(--space-md)] py-[var(--space-md)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">
                                    Alamat</p>
                                <h2 class="text-base font-semibold text-[var(--text-primary)]">Alamat tersimpan</h2>
                            </div>
                            <a href="{{ route('addresses.index') }}"
                                class="text-sm font-semibold text-[var(--accent-primary)]">Kelola</a>
                        </div>

                        <div class="mt-[var(--space-sm)] divide-y divide-[var(--border-soft)]">
                            @forelse ($addresses as $address)
                                <article class="py-3">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">
                                            {{ $address->recipient_name }}</p>
                                        @if ($address->is_default)
                                            <span class="badge-accent">Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-[var(--text-secondary)]">{{ $address->phone }}</p>
                                    <p class="mt-2 text-xs leading-5 text-[var(--text-secondary)]">
                                        {{ $address->address_line }}, {{ $address->city }}</p>
                                </article>
                            @empty
                                <div class="px-4 py-6 text-center">
                                    <x-store.empty-state icon="map-pin" title="Belum ada alamat"
                                        body="Simpan alamat untuk mempercepat checkout berikutnya." />
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="px-[var(--space-md)] py-[var(--space-md)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-muted)]">Bantuan
                        </p>
                        <h2 class="mt-2 text-base font-semibold text-[var(--text-primary)]">Radean Care</h2>
                        <p class="mt-2 text-xs text-[var(--text-secondary)]">Butuh bantuan pesanan atau akun? Tim kami
                            siap membantu.</p>
                        <button type="button" class="btn-secondary mt-3 w-full"
                            @click="$dispatch('open-modal', 'radean-care-modal')">
                            Hubungi Radean Care
                        </button>
                    </section>
                </aside>
            </div>
        </section>
    </div>

    <div class="fixed right-4 top-24 z-50 hidden" data-toast>
        <div class="rounded-[0.75rem] border border-[var(--border-soft)] bg-white px-4 py-3 text-sm font-semibold text-[var(--text-primary)] shadow-[0_8px_18px_rgba(16,24,20,0.08)]"
            data-toast-body>
        </div>
    </div>

    <script>
        const liveClock = document.querySelector('[data-live-clock]');
        if (liveClock) {
            const updateClock = () => {
                const now = new Date();
                const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                liveClock.textContent = `Sinkronisasi ${time} WIB`;
            };
            updateClock();
            setInterval(updateClock, 60000);
        }

        const toast = document.querySelector('[data-toast]');
        const toastBody = document.querySelector('[data-toast-body]');
        let toastTimer;

        const showToast = (message, tone = 'success') => {
            if (!toast || !toastBody) return;
            toastBody.textContent = message;
            toastBody.classList.remove('border-emerald-200', 'bg-emerald-50', 'text-emerald-700', 'border-rose-200', 'bg-rose-50', 'text-rose-700');
            if (tone === 'error') {
                toastBody.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-700');
            } else {
                toastBody.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
            }
            toast.classList.remove('hidden');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toast.classList.add('hidden'), 3200);
        };

        const clearErrors = (form) => {
            form.querySelectorAll('[data-error]').forEach((el) => {
                el.textContent = '';
                el.classList.add('hidden');
            });
        };

        const showErrors = (form, errors) => {
            Object.entries(errors || {}).forEach(([field, messages]) => {
                const el = form.querySelector(`[data-error="${field}"]`);
                if (el && messages.length) {
                    el.textContent = messages[0];
                    el.classList.remove('hidden');
                }
            });
        };

        const updateAvatarPreview = (file) => {
            if (!file) return;
            const url = URL.createObjectURL(file);
            document.querySelectorAll('[data-profile-avatar]').forEach((container) => {
                const existingImg = container.querySelector('img');
                if (existingImg) {
                    existingImg.src = url;
                    return;
                }
                container.innerHTML = `<img src="${url}" alt="Profil" class="h-full w-full object-cover">`;
            });
        };

        const submitAjaxForm = async (form, options = {}) => {
            clearErrors(form);
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (response.ok) {
                const successMessage = form.dataset.ajaxForm === 'password'
                    ? 'Kata sandi berhasil diperbarui.'
                    : 'Profil berhasil diperbarui.';
                showToast(successMessage, 'success');

                if (form.dataset.ajaxForm === 'password') {
                    form.querySelectorAll('input[type="password"]').forEach((input) => {
                        input.value = '';
                    });
                }

                if (options.photoUploaded) {
                    const note = form.querySelector('[data-photo-note]');
                    if (note) {
                        note.classList.remove('hidden');
                    }
                }

                return;
            }

            if (response.status === 422) {
                const payload = await response.json();
                showErrors(form, payload.errors || {});
                showToast('Periksa kembali input kamu.', 'error');
                return;
            }

            showToast('Terjadi kendala. Coba lagi.', 'error');
        };

        document.querySelectorAll('[data-ajax-form]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                submitAjaxForm(form);
            });
        });

        const photoInput = document.querySelector('[data-profile-photo-input]');
        if (photoInput) {
            photoInput.addEventListener('change', () => {
                if (!photoInput.files || !photoInput.files.length) return;
                updateAvatarPreview(photoInput.files[0]);
                const profileForm = photoInput.closest('form');
                if (profileForm) {
                    submitAjaxForm(profileForm, { photoUploaded: true });
                }
            });
        }
    </script>
</x-layouts.store>
