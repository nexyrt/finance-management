<?php

namespace App\Livewire\Permissions;

use App\Livewire\Traits\Alert;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Delete extends Component
{
    use Alert;

    public Permission $permission;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" title="Delete Permission" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        $roleCount = $this->permission->roles()->count();

        if ($roleCount > 0) {
            // Permission assigned to roles - show warning
            $this->dialog()
                ->warning(
                    "Delete permission '{$this->permission->name}'?",
                    "This permission is assigned to {$roleCount} role(s). It will be automatically revoked from all roles."
                )
                ->confirm('Delete & Revoke', 'delete', 'Permission deleted successfully')
                ->cancel('Cancel')
                ->send();
        } else {
            // Permission not assigned - simple confirmation
            $this->dialog()
                ->question(
                    "Delete permission '{$this->permission->name}'?",
                    'This action cannot be undone.'
                )
                ->confirm('Delete', 'delete', 'Permission deleted successfully')
                ->cancel('Cancel')
                ->send();
        }
    }

    public function delete(): void
    {
        try {
            DB::transaction(function () {
                $rolesWithPermission = $this->permission->roles;

                // Auto-revoke from all roles
                foreach ($rolesWithPermission as $role) {
                    $role->revokePermissionTo($this->permission);
                }

                // Delete the permission
                $this->permission->delete();
            });

            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $this->dispatch('deleted');
            $this->success('Permission deleted successfully');

        } catch (\Exception $e) {
            $this->error('Failed to delete permission: '.$e->getMessage());
        }
    }
}
