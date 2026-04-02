<x-layouts.store :title="'Profil · RadeanShoes'">
    <div class="mx-auto max-w-3xl rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">My Account</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-stone-950">Profil Akun</h1>
        <form method="POST" action="{{ route('account.profile.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="text-sm font-semibold text-stone-900" for="name">Nama</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-stone-900" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-stone-900" for="phone">Telepon</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-2 w-full rounded-2xl border border-stone-300 px-4 py-3 text-sm">
                </div>
            </div>
            <button type="submit" class="rounded-full bg-stone-950 px-5 py-3 text-sm font-semibold text-stone-50">Simpan Profil</button>
        </form>
    </div>
</x-layouts.store>
