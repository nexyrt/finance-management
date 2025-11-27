<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoanReceivablePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin - full access
        $admin = Role::findByName('admin');
        $admin->givePermissionTo($permissions);

        // Finance Manager - all
        $financeManager = Role::findByName('finance manager');
        $financeManager->givePermissionTo($permissions);

        // Staff - can view & create receivables only
        $staff = Role::findByName('staff');
        $staff->givePermissionTo([
            'view receivables',
            'create receivables',
        ]);
    }
}