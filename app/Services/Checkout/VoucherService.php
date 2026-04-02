<?php

namespace App\Services\Checkout;

use App\Models\Voucher;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    public function normalizeCode(?string $code): ?string
    {
        $normalized = strtoupper(trim((string) $code));

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * @return array{code:?string,voucher:?Voucher,discount_amount:string,error:?string}
     */
    public function preview(?string $code, float $subtotal): array
    {
        $normalizedCode = $this->normalizeCode($code);

        if (! $normalizedCode) {
            return [
                'code' => null,
                'voucher' => null,
                'discount_amount' => number_format(0, 2, '.', ''),
                'error' => null,
            ];
        }

        $voucher = Voucher::query()
            ->whereRaw('UPPER(code) = ?', [$normalizedCode])
            ->first();

        if (! $voucher) {
            return [
                'code' => $normalizedCode,
                'voucher' => null,
                'discount_amount' => number_format(0, 2, '.', ''),
                'error' => 'Kode voucher tidak ditemukan.',
            ];
        }

        if (! $voucher->isCurrentlyValid()) {
            return [
                'code' => $normalizedCode,
                'voucher' => null,
                'discount_amount' => number_format(0, 2, '.', ''),
                'error' => 'Voucher sedang tidak aktif atau sudah habis dipakai.',
            ];
        }

        if ($voucher->min_subtotal !== null && $subtotal < (float) $voucher->min_subtotal) {
            return [
                'code' => $normalizedCode,
                'voucher' => null,
                'discount_amount' => number_format(0, 2, '.', ''),
                'error' => 'Minimal belanja untuk voucher ini adalah Rp'.number_format((float) $voucher->min_subtotal, 0, ',', '.').'.',
            ];
        }

        $discountAmount = $voucher->calculateDiscount($subtotal);

        if ($discountAmount <= 0) {
            return [
                'code' => $normalizedCode,
                'voucher' => null,
                'discount_amount' => number_format(0, 2, '.', ''),
                'error' => 'Voucher tidak bisa dipakai untuk subtotal ini.',
            ];
        }

        return [
            'code' => $normalizedCode,
            'voucher' => $voucher,
            'discount_amount' => number_format($discountAmount, 2, '.', ''),
            'error' => null,
        ];
    }

    /**
     * @return array{code:?string,voucher:?Voucher,discount_amount:string,error:?string}
     */
    public function applyOrFail(?string $code, float $subtotal): array
    {
        $result = $this->preview($code, $subtotal);

        if ($result['code'] && ! $result['voucher']) {
            throw ValidationException::withMessages([
                'voucher_code' => $result['error'] ?? 'Kode voucher tidak valid.',
            ]);
        }

        return $result;
    }
}
