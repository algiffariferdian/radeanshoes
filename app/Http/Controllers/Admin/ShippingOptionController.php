<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingOptionRequest;
use App\Http\Requests\Admin\UpdateShippingOptionRequest;
use App\Models\ShippingOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingOptionController extends Controller
{
    public function index(): View
    {
        $shippingOptions = ShippingOption::query()->orderBy('sort_order')->paginate(15);

        return view('admin.shipping-options.index', compact('shippingOptions'));
    }

    public function create(): View
    {
        return view('admin.shipping-options.create');
    }

    public function store(StoreShippingOptionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        ShippingOption::create($data);

        return redirect()->route('admin.shipping-options.index')->with('status', 'Opsi pengiriman berhasil dibuat.');
    }

    public function edit(ShippingOption $shippingOption): View
    {
        return view('admin.shipping-options.edit', compact('shippingOption'));
    }

    public function update(UpdateShippingOptionRequest $request, ShippingOption $shippingOption): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $shippingOption->update($data);

        return redirect()->route('admin.shipping-options.index')->with('status', 'Opsi pengiriman berhasil diperbarui.');
    }

    public function destroy(ShippingOption $shippingOption): RedirectResponse
    {
        $shippingOption->delete();

        return redirect()->route('admin.shipping-options.index')->with('status', 'Opsi pengiriman berhasil dihapus.');
    }
}
