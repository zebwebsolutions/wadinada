@csrf

@php
    $intakeItems = old('items', [[
        'quantity' => 1,
        'condition' => 'New',
        'category' => 'Phones',
        'tracking_method' => 'imei',
        'units' => [],
    ]]);
@endphp

<datalist id="intake-brand-options">
    @foreach ($brands as $brand)
        <option value="{{ $brand->name }}"></option>
    @endforeach
</datalist>

@if ($errors->any())
    <section class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900">
        <p class="font-semibold">The batch was not saved. Review these items:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </section>
@endif

<section class="mb-6 overflow-hidden rounded-xl bg-zinc-950 text-white shadow-sm">
    <div class="grid gap-px bg-white/10 sm:grid-cols-3">
        <div class="bg-zinc-950 p-5">
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-400">Step 1</span>
            <h2 class="mt-2 font-semibold">Enter the purchase once</h2>
            <p class="mt-1 text-sm text-zinc-400">Seller, date and payment apply to the complete batch.</p>
        </div>
        <div class="bg-zinc-950 p-5">
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-400">Step 2</span>
            <h2 class="mt-2 font-semibold">Group matching variants</h2>
            <p class="mt-1 text-sm text-zinc-400">Keep each storage, color, condition or cost group together.</p>
        </div>
        <div class="bg-zinc-950 p-5">
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-400">Step 3</span>
            <h2 class="mt-2 font-semibold">Scan or paste identifiers</h2>
            <p class="mt-1 text-sm text-zinc-400">IMEIs and serials are checked before anything is saved.</p>
        </div>
    </div>
</section>

<div class="grid gap-6 xl:grid-cols-2">
    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <div class="mb-5 flex items-center gap-3">
            <span class="flex size-9 items-center justify-center rounded-lg bg-zinc-100 font-bold">01</span>
            <div>
                <h2 class="font-semibold">Purchase details</h2>
                <p class="text-xs text-zinc-500">Information shared by every inventory group below.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold">Purchase date</span>
                <input type="date" name="purchased_at" value="{{ old('purchased_at', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('purchased_at') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Payment method</span>
                <select name="payment_method" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
                    <option value="">Not specified</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block sm:col-span-2">
                <span class="text-sm font-semibold">Batch notes <span class="font-normal text-zinc-500">(optional)</span></span>
                <textarea name="batch_notes" rows="3" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">{{ old('batch_notes') }}</textarea>
            </label>
        </div>
    </section>

    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <div class="mb-5 flex items-center gap-3">
            <span class="flex size-9 items-center justify-center rounded-lg bg-zinc-100 font-bold">02</span>
            <div>
                <h2 class="font-semibold">Seller details</h2>
                <p class="text-xs text-zinc-500">Existing sellers are matched by phone number.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold">Seller name</span>
                <input name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Phone</span>
                <input name="customer_phone" value="{{ old('customer_phone') }}" required autocomplete="tel" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('customer_phone') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Email <span class="font-normal text-zinc-500">(optional)</span></span>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2.5 shadow-sm focus:border-zinc-950 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Kuwait ID image <span class="font-normal text-zinc-500">(optional)</span></span>
                <input type="file" name="customer_kuwait_id" accept="image/*" class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-1 file:font-semibold">
                @error('customer_kuwait_id') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>
    </section>
</div>

<section class="mt-6">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-zinc-500">Inventory groups</p>
            <h2 class="mt-1 text-xl font-semibold">What did you buy?</h2>
            <p class="mt-1 text-sm text-zinc-600">Create another group whenever storage, color, condition or default cost changes.</p>
        </div>
        <button type="button" data-add-line class="rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-zinc-50">
            + Add another variant
        </button>
    </div>

    <div data-intake-lines class="space-y-5">
        @foreach ($intakeItems as $index => $item)
            @include('purchases._batch-line', ['index' => $index, 'item' => $item])
        @endforeach
    </div>
</section>

<template data-line-template>
    @include('purchases._batch-line', ['index' => '__INDEX__', 'item' => []])
</template>

