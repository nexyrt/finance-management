<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'manage users']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo('manage users');
        Role::firstOrCreate(['name' => 'staff']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_update_a_user(): void
    {
        $target = User::factory()->create(['status' => 'inactive']);
        $target->assignRole('staff');

        $this->actingAs($this->admin)
            ->put("/admin/users/{$target->id}", [
                'name' => 'Nama Baru',
                'email' => $target->email,
                'status' => 'active',
                'role' => 'staff',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nama Baru',
            'status' => 'active',
        ]);
    }

    public function test_update_is_forbidden_without_manage_users(): void
    {
        $noPerm = User::factory()->create();
        $target = User::factory()->create();
        $target->assignRole('staff');

        $this->actingAs($noPerm)
            ->put("/admin/users/{$target->id}", [
                'name' => 'Hack',
                'email' => $target->email,
                'status' => 'active',
                'role' => 'staff',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['name' => 'Hack']);
    }
}
