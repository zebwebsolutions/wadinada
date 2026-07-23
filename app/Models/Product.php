<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'brand',
        'model',
        'color',
        'storage_capacity',
        'sku',
        'imei1',
        'imei2',
        'condition',
        'tracking_method',
        'stock_quantity',
        'purchase_price',
        'sale_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:3',
            'sale_price' => 'decimal:3',
            'stock_quantity' => 'integer',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function availableUnits(): HasMany
    {
        return $this->hasMany(ProductUnit::class)->where('status', 'available');
    }

    public function getVariantNameAttribute(): string
    {
        $details = array_filter([$this->storage_capacity, $this->color]);

        return $this->name.($details ? ' · '.implode(' · ', $details) : '');
    }
}
