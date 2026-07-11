<x-layouts.app heading="Dashboard" eyebrow="Sales and purchase management">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-500">Products</p>
            <p class="mt-2 text-3xl font-semibold">{{ $totalProducts }}</p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-500">Customers</p>
            <p class="mt-2 text-3xl font-semibold">{{ $totalCustomers }}</p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-500">Purchases</p>
            <p class="mt-2 text-3xl font-semibold">{{ $totalPurchases }}</p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <p class="text-sm font-medium text-zinc-500">Sales</p>
            <p class="mt-2 text-3xl font-semibold">{{ $totalSales }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="rounded-md border border-zinc-200 bg-white">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4">
                <h2 class="font-semibold">Latest Products</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-zinc-700 hover:text-zinc-950">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3">Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-5 py-3 font-medium"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                                <td class="px-5 py-3 text-zinc-600">{{ $product->category }}</td>
                                <td class="px-5 py-3">{{ $product->stock_quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-zinc-500">No products yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-md border border-zinc-200 bg-white">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4">
                <h2 class="font-semibold">Recent Customer Purchases</h2>
                <a href="{{ route('purchases.index') }}" class="text-sm font-semibold text-zinc-700 hover:text-zinc-950">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $purchase->customer->name }}</td>
                                <td class="px-5 py-3 text-zinc-600">{{ $purchase->product->name }}</td>
                                <td class="px-5 py-3">{{ $purchase->purchased_at->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ number_format($purchase->total_amount, 3) }} KD</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-500">No customer purchases recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-md border border-zinc-200 bg-white">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4">
                <h2 class="font-semibold">Recent Sales</h2>
                <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-zinc-700 hover:text-zinc-950">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $sale->product->name }}</td>
                                <td class="px-5 py-3">{{ $sale->quantity }}</td>
                                <td class="px-5 py-3">{{ $sale->sold_at->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ number_format($sale->total_amount, 3) }} KD</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-500">No sales recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
