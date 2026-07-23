<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('color')->nullable()->after('model');
            $table->string('storage_capacity')->nullable()->after('color');
            $table->string('tracking_method')->default('imei')->after('condition');
        });

        Schema::create('purchase_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->date('purchased_at')->index();
            $table->string('payment_method')->nullable();
            $table->decimal('total_amount', 12, 3)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('purchase_batch_id')
                ->nullable()
                ->after('id')
                ->constrained('purchase_batches')
                ->nullOnDelete();
            $table->index('purchased_at');
        });

        Schema::table('product_units', function (Blueprint $table) {
            $table->foreignId('purchase_id')
                ->nullable()
                ->after('product_id')
                ->constrained('purchases')
                ->nullOnDelete();
            $table->string('condition')->nullable()->after('cost_price');
            $table->index(['status', 'product_id']);
        });

        Schema::create('inventory_unit_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_unit_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('value', 160);
            $table->string('normalized_value', 160)->unique();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['product_unit_id', 'is_primary']);
        });

        DB::table('products')
            ->orderBy('id')
            ->each(function ($product) {
                $hasImei = DB::table('product_units')
                    ->where('product_id', $product->id)
                    ->whereNotNull('imei')
                    ->exists();

                DB::table('products')->where('id', $product->id)->update([
                    'tracking_method' => $hasImei ? 'imei' : 'serial',
                ]);
            });

        DB::table('product_units')
            ->whereNotNull('imei')
            ->orderBy('id')
            ->each(function ($unit) {
                $normalized = preg_replace('/\D+/', '', (string) $unit->imei);

                if ($normalized === '') {
                    $normalized = strtoupper(preg_replace('/[\s-]+/', '', (string) $unit->imei));
                }

                if (! DB::table('inventory_unit_identifiers')->where('normalized_value', $normalized)->exists()) {
                    DB::table('inventory_unit_identifiers')->insert([
                        'product_unit_id' => $unit->id,
                        'type' => 'imei',
                        'value' => $unit->imei,
                        'normalized_value' => $normalized,
                        'is_primary' => true,
                        'created_at' => $unit->created_at,
                        'updated_at' => $unit->updated_at,
                    ]);
                }
            });

        DB::table('purchases')
            ->whereNull('purchase_batch_id')
            ->orderBy('id')
            ->each(function ($purchase) {
                $batchId = DB::table('purchase_batches')->insertGetId([
                    'batch_number' => 'LEGACY-P-'.$purchase->id,
                    'customer_id' => $purchase->customer_id,
                    'purchased_at' => $purchase->purchased_at,
                    'payment_method' => $purchase->payment_method,
                    'total_amount' => $purchase->total_amount,
                    'notes' => $purchase->notes,
                    'created_at' => $purchase->created_at,
                    'updated_at' => $purchase->updated_at,
                ]);

                DB::table('purchases')->where('id', $purchase->id)->update([
                    'purchase_batch_id' => $batchId,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_unit_identifiers');

        Schema::table('product_units', function (Blueprint $table) {
            $table->dropIndex(['status', 'product_id']);
            $table->dropConstrainedForeignId('purchase_id');
            $table->dropColumn('condition');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['purchased_at']);
            $table->dropConstrainedForeignId('purchase_batch_id');
        });

        Schema::dropIfExists('purchase_batches');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['color', 'storage_capacity', 'tracking_method']);
        });
    }
};
