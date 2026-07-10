<x-layouts.app heading="Products" eyebrow="Inventory">
    <section class="rounded-md border border-zinc-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <form class="flex w-full gap-2 sm:max-w-md">
                <input name="search" value="{{ request('search') }}" placeholder="Search product, brand, model, SKU" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-950 focus:outline-none">
                <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Search</button>
            </form>
            <a href="{{ route('products.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Add Product</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Condition</th>
                        <th class="px-5 py-3">Stock</th>
                        <th class="px-5 py-3">Cost</th>
                        <th class="px-5 py-3">Sale</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-5 py-4">
                                <a href="{{ route('products.show', $product) }}" class="font-semibold hover:underline">{{ $product->name }}</a>
                                <div class="text-xs text-zinc-500">{{ trim(($product->brand ? $product->brand.' ' : '').($product->model ?? '')) ?: 'No brand/model' }}</div>
                            </td>
                            <td class="px-5 py-4">{{ $product->category }}</td>
                            <td class="px-5 py-4">{{ $product->condition }}</td>
                            <td class="px-5 py-4">{{ $product->stock_quantity }}</td>
                            <td class="px-5 py-4">{{ number_format($product->purchase_price, 3) }} KD</td>
                            <td class="px-5 py-4">{{ $product->sale_price ? number_format($product->sale_price, 3).' KD' : '-' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('products.edit', $product) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product? Products with purchase or sales history cannot be deleted.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-700 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-zinc-500">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-5 py-4">{{ $products->links() }}</div>
    </section>
</x-layouts.app>
