<?php

namespace App\Http\Requests\Admin;

use App\Support\Enums\VoucherDiscountType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
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
        return [
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\\-]+$/', 'unique:vouchers,code'],
            'name' => ['required', 'string', 'max:100'],
            'discount_type' => ['required', Rule::enum(VoucherDiscountType::class)],
            'discount_value' => ['required', 'numeric', 'gt:0'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (
                $this->input('discount_type') === VoucherDiscountType::Percent->value
                && (float) $this->input('discount_value', 0) > 100
            ) {
                $validator->errors()->add('discount_value', 'Diskon persen tidak boleh lebih dari 100.');
            }
        });
    }
}
