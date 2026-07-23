<x-layouts.app heading="Batch inventory intake" eyebrow="Fast purchase entry">
    <form method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data">
        @include('purchases._batch-form')
    </form>
</x-layouts.app>
