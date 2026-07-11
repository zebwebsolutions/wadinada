@csrf

<div class="grid gap-6 xl:grid-cols-3">
    <section class="rounded-md border border-zinc-200 bg-white p-5 xl:col-span-2">
        <h2 class="mb-4 font-semibold">Sale Details</h2>
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Scan barcode</span>
                <input type="text" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="done" data-sale-barcode-scan placeholder="Scan barcode to select product" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                <span data-sale-barcode-status class="mt-1 block text-xs text-zinc-500">Scan an item barcode or type an IMEI here. The matching product will be selected automatically.</span>
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Product</span>
                <select name="product_id" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Select product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-sku="{{ $product->sku }}" data-imei1="{{ $product->imei1 }}" data-imei2="{{ $product->imei2 }}" data-sale-price="{{ $product->sale_price }}" @selected(old('product_id', $sale->product_id) == $product->id)>
                            {{ $product->name }} {{ $product->brand ? '- '.$product->brand : '' }} ({{ $product->stock_quantity }} in stock)
                        </option>
                    @endforeach
                </select>
                @error('product_id') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Sale date</span>
                <input type="date" name="sold_at" value="{{ old('sold_at', optional($sale->sold_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('sold_at') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Salesman name</span>
                <input name="salesman_name" value="{{ old('salesman_name', $sale->salesman_name) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('salesman_name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Quantity sold</span>
                <input type="number" min="1" name="quantity" value="{{ old('quantity', $sale->quantity ?? 1) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('quantity') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Sale price KD</span>
                <input type="number" min="0" step="0.001" name="unit_price" value="{{ old('unit_price', $sale->unit_price) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('unit_price') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Payment method</span>
                <select name="payment_method" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Not specified</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(old('payment_method', $sale->payment_method) === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Notes</span>
                <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ old('notes', $sale->notes) }}</textarea>
            </label>
        </div>
    </section>

    <section class="rounded-md border border-zinc-200 bg-white p-5">
        <h2 class="mb-4 font-semibold">Buyer Details</h2>
        <div class="space-y-4">
            <label class="block">
                <span class="text-sm font-semibold">Customer name</span>
                <input name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Email</span>
                <input type="email" name="customer_email" value="{{ old('customer_email', $sale->customer_email) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_email') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Phone</span>
                <input name="customer_phone" value="{{ old('customer_phone', $sale->customer_phone) }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_phone') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>
    </section>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">{{ $buttonLabel }}</button>
    <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scanInput = document.querySelector('[data-sale-barcode-scan]');
        const status = document.querySelector('[data-sale-barcode-status]');
        const productSelect = document.querySelector('select[name="product_id"]');
        const priceInput = document.querySelector('input[name="unit_price"]');
        const quantityInput = document.querySelector('input[name="quantity"]');

        if (!scanInput || !productSelect) {
            return;
        }

        const normalizeCode = (value) => value.trim().replace(/\s+/g, '').toLowerCase();

        const findProductOption = (barcode) => {
            const normalized = normalizeCode(barcode);

            return Array.from(productSelect.options).find((option) => {
                return [option.dataset.sku, option.dataset.imei1, option.dataset.imei2].some((value) => {
                    return value && normalizeCode(value) === normalized;
                });
            });
        };

        const selectScannedProduct = () => {
            const option = findProductOption(scanInput.value);

            if (!option) {
                status.textContent = 'No in-stock product found for this barcode or IMEI.';
                status.className = 'mt-1 block text-xs text-red-700';
                scanInput.select();
                return;
            }

            productSelect.value = option.value;

            if (priceInput && option.dataset.salePrice && !priceInput.value) {
                priceInput.value = option.dataset.salePrice;
            }

            status.textContent = 'Product selected: ' + option.textContent.trim();
            status.className = 'mt-1 block text-xs text-emerald-700';
            quantityInput?.focus();
        };

        scanInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                selectScannedProduct();
            }
        });
    });
</script>
