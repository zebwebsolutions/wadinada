<x-layouts.app heading="Sell Item" eyebrow="Sales">
    @if ($units->isEmpty())
        <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            There are no products in stock to sell yet. Record a customer purchase or add stock first.
        </div>
    @else
        <form method="POST" action="{{ route('sales.store') }}" enctype="multipart/form-data">
            @include('sales._form', ['buttonLabel' => 'Record Sale'])
        </form>
    @endif
</x-layouts.app>
