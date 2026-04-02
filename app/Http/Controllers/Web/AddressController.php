<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = auth()->user()->addresses()->latest()->get();

        return view('web.addresses.index', compact('addresses'));
    }

    public function create(): View
    {
        return view('web.addresses.create');
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default') || ! $user->addresses()->exists();

        if ($data['is_default']) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create($data);

        return redirect()->route('addresses.index')->with('status', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Address $address): View
    {
        $address = $this->ownedAddress($address);

        return view('web.addresses.edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $address = $this->ownedAddress($address);
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('addresses.index')->with('status', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $address = $this->ownedAddress($address);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            auth()->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return redirect()->route('addresses.index')->with('status', 'Alamat berhasil dihapus.');
    }

    protected function ownedAddress(Address $address): Address
    {
        abort_unless($address->user_id === auth()->id(), 404);

        return $address;
    }
}
