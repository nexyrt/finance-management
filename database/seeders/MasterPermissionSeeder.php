<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MasterPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Starting Master Permission Seeder...');
        $this->command->newLine();

        // Step 1: Create/Update Roles
        $this->createRoles();

        // Step 2: Create/Update Permissions
        $this->createPermissions();

        // Step 3: Assign Permissions to Roles
        $this->assignPermissionsToRoles();

        // Step 4: Ensure admin user exists
        $this->ensureAdminUser();

        $this->command->newLine();
        $this->command->info('✅ Master Permission Seeder completed successfully!');
    }

    /**
     * Create or update roles
     */
    private function createRoles(): void
    {
        $this->command->info('📋 Creating/Updating Roles...');

        $roles = [
            ['name' => 'admin', 'icon' => 'shield-exclamation'],
            ['name' => 'finance manager', 'icon' => 'banknotes'],
            ['name' => 'staff', 'icon' => 'user'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['icon' => $roleData['icon']]
            );

            // Update icon jika role sudah ada tapi icon berbeda
            if ($role->icon !== $roleData['icon']) {
                $role->update(['icon' => $roleData['icon']]);
                $this->command->warn("  ↻ Updated icon for: {$roleData['name']}");
            } else {
                $this->command->line("  ✓ Role exists: {$roleData['name']}");
            }
        }
    }

    /**
     * Create permissions (only if not exists)
     */
    private function createPermissions(): void
    {
        $this->command->info('📋 Creating Permissions (skipping existing)...');

        $allPermissions = [
            // Clients
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',

            // Services
            'view services',
            'create services',
            'edit services',
            'delete services',

            // Invoices
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',

            // Payments
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Bank Accounts
            'view bank-accounts',
            'create bank-accounts',
            'edit bank-accounts',
            'delete bank-accounts',

            // Transactions (legacy - jika ada di prod)
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',

            // Cash Flow
            'view cash-flow',
            'manage cash-flow',

            // Recurring Invoices
            'view recurring-invoices',
            'create recurring-invoices',
            'edit recurring-invoices',
            'delete recurring-invoices',
            'publish recurring-invoices',

            // Categories
            'view categories',
            'manage categories',

            // Reimbursements
            'view reimbursements',
            'create reimbursements',
            'edit reimbursements',
            'delete reimbursements',
            'approve reimbursements',
            'pay reimbursements',

            // Fund Requests
            'view fund requests',
            'create fund requests',
            'edit fund requests',
            'delete fund requests',
            'approve fund requests',
            'disburse fund requests',

            // Loans
            'view loans',
            'create loans',
            'edit loans',
            'delete loans',
            'pay loans',

            // Receivables
            'view receivables',
            'create receivables',
            'edit receivables',
            'delete receivables',
            'approve receivables',
            'pay receivables',

            // Permission Management
            'view permissions',
            'manage permissions',

            // User Management
            'manage users',

            // Feedbacks
            'view feedbacks',
            'create feedbacks',
            'edit feedbacks',
            'delete feedbacks',
            'respond feedbacks',
            'manage feedbacks',
        ];

        $newCount = 0;
        $existingCount = 0;

        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            if ($permission->wasRecentlyCreated) {
                $this->command->line("  + Created: {$permissionName}");
                $newCount++;
            } else {
                $existingCount++;
            }
        }

        $this->command->info('  ✓ Total: '.count($allPermissions).' permissions');
        $this->command->line("    → New: {$newCount}");
        $this->command->line("    → Existing: {$existingCount}");
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info('📋 Assigning Permissions to Roles...');

        // ============================================================
        // ADMIN - Full Access
        // ============================================================
        $admin = Role::findByName('admin');
        $adminPermissions = Permission::all();
        $admin->syncPermissions($adminPermissions);
        $this->command->line("  ✓ Admin: {$adminPermissions->count()} permissions");

        // ============================================================
        // FINANCE MANAGER
        // ============================================================
        $financeManager = Role::findByName('finance manager');
        $financeManagerPermissions = [
            // Clients
            'view clients',
            'create clients',
            'edit clients',

            // Services
            'view services',
            'create services',
            'edit services',

            // Invoices
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',

            // Payments
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Bank Accounts
            'view bank-accounts',
            'create bank-accounts',
            'edit bank-accounts',
            'delete bank-accounts',

            // Transactions (legacy)
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',

            // Cash Flow
            'view cash-flow',
            'manage cash-flow',

            // Recurring Invoices
            'view recurring-invoices',
            'create recurring-invoices',
            'edit recurring-invoices',
            'delete recurring-invoices',
            'publish recurring-invoices',

            // Categories
            'view categories',
            'manage categories',

            // Reimbursements
            'view reimbursements',
            'create reimbursements',
            'edit reimbursements',
            'approve reimbursements',
            'pay reimbursements',

            // Fund Requests
            'view fund requests',
            'create fund requests',
            'edit fund requests',
            'delete fund requests',
            'approve fund requests',
            'disburse fund requests',

            // Loans & Receivables
            'view loans',
            'create loans',
            'edit loans',
            'delete loans',
            'pay loans',
            'view receivables',
            'create receivables',
            'edit receivables',
            'delete receivables',
            'approve receivables',
            'pay receivables',

            // Permissions (view only)
            'view permissions',

            // Feedbacks (can respond and manage)
            'view feedbacks',
            'create feedbacks',
            'edit feedbacks',
            'delete feedbacks',
            'respond feedbacks',
            'manage feedbacks',
        ];

        $financeManager->syncPermissions($financeManagerPermissions);
        $this->command->line('  ✓ Finance Manager: '.count($financeManagerPermissions).' permissions');

        // ============================================================
        // STAFF
        // ============================================================
        $staff = Role::findByName('staff');
        $staffPermissions = [
            // Clients
            'view clients',
            'create clients',
            'edit clients',

            // Services
            'view services',

            // Invoices
            'view invoices',
            'create invoices',

            // Reimbursements (own only)
            'view reimbursements',
            'create reimbursements',
            'edit reimbursements',
            'delete reimbursements',

            // Fund Requests (own only)
            'view fund requests',
            'create fund requests',
            'edit fund requests',
            'delete fund requests',

            // Receivables (request only)
            'view receivables',
            'create receivables',

            // Feedbacks (own only)
            'view feedbacks',
            'create feedbacks',
            'edit feedbacks',
            'delete feedbacks',
        ];

        $staff->syncPermissions($staffPermissions);
        $this->command->line('  ✓ Staff: '.count($staffPermissions).' permissions');
    }

    /**
     * Ensure admin user exists
     */
    private function ensureAdminUser(): void
    {
        $this->command->info('📋 Checking Admin User...');

        $adminRole = Role::findByName('admin');
        $adminUsers = $adminRole->users;

        if ($adminUsers->isEmpty()) {
            $this->command->warn('  ⚠ No admin users found!');

            // Option 1: Assign first user as admin
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->assignRole('admin');
                $this->command->info("  ✓ Assigned admin role to: {$firstUser->email}");
            } else {
                $this->command->error('  ✗ No users in database to assign admin role!');
            }
        } else {
            $this->command->line("  ✓ Admin users: {$adminUsers->count()}");
        }
    }
}
