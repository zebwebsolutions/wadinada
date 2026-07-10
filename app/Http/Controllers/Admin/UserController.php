<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(['role' => 'staff', 'is_active' => true]),
            'roles' => $this->roles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->validatedUser($request));

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedUser($request, $user);

        if ($request->user()->is($user) && ! (bool) $data['is_active']) {
            return back()
                ->withErrors(['is_active' => 'You cannot deactivate your own account.'])
                ->withInput();
        }

        if ($this->wouldRemoveLastActiveAdmin($user, $data)) {
            return back()
                ->withErrors(['role' => 'At least one active admin must remain.'])
                ->withInput();
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        if ($this->wouldRemoveLastActiveAdmin($user, ['role' => $user->role, 'is_active' => false])) {
            return back()->withErrors(['user' => 'At least one active admin must remain.']);
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('status', 'User deactivated successfully.');
    }

    private function validatedUser(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'is_active' => ['required', 'boolean'],
        ];

        $rules['password'] = $user
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        $data = $request->validate($rules);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }

    private function wouldRemoveLastActiveAdmin(User $user, array $data): bool
    {
        if (! $user->isAdmin() || ! $user->isActive()) {
            return false;
        }

        $willStillBeActiveAdmin = ($data['role'] ?? $user->role) === 'admin'
            && (bool) ($data['is_active'] ?? $user->is_active);

        if ($willStillBeActiveAdmin) {
            return false;
        }

        return User::query()
            ->whereKeyNot($user->id)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->doesntExist();
    }

    private function roles(): array
    {
        return [
            'admin' => 'Admin',
            'manager' => 'Manager',
            'sales' => 'Sales',
            'staff' => 'Staff',
        ];
    }
}
