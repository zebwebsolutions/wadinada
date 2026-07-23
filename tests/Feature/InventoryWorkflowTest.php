<?php

namespace Tests\Feature;

use App\Models\InventoryUnitIdentifier;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_intake_page_is_available_and_explains_scanning_workflow(): void
    {
        $this->actingAs($this->staff())
            ->get(route('purchases.create'))
            ->assertOk()
            ->assertSee('Batch inventory intake')
            ->assertSee('Scan or paste identifiers')
            ->assertSee('Paste a list');
    }

    public function test_internal_inventory_codes_are_generated_for_units_without_serials(): void
    {
        $this->actingAs($this->staff())
            ->post(route('purchases.store'), [
                'purchased_at' => now()->format('Y-m-d'),
                'items' => [[
                    'name' => 'USB-C Charger',
                    'category' => 'Accessories',
                    'brand' => 'Apple',
                    'condition' => 'New',
                    'tracking_method' => 'internal',
                    'quantity' => 3,
                    'default_cost' => 4.5,
                    'sale_price' => 8,
                ]],
                'customer_name' => 'Accessory Supplier',
                'customer_phone' => '+96550000003',
            ])
            ->assertRedirect();

        $product = Product::where('name', 'USB-C Charger')->firstOrFail();

        $this->assertSame(3, $product->units()->count());
        $this->assertSame(3, $product->fresh()->stock_quantity);
        $this->assertSame(3, InventoryUnitIdentifier::where('type', 'internal')->count());
        $this->assertSame(
            3,
            InventoryUnitIdentifier::where('type', 'internal')->distinct()->count('normalized_value')
        );
    }

    public function test_staff_can_find_an_exact_unit_by_scanning_its_serial(): void
    {
        $this->actingAs($this->staff());

        $product = Product::create([
            'name' => 'MacBook Air',
            'category' => 'Laptops',
            'brand' => 'Apple',
            'storage_capacity' => '512 GB',
            'color' => 'Midnight',
            'condition' => 'New',
            'tracking_method' => 'serial',
            'stock_quantity' => 1,
            'purchase_price' => 300,
            'sale_price' => 420,
        ]);
        $unit = ProductUnit::create([
            'product_id' => $product->id,
            'cost_price' => 300,
            'status' => 'available',
        ]);
        $unit->identifiers()->create([
            'type' => 'serial',
            'value' => 'C02-ABC-123',
            'normalized_value' => 'C02ABC123',
            'is_primary' => true,
        ]);

        $this->get(route('inventory.index', ['search' => 'C02-ABC-123', 'status' => 'all']))
            ->assertOk()
            ->assertSee('C02-ABC-123')
            ->assertSee('MacBook Air')
            ->assertSee('512 GB')
            ->assertSee('300.000 KD');
    }

    private function staff(): User
    {
        return User::create([
            'name' => 'Inventory Staff',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'staff',
        ]);
    }
}
