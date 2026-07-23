<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseBatchRequest;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseBatch;
use App\Services\InventoryIntakeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $batches = PurchaseBatch::query()
            ->with(['customer', 'items.product'])
            ->withCount('items')
            ->withSum('items as units_count', 'quantity')
            ->latest('purchased_at')
            ->paginate(12);

        return view('purchases.index', compact('batches'));
    }

    public function create(): View
    {
        return view('purchases.create', [
            'purchase' => new Purchase([
                'purchased_at' => now(),
                'quantity' => 1,
            ]),
            'product' => new Product(['condition' => 'Used']),
            'products' => Product::query()
                ->withCount('availableUnits')
                ->orderBy('brand')
                ->orderBy('name')
                ->orderBy('storage_capacity')
                ->orderBy('color')
                ->get(),
            'brands' => Brand::orderBy('name')->get(),
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
            'paymentMethods' => $this->paymentMethods(),
            'trackingMethods' => $this->trackingMethods(),
        ]);
    }

    public function store(
        StorePurchaseBatchRequest $request,
        InventoryIntakeService $inventoryIntake
    ): RedirectResponse {
        $batch = $inventoryIntake->record(
            $request->validated(),
            $request->user(),
            $request->file('customer_kuwait_id')
        );

        return redirect()
            ->route('purchase-batches.show', $batch)
            ->with('status', $batch->items->sum('quantity').' inventory unit(s) added successfully.');
    }

    public function showBatch(PurchaseBatch $purchaseBatch): View
    {
        $purchaseBatch->load([
            'customer',
            'creator',
            'items.product',
            'items.units.identifiers',
        ]);

        return view('purchases.batch-show', compact('purchaseBatch'));
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['customer', 'product', 'units.identifiers', 'batch']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View
    {
        $purchase->load(['customer', 'product']);

        return view('purchases.edit', [
            'purchase' => $purchase,
            'product' => $purchase->product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        if ($purchase->units()->exists()) {
            return back()->withErrors([
                'purchase' => 'This batch line has individually tracked units and cannot be edited. Record a correction instead.',
            ]);
        }

        $data = $this->validatedPurchase($request, $purchase->product);

        DB::transaction(function () use ($data, $purchase, $request) {
            $oldQuantity = $purchase->quantity;
            $product = $purchase->product;
            $product->update($this->productPayload($data, $product));
            $customer = $this->storeCustomer($data, $request, $purchase->customer);

            $purchase->update($this->purchasePayload($data, $data, $customer, $product));

            $product->decrement('stock_quantity', $oldQuantity);
            $product->increment('stock_quantity', $purchase->quantity);
        });

        return redirect()->route('purchases.index')->with('status', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        if ($purchase->units()->exists()) {
            return back()->withErrors([
                'purchase' => 'This purchase contains tracked inventory units and cannot be deleted.',
            ]);
        }

        DB::transaction(function () use ($purchase) {
            $purchase->product()->decrement('stock_quantity', $purchase->quantity);
            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('status', 'Purchase deleted successfully.');
    }

    private function validatedPurchase(Request $request, ?Product $product = null): array
    {
        $rules = [
            'purchased_at' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:80'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_kuwait_id' => ['nullable', 'image', 'max:10240'],
        ];

        if ($product) {
            return $request->validate($rules + [
                'product_name' => ['required', 'string', 'max:255'],
                'product_category' => ['required', 'string', 'max:80'],
                'product_brand' => ['nullable', 'string', 'max:120'],
                'product_sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product->id)],
                'product_imei1' => ['nullable', 'string', 'max:80', 'unique:products,imei1,'.($product->id)],
                'product_imei2' => ['nullable', 'string', 'max:80', 'unique:products,imei2,'.($product->id)],
                'product_condition' => ['required', 'string', 'max:80'],
                'sale_price' => ['nullable', 'numeric', 'min:0'],
                'quantity' => ['required', 'integer', 'min:1'],
                'unit_price' => ['required', 'numeric', 'min:0'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ]);
        }

        return $request->validate($rules + [
            'products' => ['required', 'array', 'min:1'],
            'products.*.name' => ['required', 'string', 'max:255'],
            'products.*.category' => ['required', 'string', 'max:80'],
            'products.*.brand' => ['nullable', 'string', 'max:120'],
            'products.*.sku' => ['nullable', 'string', 'max:80', 'distinct', 'unique:products,sku'],
            'products.*.condition' => ['required', 'string', 'max:80'],
            'products.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'products.*.notes' => ['nullable', 'string', 'max:2000'],
            'products.*.units' => ['required', 'array', 'min:1'],
            'products.*.units.*.imei' => ['nullable', 'string', 'max:80', 'distinct', 'unique:product_units,imei'],
            'products.*.units.*.cost_price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function productPayload(array $data, ?Product $product = null): array
    {
        return [
            'name' => $data['product_name'] ?? $data['name'],
            'category' => $data['product_category'] ?? $data['category'],
            'brand' => $data['product_brand'] ?? $data['brand'] ?? null,
            'sku' => $data['product_sku'] ?? $data['sku'] ?? null,
            'imei1' => $data['product_imei1'] ?? null,
            'imei2' => $data['product_imei2'] ?? null,
            'condition' => $data['product_condition'] ?? $data['condition'],
            'stock_quantity' => $product?->stock_quantity ?? 0,
            'purchase_price' => $data['unit_price'] ?? $this->averageCost($data['units']),
            'sale_price' => $data['sale_price'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function storeCustomer(array $data, Request $request, ?Customer $currentCustomer = null): Customer
    {
        $lookup = $currentCustomer ? ['id' => $currentCustomer->id] : ['phone' => $data['customer_phone']];

        $payload = [
            'name' => $data['customer_name'],
            'email' => $data['customer_email'] ?? null,
            'phone' => $data['customer_phone'],
        ];

        if ($request->hasFile('customer_kuwait_id')) {
            $payload['kuwait_id_path'] = $request->file('customer_kuwait_id')->store('customer-ids', 'public');
        }

        return Customer::updateOrCreate($lookup, $payload);
    }

    private function purchasePayload(array $data, array $item, Customer $customer, Product $product): array
    {
        return [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'purchased_at' => $data['purchased_at'],
            'quantity' => $item['quantity'] ?? count($item['units']),
            'unit_price' => $item['unit_price'] ?? $this->averageCost($item['units']),
            'total_amount' => isset($item['quantity'], $item['unit_price'])
                ? $item['quantity'] * $item['unit_price']
                : $this->totalCost($item['units']),
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $item['notes'] ?? null,
        ];
    }

    private function averageCost(array $units): float
    {
        return count($units) ? $this->totalCost($units) / count($units) : 0;
    }

    private function totalCost(array $units): float
    {
        return collect($units)->sum(fn (array $unit) => (float) $unit['cost_price']);
    }

    private function categories(): array
    {
        return ['Phones', 'Tablets', 'Laptops', 'Accessories', 'Smart Watches', 'Gaming', 'Other'];
    }

    private function conditions(): array
    {
        return ['New', 'Used', 'Open Box', 'Refurbished', 'Damaged'];
    }

    private function paymentMethods(): array
    {
        return ['Cash', 'KNET', 'Bank Transfer', 'Link Payment', 'Other'];
    }

    private function trackingMethods(): array
    {
        return [
            'imei' => 'IMEI — cellular phones and tablets',
            'serial' => 'Serial number — laptops, Wi-Fi tablets and watches',
            'barcode' => 'Manufacturer barcode',
            'internal' => 'Generate Wadi Nada inventory codes',
        ];
    }
}
