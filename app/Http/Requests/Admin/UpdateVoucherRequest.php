<?php

namespace App\Http\Requests\Admin;

use App\Models\Voucher;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Voucher|null $voucher */
        $voucher = $this->route('voucher');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\\-]+$/',
                Rule::unique('vouchers', 'code')->ignore($voucher?->id),
            ],
            'name' => ['required', 'string', 'max:100'],
            'discount_value' => ['required', 'numeric', 'gt:0', 'max:100'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
