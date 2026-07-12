@php
    $field = fn (string $name) => $prefix ? "{$prefix}[{$name}]" : 'product_'.$name;
    $oldKey = fn (string $name) => $prefix ? str_replace(['[', ']'], ['.', ''], "{$prefix}[{$name}]") : 'product_'.$name;
    $unitPrefix = $prefix ? "{$prefix}[units]" : null;
@endphp

<label class="block">
    <span class="text-sm font-semibold">Title</span>
    <input name="{{ $field('name') }}" value="{{ old($oldKey('name'), $product->name) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
</label>

<label class="block">
    <span class="text-sm font-semibold">Category</span>
    <select name="{{ $field('category') }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @foreach ($categories as $category)
            <option value="{{ $category }}" @selected(old($oldKey('category'), $product->category) === $category)>{{ $category }}</option>
        @endforeach
    </select>
</label>

<label class="block">
    <span class="text-sm font-semibold">Brand</span>
    <select name="{{ $field('brand') }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        <option value="">Select brand</option>
        @foreach ($brands as $brand)
            <option value="{{ $brand->name }}" @selected(old($oldKey('brand'), $product->brand) === $brand->name)>{{ $brand->name }}</option>
        @endforeach
    </select>
</label>

<label class="block">
    <span class="text-sm font-semibold">SKU / Serial</span>
    <input name="{{ $field('sku') }}" value="{{ old($oldKey('sku'), $product->sku) }}" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="next" data-barcode-field class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
</label>

<label class="block">
    <span class="text-sm font-semibold">Condition</span>
    <select name="{{ $field('condition') }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @foreach ($conditions as $condition)
            <option value="{{ $condition }}" @selected(old($oldKey('condition'), $product->condition) === $condition)>{{ $condition }}</option>
        @endforeach
    </select>
</label>

<label class="block">
    <span class="text-sm font-semibold">Sale price KD</span>
    <input type="number" min="0" step="0.001" name="{{ $field('sale_price') }}" value="{{ old($oldKey('sale_price'), $product->sale_price) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    <span class="mt-1 block text-xs text-zinc-500">Optional. Sales staff can set or update this at checkout.</span>
</label>

@if ($prefix)
    <div class="lg:col-span-2">
        <div class="mb-3 flex items-center justify-between">
            <h4 class="text-sm font-semibold">Units</h4>
            <button type="button" data-add-unit class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Add Unit</button>
        </div>
        <div data-units class="space-y-3">
            <div data-unit-row class="grid gap-3 rounded-md border border-zinc-200 p-3 sm:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-semibold">IMEI</span>
                    <input name="{{ $unitPrefix }}[0][imei]" autocomplete="off" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold">Cost price KD</span>
                    <input type="number" min="0" step="0.001" name="{{ $unitPrefix }}[0][cost_price]" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>
                <button type="button" data-remove-unit class="hidden text-left text-sm font-semibold text-red-700 sm:col-span-2">Remove unit</button>
            </div>
        </div>
    </div>
@else
    <label class="block">
        <span class="text-sm font-semibold">Quantity bought</span>
        <input type="number" min="1" name="{{ $field('quantity') }}" value="{{ old($oldKey('quantity'), $purchase->quantity ?? 1) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Purchase price KD</span>
        <input type="number" min="0" step="0.001" name="{{ $field('unit_price') }}" value="{{ old($oldKey('unit_price'), $purchase->unit_price) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    </label>
@endif

<label class="block lg:col-span-2">
    <span class="text-sm font-semibold">Notes</span>
    <textarea name="{{ $field('notes') }}" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ old($oldKey('notes'), $purchase->notes) }}</textarea>
</label>
