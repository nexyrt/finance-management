<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view services', 'create services', 'edit services', 'delete services'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions(['view services']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('viewer');
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/services')->assertRedirect('/login');
    }

    public function test_index_requires_view_services_permission(): void
    {
        $noPermUser = User::factory()->create();
        $this->actingAs($noPermUser)->get('/services')->assertForbidden();
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $this->actingAs($this->admin)->get('/services')->assertOk();
    }

    public function test_store_creates_service(): void
    {
        $this->actingAs($this->admin)->post('/services', [
            'name' => 'Jasa Konsultasi Pajak',
            'type' => 'Administrasi Perpajakan',
            'price' => 2500000,
        ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Jasa Konsultasi Pajak',
            'type' => 'Administrasi Perpajakan',
            'price' => 2500000,
        ]);
    }

    public function test_store_requires_create_services_permission(): void
    {
        $this->actingAs($this->viewer)->post('/services', [
            'name' => 'Unauthorized Service',
            'type' => 'Perizinan',
            'price' => 100000,
        ])->assertForbidden();

        $this->assertDatabaseMissing('services', ['name' => 'Unauthorized Service']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/services', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type', 'price']);
    }

    public function test_update_modifies_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Nama Lama',
            'type' => 'Perizinan',
            'price' => 1000000,
        ]);

        $this->actingAs($this->admin)->put("/services/{$service->id}", [
            'name' => 'Nama Baru',
            'type' => 'Perizinan',
            'price' => 1500000,
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Nama Baru',
            'price' => 1500000,
        ]);
    }

    public function test_update_requires_edit_services_permission(): void
    {
        $service = Service::factory()->create(['name' => 'Original Name']);

        $this->actingAs($this->viewer)->put("/services/{$service->id}", [
            'name' => 'Changed Name',
            'type' => 'Perizinan',
            'price' => 100000,
        ])->assertForbidden();

        $this->assertDatabaseHas('services', ['id' => $service->id, 'name' => 'Original Name']);
    }

    public function test_destroy_deletes_service(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->admin)->delete("/services/{$service->id}");

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_destroy_requires_delete_services_permission(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->viewer)->delete("/services/{$service->id}")->assertForbidden();
        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }
}
