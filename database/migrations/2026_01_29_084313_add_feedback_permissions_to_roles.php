<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create feedback permissions if not exists
        $feedbackPermissions = [
            'view feedbacks',
            'create feedbacks',
            'edit feedbacks',
            'delete feedbacks',
            'respond feedbacks',
            'manage feedbacks',
        ];

        foreach ($feedbackPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign to Admin role (all permissions)
        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo($feedbackPermissions);
        }

        // Assign to Finance Manager role (all permissions)
        if ($financeManager = Role::where('name', 'finance manager')->first()) {
            $financeManager->givePermissionTo($feedbackPermissions);
        }

        // Assign to Staff role (basic permissions only)
        if ($staff = Role::where('name', 'staff')->first()) {
            $staff->givePermissionTo([
                'view feedbacks',
                'create feedbacks',
                'edit feedbacks',
                'delete feedbacks',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove permissions from roles
        $feedbackPermissions = [
            'view feedbacks',
            'create feedbacks',
            'edit feedbacks',
            'delete feedbacks',
            'respond feedbacks',
            'manage feedbacks',
        ];

        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->revokePermissionTo($feedbackPermissions);
        }

        if ($financeManager = Role::where('name', 'finance manager')->first()) {
            $financeManager->revokePermissionTo($feedbackPermissions);
        }

        if ($staff = Role::where('name', 'staff')->first()) {
            $staff->revokePermissionTo($feedbackPermissions);
        }

        // Delete permissions
        Permission::whereIn('name', $feedbackPermissions)->delete();
    }
};
