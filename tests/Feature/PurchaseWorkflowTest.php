<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_record_a_customer_purchase_and_increase_stock(): void
    {
        $this->actingAs(User::create([
            'name' => 'Purchase Staff',
            'email' => 'purchase@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]));

        $this->post(route('purchases.store'), [
            'product_name' => 'iPhone 15 Pro',
            'product_category' => 'Phones',
            'product_brand' => 'Apple',
            'product_model' => 'A3101',
            'product_condition' => 'Used',
            'sale_price' => 250,
            'purchased_at' => now()->format('Y-m-d'),
            'quantity' => 2,
            'unit_price' => 150,
            'payment_method' => 'Cash',
            'notes' => 'Clean device with box.',
            'customer_name' => 'Ahmad Saleh',
            'customer_email' => 'ahmad@example.com',
            'customer_phone' => '+96550000000',
            'customer_kuwait_id' => '299010101234',
            'customer_address' => 'Salmiya',
        ])->assertRedirect(route('purchases.index'));

        $this->assertDatabaseHas('customers', [
            'name' => 'Ahmad Saleh',
            'email' => 'ahmad@example.com',
            'phone' => '+96550000000',
            'kuwait_id' => '299010101234',
        ]);

        $this->assertDatabaseHas('purchases', [
            'quantity' => 2,
            'unit_price' => 150,
            'total_amount' => 300,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15 Pro',
            'brand' => 'Apple',
            'stock_quantity' => 2,
            'purchase_price' => 150,
            'sale_price' => 250,
        ]);
    }

    public function test_staff_can_delete_purchase_and_reduce_stock(): void
    {
        $this->actingAs(User::create([
            'name' => 'Purchase Staff',
            'email' => 'purchase-delete@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]));

        $product = Product::create([
            'name' => 'MacBook Air',
            'category' => 'Laptops',
            'condition' => 'Used',
            'stock_quantity' => 3,
            'purchase_price' => 150,
            'sale_price' => 220,
        ]);

        $customer = Customer::create([
            'name' => 'Customer',
            'phone' => '+96550000000',
        ]);

        $purchase = Purchase::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'purchased_at' => now(),
            'quantity' => 2,
            'unit_price' => 150,
            'total_amount' => 300,
        ]);

        $this->delete(route('purchases.destroy', $purchase))
            ->assertRedirect(route('purchases.index'));

        $this->assertDatabaseMissing('purchases', [
            'id' => $purchase->id,
        ]);

        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
