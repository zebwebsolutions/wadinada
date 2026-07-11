<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Wadi Nada Phone Dashboard' }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-100 text-zinc-950 antialiased">
    <div class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-zinc-200 bg-white lg:block">
            <div class="flex h-16 items-center border-b border-zinc-200 px-6">
                <a href="{{ route('dashboard') }}" class="text-xl font-semibold">Wadi Nada Phone</a>
            </div>
            <nav class="space-y-1 px-3 py-5 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Dashboard</a>
                <a href="{{ route('products.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('products.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Products</a>
                <a href="{{ route('orders.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('orders.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Orders</a>
                <a href="{{ route('customers.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('customers.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Customers</a>
                <a href="{{ route('purchases.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('purchases.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Customer Purchases</a>
                <a href="{{ route('sales.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('sales.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Sales</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}">Users</a>
                @endif
            </nav>
        </aside>

        <div class="lg:pl-64">
            <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white/95 backdrop-blur">
                <div class="flex min-h-16 flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                    <div>
                        <p class="text-sm font-medium text-zinc-500">{{ $eyebrow ?? 'Management' }}</p>
                        <h1 class="text-2xl font-semibold tracking-normal">{{ $heading ?? 'Dashboard' }}</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="mr-2 text-right text-xs text-zinc-500">
                            <div class="font-semibold text-zinc-800">{{ auth()->user()->name }}</div>
                            <div>{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <a href="{{ route('products.create') }}" class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Add Product</a>
                        <a href="{{ route('purchases.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Record Purchase</a>
                        <a href="{{ route('sales.create') }}" class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Sell Item</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-zinc-50">Logout</button>
                        </form>
                    </div>
                </div>
                <div class="flex gap-2 overflow-x-auto border-t border-zinc-100 px-4 py-2 text-sm lg:hidden">
                    <a href="{{ route('dashboard') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Dashboard</a>
                    <a href="{{ route('products.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('products.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Products</a>
                    <a href="{{ route('orders.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('orders.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Orders</a>
                    <a href="{{ route('customers.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('customers.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Customers</a>
                    <a href="{{ route('purchases.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('purchases.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Purchases</a>
                    <a href="{{ route('sales.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('sales.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Sales</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="whitespace-nowrap rounded-md px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700' }}">Users</a>
                    @endif
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold">Please fix the highlighted fields.</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' || !event.target.matches('[data-barcode-field]')) {
                return;
            }

            event.preventDefault();

            const fields = Array.from(event.target.form?.querySelectorAll('input, select, textarea, button') ?? []);
            const nextField = fields[fields.indexOf(event.target) + 1];
            nextField?.focus();
        });
    </script>
</body>
</html>
