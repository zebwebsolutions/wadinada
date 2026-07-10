<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_deactivate_a_user(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Sales User',
                'email' => 'sales-user@example.com',
                'role' => 'sales',
                'is_active' => '1',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'sales-user@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Sales Manager',
                'email' => 'sales-manager@example.com',
                'role' => 'manager',
                'is_active' => '1',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Sales Manager',
            'email' => 'sales-manager@example.com',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $user = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'staff',
        ]);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'name' => 'Inactive Staff',
            'email' => 'inactive@example.com',
            'password' => 'password',
            'role' => 'staff',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_last_active_admin_cannot_be_deactivated(): void
    {
        $admin = User::create([
            'name' => 'Only Admin',
            'email' => 'only-admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => 'Only Admin',
                'email' => 'only-admin@example.com',
                'role' => 'admin',
                'is_active' => '0',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertSessionHasErrors('is_active');

        $this->assertTrue($admin->fresh()->is_active);
    }
}
