<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->withCount(['units', 'availableUnits'])
            ->when(request('search'), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhere('storage_capacity', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('imei1', 'like', "%{$search}%")
                        ->orWhere('imei2', 'like', "%{$search}%")
                        ->orWhereHas('units', function ($query) use ($search) {
                            $query->where('imei', 'like', "%{$search}%")
                                ->orWhereHas('identifiers', function ($query) use ($search) {
                                    $query->where('value', 'like', "%{$search}%")
                                        ->orWhere('normalized_value', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product(['condition' => 'Used']),
            'brands' => Brand::orderBy('name')->get(),
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
            'trackingMethods' => $this->trackingMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validatedProduct($request) + [
            'stock_quantity' => 0,
            'purchase_price' => 0,
        ]);

        return redirect()->route('products.index')->with('status', 'Product added successfully.');
    }

    public function show(Product $product): View
    {
        $product->load([
            'purchases.customer',
            'purchases.batch',
            'units.identifiers',
            'units.purchase.batch',
            'units.purchase.customer',
        ]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
            'trackingMethods' => $this->trackingMethods(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedProduct($request, $product));

        return redirect()->route('products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (
            $product->purchases()->exists()
            || $product->sales()->exists()
            || $product->orderItems()->exists()
            || $product->units()->exists()
        ) {
            return back()->withErrors([
                'product' => 'This product has purchase or sales history and cannot be deleted.',
            ]);
        }

        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product deleted successfully.');
    }

    private function validatedProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:120'],
            'color' => ['nullable', 'string', 'max:80'],
            'storage_capacity' => ['nullable', 'string', 'max:80'],
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'condition' => ['required', 'string', 'max:80'],
            'tracking_method' => ['required', 'in:imei,serial,barcode,internal'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function categories(): array
    {
        return ['Phones', 'Tablets', 'Laptops', 'Accessories', 'Smart Watches', 'Gaming', 'Other'];
    }

    private function conditions(): array
    {
        return ['New', 'Used', 'Open Box', 'Refurbished', 'Damaged'];
    }

    private function trackingMethods(): array
    {
        return [
            'imei' => 'IMEI',
            'serial' => 'Serial number',
            'barcode' => 'Manufacturer barcode',
            'internal' => 'Generated Wadi Nada code',
        ];
    }
}
