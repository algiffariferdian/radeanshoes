<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingOption extends Model
{
    /** @use HasFactory<\Database\Factories\ShippingOptionFactory> */
    use HasFactory;

    protected $fillable = [
        'courier_name',
        'service_name',
        'etd_text',
        'price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function displayName(): string
    {
        return "{$this->courier_name} - {$this->service_name}";
    }
}
