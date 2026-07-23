@php
    $item = $item ?? [];
    $selectedProductId = (string) ($item['product_id'] ?? '');
    $isExisting = $selectedProductId !== '';
    $lineTracking = $item['tracking_method'] ?? 'imei';
    $units = array_values($item['units'] ?? []);
@endphp

<article data-intake-line data-line-index="{{ $index }}" class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
    <header class="flex flex-col gap-3 border-b border-zinc-200 bg-zinc-50/80 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span data-line-number class="flex size-8 items-center justify-center rounded-full bg-zinc-950 text-sm font-bold text-white">{{ is_numeric($index) ? $index + 1 : 1 }}</span>
            <div>
                <h3 class="font-semibold" data-line-heading>Inventory group</h3>
                <p class="text-xs text-zinc-500">One model, storage, color, condition and default cost.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span data-line-progress class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">0 / 1 scanned</span>
            <button type="button" data-remove-line class="text-sm font-semibold text-red-700 hover:text-red-900">Remove group</button>
        </div>
    </header>

    <div class="space-y-6 p-5">
        <div class="grid gap-4 lg:grid-cols-12">
            <label class="block lg:col-span-5">
                <span class="text-sm font-semibold">Use an existing variant</span>
                <select name="items[{{ $index }}][product_id]" data-product-select class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Create a new variant</option>
                    @foreach ($products as $existingProduct)
                        <option
                            value="{{ $existingProduct->id }}"
                            data-tracking="{{ $existingProduct->tracking_method }}"
                            data-label="{{ $existingProduct->variant_name }}"
                            data-specs="{{ collect([$existingProduct->brand, $existingProduct->condition, $existingProduct->sku ? 'SKU '.$existingProduct->sku : null])->filter()->join(' · ') }}"
                            @selected($selectedProductId === (string) $existingProduct->id)
                        >
                            {{ $existingProduct->brand ? $existingProduct->brand.' · ' : '' }}{{ $existingProduct->variant_name }} · {{ $existingProduct->condition }} · {{ $existingProduct->available_units_count }} available
                        </option>
                    @endforeach
                </select>
                <span class="mt-1 block text-xs text-zinc-500">Reuse variants to keep all batches of the same device together.</span>
                @error("items.{$index}.product_id") <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Expected quantity</span>
                <input type="number" min="1" max="500" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required data-quantity class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error("items.{$index}.quantity") <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Default cost</span>
                <div class="relative mt-1">
                    <input type="number" min="0" step="0.001" name="items[{{ $index }}][default_cost]" value="{{ $item['default_cost'] ?? '' }}" required data-default-cost class="w-full rounded-lg border border-zinc-300 py-2.5 pl-3 pr-10 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-zinc-500">KD</span>
                </div>
                @error("items.{$index}.default_cost") <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block lg:col-span-3">
                <span class="text-sm font-semibold">Suggested sale price</span>
                <div class="relative mt-1">
                    <input type="number" min="0" step="0.001" name="items[{{ $index }}][sale_price]" value="{{ $item['sale_price'] ?? '' }}" class="w-full rounded-lg border border-zinc-300 py-2.5 pl-3 pr-10 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-zinc-500">KD</span>
                </div>
            </label>
        </div>

        <div data-new-variant-fields @class(['rounded-lg border border-sky-200 bg-sky-50/60 p-4', 'hidden' => $isExisting])>
            <div class="mb-4">
                <h4 class="font-semibold text-sky-950">New variant details</h4>
                <p class="text-xs text-sky-800">Example: Apple · iPhone 17 · 256 GB · Black.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-semibold">Product name</span>
                    <input name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" placeholder="iPhone 17" data-variant-name class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    @error("items.{$index}.name") <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Brand</span>
                    <input name="items[{{ $index }}][brand]" value="{{ $item['brand'] ?? '' }}" list="intake-brand-options" placeholder="Apple" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Category</span>
                    <select name="items[{{ $index }}][category]" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(($item['category'] ?? 'Phones') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Condition</span>
                    <select name="items[{{ $index }}][condition]" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition }}" @selected(($item['condition'] ?? 'New') === $condition)>{{ $condition }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Storage / capacity</span>
                    <input name="items[{{ $index }}][storage_capacity]" value="{{ $item['storage_capacity'] ?? '' }}" placeholder="256 GB" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Color</span>
                    <input name="items[{{ $index }}][color]" value="{{ $item['color'] ?? '' }}" placeholder="Black" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">SKU / model barcode</span>
                    <input name="items[{{ $index }}][sku]" value="{{ $item['sku'] ?? '' }}" autocomplete="off" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    @error("items.{$index}.sku") <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">How each unit is tracked</span>
                    <select name="items[{{ $index }}][tracking_method]" data-new-tracking class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                        @foreach ($trackingMethods as $value => $label)
                            <option value="{{ $value }}" @selected($lineTracking === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error("items.{$index}.tracking_method") <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <section data-manual-identifiers class="rounded-lg border border-zinc-200">
            <div class="grid gap-4 border-b border-zinc-200 bg-zinc-50 p-4 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <label class="block">
                        <span data-scan-label class="text-sm font-semibold">Scan IMEI</span>
                        <input data-scan-input autocomplete="off" inputmode="numeric" placeholder="Scan a box and press Enter" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-3 font-mono text-base shadow-sm focus:border-zinc-950 focus:outline-none focus:ring-2 focus:ring-zinc-200">
                    </label>
                    <p data-scan-status class="mt-2 text-xs text-zinc-500">Each scan is added immediately. Duplicate identifiers are blocked.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-add-scan class="rounded-lg bg-zinc-950 px-4 py-3 text-sm font-semibold text-white hover:bg-zinc-800">Add identifier</button>
                    <button type="button" data-toggle-paste class="rounded-lg border border-zinc-300 bg-white px-4 py-3 text-sm font-semibold hover:bg-zinc-100">Paste a list</button>
                </div>
            </div>

            <div data-paste-panel class="hidden border-b border-zinc-200 bg-amber-50 p-4">
                <label class="block">
                    <span class="text-sm font-semibold text-amber-950">Paste from a spreadsheet or text file</span>
                    <textarea data-bulk-input rows="5" placeholder="One identifier per line&#10;Or paste columns: identifier    second identifier    cost" class="mt-1 w-full rounded-lg border border-amber-300 bg-white px-3 py-2 font-mono text-sm focus:border-amber-700 focus:outline-none"></textarea>
                </label>
                <div class="mt-3 flex items-center gap-3">
                    <button type="button" data-import-list class="rounded-lg bg-amber-900 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-800">Import list</button>
                    <span class="text-xs text-amber-800">Tabs or commas are treated as columns.</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-white text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        <tr>
                            <th class="w-12 px-4 py-3">#</th>
                            <th class="min-w-56 px-4 py-3" data-primary-heading>IMEI 1</th>
                            <th class="min-w-56 px-4 py-3" data-secondary-heading>IMEI 2 <span class="normal-case font-normal">(optional)</span></th>
                            <th class="min-w-36 px-4 py-3">Cost override</th>
                            <th class="w-20 px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody data-unit-rows class="divide-y divide-zinc-100 bg-white">
                        @foreach ($units as $unitIndex => $unit)
                            <tr data-unit-row>
                                <td data-unit-number class="px-4 py-3 font-semibold text-zinc-500">{{ $unitIndex + 1 }}</td>
                                <td class="px-4 py-3">
                                    <input name="items[{{ $index }}][units][{{ $unitIndex }}][identifier]" value="{{ $unit['identifier'] ?? '' }}" data-unit-identifier class="w-full rounded-md border border-zinc-300 px-3 py-2 font-mono focus:border-zinc-950 focus:outline-none">
                                </td>
                                <td class="px-4 py-3">
                                    <input name="items[{{ $index }}][units][{{ $unitIndex }}][secondary_identifier]" value="{{ $unit['secondary_identifier'] ?? '' }}" data-secondary-identifier class="w-full rounded-md border border-zinc-300 px-3 py-2 font-mono focus:border-zinc-950 focus:outline-none">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" min="0" step="0.001" name="items[{{ $index }}][units][{{ $unitIndex }}][cost_price]" value="{{ $unit['cost_price'] ?? '' }}" data-unit-cost class="w-full rounded-md border border-zinc-300 px-3 py-2 focus:border-zinc-950 focus:outline-none">
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" data-remove-unit class="font-semibold text-red-700 hover:text-red-900">Remove</button>
                                </td>
                            </tr>
                        @endforeach
                        <tr data-empty-units @class(['hidden' => count($units) > 0])>
                            <td colspan="5" class="px-4 py-8 text-center text-zinc-500">
                                No identifiers scanned yet. Keep the cursor in the scan field and scan each box.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @error("items.{$index}.units") <div class="border-t border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ $message }}</div> @enderror
        </section>

        <section data-internal-identifiers class="hidden rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <h4 class="font-semibold text-emerald-950">Automatic inventory codes</h4>
            <p class="mt-1 text-sm text-emerald-800">
                Wadi Nada codes will be generated for the requested quantity. Use this for items without a useful serial or manufacturer barcode.
            </p>
        </section>

        <label class="block">
            <span class="text-sm font-semibold">Line notes <span class="font-normal text-zinc-500">(optional)</span></span>
            <textarea name="items[{{ $index }}][notes]" rows="2" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ $item['notes'] ?? '' }}</textarea>
        </label>

        <div class="flex items-center justify-between rounded-lg bg-zinc-950 px-4 py-3 text-white">
            <span class="text-sm text-zinc-300">Estimated group cost</span>
            <strong data-line-total class="text-lg">0.000 KD</strong>
        </div>
    </div>
</article>
