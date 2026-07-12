@csrf

<label class="block">
    <span class="text-sm font-semibold">Brand name</span>
    <input name="name" value="{{ old('name', $brand->name) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    @error('name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
</label>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">{{ $buttonLabel }}</button>
    <a href="{{ route('brands.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>
