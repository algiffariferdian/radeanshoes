<x-layouts.admin :title="'Customers · Admin RadeanShoes'">
    <div class="space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">CRM</p>
            <h1 class="text-3xl font-black tracking-tight text-stone-950">Customers</h1>
        </div>

        <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-stone-500">
                        <tr>
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">Email</th>
                            <th class="pb-3">Telepon</th>
                            <th class="pb-3">Alamat</th>
                            <th class="pb-3">Orders</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="py-4 font-semibold text-stone-900">{{ $customer->name }}</td>
                                <td class="py-4 text-stone-600">{{ $customer->email }}</td>
                                <td class="py-4 text-stone-600">{{ $customer->phone ?: '-' }}</td>
                                <td class="py-4 text-stone-600">{{ $customer->addresses_count }}</td>
                                <td class="py-4 text-stone-600">{{ $customer->orders_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $customers->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
