<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'view profit-loss']);

        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo('view profit-loss');
        }

        if ($financeManager = Role::where('name', 'finance manager')->first()) {
            $financeManager->givePermissionTo('view profit-loss');
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->revokePermissionTo('view profit-loss');
        }

        if ($financeManager = Role::where('name', 'finance manager')->first()) {
            $financeManager->revokePermissionTo('view profit-loss');
        }

        Permission::where('name', 'view profit-loss')->delete();
    }
};
