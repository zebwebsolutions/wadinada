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
        <input name="brand" value="{{ old('brand', $product->brand) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    </label>

    <label class="block">
        <span class="text-sm font-semibold">SKU / Barcode</span>
        <input name="sku" value="{{ old('sku', $product->sku) }}" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="next" data-barcode-field class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        <span class="mt-1 block text-xs text-zinc-500">Click here and scan with the hand scanner, or type the barcode manually.</span>
        @error('sku') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">IMEI 1</span>
        <input name="imei1" value="{{ old('imei1', $product->imei1) }}" autocomplete="off" autocapitalize="off" spellcheck="false" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('imei1') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">IMEI 2</span>
        <input name="imei2" value="{{ old('imei2', $product->imei2) }}" autocomplete="off" autocapitalize="off" spellcheck="false" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('imei2') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
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
        <span class="text-sm font-semibold">Stock quantity</span>
        <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('stock_quantity') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Purchase price KD</span>
        <input type="number" step="0.001" min="0" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('purchase_price') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
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

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">{{ $buttonLabel }}</button>
    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>
