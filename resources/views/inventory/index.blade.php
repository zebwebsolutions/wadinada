<x-layouts.app heading="Inventory lookup" eyebrow="Scan any device">
    <section class="overflow-hidden rounded-xl bg-zinc-950 text-white shadow-sm">
        <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <form class="max-w-3xl">
                <label class="block">
                    <span class="text-sm font-semibold">Scan IMEI, serial number or barcode</span>
                    <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                        <input
                            name="search"
                            value="{{ $search }}"
                            autofocus
                            autocomplete="off"
                            autocapitalize="off"
                            spellcheck="false"
                            enterkeyhint="search"
                            placeholder="Keep the cursor here and scan a box"
                            class="min-w-0 flex-1 rounded-lg border border-zinc-700 bg-white px-4 py-3 font-mono text-base text-zinc-950 shadow-sm focus:border-white focus:outline-none focus:ring-2 focus:ring-white/30"
                        >
                        <input type="hidden" name="status" value="{{ $status }}">
                        <button class="rounded-lg bg-white px-5 py-3 text-sm font-bold text-zinc-950 hover:bg-zinc-200">Find unit</button>
                    </div>
                </label>
                <p class="mt-2 text-xs text-zinc-400">Search also accepts product name, brand, storage, color or SKU.</p>
            </form>
            <a href="{{ route('purchases.create') }}" class="rounded-lg border border-zinc-700 px-4 py-3 text-center text-sm font-semibold hover:bg-zinc-900">+ Receive inventory</a>
        </div>
    </section>

    <div class="mt-5 grid gap-4 sm:grid-cols-3">
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Available units</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($availableCount) }}</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Inventory cost</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($inventoryValue, 3) }} <span class="text-base text-zinc-500">KD</span></p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Sold units</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($soldCount) }}</p>
        </section>
    </div>

    <section class="mt-5 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-zinc-200 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold">{{ $search ? 'Search results for “'.$search.'”' : 'Physical inventory units' }}</h2>
                <p class="mt-1 text-xs text-zinc-500">{{ $units->total() }} matching {{ Str::plural('unit', $units->total()) }}</p>
            </div>
            <div class="flex rounded-lg bg-zinc-100 p-1 text-sm font-semibold">
                <a href="{{ route('inventory.index', array_filter(['search' => $search, 'status' => 'available'])) }}" @class([
                    'rounded-md px-3 py-2',
                    'bg-white shadow-sm' => $status === 'available',
                    'text-zinc-600' => $status !== 'available',
                ])>Available</a>
                <a href="{{ route('inventory.index', array_filter(['search' => $search, 'status' => 'sold'])) }}" @class([
                    'rounded-md px-3 py-2',
                    'bg-white shadow-sm' => $status === 'sold',
                    'text-zinc-600' => $status !== 'sold',
                ])>Sold</a>
                <a href="{{ route('inventory.index', array_filter(['search' => $search, 'status' => 'all'])) }}" @class([
                    'rounded-md px-3 py-2',
                    'bg-white shadow-sm' => $status === 'all',
                    'text-zinc-600' => $status !== 'all',
                ])>All</a>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Identifier</th>
                        <th class="px-5 py-3">Product variant</th>
                        <th class="px-5 py-3">Exact cost</th>
                        <th class="px-5 py-3">Source</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($units as $unit)
                        @php($primary = $unit->identifiers->firstWhere('is_primary', true))
                        <tr class="{{ $search && $units->total() === 1 ? 'bg-emerald-50/70' : 'hover:bg-zinc-50/70' }}">
                            <td class="px-5 py-4">
                                <div class="font-mono font-bold">{{ $primary?->value ?? $unit->imei ?? 'Unit #'.$unit->id }}</div>
                                <div class="mt-1 text-xs uppercase text-zinc-500">{{ $primary?->type ?? 'legacy' }}</div>
                                @if ($unit->identifiers->where('is_primary', false)->isNotEmpty())
                                    <div class="mt-1 font-mono text-xs text-zinc-500">{{ $unit->identifiers->where('is_primary', false)->pluck('value')->join(' · ') }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('products.show', $unit->product) }}" class="font-semibold hover:underline">{{ $unit->product->variant_name }}</a>
                                <div class="mt-1 text-xs text-zinc-500">
                                    {{ collect([$unit->product->brand, $unit->condition ?? $unit->product->condition, $unit->product->sku ? 'SKU '.$unit->product->sku : null])->filter()->join(' · ') }}
                                </div>
                            </td>
                            <td class="px-5 py-4 font-semibold">{{ number_format($unit->cost_price, 3) }} KD</td>
                            <td class="px-5 py-4">
                                @if ($unit->purchase)
                                    <div>{{ $unit->purchase->customer->name }}</div>
                                    <div class="mt-1 text-xs text-zinc-500">
                                        @if ($unit->purchase->batch)
                                            <a href="{{ route('purchase-batches.show', $unit->purchase->batch) }}" class="hover:underline">{{ $unit->purchase->batch->batch_number }}</a>
                                        @endif
                                        · {{ $unit->purchase->purchased_at->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-zinc-500">Legacy inventory</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-800' => $unit->status === 'available',
                                    'bg-zinc-200 text-zinc-700' => $unit->status === 'sold',
                                    'bg-amber-100 text-amber-800' => ! in_array($unit->status, ['available', 'sold'], true),
                                ])>{{ ucfirst($unit->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($unit->status === 'available')
                                    <a href="{{ route('sales.create', ['unit' => $unit->id]) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">Sell</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="font-semibold">No inventory unit found</p>
                                <p class="mt-1 text-sm text-zinc-500">Check the scan or change the status filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-5 py-4">{{ $units->links() }}</div>
    </section>
</x-layouts.app>
