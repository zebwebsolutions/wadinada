<x-layouts.app heading="{{ $purchaseBatch->batch_number }}" eyebrow="Purchase batch">
    @php
        $unitCount = $purchaseBatch->items->sum('quantity');
        $availableCount = $purchaseBatch->items->sum(fn ($item) => $item->units->where('status', 'available')->count());
    @endphp

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-zinc-600">
                {{ $purchaseBatch->purchased_at->format('d M Y') }} ·
                {{ $purchaseBatch->customer->name }} ·
                {{ $unitCount }} {{ Str::plural('unit', $unitCount) }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('inventory.index') }}" class="rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-zinc-50">Search inventory</a>
            <a href="{{ route('purchases.create') }}" class="rounded-lg bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">New batch</a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Total cost</p>
            <p class="mt-2 text-2xl font-bold">{{ number_format($purchaseBatch->total_amount, 3) }} KD</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Units received</p>
            <p class="mt-2 text-2xl font-bold">{{ $unitCount }}</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Still available</p>
            <p class="mt-2 text-2xl font-bold">{{ $availableCount }}</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Payment</p>
            <p class="mt-2 text-lg font-bold">{{ $purchaseBatch->payment_method ?: 'Not specified' }}</p>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-5">
            @foreach ($purchaseBatch->items as $item)
                <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
                    <header class="flex flex-col gap-3 border-b border-zinc-200 bg-zinc-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <a href="{{ route('products.show', $item->product) }}" class="text-lg font-semibold hover:underline">{{ $item->product->variant_name }}</a>
                            <p class="mt-1 text-xs text-zinc-500">
                                {{ collect([$item->product->brand, $item->product->condition, $item->product->sku ? 'SKU '.$item->product->sku : null])->filter()->join(' · ') }}
                            </p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="font-semibold">{{ $item->quantity }} units · {{ number_format($item->total_amount, 3) }} KD</p>
                            <p class="text-xs text-zinc-500">Average {{ number_format($item->unit_price, 3) }} KD</p>
                        </div>
                    </header>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-sm">
                            <thead class="bg-white text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                <tr>
                                    <th class="px-5 py-3">Primary identifier</th>
                                    <th class="px-5 py-3">Other identifiers</th>
                                    <th class="px-5 py-3">Exact cost</th>
                                    <th class="px-5 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach ($item->units as $unit)
                                    @php($primary = $unit->identifiers->firstWhere('is_primary', true))
                                    <tr>
                                        <td class="px-5 py-3 font-mono font-semibold">{{ $primary?->value ?? $unit->imei ?? 'Unit #'.$unit->id }}</td>
                                        <td class="px-5 py-3 font-mono text-xs text-zinc-600">
                                            {{ $unit->identifiers->where('is_primary', false)->pluck('value')->join(', ') ?: '—' }}
                                        </td>
                                        <td class="px-5 py-3">{{ number_format($unit->cost_price, 3) }} KD</td>
                                        <td class="px-5 py-3">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-emerald-100 text-emerald-800' => $unit->status === 'available',
                                                'bg-zinc-200 text-zinc-700' => $unit->status !== 'available',
                                            ])>{{ ucfirst($unit->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>

        <aside class="space-y-5">
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold">Seller</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Name</dt>
                        <dd class="mt-1 font-medium">{{ $purchaseBatch->customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Phone</dt>
                        <dd class="mt-1 font-medium">{{ $purchaseBatch->customer->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Email</dt>
                        <dd class="mt-1 font-medium">{{ $purchaseBatch->customer->email ?: '—' }}</dd>
                    </div>
                </dl>
                <a href="{{ route('customers.show', $purchaseBatch->customer) }}" class="mt-4 inline-block text-sm font-semibold text-zinc-700 hover:text-zinc-950">View seller history →</a>
            </section>

            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold">Batch details</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-zinc-500">Recorded by</dt><dd class="font-medium">{{ $purchaseBatch->creator?->name ?: 'System' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-500">Variants</dt><dd class="font-medium">{{ $purchaseBatch->items->count() }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-zinc-500">Date</dt><dd class="font-medium">{{ $purchaseBatch->purchased_at->format('d M Y') }}</dd></div>
                </dl>
                @if ($purchaseBatch->notes)
                    <p class="mt-4 rounded-lg bg-zinc-50 p-3 text-sm text-zinc-700">{{ $purchaseBatch->notes }}</p>
                @endif
            </section>
        </aside>
    </div>
</x-layouts.app>
