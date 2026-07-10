<x-layouts.app heading="Add Product" eyebrow="Inventory">
    <form method="POST" action="{{ route('products.store') }}" class="rounded-md border border-zinc-200 bg-white p-5">
        @include('products._form', ['buttonLabel' => 'Save Product'])
    </form>
</x-layouts.app>
