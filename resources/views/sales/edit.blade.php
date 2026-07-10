<x-layouts.app heading="Edit Sale" eyebrow="Sales">
    <form method="POST" action="{{ route('sales.update', $sale) }}">
        @method('PUT')
        @include('sales._form', ['buttonLabel' => 'Update Sale'])
    </form>

    <section class="mt-6 rounded-md border border-red-200 bg-white p-5">
        <h2 class="font-semibold text-red-800">Delete Sale</h2>
        <p class="mt-2 text-sm text-zinc-600">Deleting this sale will restore the linked product stock by {{ $sale->quantity }}.</p>
        <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="mt-4" onsubmit="return confirm('Delete this sale? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Delete Sale</button>
        </form>
    </section>
</x-layouts.app>
