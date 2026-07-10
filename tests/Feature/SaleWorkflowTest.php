<?php

namespace Tests\Feature;

use App\Models\Product;
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
            'model' => 'S26',
            'condition' => 'New',
            'stock_quantity' => 3,
            'purchase_price' => 180,
            'sale_price' => 240,
        ]);

        $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'sold_at' => now()->format('Y-m-d'),
            'quantity' => 1,
            'unit_price' => 240,
            'payment_method' => 'KNET',
            'salesman_name' => 'Yousef',
            'customer_name' => 'Mariam',
            'customer_email' => 'mariam@example.com',
            'customer_phone' => '+96551111111',
        ])->assertRedirect(route('sales.index'));

        $this->assertDatabaseHas('sales', [
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 240,
            'total_amount' => 240,
            'salesman_name' => 'Yousef',
        ]);

        $this->assertSame(2, $product->fresh()->stock_quantity);
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
}
