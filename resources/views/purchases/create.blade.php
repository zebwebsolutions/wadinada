<x-layouts.app heading="Record Customer Purchase" eyebrow="Purchase intake">
    <form method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data">
        @include('purchases._form', ['buttonLabel' => 'Record Purchase'])
    </form>
</x-layouts.app>
