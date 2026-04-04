<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
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
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:12288', 'dimensions:width=1920,height=720'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.max' => 'Ukuran gambar banner maksimal 12 MB.',
            'image.mimes' => 'Banner hanya mendukung file JPG, JPEG, PNG, atau WEBP.',
            'image.dimensions' => 'Ukuran banner harus tepat 1920 x 720 piksel.',
            'image.uploaded' => 'Upload gambar banner gagal. Coba gunakan file yang lebih kecil lalu upload ulang.',
        ];
    }
}
