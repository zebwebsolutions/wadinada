<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_salesman_can_sell_an_item_and_reduce_stock(): void
    {
        $this->actingAs(User::create([
            'name' => 'Sales Staff',
            'email' => 'sales@example.com',
            'password' => 'password',
            'role' => 'sales',
        ]));

        $product = Product::create([
            'name' => 'Samsung Galaxy S26',
            'category' => 'Phones',
            'brand' => 'Samsung',
            'imei1' => '861234567890123',
            'condition' => 'New',
            'stock_quantity' => 3,
            'purchase_price' => 180,
            'sale_price' => 240,
        ]);
        $unit = ProductUnit::create([
            'product_id' => $product->id,
            'imei' => '861234567890123',
            'cost_price' => 180,
            'status' => 'available',
        ]);
        ProductUnit::create([
            'product_id' => $product->id,
            'cost_price' => 180,
            'status' => 'available',
        ]);
        ProductUnit::create([
            'product_id' => $product->id,
            'cost_price' => 180,
            'status' => 'available',
        ]);

        $this->post(route('sales.store'), [
            'ordered_at' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_unit_id' => $unit->id,
                    'unit_price' => 240,
                ],
            ],
            'payment_method' => 'KNET',
            'salesman_name' => 'Yousef',
            'customer_name' => 'Mariam',
            'customer_phone' => '+96551111111',
            'customer_id_number' => '299010101234',
        ])->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 240,
            'total_amount' => 240,
            'salesman_name' => 'Yousef',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Mariam',
            'customer_phone' => '+96551111111',
            'customer_id_number' => '299010101234',
            'total_amount' => 240,
        ]);

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertSame('sold', $unit->fresh()->status);
    }

    public function test_salesman_can_delete_sale_and_restore_stock(): void
    {
        $this->actingAs(User::create([
            'name' => 'Sales Staff',
            'email' => 'sales-delete@example.com',
            'password' => 'password',
            'role' => 'sales',
        ]));

        $product = Product::create([
            'name' => 'iPhone 16',
            'category' => 'Phones',
            'brand' => 'Apple',
            'condition' => 'Used',
            'stock_quantity' => 1,
            'purchase_price' => 200,
            'sale_price' => 260,
        ]);

        $sale = Sale::create([
            'product_id' => $product->id,
            'sold_at' => now(),
            'quantity' => 2,
            'unit_price' => 260,
            'total_amount' => 520,
            'salesman_name' => 'Yousef',
        ]);

        $this->delete(route('sales.destroy', $sale))
            ->assertRedirect(route('sales.index'));

        $this->assertDatabaseMissing('sales', [
            'id' => $sale->id,
        ]);

        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_salesman_can_find_and_print_order_receipt(): void
    {
        $this->actingAs(User::create([
            'name' => 'Sales Staff',
            'email' => 'sales-orders@example.com',
            'password' => 'password',
            'role' => 'sales',
        ]));

        $product = Product::create([
            'name' => 'Pixel Phone',
            'category' => 'Phones',
            'brand' => 'Google',
            'sku' => 'PX-001',
            'imei1' => '351111222233334',
            'condition' => 'Used',
            'stock_quantity' => 1,
            'purchase_price' => 120,
            'sale_price' => 180,
        ]);
        $unit = ProductUnit::create([
            'product_id' => $product->id,
            'imei' => '351111222233334',
            'cost_price' => 120,
            'status' => 'sold',
        ]);

        $order = Order::create([
            'order_number' => 'WN-TEST-001',
            'ordered_at' => now(),
            'customer_name' => 'Noura',
            'customer_phone' => '+96552222222',
            'total_amount' => 180,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_unit_id' => $unit->id,
            'quantity' => 1,
            'unit_price' => 180,
            'total_amount' => 180,
        ]);

        Sale::create([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'product_id' => $product->id,
            'product_unit_id' => $unit->id,
            'sold_at' => now(),
            'quantity' => 1,
            'unit_price' => 180,
            'total_amount' => 180,
            'customer_name' => 'Noura',
            'customer_phone' => '+96552222222',
        ]);

        $this->get(route('orders.index', ['search' => '351111222233334']))
            ->assertOk()
            ->assertSee('Pixel Phone')
            ->assertSee('Noura');

        $this->get(route('orders.print', $order))
            ->assertOk()
            ->assertSee('Wadi Nada Phone')
            ->assertSee('Shop 15, Khalid Bin Waleed Street, Sharq');
    }

    public function test_same_inventory_unit_cannot_be_submitted_twice_in_one_checkout(): void
    {
        $this->actingAs(User::create([
            'name' => 'Sales Staff',
            'email' => 'duplicate-unit-sale@example.com',
            'password' => 'password',
            'role' => 'sales',
        ]));

        $product = Product::create([
            'name' => 'iPhone 17',
            'category' => 'Phones',
            'condition' => 'New',
            'tracking_method' => 'imei',
            'stock_quantity' => 1,
            'purchase_price' => 200,
            'sale_price' => 280,
        ]);
        $unit = ProductUnit::create([
            'product_id' => $product->id,
            'imei' => '444444444444444',
            'cost_price' => 200,
            'status' => 'available',
        ]);

        $this->post(route('sales.store'), [
            'ordered_at' => now()->format('Y-m-d'),
            'items' => [
                ['product_unit_id' => $unit->id, 'unit_price' => 280],
                ['product_unit_id' => $unit->id, 'unit_price' => 280],
            ],
        ])->assertSessionHasErrors('items.1.product_unit_id');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame('available', $unit->fresh()->status);
    }
}
