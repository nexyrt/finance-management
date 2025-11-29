<?php

namespace App\Livewire\Roles;

use App\Livewire\Traits\Alert;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Delete extends Component
{
    use Alert;

    public Role $role;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" title="Delete" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        // Prevent deletion of critical default roles
        if (in_array($this->role->name, ['admin', 'staff'])) {
            $this->error("Cannot delete default role '{$this->role->name}'");

            return;
        }

        // Check if user is trying to delete their own role
        $currentUserRole = auth()->user()->roles->first();
        if ($currentUserRole && $currentUserRole->id === $this->role->id) {
            $this->error('Cannot delete your own role');

            return;
        }

        $userCount = $this->role->users()->count();

        if ($userCount > 0) {
            // Role has users - show reassignment warning
            $this->dialog()
                ->warning(
                    "Delete role '{$this->role->name}'?",
                    "This role has {$userCount} user(s). They will be reassigned to the role with the least permissions."
                )
                ->confirm('Delete & Reassign', 'delete', 'Role deleted successfully')
                ->cancel('Cancel')
                ->send();
        } else {
            // Role has no users - simple confirmation
            $this->dialog()
                ->question(
                    "Delete role '{$this->role->name}'?",
                    'This action cannot be undone.'
                )
                ->confirm('Delete', 'delete', 'Role deleted successfully')
                ->cancel('Cancel')
                ->send();
        }
    }

    public function delete(): void
    {
        // Double check permissions in case method called directly
        if (in_array($this->role->name, ['admin', 'staff'])) {
            $this->error("Cannot delete default role '{$this->role->name}'");

            return;
        }

        $currentUserRole = auth()->user()->roles->first();
        if ($currentUserRole && $currentUserRole->id === $this->role->id) {
            $this->error('Cannot delete your own role');

            return;
        }

        try {
            DB::transaction(function () {
                $usersWithRole = $this->role->users;

                if ($usersWithRole->count() > 0) {
                    // Find role with least permissions (fallback to 'staff')
                    $fallbackRole = Role::withCount('permissions')
                        ->where('name', '!=', $this->role->name)
                        ->orderBy('permissions_count', 'asc')
                        ->first();

                    if (! $fallbackRole) {
                        // If no other role exists, create or find 'staff' role
                        $fallbackRole = Role::firstOrCreate(
                            ['name' => 'staff'],
                            ['guard_name' => 'web', 'icon' => 'user']
                        );
                    }

                    // Reassign users to fallback role
                    foreach ($usersWithRole as $user) {
                        $user->syncRoles([$fallbackRole->name]);
                    }
                }

                // Delete the role
                $this->role->delete();
            });

            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $this->dispatch('deleted');
            $this->success('Role deleted successfully');

        } catch (\Exception $e) {
            $this->error('Failed to delete role: '.$e->getMessage());
        }
    }
}
