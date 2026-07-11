<x-layouts.app heading="Purchase #{{ $purchase->id }}" eyebrow="Purchase intake">
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500">{{ $purchase->purchased_at->format('d M Y') }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $purchase->product->name }}</h2>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('purchases.edit', $purchase) }}" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Edit</a>
                    <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" onsubmit="return confirm('Delete this customer purchase? Product stock will be reduced by this purchase quantity.');">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            </div>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Quantity</dt><dd class="font-medium">{{ $purchase->quantity }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Unit price</dt><dd class="font-medium">{{ number_format($purchase->unit_price, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Total</dt><dd class="font-medium">{{ number_format($purchase->total_amount, 3) }} KD</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Payment</dt><dd class="font-medium">{{ $purchase->payment_method ?: '-' }}</dd></div>
            </dl>
            @if ($purchase->notes)
                <p class="mt-5 rounded-md bg-zinc-50 p-3 text-sm text-zinc-700">{{ $purchase->notes }}</p>
            @endif
        </section>

        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Customer Details</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Name</dt><dd class="font-medium">{{ $purchase->customer->name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Email</dt><dd class="font-medium">{{ $purchase->customer->email ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Phone</dt><dd class="font-medium">{{ $purchase->customer->phone }}</dd></div>
            </dl>
            <div class="mt-5">
                @if ($purchase->customer->kuwait_id_path)
                    <a href="{{ asset('storage/'.$purchase->customer->kuwait_id_path) }}" target="_blank" class="inline-block rounded-md border border-zinc-200 p-2 text-sm font-semibold hover:bg-zinc-50">
                        <img src="{{ asset('storage/'.$purchase->customer->kuwait_id_path) }}" alt="Kuwait ID" class="mb-2 h-36 w-56 rounded object-cover">
                        Kuwait ID
                    </a>
                @else
                    <p class="text-sm text-zinc-500">No Kuwait ID image uploaded.</p>
                @endif
            </div>
        </section>
    </div>
</x-layouts.app>
