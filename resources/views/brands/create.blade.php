<x-layouts.app heading="Add Brand" eyebrow="Inventory">
    <form method="POST" action="{{ route('brands.store') }}" class="rounded-md border border-zinc-200 bg-white p-5">
        @include('brands._form', ['buttonLabel' => 'Save Brand'])
    </form>
</x-layouts.app>
