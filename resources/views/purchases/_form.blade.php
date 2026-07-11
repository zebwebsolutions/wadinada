@csrf

<div class="space-y-6">
    <section class="rounded-md border border-zinc-200 bg-white p-5">
        <h2 class="mb-4 font-semibold">Device / Product Information</h2>
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold">Product name</span>
                <input name="product_name" value="{{ old('product_name', $product->name) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('product_name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Category</span>
                <select name="product_category" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(old('product_category', $product->category) === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                @error('product_category') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Brand</span>
                <input name="product_brand" value="{{ old('product_brand', $product->brand) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">SKU / Barcode</span>
                <input name="product_sku" value="{{ old('product_sku', $product->sku) }}" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="next" data-barcode-field class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                <span class="mt-1 block text-xs text-zinc-500">Click here and scan the device barcode. Scanner Enter will move to the next field instead of submitting.</span>
                @error('product_sku') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">IMEI 1</span>
                <input name="product_imei1" value="{{ old('product_imei1', $product->imei1) }}" autocomplete="off" autocapitalize="off" spellcheck="false" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('product_imei1') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">IMEI 2</span>
                <input name="product_imei2" value="{{ old('product_imei2', $product->imei2) }}" autocomplete="off" autocapitalize="off" spellcheck="false" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('product_imei2') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Condition</span>
                <select name="product_condition" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    @foreach ($conditions as $condition)
                        <option value="{{ $condition }}" @selected(old('product_condition', $product->condition) === $condition)>{{ $condition }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Purchase date</span>
                <input type="date" name="purchased_at" value="{{ old('purchased_at', optional($purchase->purchased_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('purchased_at') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Quantity bought</span>
                <input type="number" min="1" name="quantity" value="{{ old('quantity', $purchase->quantity ?? 1) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('quantity') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Purchase price KD</span>
                <input type="number" min="0" step="0.001" name="unit_price" value="{{ old('unit_price', $purchase->unit_price) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('unit_price') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Expected sale price KD</span>
                <input type="number" min="0" step="0.001" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('sale_price') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Payment method</span>
                <select name="payment_method" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Not specified</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(old('payment_method', $purchase->payment_method) === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Device notes</span>
                <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ old('notes', $purchase->notes) }}</textarea>
            </label>
        </div>
    </section>

    <section class="rounded-md border border-zinc-200 bg-white p-5">
        <h2 class="mb-4 font-semibold">Customer Information</h2>
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold">Customer name</span>
                <input name="customer_name" value="{{ old('customer_name', $purchase->customer->name ?? '') }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Email</span>
                <input type="email" name="customer_email" value="{{ old('customer_email', $purchase->customer->email ?? '') }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_email') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Phone</span>
                <input name="customer_phone" value="{{ old('customer_phone', $purchase->customer->phone ?? '') }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_phone') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Kuwait ID</span>
                <input type="file" name="customer_kuwait_id" accept="image/*" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm shadow-sm focus:border-zinc-950 focus:outline-none">
                <span class="mt-1 block text-xs text-zinc-500">Image files up to 10 MB are accepted.</span>
                @error('customer_kuwait_id') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                @if (! empty($purchase->customer?->kuwait_id_path))
                    <a href="{{ asset('storage/'.$purchase->customer->kuwait_id_path) }}" target="_blank" class="mt-3 inline-block rounded-md border border-zinc-200 bg-white p-2 hover:bg-zinc-50">
                        <img src="{{ asset('storage/'.$purchase->customer->kuwait_id_path) }}" alt="Current Kuwait ID" class="h-24 w-36 rounded object-cover">
                        <span class="mt-1 block text-xs font-semibold text-zinc-700">View Kuwait ID</span>
                    </a>
                @endif
            </label>
        </div>
    </section>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">{{ $buttonLabel }}</button>
    <a href="{{ route('purchases.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>
