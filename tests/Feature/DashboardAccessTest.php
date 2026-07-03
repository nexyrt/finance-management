<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['view dashboard', 'view expense'] as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    public function test_user_with_permission_sees_dashboard(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view dashboard');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_user_without_dashboard_is_redirected_to_expenses(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view expense');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('cash-flow.expenses'));
    }
}
