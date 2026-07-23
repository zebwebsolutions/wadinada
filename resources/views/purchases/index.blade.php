<x-layouts.app heading="Purchase batches" eyebrow="Inventory intake">
    <div class="mb-6 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <h2 class="text-xl font-semibold">Bought from customers and suppliers</h2>
            <p class="mt-1 text-sm text-zinc-600">Each batch keeps the seller, variants, identifiers and exact unit costs together.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="rounded-lg bg-zinc-950 px-4 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-zinc-800">
            + New inventory batch
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Batch</th>
                        <th class="px-5 py-3">Seller</th>
                        <th class="px-5 py-3">Variants</th>
                        <th class="px-5 py-3">Units</th>
                        <th class="px-5 py-3">Total cost</th>
                        <th class="px-5 py-3">Payment</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($batches as $batch)
                        <tr class="hover:bg-zinc-50/70">
                            <td class="px-5 py-4">
                                <a href="{{ route('purchase-batches.show', $batch) }}" class="font-semibold hover:underline">{{ $batch->batch_number }}</a>
                                <div class="mt-1 text-xs text-zinc-500">{{ $batch->purchased_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium">{{ $batch->customer->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $batch->customer->phone }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="max-w-xs">
                                    @foreach ($batch->items->take(2) as $item)
                                        <div class="truncate">{{ $item->product->variant_name }}</div>
                                    @endforeach
                                    @if ($batch->items_count > 2)
                                        <div class="text-xs font-semibold text-zinc-500">+ {{ $batch->items_count - 2 }} more</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 font-semibold">{{ (int) $batch->units_count }}</span>
                            </td>
                            <td class="px-5 py-4 font-semibold">{{ number_format($batch->total_amount, 3) }} KD</td>
                            <td class="px-5 py-4">{{ $batch->payment_method ?: '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('purchase-batches.show', $batch) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">View batch</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="font-semibold">No purchase batches yet</p>
                                <p class="mt-1 text-sm text-zinc-500">Record your first batch and scan its devices into inventory.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-5 py-4">{{ $batches->links() }}</div>
    </section>
</x-layouts.app>
