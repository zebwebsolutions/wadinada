<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_delete_product_without_history(): void
    {
        $this->actingAs(User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]));

        $product = Product::create([
            'name' => 'USB-C Cable',
            'category' => 'Accessories',
            'condition' => 'New',
            'stock_quantity' => 5,
            'purchase_price' => 1.5,
            'sale_price' => 3,
        ]);

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_with_purchase_history_cannot_be_deleted(): void
    {
        $this->actingAs(User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]));

        $product = Product::create([
            'name' => 'iPad Pro',
            'category' => 'Tablets',
            'condition' => 'Used',
            'stock_quantity' => 1,
            'purchase_price' => 100,
            'sale_price' => 150,
        ]);

        $customer = Customer::create([
            'name' => 'Customer',
            'phone' => '+96550000000',
        ]);

        Purchase::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'purchased_at' => now(),
            'quantity' => 1,
            'unit_price' => 100,
            'total_amount' => 100,
        ]);

        $this->delete(route('products.destroy', $product))
            ->assertSessionHasErrors('product');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }
}
