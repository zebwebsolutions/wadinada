<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('imei')->nullable()->unique();
            $table->decimal('cost_price', 10, 3)->default(0);
            $table->string('status')->default('available');
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_unit_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('product_unit_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });

        DB::table('products')
            ->orderBy('id')
            ->each(function ($product) {
                for ($i = 0; $i < (int) $product->stock_quantity; $i++) {
                    DB::table('product_units')->insert([
                        'product_id' => $product->id,
                        'imei' => $i === 0 ? $product->imei1 : ($i === 1 ? $product->imei2 : null),
                        'cost_price' => $product->purchase_price,
                        'status' => 'available',
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_unit_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_unit_id');
        });

        Schema::dropIfExists('product_units');
    }
};
