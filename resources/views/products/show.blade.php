<x-layouts.app heading="{{ $product->name }}" eyebrow="Product details">
    <div class="grid gap-6 xl:grid-cols-3">
        <section class="rounded-md border border-zinc-200 bg-white p-5 xl:col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500">{{ $product->category }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $product->name }}</h2>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('products.edit', $product) }}" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Edit</a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product? Products with purchase or sales history cannot be deleted.');">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            </div>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Brand</dt><dd class="font-medium">{{ $product->brand ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">SKU</dt><dd class="font-medium">{{ $product->sku ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">IMEI 1</dt><dd class="font-medium">{{ $product->imei1 ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">IMEI 2</dt><dd class="font-medium">{{ $product->imei2 ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Condition</dt><dd class="font-medium">{{ $product->condition }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Stock</dt><dd class="font-medium">{{ $product->stock_quantity }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Sale</dt><dd class="font-medium">{{ $product->sale_price ? number_format($product->sale_price, 3).' KD' : '-' }}</dd></div>
            </dl>
            @if ($product->notes)
                <p class="mt-5 rounded-md bg-zinc-50 p-3 text-sm text-zinc-700">{{ $product->notes }}</p>
            @endif
        </section>

        <section class="rounded-md border border-zinc-200 bg-white xl:col-span-2">
            <div class="border-b border-zinc-200 px-5 py-4">
                <h2 class="font-semibold">Purchase History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($product->purchases as $purchase)
                            <tr>
                                <td class="px-5 py-3">{{ $purchase->purchased_at->format('d M Y') }}</td>
                                <td class="px-5 py-3">{{ $purchase->customer->name }}</td>
                                <td class="px-5 py-3">{{ $purchase->quantity }}</td>
                                <td class="px-5 py-3">{{ number_format($purchase->total_amount, 3) }} KD</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-500">No customer purchases for this product yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-md border border-zinc-200 bg-white xl:col-span-3">
            <div class="border-b border-zinc-200 px-5 py-4">
                <h2 class="font-semibold">Units</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-5 py-3">IMEI</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($product->units as $unit)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $unit->imei ?: 'Unit #'.$unit->id }}</td>
                                <td class="px-5 py-3">{{ ucfirst($unit->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-5 py-8 text-center text-zinc-500">No units recorded for this product.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
