<x-layouts.app heading="Add User" eyebrow="Admin">
    <form method="POST" action="{{ route('admin.users.store') }}" class="rounded-md border border-zinc-200 bg-white p-5">
        @include('admin.users._form', ['buttonLabel' => 'Create User'])
    </form>
</x-layouts.app>
