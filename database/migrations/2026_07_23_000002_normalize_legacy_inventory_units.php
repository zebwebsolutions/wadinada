<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->orderBy('id')
            ->each(function ($product) {
                $availableUnitIds = DB::table('product_units')
                    ->where('product_id', $product->id)
                    ->whereNull('purchase_id')
                    ->orderBy('id')
                    ->pluck('id')
                    ->values();

                DB::table('purchases')
                    ->where('product_id', $product->id)
                    ->orderBy('purchased_at')
                    ->orderBy('id')
                    ->each(function ($purchase) use ($availableUnitIds) {
                        $unitIds = $availableUnitIds->splice(0, (int) $purchase->quantity);

                        if ($unitIds->isNotEmpty()) {
                            DB::table('product_units')
                                ->whereIn('id', $unitIds)
                                ->update(['purchase_id' => $purchase->id]);
                        }
                    });

                $availableCount = DB::table('product_units')
                    ->where('product_id', $product->id)
                    ->where('status', 'available')
                    ->count();

                DB::table('products')->where('id', $product->id)->update([
                    'stock_quantity' => $availableCount,
                ]);
            });

        DB::table('product_units')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('inventory_unit_identifiers')
                    ->whereColumn('inventory_unit_identifiers.product_unit_id', 'product_units.id');
            })
            ->orderBy('id')
            ->each(function ($unit) {
                $value = 'WN-LEGACY-'.str_pad((string) $unit->id, 8, '0', STR_PAD_LEFT);

                DB::table('inventory_unit_identifiers')->insert([
                    'product_unit_id' => $unit->id,
                    'type' => 'internal',
                    'value' => $value,
                    'normalized_value' => str_replace('-', '', $value),
                    'is_primary' => true,
                    'created_at' => $unit->created_at,
                    'updated_at' => $unit->updated_at,
                ]);
            });
    }

    public function down(): void
    {
        DB::table('inventory_unit_identifiers')
            ->where('value', 'like', 'WN-LEGACY-%')
            ->delete();

        DB::table('product_units')->update(['purchase_id' => null]);
    }
};
