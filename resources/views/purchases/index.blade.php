<x-layouts.app heading="Customer Purchases" eyebrow="Purchase intake">
    <section class="rounded-md border border-zinc-200 bg-white">
        <div class="flex items-center justify-between border-b border-zinc-200 p-5">
            <h2 class="font-semibold">Bought from customers</h2>
            <a href="{{ route('purchases.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Record Purchase</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">Qty</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Payment</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td class="px-5 py-4">{{ $purchase->purchased_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('purchases.show', $purchase) }}" class="font-semibold hover:underline">{{ $purchase->customer->name }}</a>
                                <div class="text-xs text-zinc-500">{{ $purchase->customer->phone }}{{ $purchase->customer->kuwait_id ? ' / ID '.$purchase->customer->kuwait_id : '' }}</div>
                            </td>
                            <td class="px-5 py-4">{{ $purchase->product->name }}</td>
                            <td class="px-5 py-4">{{ $purchase->quantity }}</td>
                            <td class="px-5 py-4">{{ number_format($purchase->total_amount, 3) }} KD</td>
                            <td class="px-5 py-4">{{ $purchase->payment_method ?: '-' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">Edit</a>
                                    <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" onsubmit="return confirm('Delete this customer purchase? Product stock will be reduced by this purchase quantity.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-700 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-zinc-500">No purchases recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-5 py-4">{{ $purchases->links() }}</div>
    </section>
</x-layouts.app>
