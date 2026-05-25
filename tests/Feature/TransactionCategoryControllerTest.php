<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TransactionCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view categories', 'manage categories'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions(['view categories']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('viewer');
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/transaction-categories')->assertRedirect('/login');
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $this->actingAs($this->admin)->get('/transaction-categories')->assertOk();
    }

    public function test_store_creates_parent_category(): void
    {
        $this->actingAs($this->admin)->post('/transaction-categories', [
            'type' => 'expense',
            'label' => 'Biaya Operasional',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'type' => 'expense',
            'label' => 'Biaya Operasional',
            'parent_id' => null,
        ]);
    }

    public function test_store_creates_child_category(): void
    {
        $parent = TransactionCategory::create(['type' => 'expense', 'label' => 'Induk Kategori']);

        $this->actingAs($this->admin)->post('/transaction-categories', [
            'type' => 'expense',
            'label' => 'Sub Kategori',
            'parent_id' => $parent->id,
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'label' => 'Sub Kategori',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_store_requires_manage_categories_permission(): void
    {
        $this->actingAs($this->viewer)->post('/transaction-categories', [
            'type' => 'expense',
            'label' => 'Forbidden Category',
        ])->assertForbidden();

        $this->assertDatabaseMissing('transaction_categories', ['label' => 'Forbidden Category']);
    }

    public function test_store_saves_pl_group(): void
    {
        $this->actingAs($this->admin)->post('/transaction-categories', [
            'type' => 'expense',
            'pl_group' => 'cogs',
            'label' => 'HPP Jasa',
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'label' => 'HPP Jasa',
            'pl_group' => 'cogs',
        ]);
    }

    public function test_store_rejects_invalid_pl_group(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/transaction-categories', [
                'type' => 'expense',
                'pl_group' => 'not_a_real_group',
                'label' => 'Kategori Salah',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('pl_group');
    }

    public function test_index_reports_unclassified_count_and_filter(): void
    {
        TransactionCategory::create(['type' => 'expense', 'label' => 'Tanpa Grup']);
        TransactionCategory::create(['type' => 'expense', 'label' => 'Dengan Grup', 'pl_group' => 'opex']);

        $this->actingAs($this->admin)
            ->get('/transaction-categories')
            ->assertInertia(fn (AssertableInertia $page) => $page->where('stats.unclassified', 1));

        $this->actingAs($this->admin)
            ->get('/transaction-categories?pl_status=unclassified')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('categories.data', 1)
                ->where('categories.data.0.label', 'Tanpa Grup')
            );
    }

    public function test_update_modifies_pl_group(): void
    {
        $category = TransactionCategory::create(['type' => 'expense', 'label' => 'Beban', 'pl_group' => 'opex']);

        $this->actingAs($this->admin)->put("/transaction-categories/{$category->id}", [
            'type' => 'expense',
            'pl_group' => 'tax',
            'label' => 'Beban',
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'id' => $category->id,
            'pl_group' => 'tax',
        ]);
    }

    public function test_update_modifies_category(): void
    {
        $category = TransactionCategory::create(['type' => 'expense', 'label' => 'Lama']);

        $this->actingAs($this->admin)->put("/transaction-categories/{$category->id}", [
            'type' => 'income',
            'label' => 'Diperbarui',
        ]);

        $this->assertDatabaseHas('transaction_categories', [
            'id' => $category->id,
            'label' => 'Diperbarui',
            'type' => 'income',
        ]);
    }

    public function test_destroy_deletes_unused_category(): void
    {
        $category = TransactionCategory::create(['type' => 'expense', 'label' => 'Kosong']);

        $this->actingAs($this->admin)->delete("/transaction-categories/{$category->id}");

        $this->assertDatabaseMissing('transaction_categories', ['id' => $category->id]);
    }

    public function test_destroy_blocked_when_category_has_children(): void
    {
        $parent = TransactionCategory::create(['type' => 'expense', 'label' => 'Parent']);
        TransactionCategory::create(['type' => 'expense', 'label' => 'Child', 'parent_id' => $parent->id]);

        $this->actingAs($this->admin)
            ->delete("/transaction-categories/{$parent->id}")
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('transaction_categories', ['id' => $parent->id]);
    }

    public function test_destroy_blocked_when_category_has_transactions(): void
    {
        $category = TransactionCategory::create(['type' => 'expense', 'label' => 'Digunakan']);
        $account = BankAccount::factory()->create();

        BankTransaction::create([
            'bank_account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 100000,
            'transaction_date' => '2026-03-01',
            'transaction_type' => 'debit',
            'description' => 'Test transaction',
        ]);

        $this->actingAs($this->admin)
            ->delete("/transaction-categories/{$category->id}")
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('transaction_categories', ['id' => $category->id]);
    }
}
