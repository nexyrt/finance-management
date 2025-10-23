<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view services',
            'create services',
            'edit services',
            'delete services',
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            'view bank-accounts',
            'create bank-accounts',
            'edit bank-accounts',
            'delete bank-accounts',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'view cash-flow',
            'manage cash-flow',
            'view recurring-invoices',
            'create recurring-invoices',
            'edit recurring-invoices',
            'delete recurring-invoices',
            'publish recurring-invoices',
            'view categories',
            'manage categories',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $financeManager = Role::create(['name' => 'finance manager']);
        $financeManager->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'view services',
            'create services',
            'edit services',
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            'view bank-accounts',
            'create bank-accounts',
            'edit bank-accounts',
            'delete bank-accounts',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'view cash-flow',
            'manage cash-flow',
            'view recurring-invoices',
            'create recurring-invoices',
            'edit recurring-invoices',
            'delete recurring-invoices',
            'publish recurring-invoices',
            'view categories',
            'manage categories',
        ]);

        $staff = Role::create(['name' => 'staff']);
        $staff->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'view services',
            'view invoices',
            'create invoices',
        ]);

        // 👇 ASSIGN SEMUA USER SEBAGAI STAFF
        User::all()->each(fn($user) => $user->assignRole('staff'));
    }
}