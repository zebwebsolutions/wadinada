@csrf

<div class="grid gap-6 xl:grid-cols-3">
    <section class="rounded-md border border-zinc-200 bg-white p-5 xl:col-span-2">
        <h2 class="mb-4 font-semibold">Cart</h2>

        <div class="grid gap-4 lg:grid-cols-5">
            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Scan serial / IMEI / barcode</span>
                <input type="text" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="done" data-sale-scan class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                <span data-sale-status class="mt-1 block text-xs text-zinc-500">Scan or type a product SKU/serial or unit IMEI.</span>
            </label>

            <label class="block lg:col-span-2">
                <span class="text-sm font-semibold">Unit</span>
                <select data-product-picker class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Select unit</option>
                    @foreach ($units as $unit)
                        @php($product = $unit->product)
                        <option value="{{ $unit->id }}" data-name="{{ $product->name }}" data-brand="{{ $product->brand }}" data-sku="{{ $product->sku }}" data-imei="{{ $unit->imei }}" data-sale-price="{{ $product->sale_price ?: 0 }}">
                            {{ $product->name }} {{ $product->brand ? '- '.$product->brand : '' }} {{ $unit->imei ? '- IMEI '.$unit->imei : '- Unit #'.$unit->id }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button type="button" data-add-to-cart class="mt-6 rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Add Unit</button>
        </div>

        <div class="mt-5 overflow-x-auto rounded-md border border-zinc-200">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-4 py-3">Unit</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-3">Sale price KD</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody data-cart-rows class="divide-y divide-zinc-100">
                    <tr data-empty-cart><td colspan="5" class="px-4 py-8 text-center text-zinc-500">No units added yet.</td></tr>
                </tbody>
                <tfoot class="bg-zinc-50 font-semibold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Cart total</td>
                        <td class="px-4 py-3" data-cart-total>0.000 KD</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @error('items') <span class="mt-2 block text-sm text-red-700">{{ $message }}</span> @enderror
    </section>

    <section class="rounded-md border border-zinc-200 bg-white p-5">
        <h2 class="mb-4 font-semibold">Checkout</h2>
        <div class="space-y-4">
            <label class="block">
                <span class="text-sm font-semibold">Order date</span>
                <input type="date" name="ordered_at" value="{{ old('ordered_at', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('ordered_at') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Salesman name</span>
                <input name="salesman_name" value="{{ old('salesman_name') }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Client name</span>
                <input name="customer_name" value="{{ old('customer_name') }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Client phone</span>
                <input name="customer_phone" value="{{ old('customer_phone') }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Client ID</span>
                <input name="customer_id_number" value="{{ old('customer_id_number') }}" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Kuwait ID image</span>
                <input type="file" name="kuwait_id" accept="image/*" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm shadow-sm focus:border-zinc-950 focus:outline-none">
                <span class="mt-1 block text-xs text-zinc-500">Optional. Image files up to 10 MB.</span>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Payment method</span>
                <select name="payment_method" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Not specified</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Notes</span>
                <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ old('notes') }}</textarea>
            </label>
        </div>
    </section>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Checkout & Print</button>
    <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scanInput = document.querySelector('[data-sale-scan]');
        const status = document.querySelector('[data-sale-status]');
        const picker = document.querySelector('[data-product-picker]');
        const addButton = document.querySelector('[data-add-to-cart]');
        const rows = document.querySelector('[data-cart-rows]');
        const emptyRow = document.querySelector('[data-empty-cart]');
        const totalCell = document.querySelector('[data-cart-total]');
        let rowIndex = 0;

        const normalize = (value) => value.trim().replace(/\s+/g, '').toLowerCase();
        const money = (value) => Number(value || 0).toFixed(3) + ' KD';

        const matchingOption = (code) => {
            const normalized = normalize(code);
            return Array.from(picker.options).find((option) => {
                return [option.dataset.sku, option.dataset.imei].some((value) => value && normalize(value) === normalized);
            });
        };

        const refreshTotal = () => {
            let total = 0;
            rows.querySelectorAll('[data-cart-row]').forEach((row) => {
                const price = Number(row.querySelector('[data-cart-price]').value || 0);
                row.querySelector('[data-row-total]').textContent = money(price);
                total += price;
            });
            totalCell.textContent = money(total);
            emptyRow.hidden = rows.querySelectorAll('[data-cart-row]').length > 0;
        };

        const addSelected = () => {
            const option = picker.selectedOptions[0];
            if (!option || !option.value) {
                status.textContent = 'Select or scan an available unit first.';
                status.className = 'mt-1 block text-xs text-red-700';
                return;
            }

            if (rows.querySelector(`[data-unit-id="${option.value}"]`)) {
                status.textContent = 'This unit is already in the cart.';
                status.className = 'mt-1 block text-xs text-red-700';
                return;
            }

            const price = Number(option.dataset.salePrice || 0).toFixed(3);
            const tr = document.createElement('tr');
            tr.dataset.cartRow = 'true';
            tr.dataset.unitId = option.value;
            tr.innerHTML = `
                <td class="px-4 py-3">
                    <input type="hidden" name="items[${rowIndex}][product_unit_id]" value="${option.value}">
                    <div class="font-semibold">${option.dataset.name}</div>
                    <div class="text-xs text-zinc-500">${option.dataset.brand || ''} ${option.dataset.sku ? 'SKU ' + option.dataset.sku : ''} ${option.dataset.imei ? 'IMEI ' + option.dataset.imei : 'Unit #' + option.value}</div>
                </td>
                <td class="px-4 py-3">1</td>
                <td class="px-4 py-3"><input data-cart-price type="number" min="0" step="0.001" name="items[${rowIndex}][unit_price]" value="${price}" class="w-28 rounded-md border border-zinc-300 px-2 py-1"></td>
                <td class="px-4 py-3" data-row-total>${money(price)}</td>
                <td class="px-4 py-3 text-right"><button type="button" data-remove-row class="font-semibold text-red-700">Remove</button></td>
            `;
            rows.appendChild(tr);
            rowIndex += 1;
            scanInput.value = '';
            status.textContent = 'Added to cart: ' + option.textContent.trim();
            status.className = 'mt-1 block text-xs text-emerald-700';
            refreshTotal();
            scanInput.focus();
        };

        scanInput.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') {
                return;
            }

            event.preventDefault();
            const option = matchingOption(scanInput.value);
            if (!option) {
                status.textContent = 'No available unit found for this serial, IMEI, or barcode.';
                status.className = 'mt-1 block text-xs text-red-700';
                scanInput.select();
                return;
            }

            picker.value = option.value;
            addSelected();
        });

        addButton.addEventListener('click', addSelected);
        rows.addEventListener('input', (event) => {
            if (event.target.matches('[data-cart-price]')) {
                refreshTotal();
            }
        });
        rows.addEventListener('click', (event) => {
            if (event.target.matches('[data-remove-row]')) {
                event.target.closest('[data-cart-row]').remove();
                refreshTotal();
            }
        });
    });
</script>
