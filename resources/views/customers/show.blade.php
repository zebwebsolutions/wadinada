<x-layouts.app heading="{{ $customer->name }}" eyebrow="Customer profile">
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-md border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Customer Details</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Name</dt><dd class="font-medium">{{ $customer->name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Phone</dt><dd class="font-medium">{{ $customer->phone }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-zinc-500">Email</dt><dd class="font-medium">{{ $customer->email ?: '-' }}</dd></div>
            </dl>

            <div class="mt-5">
                @if ($customer->kuwait_id_path)
                    <a href="{{ asset('storage/'.$customer->kuwait_id_path) }}" target="_blank" class="inline-block rounded-md border border-zinc-200 p-2 text-sm font-semibold hover:bg-zinc-50">
                        <img src="{{ asset('storage/'.$customer->kuwait_id_path) }}" alt="Kuwait ID" class="mb-2 h-36 w-56 rounded object-cover">
                        Kuwait ID
                    </a>
                @else
                    <p class="text-sm text-zinc-500">No Kuwait ID image uploaded.</p>
                @endif
            </div>
        </section>

        <section class="rounded-md border border-zinc-200 bg-white lg:col-span-2">
            <div class="border-b border-zinc-200 px-5 py-4">
                <h2 class="font-semibold">Purchase History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Qty</th>
                            <th class="px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($customer->purchases as $purchase)
                            <tr>
                                <td class="px-5 py-3">{{ $purchase->purchased_at->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="font-semibold hover:underline">{{ $purchase->product->name }}</a>
                                </td>
                                <td class="px-5 py-3">{{ $purchase->quantity }}</td>
                                <td class="px-5 py-3">{{ number_format($purchase->total_amount, 3) }} KD</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-zinc-500">No purchases recorded for this customer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
