<x-layouts.app heading="Edit Product" eyebrow="Inventory">
    <form method="POST" action="{{ route('products.update', $product) }}" class="rounded-md border border-zinc-200 bg-white p-5">
        @method('PUT')
        @include('products._form', ['buttonLabel' => 'Update Product'])
    </form>

    <section class="mt-6 rounded-md border border-red-200 bg-white p-5">
        <h2 class="font-semibold text-red-800">Delete Product</h2>
        <p class="mt-2 text-sm text-zinc-600">Deleting is only allowed when the product has no purchase or sales history.</p>
        <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-4" onsubmit="return confirm('Delete this product? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Delete Product</button>
        </form>
    </section>
</x-layouts.app>
