<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_unit_id',
        'order_id',
        'order_number',
        'sold_at',
        'quantity',
        'unit_price',
        'total_amount',
        'payment_method',
        'salesman_name',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_id_number',
        'kuwait_id_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sold_at' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }
}
