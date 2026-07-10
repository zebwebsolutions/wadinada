@csrf

<div class="grid gap-5 lg:grid-cols-2">
    <label class="block">
        <span class="text-sm font-semibold">Name</span>
        <input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Email</span>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @error('email') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Role</span>
        <select name="role" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Status</span>
        <select name="is_active" required class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
            <option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Inactive</option>
        </select>
        @error('is_active') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">{{ $user->exists ? 'New password' : 'Password' }}</span>
        <input type="password" name="password" @required(! $user->exists) autocomplete="new-password" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
        @if ($user->exists)
            <span class="mt-1 block text-xs text-zinc-500">Leave blank to keep the current password.</span>
        @endif
        @error('password') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold">Confirm password</span>
        <input type="password" name="password_confirmation" @required(! $user->exists) autocomplete="new-password" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 shadow-sm focus:border-zinc-950 focus:outline-none">
    </label>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">{{ $buttonLabel }}</button>
    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-zinc-600 hover:text-zinc-950">Cancel</a>
</div>
