<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryUnitIdentifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_unit_id',
        'type',
        'value',
        'normalized_value',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public static function normalize(string $value, string $type = 'serial'): string
    {
        $value = trim($value);

        if (str_starts_with($type, 'imei')) {
            return preg_replace('/\D+/', '', $value) ?: '';
        }

        return strtoupper(preg_replace('/[\s-]+/', '', $value) ?: '');
    }
}
