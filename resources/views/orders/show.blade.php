<x-layouts.app heading="Receipt #{{ $order->id }}" eyebrow="Order details">
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500">{{ $order->sold_at->format('d M Y') }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $order->product->name }}</h2>
                </div>
                <a href="{{ route('orders.print', $order) }}" target="_blank" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Print Receipt</a>
            </div>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Quantity</dt><dd class="font-medium">{{ $order->quantity }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Unit price</dt><dd class="font-medium">{{ number_format($order->unit_price, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Total</dt><dd class="font-medium">{{ number_format($order->total_amount, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Payment</dt><dd class="font-medium">{{ $order->payment_method ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Salesman</dt><dd class="font-medium">{{ $order->salesman_name ?: '-' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Customer</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Name</dt><dd class="font-medium">{{ $order->customer_name ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Email</dt><dd class="font-medium">{{ $order->customer_email ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Phone</dt><dd class="font-medium">{{ $order->customer_phone ?: '-' }}</dd></div>
            </dl>
        </section>
    </div>
</x-layouts.app>
