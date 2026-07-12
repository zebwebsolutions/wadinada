<x-layouts.app heading="Orders" eyebrow="Receipt lookup">
    <section class="rounded-md border border-zinc-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <form class="flex w-full gap-2 sm:max-w-md">
                <input name="search" value="{{ request('search') }}" placeholder="Search receipt, customer, phone, product, IMEI" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-950 focus:outline-none">
                <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Search</button>
            </form>
            <a href="{{ route('sales.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Sell Item</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Receipt</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-5 py-4 font-semibold">{{ $order->order_number }}</td>
                            <td class="px-5 py-4">{{ $order->ordered_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div>{{ $order->customer_name ?: '-' }}</div>
                                <div class="text-xs text-zinc-500">{{ $order->customer_phone ?: '' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold hover:underline">{{ $order->items->first()?->product?->name ?: 'Order items' }}</a>
                                <div class="text-xs text-zinc-500">{{ $order->items->count() }} item(s)</div>
                            </td>
                            <td class="px-5 py-4">{{ number_format($order->total_amount, 3) }} KD</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('orders.show', $order) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">View</a>
                                    <a href="{{ route('orders.print', $order) }}" target="_blank" class="font-semibold text-zinc-700 hover:text-zinc-950">Print</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-zinc-500">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-5 py-4">{{ $orders->links() }}</div>
    </section>
</x-layouts.app>
