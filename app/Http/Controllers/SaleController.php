<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('product')
            ->latest('sold_at')
            ->paginate(12);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        return view('sales.create', [
            'sale' => new Sale([
                'sold_at' => now(),
                'quantity' => 1,
            ]),
            'products' => Product::where('stock_quantity', '>', 0)->orderBy('name')->get(),
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedSale($request);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            if ($product->stock_quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only '.$product->stock_quantity.' item(s) are available in stock.',
                ]);
            }

            Sale::create($this->salePayload($data));
            $product->decrement('stock_quantity', $data['quantity']);
        });

        return redirect()->route('sales.index')->with('status', 'Sale recorded successfully.');
    }

    public function show(Sale $sale): View
    {
        $sale->load('product');

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale): View
    {
        return view('sales.edit', [
            'sale' => $sale,
            'products' => Product::orderBy('name')->get(),
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $data = $this->validatedSale($request);

        DB::transaction(function () use ($data, $sale) {
            $oldProduct = Product::lockForUpdate()->findOrFail($sale->product_id);
            $newProduct = Product::lockForUpdate()->findOrFail($data['product_id']);

            $oldProduct->increment('stock_quantity', $sale->quantity);

            if ($newProduct->stock_quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only '.$newProduct->stock_quantity.' item(s) are available in stock.',
                ]);
            }

            $sale->update($this->salePayload($data));
            $newProduct->decrement('stock_quantity', $data['quantity']);
        });

        return redirect()->route('sales.index')->with('status', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($sale) {
            $sale->product()->increment('stock_quantity', $sale->quantity);
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('status', 'Sale deleted and stock restored.');
    }

    private function validatedSale(Request $request): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sold_at' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:80'],
            'salesman_name' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function salePayload(array $data): array
    {
        return [
            'product_id' => $data['product_id'],
            'sold_at' => $data['sold_at'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_amount' => $data['quantity'] * $data['unit_price'],
            'payment_method' => $data['payment_method'] ?? null,
            'salesman_name' => $data['salesman_name'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function paymentMethods(): array
    {
        return ['Cash', 'KNET', 'Bank Transfer', 'Link Payment', 'Other'];
    }
}
