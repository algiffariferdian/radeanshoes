<x-layouts.store :title="'Akun Saya - RadeanShoes'">
    <div class="space-y-6" x-data>
        <x-store.breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Akun Saya'],
        ]" />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="heading-eyebrow">Akun saya</p>
                <h1 class="heading-page text-[clamp(1.75rem,2.8vw,2.4rem)]">Kelola profil, pesanan, dan alamat</h1>
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Semua kebutuhan account management dibuat ringkas agar tetap mudah dipakai di desktop maupun mobile.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('orders.index') }}" class="btn-secondary">Pesanan Saya</a>
                <a href="{{ route('addresses.index') }}" class="btn-secondary">Alamat Saya</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="surface-card p-5">
                <p class="meta-copy">Total pesanan</p>
                <p class="mt-2 text-2xl font-bold text-[var(--text-primary)]">{{ $user->orders_count }}</p>
            </div>
            <div class="surface-card p-5">
                <p class="meta-copy">Alamat tersimpan</p>
                <p class="mt-2 text-2xl font-bold text-[var(--text-primary)]">{{ $user->addresses_count }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <section class="surface-card-strong p-6">
                    <div class="mb-5">
                        <p class="heading-eyebrow">Profil</p>
                        <h2 class="heading-section">Informasi akun</h2>
                    </div>

                    <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="field-label" for="name">Nama lengkap</label>
                                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                            </div>
                            <div>
                                <label class="field-label" for="phone">Nomor telepon</label>
                                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div>
                            <label class="field-label" for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                        </div>
                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="submit" class="btn-primary">Simpan Profil</button>
                            <a href="{{ route('addresses.index') }}" class="btn-secondary">Kelola Alamat</a>
                        </div>
                    </form>
                </section>

                <section class="surface-card p-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="heading-eyebrow">Daftar pesanan</p>
                            <h2 class="heading-section">Order terbaru</h2>
                        </div>
                        <a href="{{ route('orders.index') }}" class="btn-ghost px-0 py-0 text-sm">Lihat semua</a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($recentOrders as $order)
                            <article class="surface-soft p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $order->order_number }}</p>
                                        <p class="mt-1 text-sm text-[var(--text-secondary)]">Total Rp{{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->order_status->badgeClasses() }}">{{ $order->order_status->label() }}</span>
                                        <a href="{{ route('orders.show', $order->order_number) }}" class="btn-secondary px-3 py-2 text-xs">Detail</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <x-store.empty-state
                                icon="package"
                                title="Belum ada pesanan"
                                body="Riwayat pembelian akan muncul di sini setelah kamu checkout."
                            />
                        @endforelse
                    </div>
                </section>

            </div>

            <aside class="space-y-6">
                <section class="surface-card p-5">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="heading-eyebrow">Alamat tersimpan</p>
                            <h2 class="text-lg font-bold text-[var(--text-primary)]">Alamat utama dan terbaru</h2>
                        </div>
                        <a href="{{ route('addresses.index') }}" class="btn-ghost px-0 py-0 text-sm">Lihat semua</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($addresses as $address)
                            <article class="surface-soft p-4">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $address->recipient_name }}</p>
                                    @if ($address->is_default)
                                        <span class="badge-accent">Utama</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $address->phone }}</p>
                                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">{{ $address->address_line }}, {{ $address->city }}</p>
                            </article>
                        @empty
                            <x-store.empty-state
                                icon="map-pin"
                                title="Belum ada alamat"
                                body="Simpan alamat untuk mempercepat checkout berikutnya."
                            />
                        @endforelse
                    </div>
                </section>

                <section class="surface-card p-5">
                    <p class="heading-eyebrow">Pengaturan akun</p>
                    <div class="mt-4 space-y-3 text-sm text-[var(--text-secondary)]">
                        <div class="surface-soft p-4">
                            <p class="font-semibold text-[var(--text-primary)]">Keamanan akun</p>
                            <p class="mt-1">Pastikan email dan nomor telepon aktif agar notifikasi order tetap akurat.</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts.store>
