<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\InventoryUnitIdentifier;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Purchase;
use App\Models\PurchaseBatch;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryIntakeService
{
    public function record(array $data, User $user, ?UploadedFile $kuwaitId = null): PurchaseBatch
    {
        return DB::transaction(function () use ($data, $user, $kuwaitId) {
            $customer = $this->storeCustomer($data, $kuwaitId);

            $batch = PurchaseBatch::create([
                'batch_number' => 'PB-'.now()->format('Ymd').'-'.strtoupper(substr((string) Str::ulid(), -8)),
                'customer_id' => $customer->id,
                'purchased_at' => $data['purchased_at'],
                'payment_method' => $data['payment_method'] ?? null,
                'total_amount' => 0,
                'notes' => $data['batch_notes'] ?? null,
                'created_by' => $user->id,
            ]);

            $batchTotal = 0.0;

            foreach ($data['items'] as $lineIndex => $item) {
                $product = $this->resolveProduct($item);
                $trackingMethod = $product->tracking_method;
                $units = $trackingMethod === 'internal'
                    ? $this->internalUnits((int) $item['quantity'], $batch, $lineIndex)
                    : array_values($item['units']);

                $costs = collect($units)
                    ->map(fn (array $unit) => (float) ($unit['cost_price'] ?? $item['default_cost']));
                $lineTotal = (float) $costs->sum();
                $averageCost = $costs->isNotEmpty() ? $lineTotal / $costs->count() : 0;

                $purchase = Purchase::create([
                    'purchase_batch_id' => $batch->id,
                    'product_id' => $product->id,
                    'customer_id' => $customer->id,
                    'purchased_at' => $data['purchased_at'],
                    'quantity' => count($units),
                    'unit_price' => $averageCost,
                    'total_amount' => $lineTotal,
                    'payment_method' => $data['payment_method'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);

                foreach ($units as $unitData) {
                    $identifier = trim((string) $unitData['identifier']);
                    $costPrice = (float) ($unitData['cost_price'] ?? $item['default_cost']);

                    $unit = ProductUnit::create([
                        'product_id' => $product->id,
                        'purchase_id' => $purchase->id,
                        'imei' => $trackingMethod === 'imei' ? $identifier : null,
                        'cost_price' => $costPrice,
                        'condition' => $product->condition,
                        'status' => 'available',
                    ]);

                    $this->storeIdentifier($unit, $trackingMethod, $identifier, true);

                    $secondary = trim((string) ($unitData['secondary_identifier'] ?? ''));

                    if ($secondary !== '') {
                        $this->storeIdentifier(
                            $unit,
                            $trackingMethod === 'imei' ? 'imei2' : 'alternate',
                            $secondary,
                            false
                        );
                    }
                }

                $product->update([
                    'stock_quantity' => $product->units()->where('status', 'available')->count(),
                    'purchase_price' => $product->units()->avg('cost_price') ?? $averageCost,
                ]);

                $batchTotal += $lineTotal;
            }

            $batch->update(['total_amount' => $batchTotal]);

            return $batch->fresh(['customer', 'items.product', 'items.units.identifiers']);
        });
    }

    private function storeCustomer(array $data, ?UploadedFile $kuwaitId): Customer
    {
        $payload = [
            'name' => $data['customer_name'],
            'email' => $data['customer_email'] ?? null,
            'phone' => $data['customer_phone'],
        ];

        if ($kuwaitId) {
            $payload['kuwait_id_path'] = $kuwaitId->store('customer-ids', 'public');
        }

        return Customer::updateOrCreate(['phone' => $data['customer_phone']], $payload);
    }

    private function resolveProduct(array $item): Product
    {
        if (filled($item['product_id'] ?? null)) {
            return Product::lockForUpdate()->findOrFail($item['product_id']);
        }

        $identity = [
            'name' => trim($item['name']),
            'category' => $item['category'],
            'brand' => filled($item['brand'] ?? null) ? trim($item['brand']) : null,
            'color' => filled($item['color'] ?? null) ? trim($item['color']) : null,
            'storage_capacity' => filled($item['storage_capacity'] ?? null)
                ? trim($item['storage_capacity'])
                : null,
            'condition' => $item['condition'],
            'tracking_method' => $item['tracking_method'],
        ];

        $product = Product::query()
            ->where($identity)
            ->first();

        if ($product) {
            if (filled($item['sale_price'] ?? null)) {
                $product->update(['sale_price' => $item['sale_price']]);
            }

            return $product;
        }

        return Product::create($identity + [
            'sku' => $item['sku'] ?? null,
            'stock_quantity' => 0,
            'purchase_price' => $item['default_cost'],
            'sale_price' => $item['sale_price'] ?? null,
            'notes' => $item['notes'] ?? null,
        ]);
    }

    private function internalUnits(int $quantity, PurchaseBatch $batch, int $lineIndex): array
    {
        return collect(range(1, $quantity))
            ->map(fn (int $number) => [
                'identifier' => sprintf(
                    'WN-%s-%02d-%03d',
                    strtoupper(preg_replace('/[^A-Z0-9]+/i', '', $batch->batch_number)),
                    $lineIndex + 1,
                    $number
                ),
            ])
            ->all();
    }

    private function storeIdentifier(
        ProductUnit $unit,
        string $type,
        string $value,
        bool $primary
    ): InventoryUnitIdentifier {
        return $unit->identifiers()->create([
            'type' => $type,
            'value' => $value,
            'normalized_value' => InventoryUnitIdentifier::normalize($value, $type),
            'is_primary' => $primary,
        ]);
    }
}
