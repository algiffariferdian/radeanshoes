<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVoucherRequest;
use App\Http\Requests\Admin\UpdateVoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::query()
            ->latest()
            ->paginate(15);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        Voucher::create($this->normalizedPayload($request->validated(), $request->boolean('is_active', true)));

        return redirect()->route('admin.vouchers.index')->with('status', 'Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $voucher->update($this->normalizedPayload($request->validated(), $request->boolean('is_active')));

        return redirect()->route('admin.vouchers.index')->with('status', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('status', 'Voucher berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizedPayload(array $data, bool $isActive): array
    {
        $data['code'] = strtoupper(trim((string) $data['code']));
        $data['is_active'] = $isActive;

        return $data;
    }
}
