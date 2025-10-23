<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\Alert;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagement extends Component
{
    use Alert;

    public ?int $selectedRoleId = null;
    public array $permissions = [];

    public function mount(): void
    {
        // Select first role by default
        $firstRole = Role::orderBy('name')->first();
        if ($firstRole) {
            $this->selectRole($firstRole->id);
        }
    }

    public function render(): View
    {
        return view('livewire.admin.role-management');
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
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

        // Group by module
        $grouped = [];
        foreach ($allPermissions as $permission) {
            // Extract module name from permission (e.g., "view clients" -> "clients")
            $parts = explode(' ', $permission->name);
            $module = count($parts) > 1 ? $parts[1] : 'other';

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $parts[0] ?? 'manage',
                'has' => in_array($permission->id, $this->permissions)
            ];
        }

        return $grouped;
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $role = Role::find($roleId);

        if ($role) {
            $this->permissions = $role->permissions->pluck('id')->toArray();
        }
    }

    public function togglePermission(int $permissionId): void
    {
        if (!$this->selectedRole) {
            return;
        }

        $permission = Permission::find($permissionId);

        if (!$permission) {
            return;
        }

        if (in_array($permissionId, $this->permissions)) {
            // Remove permission
            $this->selectedRole->revokePermissionTo($permission);
            $this->permissions = array_diff($this->permissions, [$permissionId]);
            $this->success("Permission '{$permission->name}' removed from {$this->selectedRole->name}");
        } else {
            // Add permission
            $this->selectedRole->givePermissionTo($permission);
            $this->permissions[] = $permissionId;
            $this->success("Permission '{$permission->name}' granted to {$this->selectedRole->name}");
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function grantAllPermissions(): void
    {
        if (!$this->selectedRole) {
            return;
        }

        $this->selectedRole->syncPermissions(Permission::all());
        $this->permissions = Permission::pluck('id')->toArray();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->success("All permissions granted to {$this->selectedRole->name}");
    }

    public function revokeAllPermissions(): void
    {
        if (!$this->selectedRole) {
            return;
        }

        $this->selectedRole->syncPermissions([]);
        $this->permissions = [];

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->success("All permissions revoked from {$this->selectedRole->name}");
    }

    #[Computed]
    public function totalPermissions()
    {
        return Permission::count();
    }
}