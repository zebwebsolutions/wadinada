<x-layouts.app heading="Edit Brand" eyebrow="Inventory">
    <form method="POST" action="{{ route('brands.update', $brand) }}" class="rounded-md border border-zinc-200 bg-white p-5">
        @method('PUT')
        @include('brands._form', ['buttonLabel' => 'Update Brand'])
    </form>
</x-layouts.app>
