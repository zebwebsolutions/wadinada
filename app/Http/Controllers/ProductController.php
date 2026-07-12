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
            ->when(request('search'), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('imei1', 'like', "%{$search}%")
                        ->orWhere('imei2', 'like', "%{$search}%");
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
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validatedProduct($request));

        return redirect()->route('products.index')->with('status', 'Product added successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['purchases.customer', 'sales']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => $this->categories(),
            'conditions' => $this->conditions(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedProduct($request, $product));

        return redirect()->route('products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->purchases()->exists() || $product->sales()->exists()) {
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
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'imei1' => ['nullable', 'string', 'max:80', 'unique:products,imei1,'.($product?->id ?? 'NULL')],
            'imei2' => ['nullable', 'string', 'max:80', 'unique:products,imei2,'.($product?->id ?? 'NULL')],
            'condition' => ['required', 'string', 'max:80'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
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
}
