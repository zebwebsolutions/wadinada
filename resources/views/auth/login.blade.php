<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - WadiNada</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-100 text-zinc-950 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-md border border-zinc-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <p class="text-sm font-medium text-zinc-500">Staff dashboard</p>
                <h1 class="mt-1 text-2xl font-semibold">Sign in to WadiNada</h1>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <label class="block">
                    <span class="text-sm font-semibold">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Password</span>
                    <input type="password" name="password" required autocomplete="current-password" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
                </label>

                <label class="flex items-center gap-2 text-sm text-zinc-700">
                    <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300">
                    Remember me
                </label>

                <button class="w-full rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">
                    Sign in
                </button>
            </form>
        </section>
    </main>
</body>
</html>
