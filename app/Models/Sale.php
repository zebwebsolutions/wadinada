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
        'sold_at',
        'quantity',
        'unit_price',
        'total_amount',
        'payment_method',
        'salesman_name',
        'customer_name',
        'customer_email',
        'customer_phone',
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
}
