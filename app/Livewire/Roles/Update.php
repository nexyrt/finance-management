<?php

namespace App\Livewire\Roles;

use App\Livewire\Traits\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Update extends Component
{
    use Alert;

    // Modal Control
    public bool $modal = false;

    // Role ID untuk tracking
    public $roleId = null;

    // Form Fields - NO TYPE DECLARATION
    public $name = null;

    public $icon = 'shield-check';

    public $originalName = null; // For display purposes

    // Available icons (Heroicons)
    public array $availableIcons = [
        'shield-check',
        'shield-exclamation',
        'user',
        'user-group',
        'users',
        'banknotes',
        'currency-dollar',
        'document-text',
        'folder',
        'briefcase',
        'chart-bar',
        'cog',
        'key',
        'lock-closed',
        'lock-open',
        'eye',
        'eye-slash',
        'pencil',
        'trash',
        'check-circle',
        'x-circle',
        'exclamation-circle',
        'information-circle',
        'star',
        'heart',
        'bell',
        'clipboard',
        'document-duplicate',
        'archive-box',
        'inbox',
        'wrench',
        'beaker',
        'calculator',
        'calendar',
        'clock',
        'tag',
        'bookmark',
    ];

    public function render(): View
    {
        return view('livewire.roles.update');
    }

    // Event Listener - Dipanggil dari parent
    #[On('load::role')]
    public function load(Role $role): void
    {
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->icon = $role->icon ?? 'shield-check';
        $this->originalName = $role->name;

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->roleId),
            ],
            'icon' => ['required', 'string', 'in:'.implode(',', $this->availableIcons)],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $role = Role::findOrFail($this->roleId);

        $role->update([
            'name' => strtolower($validated['name']),
            'icon' => $validated['icon'],
        ]);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->dispatch('updated');
        $this->reset();
        $this->success('Role updated successfully');
    }

    public function selectIcon(string $iconName): void
    {
        $this->icon = $iconName;
    }
}
