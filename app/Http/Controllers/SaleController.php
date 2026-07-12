<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
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
            'products' => Product::where('stock_quantity', '>', 0)->orderBy('name')->get(),
            'paymentMethods' => $this->paymentMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedCheckout($request);

        $order = DB::transaction(function () use ($data, $request) {
            $products = Product::lockForUpdate()
                ->whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            foreach ($data['items'] as $index => $item) {
                $product = $products->get($item['product_id']);

                if (! $product || $product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items.'.$index.'.quantity' => 'Only '.($product?->stock_quantity ?? 0).' item(s) are available in stock.',
                    ]);
                }
            }

            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'ordered_at' => $data['ordered_at'],
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_id_number' => $data['customer_id_number'] ?? null,
                'kuwait_id_path' => $request->hasFile('kuwait_id')
                    ? $request->file('kuwait_id')->store('order-ids', 'public')
                    : null,
                'payment_method' => $data['payment_method'] ?? null,
                'salesman_name' => $data['salesman_name'] ?? null,
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $total += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $lineTotal,
                ]);

                Sale::create([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'product_id' => $product->id,
                    'sold_at' => $data['ordered_at'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $lineTotal,
                    'payment_method' => $data['payment_method'] ?? null,
                    'salesman_name' => $data['salesman_name'] ?? null,
                    'customer_name' => $data['customer_name'] ?? null,
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'customer_id_number' => $data['customer_id_number'] ?? null,
                    'kuwait_id_path' => $order->kuwait_id_path,
                    'notes' => $data['notes'] ?? null,
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('status', 'Order checked out successfully.');
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

    private function validatedCheckout(Request $request): array
    {
        return $request->validate([
            'ordered_at' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:80'],
            'salesman_name' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'customer_id_number' => ['nullable', 'string', 'max:80'],
            'kuwait_id' => ['nullable', 'image', 'max:10240'],
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

    private function nextOrderNumber(): string
    {
        return 'WN-'.now()->format('Ymd-His').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }
}
