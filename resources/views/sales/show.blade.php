<x-layouts.app heading="Sale #{{ $sale->id }}" eyebrow="Sales">
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500">{{ $sale->sold_at->format('d M Y') }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $sale->product->variant_name }}</h2>
                </div>
                <div class="flex gap-2">
                    @if ($sale->order_id)
                        <a href="{{ route('orders.print', $sale->order_id) }}" target="_blank" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Print Receipt</a>
                    @else
                        <a href="{{ route('sales.edit', $sale) }}" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Edit</a>
                        <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this legacy sale? Product stock will be restored.');">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-md border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Quantity</dt><dd class="font-medium">{{ $sale->quantity }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Unit price</dt><dd class="font-medium">{{ number_format($sale->unit_price, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Total</dt><dd class="font-medium">{{ number_format($sale->total_amount, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Payment</dt><dd class="font-medium">{{ $sale->payment_method ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Salesman</dt><dd class="font-medium">{{ $sale->salesman_name ?: '-' }}</dd></div>
            </dl>
            @if ($sale->notes)
                <p class="mt-5 rounded-md bg-zinc-50 p-3 text-sm text-zinc-700">{{ $sale->notes }}</p>
            @endif
        </section>

        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Buyer Details</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Name</dt><dd class="font-medium">{{ $sale->customer_name ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Email</dt><dd class="font-medium">{{ $sale->customer_email ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Phone</dt><dd class="font-medium">{{ $sale->customer_phone ?: '-' }}</dd></div>
            </dl>
        </section>
    </div>
</x-layouts.app>
