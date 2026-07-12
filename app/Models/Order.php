<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'ordered_at',
        'customer_name',
        'customer_phone',
        'customer_id_number',
        'kuwait_id_path',
        'payment_method',
        'salesman_name',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'date',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
