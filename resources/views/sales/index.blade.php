<x-layouts.app heading="Sales" eyebrow="Sales">
    <section class="rounded-md border border-zinc-200 bg-white">
        <div class="flex items-center justify-between border-b border-zinc-200 p-5">
            <h2 class="font-semibold">Sold items</h2>
            <a href="{{ route('sales.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Sell Item</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">Buyer</th>
                        <th class="px-5 py-3">Salesman</th>
                        <th class="px-5 py-3">Qty</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($sales as $sale)
                        <tr>
                            <td class="px-5 py-4">{{ $sale->sold_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('sales.show', $sale) }}" class="font-semibold hover:underline">{{ $sale->product->name }}</a>
                                <div class="text-xs text-zinc-500">{{ $sale->product->brand ?: 'No brand' }}</div>
                            </td>
                            <td class="px-5 py-4">{{ $sale->customer_name ?: '-' }}</td>
                            <td class="px-5 py-4">{{ $sale->salesman_name ?: '-' }}</td>
                            <td class="px-5 py-4">{{ $sale->quantity }}</td>
                            <td class="px-5 py-4">{{ number_format($sale->total_amount, 3) }} KD</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    @if ($sale->order_id)
                                        <a href="{{ route('orders.show', $sale->order_id) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">Order</a>
                                        <a href="{{ route('orders.print', $sale->order_id) }}" target="_blank" class="font-semibold text-zinc-700 hover:text-zinc-950">Print</a>
                                    @endif
                                    <a href="{{ route('sales.edit', $sale) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">Edit</a>
                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale? Product stock will be restored by this sale quantity.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-700 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-zinc-500">No sales recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-5 py-4">{{ $sales->links() }}</div>
    </section>
</x-layouts.app>
