<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_id',
        'imei',
        'cost_price',
        'condition',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:3',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(InventoryUnitIdentifier::class);
    }

    public function primaryIdentifier(): HasOne
    {
        return $this->hasOne(InventoryUnitIdentifier::class)->where('is_primary', true);
    }
}
