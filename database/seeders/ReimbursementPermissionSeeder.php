<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ReimbursementPermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view reimbursements',
            'create reimbursements',
            'edit reimbursements',
            'delete reimbursements',
            'approve reimbursements',
            'pay reimbursements',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin - full access
        $admin = Role::findByName('admin');
        $admin->givePermissionTo($permissions);

        // Finance Manager - all except create (users create their own)
        $financeManager = Role::findByName('finance manager');
        $financeManager->givePermissionTo([
            'view reimbursements',
            'create reimbursements',
            'edit reimbursements',
            'approve reimbursements',
            'pay reimbursements',
        ]);

        // Staff - can only manage own reimbursements
        $staff = Role::findByName('staff');
        $staff->givePermissionTo([
            'view reimbursements',
            'create reimbursements',
            'edit reimbursements',
            'delete reimbursements',
        ]);
    }
}