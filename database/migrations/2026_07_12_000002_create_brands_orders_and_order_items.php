<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('ordered_at');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_id_number')->nullable();
            $table->string('kuwait_id_path')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('salesman_name')->nullable();
            $table->decimal('total_amount', 10, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 3);
            $table->decimal('total_amount', 10, 3);
            $table->timestamps();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('order_number')->nullable()->after('order_id');
            $table->string('customer_id_number')->nullable()->after('customer_phone');
            $table->string('kuwait_id_path')->nullable()->after('customer_id_number');
        });

        DB::table('sales')
            ->orderBy('id')
            ->whereNull('order_id')
            ->each(function ($sale) {
                $orderNumber = 'LEGACY-'.$sale->id;
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => $orderNumber,
                    'ordered_at' => $sale->sold_at,
                    'customer_name' => $sale->customer_name,
                    'customer_phone' => $sale->customer_phone,
                    'payment_method' => $sale->payment_method,
                    'salesman_name' => $sale->salesman_name,
                    'total_amount' => $sale->total_amount,
                    'notes' => $sale->notes,
                    'created_at' => $sale->created_at,
                    'updated_at' => $sale->updated_at,
                ]);

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $sale->product_id,
                    'quantity' => $sale->quantity,
                    'unit_price' => $sale->unit_price,
                    'total_amount' => $sale->total_amount,
                    'created_at' => $sale->created_at,
                    'updated_at' => $sale->updated_at,
                ]);

                DB::table('sales')->where('id', $sale->id)->update([
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropColumn(['order_number', 'customer_id_number', 'kuwait_id_path']);
        });

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('brands');
    }
};
