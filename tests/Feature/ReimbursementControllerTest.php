<?php

namespace Tests\Feature;

use App\Models\Reimbursement;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReimbursementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view reimbursements', 'create reimbursements', 'edit reimbursements',
            'delete reimbursements', 'approve reimbursements', 'pay reimbursements',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->syncPermissions(['view reimbursements', 'create reimbursements']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('staff');
    }

    private function makeReimbursement(User $user, array $attributes = []): Reimbursement
    {
        return Reimbursement::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Transport ke Klien',
            'description' => 'Perjalanan dinas',
            'amount' => 250000,
            'expense_date' => '2026-03-10',
            'category_input' => 'transport',
            'status' => 'draft',
            'payment_status' => 'unpaid',
        ], $attributes));
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/reimbursements')->assertRedirect('/login');
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $this->actingAs($this->admin)->get('/reimbursements')->assertOk();
    }

    public function test_create_page_renders(): void
    {
        $this->actingAs($this->staff)->get('/reimbursements/create')->assertOk();
    }

    public function test_store_creates_draft_reimbursement(): void
    {
        $this->actingAs($this->staff)->post('/reimbursements', [
            'title' => 'Beli Alat Tulis',
            'description' => 'Keperluan kantor',
            'amount' => 150000,
            'expense_date' => '2026-03-12',
            'category' => 'office_supplies',
            'action' => 'draft',
        ]);

        $this->assertDatabaseHas('reimbursements', [
            'user_id' => $this->staff->id,
            'title' => 'Beli Alat Tulis',
            'status' => 'draft',
        ]);
    }

    public function test_store_with_submit_action_sets_pending_status(): void
    {
        $this->actingAs($this->staff)->post('/reimbursements', [
            'title' => 'Biaya Internet',
            'description' => 'Internet bulanan',
            'amount' => 300000,
            'expense_date' => '2026-03-01',
            'category' => 'communication',
            'action' => 'submit',
        ]);

        $this->assertDatabaseHas('reimbursements', [
            'user_id' => $this->staff->id,
            'title' => 'Biaya Internet',
            'status' => 'pending',
        ]);
    }

    public function test_submit_changes_status_to_pending(): void
    {
        $reimbursement = $this->makeReimbursement($this->staff);

        $this->actingAs($this->staff)->post("/reimbursements/{$reimbursement->id}/submit");

        $this->assertDatabaseHas('reimbursements', [
            'id' => $reimbursement->id,
            'status' => 'pending',
        ]);
    }

    public function test_destroy_deletes_draft_reimbursement(): void
    {
        $reimbursement = $this->makeReimbursement($this->admin);

        $this->actingAs($this->admin)->delete("/reimbursements/{$reimbursement->id}");

        $this->assertDatabaseMissing('reimbursements', ['id' => $reimbursement->id]);
    }

    public function test_staff_cannot_delete_without_permission(): void
    {
        $reimbursement = $this->makeReimbursement($this->staff);

        $this->actingAs($this->staff)->delete("/reimbursements/{$reimbursement->id}")->assertForbidden();
        $this->assertDatabaseHas('reimbursements', ['id' => $reimbursement->id]);
    }

    public function test_review_approve_changes_status(): void
    {
        $category = TransactionCategory::create([
            'type' => 'expense',
            'label' => 'Transport',
        ]);

        $reimbursement = $this->makeReimbursement($this->staff, ['status' => 'pending']);

        $this->actingAs($this->admin)->post("/reimbursements/{$reimbursement->id}/review", [
            'action' => 'approve',
            'review_notes' => 'Disetujui',
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('reimbursements', [
            'id' => $reimbursement->id,
            'status' => 'approved',
        ]);
    }

    public function test_review_requires_approve_permission(): void
    {
        $reimbursement = $this->makeReimbursement($this->staff, ['status' => 'pending']);

        $this->actingAs($this->staff)->post("/reimbursements/{$reimbursement->id}/review", [
            'action' => 'approve',
            'review_notes' => '',
        ])->assertForbidden();
    }
}
