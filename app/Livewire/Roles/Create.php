<?php

namespace App\Livewire\Roles;

use App\Livewire\Traits\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    use Alert;

    // Modal Control
    public bool $modal = false;

    // Form Fields - NO TYPE DECLARATION
    public $name = null;

    public $icon = 'shield-check'; // Default icon

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
        return view('livewire.roles.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'icon' => ['required', 'string', 'in:'.implode(',', $this->availableIcons)],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        Role::create([
            'name' => strtolower($validated['name']),
            'icon' => $validated['icon'],
            'guard_name' => 'web',
        ]);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->dispatch('created');
        $this->reset();
        $this->success('Role created successfully');
    }

    public function selectIcon(string $iconName): void
    {
        $this->icon = $iconName;
    }
}
