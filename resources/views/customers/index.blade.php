<x-layouts.app heading="Customers" eyebrow="Customer lookup">
    <section class="rounded-md border border-zinc-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <form class="flex w-full gap-2 sm:max-w-md">
                <input name="search" value="{{ request('search') }}" placeholder="Search name, phone, email" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-950 focus:outline-none">
                <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Search</button>
            </form>
            <a href="{{ route('purchases.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Record Purchase</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase text-zinc-500">
                    <tr>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Purchases</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="px-5 py-4 font-semibold">
                                <a href="{{ route('customers.show', $customer) }}" class="hover:underline">{{ $customer->name }}</a>
                            </td>
                            <td class="px-5 py-4">{{ $customer->phone }}</td>
                            <td class="px-5 py-4">{{ $customer->email ?: '-' }}</td>
                            <td class="px-5 py-4">{{ $customer->purchases_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('customers.show', $customer) }}" class="font-semibold text-zinc-700 hover:text-zinc-950">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-zinc-500">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-5 py-4">{{ $customers->links() }}</div>
    </section>
</x-layouts.app>
