<x-layouts.app heading="Brands" eyebrow="Inventory">
    <section class="rounded-md border border-zinc-200 bg-white">
        <div class="flex items-center justify-between border-b border-zinc-200 p-5">
            <h2 class="font-semibold">Product brands</h2>
            <a href="{{ route('brands.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Add Brand</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Brand</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($brands as $brand)
                        <tr>
                            <td class="px-5 py-4 font-semibold">{{ $brand->name }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('brands.edit', $brand) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">Edit</a>
                                    <form method="POST" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand? Existing products keep their brand text.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-700 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-5 py-10 text-center text-zinc-500">No brands yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-5 py-4">{{ $brands->links() }}</div>
    </section>
</x-layouts.app>
