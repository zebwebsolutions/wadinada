<x-layouts.app heading="Edit Purchase" eyebrow="Purchase intake">
    <form method="POST" action="{{ route('purchases.update', $purchase) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('purchases._form', ['buttonLabel' => 'Update Purchase'])
    </form>

    <section class="mt-6 rounded-md border border-red-200 bg-white p-5">
        <h2 class="font-semibold text-red-800">Delete Purchase</h2>
        <p class="mt-2 text-sm text-zinc-600">Deleting this purchase will reduce the linked product stock by {{ $purchase->quantity }}.</p>
        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="mt-4" onsubmit="return confirm('Delete this customer purchase? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Delete Purchase</button>
        </form>
    </section>
</x-layouts.app>
