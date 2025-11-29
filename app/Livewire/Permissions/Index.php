<?php

namespace App\Livewire\Permissions;

use App\Livewire\Traits\Alert;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use Alert;

    public ?int $selectedRoleId = null;

    public array $selectedPermissions = [];

    public string $searchPermission = '';

    public function mount(): void
    {
        // Check permission
        abort_unless(auth()->user()->can('view permissions'), 403);

        // Select first role by default
        $firstRole = Role::orderBy('name')->first();
        if ($firstRole) {
            $this->selectRole($firstRole->id);
        }
    }

    public function render(): View
    {
        return view('livewire.permissions.index');
    }

    #[Computed]
    public function roles()
    {
        return Role::with('permissions')->orderBy('name')->get();
    }

    #[Computed]
    public function selectedRole()
    {
        return Role::find($this->selectedRoleId);
    }

    #[Computed]
    public function groupedPermissions()
    {
        $allPermissions = Permission::orderBy('name')->get();

        // Filter by search
        if ($this->searchPermission) {
            $allPermissions = $allPermissions->filter(function ($permission) {
                return str_contains(
                    strtolower($permission->name),
                    strtolower($this->searchPermission)
                );
            });
        }

        // Group by module (second word in permission name)
        return $allPermissions->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);

            return count($parts) > 1 ? ucfirst($parts[1]) : 'Other';
        })->sortKeys();
    }

    #[Computed]
    public function totalPermissions()
    {
        return Permission::count();
    }

    #[Computed]
    public function canManagePermissions(): bool
    {
        return auth()->user()->can('manage permissions');
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $role = Role::find($roleId);

        if ($role) {
            $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        }

        // Reset search when changing role
        $this->searchPermission = '';
    }

    public function togglePermission(int $permissionId): void
    {
        // Check permission
        if (! $this->canManagePermissions) {
            $this->error('You do not have permission to manage permissions');

            return;
        }

        if (! $this->selectedRole) {
            $this->error('No role selected');

            return;
        }

        $permission = Permission::find($permissionId);
        if (! $permission) {
            $this->error('Permission not found');

            return;
        }

        if (in_array($permissionId, $this->selectedPermissions)) {
            // Revoke permission
            $this->selectedRole->revokePermissionTo($permission);
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, [$permissionId])
            );
            $this->success("Permission '{$permission->name}' revoked");
        } else {
            // Grant permission
            $this->selectedRole->givePermissionTo($permission);
            $this->selectedPermissions[] = $permissionId;
            $this->success("Permission '{$permission->name}' granted");
        }

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function selectModule(string $module): void
    {
        // Check permission
        if (! $this->canManagePermissions) {
            $this->error('You do not have permission to manage permissions');

            return;
        }

        if (! $this->selectedRole) {
            $this->error('No role selected');

            return;
        }

        $modulePermissions = $this->groupedPermissions[$module] ?? collect();
        $permissionIds = $modulePermissions->pluck('id')->toArray();

        $newPermissions = array_diff($permissionIds, $this->selectedPermissions);

        if (empty($newPermissions)) {
            $this->info('All permissions already granted');

            return;
        }

        foreach ($newPermissions as $permissionId) {
            $permission = Permission::find($permissionId);
            if ($permission) {
                $this->selectedRole->givePermissionTo($permission);
            }
        }

        $this->selectedPermissions = array_unique(
            array_merge($this->selectedPermissions, $permissionIds)
        );

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->success("All {$module} permissions granted");
    }

    public function deselectModule(string $module): void
    {
        // Check permission
        if (! $this->canManagePermissions) {
            $this->error('You do not have permission to manage permissions');

            return;
        }

        if (! $this->selectedRole) {
            $this->error('No role selected');

            return;
        }

        $modulePermissions = $this->groupedPermissions[$module] ?? collect();
        $permissionIds = $modulePermissions->pluck('id')->toArray();

        $toRevoke = array_intersect($permissionIds, $this->selectedPermissions);

        if (empty($toRevoke)) {
            $this->info('No permissions to revoke');

            return;
        }

        foreach ($toRevoke as $permissionId) {
            $permission = Permission::find($permissionId);
            if ($permission) {
                $this->selectedRole->revokePermissionTo($permission);
            }
        }

        $this->selectedPermissions = array_values(
            array_diff($this->selectedPermissions, $permissionIds)
        );

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->success("All {$module} permissions revoked");
    }

    public function grantAllPermissions(): void
    {
        // Check permission
        if (! $this->canManagePermissions) {
            $this->error('You do not have permission to manage permissions');

            return;
        }

        if (! $this->selectedRole) {
            $this->error('No role selected');

            return;
        }

        $this->selectedRole->syncPermissions(Permission::all());
        $this->selectedPermissions = Permission::pluck('id')->toArray();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->success("All permissions granted to '{$this->selectedRole->name}'");
    }

    public function revokeAllPermissions(): void
    {
        // Check permission
        if (! $this->canManagePermissions) {
            $this->error('You do not have permission to manage permissions');

            return;
        }

        if (! $this->selectedRole) {
            $this->error('No role selected');

            return;
        }

        $this->selectedRole->syncPermissions([]);
        $this->selectedPermissions = [];

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->success("All permissions revoked from '{$this->selectedRole->name}'");
    }
}
