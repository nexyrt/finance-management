<?php

namespace Tests\Feature;

use App\Models\FundRequest;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SidebarActionCountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ([
            'view dashboard',
            'approve reimbursements', 'pay reimbursements',
            'approve fund requests', 'disburse fund requests',
        ] as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    private function makeReimbursement(string $status): void
    {
        Reimbursement::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'R',
            'description' => 'x',
            'amount' => 500_000,
            'expense_date' => now()->toDateString(),
            'category_input' => 'Transport',
            'status' => $status,
        ]);
    }

    private function makeFundRequest(string $status): void
    {
        FundRequest::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'F',
            'purpose' => 'x',
            'total_amount' => 1_000_000,
            'priority' => 'medium',
            'needed_by_date' => now()->addDays(5)->toDateString(),
            'status' => $status,
        ]);
    }

    public function test_counts_sum_pending_and_approved_for_a_fully_permitted_user(): void
    {
        $this->makeReimbursement('pending');
        $this->makeReimbursement('pending');
        $this->makeReimbursement('approved');
        $this->makeReimbursement('paid'); // done — excluded
        $this->makeFundRequest('pending');
        $this->makeFundRequest('approved');
        $this->makeFundRequest('approved');
        $this->makeFundRequest('disbursed'); // done — excluded

        $user = User::factory()->create();
        $user->givePermissionTo('view dashboard', 'approve reimbursements', 'pay reimbursements', 'approve fund requests', 'disburse fund requests');

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('actionCounts.reimbursements', 3) // 2 pending + 1 approved
                ->where('actionCounts.fund_requests', 3)  // 1 pending + 2 approved
            );
    }

    public function test_counts_are_permission_aware(): void
    {
        $this->makeReimbursement('pending');
        $this->makeReimbursement('approved');
        $this->makeFundRequest('pending');
        $this->makeFundRequest('approved');

        // Can review but not pay/disburse → only pending is actionable.
        $reviewer = User::factory()->create();
        $reviewer->givePermissionTo('view dashboard', 'approve reimbursements', 'approve fund requests');

        $this->actingAs($reviewer)
            ->get('/dashboard')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('actionCounts.reimbursements', 1)
                ->where('actionCounts.fund_requests', 1)
            );

        // No review/pay permissions → nothing is actionable.
        $plain = User::factory()->create();
        $plain->givePermissionTo('view dashboard');

        $this->actingAs($plain)
            ->get('/dashboard')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('actionCounts.reimbursements', 0)
                ->where('actionCounts.fund_requests', 0)
            );
    }
}
