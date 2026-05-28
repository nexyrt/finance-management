<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view feedbacks', 'create feedbacks', 'edit feedbacks',
            'delete feedbacks', 'respond feedbacks', 'manage feedbacks',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'finance manager']);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->syncPermissions(['view feedbacks', 'create feedbacks', 'edit feedbacks', 'delete feedbacks']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('staff');
    }

    private function makeFeedback(User $user, array $attributes = []): Feedback
    {
        return Feedback::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Tombol tidak berfungsi',
            'description' => 'Tombol simpan di halaman invoice tidak merespon klik.',
            'type' => 'bug',
            'priority' => 'high',
            'status' => 'open',
        ], $attributes));
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/feedbacks')->assertRedirect('/login');
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $this->actingAs($this->admin)->get('/feedbacks')->assertOk();
    }

    public function test_store_creates_feedback(): void
    {
        $this->actingAs($this->staff)->post('/feedbacks', [
            'title' => 'Fitur Export Excel',
            'description' => 'Mohon tambahkan fitur export ke Excel pada halaman laporan.',
            'type' => 'feature',
            'priority' => 'medium',
        ]);

        $this->assertDatabaseHas('feedbacks', [
            'user_id' => $this->staff->id,
            'title' => 'Fitur Export Excel',
            'type' => 'feature',
            'status' => 'open',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->staff)
            ->postJson('/feedbacks', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'type', 'priority']);
    }

    public function test_update_modifies_own_feedback(): void
    {
        $feedback = $this->makeFeedback($this->staff);

        $this->actingAs($this->staff)->put("/feedbacks/{$feedback->id}", [
            'title' => 'Judul Diperbarui',
            'description' => 'Deskripsi baru yang lebih lengkap.',
            'type' => 'bug',
            'priority' => 'critical',
        ]);

        $this->assertDatabaseHas('feedbacks', [
            'id' => $feedback->id,
            'title' => 'Judul Diperbarui',
            'priority' => 'critical',
        ]);
    }

    public function test_destroy_deletes_own_feedback(): void
    {
        $feedback = $this->makeFeedback($this->staff);

        $this->actingAs($this->staff)->delete("/feedbacks/{$feedback->id}");

        $this->assertDatabaseMissing('feedbacks', ['id' => $feedback->id]);
    }

    public function test_staff_cannot_delete_others_feedback(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('staff');

        $feedback = $this->makeFeedback($otherUser);

        $this->actingAs($this->staff)->delete("/feedbacks/{$feedback->id}")->assertForbidden();
        $this->assertDatabaseHas('feedbacks', ['id' => $feedback->id]);
    }

    public function test_respond_adds_admin_response_and_updates_status(): void
    {
        $feedback = $this->makeFeedback($this->staff);

        $this->actingAs($this->admin)->post("/feedbacks/{$feedback->id}/respond", [
            'response' => 'Sudah diperbaiki pada versi 2.1.',
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('feedbacks', [
            'id' => $feedback->id,
            'admin_response' => 'Sudah diperbaiki pada versi 2.1.',
            'status' => 'resolved',
        ]);
    }

    public function test_respond_requires_respond_feedbacks_permission(): void
    {
        $feedback = $this->makeFeedback($this->staff);

        $this->actingAs($this->staff)->post("/feedbacks/{$feedback->id}/respond", [
            'response' => 'Unauthorized response.',
            'status' => 'resolved',
        ])->assertForbidden();

        $this->assertDatabaseHas('feedbacks', [
            'id' => $feedback->id,
            'admin_response' => null,
        ]);
    }

    public function test_change_status_updates_feedback_status(): void
    {
        $feedback = $this->makeFeedback($this->staff);

        $this->actingAs($this->admin)->post("/feedbacks/{$feedback->id}/status", [
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('feedbacks', [
            'id' => $feedback->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_change_status_requires_manage_feedbacks_permission(): void
    {
        $feedback = $this->makeFeedback($this->staff);

        $this->actingAs($this->staff)->post("/feedbacks/{$feedback->id}/status", [
            'status' => 'closed',
        ])->assertForbidden();

        $this->assertDatabaseHas('feedbacks', ['id' => $feedback->id, 'status' => 'open']);
    }
}
