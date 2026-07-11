<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with(['customer', 'product'])
            ->latest('purchased_at')
            ->paginate(12);

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        return view('purchases.create', [
            'purchase' => new Purchase([
                'purchased_at' => now(),
                'quantity' => 1,
            ]),
            'product' => new Product(['condition' => 'Used']),
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedPurchase($request);

        DB::transaction(function () use ($data, $request) {
            $product = Product::create($this->productPayload($data));
            $customer = $this->storeCustomer($data, $request);
            $purchase = Purchase::create($this->purchasePayload($data, $customer, $product));

            $product->increment('stock_quantity', $purchase->quantity);
        });

        return redirect()->route('purchases.index')->with('status', 'Customer purchase recorded successfully.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['customer', 'product']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View
    {
        $purchase->load(['customer', 'product']);

        return view('purchases.edit', [
            'purchase' => $purchase,
            'product' => $purchase->product,
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $this->validatedPurchase($request, $purchase->product);

        DB::transaction(function () use ($data, $purchase, $request) {
            $oldQuantity = $purchase->quantity;
            $product = $purchase->product;
            $product->update($this->productPayload($data, $product));
            $customer = $this->storeCustomer($data, $request, $purchase->customer);

            $purchase->update($this->purchasePayload($data, $customer, $product));

            $product->decrement('stock_quantity', $oldQuantity);
            $product->increment('stock_quantity', $purchase->quantity);
        });

        return redirect()->route('purchases.index')->with('status', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        DB::transaction(function () use ($purchase) {
            $purchase->product()->decrement('stock_quantity', $purchase->quantity);
            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('status', 'Purchase deleted successfully.');
    }

    private function validatedPurchase(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_category' => ['required', 'string', 'max:80'],
            'product_brand' => ['nullable', 'string', 'max:120'],
            'product_sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'product_imei1' => ['nullable', 'string', 'max:80', 'unique:products,imei1,'.($product?->id ?? 'NULL')],
            'product_imei2' => ['nullable', 'string', 'max:80', 'unique:products,imei2,'.($product?->id ?? 'NULL')],
            'product_condition' => ['required', 'string', 'max:80'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'purchased_at' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_kuwait_id' => ['nullable', 'image', 'max:10240'],
        ]);
    }

    private function productPayload(array $data, ?Product $product = null): array
    {
        return [
            'name' => $data['product_name'],
            'category' => $data['product_category'],
            'brand' => $data['product_brand'] ?? null,
            'sku' => $data['product_sku'] ?? null,
            'imei1' => $data['product_imei1'] ?? null,
            'imei2' => $data['product_imei2'] ?? null,
            'condition' => $data['product_condition'],
            'stock_quantity' => $product?->stock_quantity ?? 0,
            'purchase_price' => $data['unit_price'],
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

    private function purchasePayload(array $data, Customer $customer, Product $product): array
    {
        return [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'purchased_at' => $data['purchased_at'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_amount' => $data['quantity'] * $data['unit_price'],
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
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
}
