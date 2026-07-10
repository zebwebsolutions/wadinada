<x-layouts.app heading="Edit User" eyebrow="Admin">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="rounded-md border border-zinc-200 bg-white p-5">
        @method('PUT')
        @include('admin.users._form', ['buttonLabel' => 'Update User'])
    </form>
</x-layouts.app>
