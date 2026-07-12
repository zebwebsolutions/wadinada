<x-layouts.app heading="Receipt {{ $order->order_number }}" eyebrow="Order details">
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500">{{ $order->ordered_at->format('d M Y') }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $order->order_number }}</h2>
                </div>
                <a href="{{ route('orders.print', $order) }}" target="_blank" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Print Receipt</a>
            </div>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Price</th>
                            <th class="px-3 py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-3 py-2 font-semibold">{{ $item->product->name }}</td>
                                <td class="px-3 py-2">{{ $item->quantity }}</td>
                                <td class="px-3 py-2">{{ number_format($item->unit_price, 3) }} KD</td>
                                <td class="px-3 py-2">{{ number_format($item->total_amount, 3) }} KD</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Total</dt><dd class="font-medium">{{ number_format($order->total_amount, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Payment</dt><dd class="font-medium">{{ $order->payment_method ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Salesman</dt><dd class="font-medium">{{ $order->salesman_name ?: '-' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Customer</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Name</dt><dd class="font-medium">{{ $order->customer_name ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Phone</dt><dd class="font-medium">{{ $order->customer_phone ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Client ID</dt><dd class="font-medium">{{ $order->customer_id_number ?: '-' }}</dd></div>
            </dl>
            @if ($order->kuwait_id_path)
                <a href="{{ asset('storage/'.$order->kuwait_id_path) }}" target="_blank" class="mt-5 inline-block rounded-md border border-zinc-200 p-2 text-sm font-semibold hover:bg-zinc-50">
                    <img src="{{ asset('storage/'.$order->kuwait_id_path) }}" alt="Kuwait ID" class="mb-2 h-36 w-56 rounded object-cover">
                    Kuwait ID
                </a>
            @endif
        </section>
    </div>
</x-layouts.app>