<div class="sticky bottom-3 z-20 mt-6 flex flex-col gap-3 rounded-xl border border-zinc-700 bg-zinc-950 p-4 text-white shadow-xl sm:flex-row sm:items-center sm:justify-between">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Estimated batch total</p>
        <p data-batch-total class="mt-1 text-2xl font-bold">0.000 KD</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('purchases.index') }}" class="px-3 py-2 text-sm font-semibold text-zinc-300 hover:text-white">Cancel</a>
        <button class="rounded-lg bg-white px-5 py-3 text-sm font-bold text-zinc-950 hover:bg-zinc-200">
            Save batch to inventory
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('[data-intake-lines]');
        const template = document.querySelector('[data-line-template]');
        const addLineButton = document.querySelector('[data-add-line]');
        const batchTotal = document.querySelector('[data-batch-total]');
        const form = container.closest('form');
        let nextLineIndex = Math.max(
            0,
            ...Array.from(container.querySelectorAll('[data-intake-line]'))
                .map((line) => Number(line.dataset.lineIndex) || 0),
        ) + 1;

        const money = (value) => Number(value || 0).toFixed(3) + ' KD';
        const normalize = (value, tracking = 'serial') => {
            const trimmed = String(value || '').trim();
            return tracking.startsWith('imei')
                ? trimmed.replace(/\D/g, '')
                : trimmed.replace(/[\s-]+/g, '').toUpperCase();
        };

        const trackingFor = (line) => {
            const productSelect = line.querySelector('[data-product-select]');
            const selected = productSelect.selectedOptions[0];
            return selected?.value
                ? selected.dataset.tracking
                : line.querySelector('[data-new-tracking]').value;
        };

        const allIdentifiers = (exceptInput = null) => {
            return Array.from(container.querySelectorAll('[data-unit-identifier], [data-secondary-identifier]'))
                .filter((input) => input !== exceptInput && input.value.trim())
                .map((input) => normalize(input.value, input.closest('[data-intake-line]') ? trackingFor(input.closest('[data-intake-line]')) : 'serial'));
        };

        const refreshNumbers = () => {
            const lines = container.querySelectorAll('[data-intake-line]');
            lines.forEach((line, index) => {
                line.querySelector('[data-line-number]').textContent = String(index + 1);
                line.querySelector('[data-remove-line]').classList.toggle('invisible', lines.length === 1);
            });
        };

        const refreshLine = (line) => {
            const productSelect = line.querySelector('[data-product-select]');
            const selected = productSelect.selectedOptions[0];
            const existing = Boolean(selected?.value);
            const tracking = trackingFor(line);
            const rows = line.querySelectorAll('[data-unit-row]');
            const quantityInput = line.querySelector('[data-quantity]');
            const quantity = Math.max(1, Number(quantityInput.value) || 1);
            const manualSection = line.querySelector('[data-manual-identifiers]');
            const internalSection = line.querySelector('[data-internal-identifiers]');
            const emptyRow = line.querySelector('[data-empty-units]');
            const progress = line.querySelector('[data-line-progress]');
            const defaultCost = Number(line.querySelector('[data-default-cost]').value) || 0;

            line.querySelector('[data-new-variant-fields]').classList.toggle('hidden', existing);
            manualSection.classList.toggle('hidden', tracking === 'internal');
            internalSection.classList.toggle('hidden', tracking !== 'internal');
            emptyRow.classList.toggle('hidden', rows.length > 0);

            if (existing) {
                line.querySelector('[data-line-heading]').textContent = selected.dataset.label || selected.textContent.trim();
            } else {
                line.querySelector('[data-line-heading]').textContent =
                    line.querySelector('[data-variant-name]').value.trim() || 'New inventory variant';
            }

            const labels = {
                imei: ['Scan IMEI', 'IMEI 1', 'IMEI 2'],
                serial: ['Scan serial number', 'Serial number', 'Alternate ID'],
                barcode: ['Scan manufacturer barcode', 'Barcode', 'Alternate ID'],
                internal: ['Generated code', 'Inventory code', 'Alternate ID'],
            }[tracking];

            line.querySelector('[data-scan-label]').textContent = labels[0];
            line.querySelector('[data-primary-heading]').textContent = labels[1];
            line.querySelector('[data-secondary-heading]').textContent = labels[2] + ' (optional)';
            line.querySelector('[data-scan-input]').inputMode = tracking === 'imei' ? 'numeric' : 'text';

            rows.forEach((row, index) => {
                row.querySelector('[data-unit-number]').textContent = String(index + 1);
                row.querySelector('[data-unit-cost]').placeholder = defaultCost.toFixed(3);
            });

            if (tracking === 'internal') {
                progress.textContent = quantity + ' code' + (quantity === 1 ? '' : 's') + ' will be generated';
                progress.className = 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800';
            } else {
                progress.textContent = rows.length + ' / ' + quantity + ' scanned';
                const complete = rows.length === quantity;
                progress.className = complete
                    ? 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800'
                    : 'rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800';
            }

            let lineTotal = 0;
            if (tracking === 'internal') {
                lineTotal = quantity * defaultCost;
            } else {
                rows.forEach((row) => {
                    lineTotal += Number(row.querySelector('[data-unit-cost]').value || defaultCost);
                });
            }
            line.querySelector('[data-line-total]').textContent = money(lineTotal);

            let total = 0;
            container.querySelectorAll('[data-line-total]').forEach((cell) => {
                total += Number(cell.textContent.replace(/[^\d.-]/g, '')) || 0;
            });
            batchTotal.textContent = money(total);
        };

        const createCell = (className = '') => {
            const cell = document.createElement('td');
            cell.className = className;
            return cell;
        };

        const createInput = (name, value, dataAttribute, className, type = 'text') => {
            const input = document.createElement('input');
            input.type = type;
            input.name = name;
            input.value = value || '';
            input.className = className;
            input.setAttribute(dataAttribute, '');
            if (type === 'number') {
                input.min = '0';
                input.step = '0.001';
            } else {
                input.autocomplete = 'off';
            }
            return input;
        };

        const addUnit = (line, identifier, secondary = '', cost = '') => {
            const tracking = trackingFor(line);
            const normalized = normalize(identifier, tracking);
            const status = line.querySelector('[data-scan-status]');

            if (!normalized) {
                status.textContent = 'Enter or scan an identifier first.';
                status.className = 'mt-2 text-xs font-semibold text-red-700';
                return false;
            }

            if (tracking === 'imei' && !/^\d{15}$/.test(normalized)) {
                status.textContent = 'An IMEI must contain exactly 15 digits.';
                status.className = 'mt-2 text-xs font-semibold text-red-700';
                return false;
            }

            if (allIdentifiers().includes(normalized)) {
                status.textContent = 'That identifier is already in this batch.';
                status.className = 'mt-2 text-xs font-semibold text-red-700';
                return false;
            }

            const lineIndex = line.dataset.lineIndex;
            const rowsContainer = line.querySelector('[data-unit-rows]');
            const unitIndex = Number(line.dataset.nextUnitIndex || rowsContainer.querySelectorAll('[data-unit-row]').length);
            line.dataset.nextUnitIndex = String(unitIndex + 1);
            const base = `items[${lineIndex}][units][${unitIndex}]`;
            const row = document.createElement('tr');
            row.setAttribute('data-unit-row', '');

            const numberCell = createCell('px-4 py-3 font-semibold text-zinc-500');
            numberCell.setAttribute('data-unit-number', '');
            row.appendChild(numberCell);

            const identifierCell = createCell('px-4 py-3');
            identifierCell.appendChild(createInput(
                `${base}[identifier]`,
                identifier,
                'data-unit-identifier',
                'w-full rounded-md border border-zinc-300 px-3 py-2 font-mono focus:border-zinc-950 focus:outline-none',
            ));
            row.appendChild(identifierCell);

            const secondaryCell = createCell('px-4 py-3');
            secondaryCell.appendChild(createInput(
                `${base}[secondary_identifier]`,
                secondary,
                'data-secondary-identifier',
                'w-full rounded-md border border-zinc-300 px-3 py-2 font-mono focus:border-zinc-950 focus:outline-none',
            ));
            row.appendChild(secondaryCell);

            const costCell = createCell('px-4 py-3');
            costCell.appendChild(createInput(
                `${base}[cost_price]`,
                cost,
                'data-unit-cost',
                'w-full rounded-md border border-zinc-300 px-3 py-2 focus:border-zinc-950 focus:outline-none',
                'number',
            ));
            row.appendChild(costCell);

            const removeCell = createCell('px-4 py-3 text-right');
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.textContent = 'Remove';
            removeButton.className = 'font-semibold text-red-700 hover:text-red-900';
            removeButton.setAttribute('data-remove-unit', '');
            removeCell.appendChild(removeButton);
            row.appendChild(removeCell);

            rowsContainer.insertBefore(row, line.querySelector('[data-empty-units]'));

            const count = rowsContainer.querySelectorAll('[data-unit-row]').length;
            const quantityInput = line.querySelector('[data-quantity]');
            if (count > Number(quantityInput.value || 0)) {
                quantityInput.value = String(count);
            }

            status.textContent = 'Added ' + identifier + '. Ready for the next scan.';
            status.className = 'mt-2 text-xs font-semibold text-emerald-700';
            refreshLine(line);
            return true;
        };

        const importList = (line) => {
            const textarea = line.querySelector('[data-bulk-input]');
            const records = textarea.value
                .split(/\r?\n/)
                .map((row) => row.trim())
                .filter(Boolean);
            let imported = 0;

            records.forEach((record) => {
                const columns = record.split(/\t|,/).map((column) => column.trim());
                let secondary = columns[1] || '';
                let cost = columns[2] || '';
                const looksLikeSecondImei = trackingFor(line) === 'imei' && /^\d{15}$/.test(columns[1] || '');
                if (columns.length === 2 && /^\d+(?:\.\d+)?$/.test(columns[1]) && !looksLikeSecondImei) {
                    cost = columns[1];
                    secondary = '';
                }
                if (addUnit(line, columns[0], secondary, cost)) {
                    imported += 1;
                }
            });

            textarea.value = '';
            line.querySelector('[data-paste-panel]').classList.add('hidden');
            const status = line.querySelector('[data-scan-status]');
            status.textContent = imported + ' identifier' + (imported === 1 ? '' : 's') + ' imported.';
            status.className = 'mt-2 text-xs font-semibold text-emerald-700';
            line.querySelector('[data-scan-input]').focus();
        };

        const initializeLine = (line) => {
            const existingRows = line.querySelectorAll('[data-unit-row]').length;
            line.dataset.nextUnitIndex = String(existingRows);
            refreshLine(line);
        };

        container.querySelectorAll('[data-intake-line]').forEach(initializeLine);
        refreshNumbers();

        addLineButton.addEventListener('click', () => {
            const fragment = template.content.cloneNode(true);
            const line = fragment.querySelector('[data-intake-line]');
            line.dataset.lineIndex = String(nextLineIndex);
            line.querySelectorAll('[name]').forEach((field) => {
                field.name = field.name.replaceAll('__INDEX__', String(nextLineIndex));
            });
            nextLineIndex += 1;
            container.appendChild(fragment);
            initializeLine(container.lastElementChild);
            refreshNumbers();
            container.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        container.addEventListener('click', (event) => {
            const line = event.target.closest('[data-intake-line]');
            if (!line) return;

            if (event.target.matches('[data-remove-line]')) {
                if (container.querySelectorAll('[data-intake-line]').length > 1) {
                    line.remove();
                    refreshNumbers();
                    container.querySelectorAll('[data-intake-line]').forEach(refreshLine);
                }
            }

            if (event.target.matches('[data-add-scan]')) {
                const input = line.querySelector('[data-scan-input]');
                if (addUnit(line, input.value)) {
                    input.value = '';
                    input.focus();
                }
            }

            if (event.target.matches('[data-toggle-paste]')) {
                const panel = line.querySelector('[data-paste-panel]');
                panel.classList.toggle('hidden');
                if (!panel.classList.contains('hidden')) {
                    panel.querySelector('[data-bulk-input]').focus();
                }
            }

            if (event.target.matches('[data-import-list]')) {
                importList(line);
            }

            if (event.target.matches('[data-remove-unit]')) {
                event.target.closest('[data-unit-row]').remove();
                refreshLine(line);
                line.querySelector('[data-scan-input]').focus();
            }
        });

        container.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' || !event.target.matches('[data-scan-input]')) return;
            event.preventDefault();
            const line = event.target.closest('[data-intake-line]');
            if (addUnit(line, event.target.value)) {
                event.target.value = '';
            }
        });

        container.addEventListener('paste', (event) => {
            if (!event.target.matches('[data-scan-input]')) return;
            const text = event.clipboardData.getData('text');
            if (!/[\n\t,]/.test(text)) return;
            event.preventDefault();
            const line = event.target.closest('[data-intake-line]');
            line.querySelector('[data-bulk-input]').value = text;
            importList(line);
        });

        container.addEventListener('input', (event) => {
            const line = event.target.closest('[data-intake-line]');
            if (!line) return;

            if (event.target.matches('[data-unit-identifier], [data-secondary-identifier]')) {
                const tracking = trackingFor(line);
                const normalized = normalize(event.target.value, tracking);
                const duplicate = normalized && allIdentifiers(event.target).includes(normalized);
                event.target.classList.toggle('border-red-500', duplicate);
                event.target.classList.toggle('bg-red-50', duplicate);
            }

            refreshLine(line);
        });

        container.addEventListener('change', (event) => {
            const line = event.target.closest('[data-intake-line]');
            if (line) refreshLine(line);
        });

        form.addEventListener('submit', (event) => {
            for (const line of container.querySelectorAll('[data-intake-line]')) {
                const tracking = trackingFor(line);
                if (tracking === 'internal') continue;
                const quantity = Number(line.querySelector('[data-quantity]').value);
                const count = line.querySelectorAll('[data-unit-row]').length;
                if (quantity !== count) {
                    event.preventDefault();
                    const status = line.querySelector('[data-scan-status]');
                    status.textContent = `Expected ${quantity} identifier(s), but ${count} have been scanned.`;
                    status.className = 'mt-2 text-xs font-semibold text-red-700';
                    line.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    line.querySelector('[data-scan-input]').focus();
                    return;
                }
            }
        });
    });
</script>
