<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InventoryUnitIdentifier;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Purchase;
use App\Models\PurchaseBatch;
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
            'purchased_at' => now()->format('Y-m-d'),
            'payment_method' => 'Cash',
            'items' => [
                [
                    'name' => 'iPhone 15 Pro',
                    'category' => 'Phones',
                    'brand' => 'Apple',
                    'storage_capacity' => '256 GB',
                    'color' => 'Black',
                    'condition' => 'Used',
                    'tracking_method' => 'imei',
                    'quantity' => 2,
                    'default_cost' => 150,
                    'sale_price' => 250,
                    'notes' => 'Clean device with box.',
                    'units' => [
                        ['identifier' => '353456789012345'],
                        ['identifier' => '353456789012346', 'cost_price' => 160],
                    ],
                ],
                [
                    'name' => 'iPad Air',
                    'category' => 'Tablets',
                    'brand' => 'Apple',
                    'storage_capacity' => '128 GB',
                    'color' => 'Blue',
                    'condition' => 'Used',
                    'tracking_method' => 'serial',
                    'quantity' => 1,
                    'default_cost' => 100,
                    'sale_price' => 180,
                    'units' => [
                        ['identifier' => 'IPAD-SERIAL-001'],
                    ],
                ],
            ],
            'customer_name' => 'Ahmad Saleh',
            'customer_email' => 'ahmad@example.com',
            'customer_phone' => '+96550000000',
        ])->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'name' => 'Ahmad Saleh',
            'email' => 'ahmad@example.com',
            'phone' => '+96550000000',
        ]);

        $this->assertDatabaseHas('purchases', [
            'quantity' => 2,
            'unit_price' => 155,
            'total_amount' => 310,
        ]);

        $this->assertDatabaseHas('purchases', [
            'quantity' => 1,
            'unit_price' => 100,
            'total_amount' => 100,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15 Pro',
            'brand' => 'Apple',
            'storage_capacity' => '256 GB',
            'color' => 'Black',
            'stock_quantity' => 2,
            'purchase_price' => 155,
            'sale_price' => 250,
        ]);

        $phone = Product::where('name', 'iPhone 15 Pro')->firstOrFail();
        $firstUnit = ProductUnit::where('product_id', $phone->id)->orderBy('id')->firstOrFail();

        $this->assertDatabaseHas('inventory_unit_identifiers', [
            'product_unit_id' => $firstUnit->id,
            'normalized_value' => '353456789012345',
            'type' => 'imei',
        ]);

        $this->assertDatabaseHas('product_units', [
            'product_id' => $phone->id,
            'cost_price' => 150,
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('product_units', [
            'product_id' => $phone->id,
            'cost_price' => 160,
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPad Air',
            'stock_quantity' => 1,
            'purchase_price' => 100,
        ]);

        $this->assertSame(1, PurchaseBatch::count());
        $this->assertSame(3, InventoryUnitIdentifier::count());
    }

    public function test_existing_variant_can_be_received_at_a_different_exact_cost(): void
    {
        $user = User::create([
            'name' => 'Purchase Staff',
            'email' => 'repeat-purchase@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]);
        $this->actingAs($user);

        $product = Product::create([
            'name' => 'iPhone 17',
            'category' => 'Phones',
            'brand' => 'Apple',
            'storage_capacity' => '256 GB',
            'color' => 'Black',
            'condition' => 'New',
            'tracking_method' => 'imei',
            'stock_quantity' => 0,
            'purchase_price' => 0,
            'sale_price' => 300,
        ]);

        $this->post(route('purchases.store'), [
            'purchased_at' => now()->format('Y-m-d'),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'default_cost' => 210,
                'units' => [
                    ['identifier' => '111111111111111'],
                    ['identifier' => '222222222222222', 'cost_price' => 225],
                ],
            ]],
            'customer_name' => 'Supplier',
            'customer_phone' => '+96550000001',
        ])->assertRedirect();

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('product_units', [
            'product_id' => $product->id,
            'cost_price' => 210,
        ]);
        $this->assertDatabaseHas('product_units', [
            'product_id' => $product->id,
            'cost_price' => 225,
        ]);
        $this->assertDatabaseHas('purchases', [
            'product_id' => $product->id,
            'quantity' => 2,
            'total_amount' => 435,
        ]);
    }

    public function test_duplicate_identifier_is_rejected_before_inventory_is_created(): void
    {
        $this->actingAs(User::create([
            'name' => 'Purchase Staff',
            'email' => 'duplicate-purchase@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]));

        $this->from(route('purchases.create'))->post(route('purchases.store'), [
            'purchased_at' => now()->format('Y-m-d'),
            'items' => [[
                'name' => 'iPhone 17',
                'category' => 'Phones',
                'brand' => 'Apple',
                'condition' => 'New',
                'tracking_method' => 'imei',
                'quantity' => 2,
                'default_cost' => 200,
                'units' => [
                    ['identifier' => '333333333333333'],
                    ['identifier' => '333333333333333'],
                ],
            ]],
            'customer_name' => 'Supplier',
            'customer_phone' => '+96550000002',
        ])->assertRedirect(route('purchases.create'))
            ->assertSessionHasErrors('items.0.units.1.identifier');

        $this->assertDatabaseCount('purchase_batches', 0);
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
