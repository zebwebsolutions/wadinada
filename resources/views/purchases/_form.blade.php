@csrf

@php($isEditing = $purchase->exists)

<div class="space-y-6">
    <section class="rounded-md border border-zinc-200 bg-white p-5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="font-semibold">Product Information</h2>
            @unless ($isEditing)
                <button type="button" data-add-device class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Add Product</button>
            @endunless
        </div>

        @if ($isEditing)
            <div class="grid gap-5 lg:grid-cols-2">
                @include('purchases._device-fields', ['prefix' => null, 'index' => null, 'product' => $product])
            </div>
        @else
            <div data-devices class="space-y-5">
                <div data-device class="rounded-md border border-zinc-200 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold">Product 1</h3>
                        <button type="button" data-remove-device class="hidden text-sm font-semibold text-red-700">Remove</button>
                    </div>
                    <div class="grid gap-5 lg:grid-cols-2">
                        @include('purchases._device-fields', ['prefix' => 'products[0]', 'index' => 0, 'product' => new App\Models\Product(['condition' => 'Used'])])
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold">Purchase date</span>
                <input type="date" name="purchased_at" value="{{ old('purchased_at', optional($purchase->purchased_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                @error('purchased_at') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
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

@unless ($isEditing)
    <template data-device-template>
        <div data-device class="rounded-md border border-zinc-200 p-4">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold">Product __NUMBER__</h3>
                <button type="button" data-remove-device class="text-sm font-semibold text-red-700">Remove product</button>
            </div>
            <div class="grid gap-5 lg:grid-cols-2">
                @include('purchases._device-fields', ['prefix' => 'products[__INDEX__]', 'index' => '__INDEX__', 'product' => new App\Models\Product(['condition' => 'Used'])])
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.querySelector('[data-devices]');
            const template = document.querySelector('[data-device-template]');
            const addButton = document.querySelector('[data-add-device]');
            let nextIndex = 1;

            const refreshButtons = () => {
                const devices = list.querySelectorAll('[data-device]');
                devices.forEach((device, index) => {
                    device.querySelector('h3').textContent = 'Product ' + (index + 1);
                    const remove = device.querySelector('[data-remove-device]');
                    remove.classList.toggle('hidden', devices.length === 1);
                });
            };

            const refreshUnitButtons = (device) => {
                const units = device.querySelectorAll('[data-unit-row]');
                units.forEach((unit) => {
                    unit.querySelector('[data-remove-unit]').classList.toggle('hidden', units.length === 1);
                });
            };

            addButton.addEventListener('click', () => {
                const html = template.innerHTML
                    .replaceAll('__INDEX__', String(nextIndex))
                    .replaceAll('__NUMBER__', String(nextIndex + 1));
                list.insertAdjacentHTML('beforeend', html);
                nextIndex += 1;
                refreshButtons();
            });

            list.addEventListener('click', (event) => {
                if (event.target.matches('[data-remove-device]')) {
                    event.target.closest('[data-device]').remove();
                    refreshButtons();
                }

                if (event.target.matches('[data-add-unit]')) {
                    const device = event.target.closest('[data-device]');
                    const units = device.querySelector('[data-units]');
                    const first = units.querySelector('[data-unit-row]');
                    const unitIndex = units.querySelectorAll('[data-unit-row]').length;
                    const clone = first.cloneNode(true);

                    clone.querySelectorAll('input').forEach((input) => {
                        input.value = '';
                        input.name = input.name.replace(/\[units]\[\d+]/, '[units][' + unitIndex + ']');
                    });

                    units.appendChild(clone);
                    refreshUnitButtons(device);
                }

                if (event.target.matches('[data-remove-unit]')) {
                    const device = event.target.closest('[data-device]');
                    event.target.closest('[data-unit-row]').remove();
                    refreshUnitButtons(device);
                }
            });
        });
    </script>
@endunless
