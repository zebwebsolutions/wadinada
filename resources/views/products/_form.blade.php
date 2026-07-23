@csrf

<div class="grid gap-5 lg:grid-cols-2">
    <label class="block">
        <span class="text-sm font-semibold">Product name</span>
        <input name="name" value="{{ old('name', $product->name) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Category</span>
        <select name="category" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>
            @endforeach
        </select>
        @error('category') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Brand</span>
        <select name="brand" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            <option value="">Select brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->name }}" @selected(old('brand', $product->brand) === $brand->name)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-semibold">SKU / Barcode</span>
        <input name="sku" value="{{ old('sku', $product->sku) }}" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="next" data-barcode-field class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        <span class="mt-1 block text-xs text-zinc-500">Click here and scan with the hand scanner, or type the barcode manually.</span>
        @error('sku') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Condition</span>
        <select name="condition" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            @foreach ($conditions as $condition)
                <option value="{{ $condition }}" @selected(old('condition', $product->condition) === $condition)>{{ $condition }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Storage / capacity</span>
        <input name="storage_capacity" value="{{ old('storage_capacity', $product->storage_capacity) }}" placeholder="256 GB" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Color</span>
        <input name="color" value="{{ old('color', $product->color) }}" placeholder="Black" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Unit tracking</span>
        <select name="tracking_method" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            @foreach ($trackingMethods as $value => $label)
                <option value="{{ $value }}" @selected(old('tracking_method', $product->tracking_method ?? 'imei') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <span class="mt-1 block text-xs text-zinc-500">This decides what staff scan when receiving and selling a unit.</span>
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Sale price KD</span>
        <input type="number" step="0.001" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('sale_price') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block lg:col-span-2">
        <span class="text-sm font-semibold">Notes</span>
        <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ old('notes', $product->notes) }}</textarea>
    </label>
</div>

<div class="mt-5 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
    This form defines a reusable product variant. Stock and purchase cost are added through
    <a href="{{ route('purchases.create') }}" class="font-semibold underline">Batch inventory intake</a>
    so every physical unit keeps its own identifier and exact cost.
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">{{ $buttonLabel }}</button>
    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>
