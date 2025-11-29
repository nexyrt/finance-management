<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
                Permission Management
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                Manage role permissions
            </p>
        </div>

        {{-- Add Role Button (only for users who can manage permissions) --}}
        @if ($this->canManagePermissions)
            <livewire:roles.create @created="$refresh" />
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Roles --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="user-group" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $this->roles->count() }}
                    </div>
                    <div class="text-sm text-dark-500 dark:text-dark-400">Total Roles</div>
                </div>
            </div>
        </div>

        {{-- Total Permissions --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="shield-check" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $this->totalPermissions }}
                    </div>
                    <div class="text-sm text-dark-500 dark:text-dark-400">Total Permissions</div>
                </div>
            </div>
        </div>

        {{-- Total Modules --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="folder" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $this->groupedPermissions->count() }}
                    </div>
                    <div class="text-sm text-dark-500 dark:text-dark-400">Modules</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: Sidebar + Permission Panel --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Role Sidebar (Auto Height) --}}
        <div class="lg:col-span-3">
            <div
                class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 lg:sticky lg:top-6">
                {{-- Sidebar Header --}}
                <div
                    class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 px-4 py-4 border-b border-dark-200 dark:border-dark-600 rounded-t-xl">
                    <h3 class="font-semibold text-dark-900 dark:text-dark-50 flex items-center gap-2">
                        <x-icon name="user-group" class="w-5 h-5" />
                        Roles
                    </h3>
                </div>

                {{-- Role List --}}
                <div class="p-2 space-y-1 max-h-[calc(100vh-24rem)] overflow-y-auto">
                    @foreach ($this->roles as $role)
                        <div class="relative group">
                            {{-- Role Card --}}
                            <button type="button" wire:click="selectRole({{ $role->id }})"
                                class="w-full text-left px-4 py-3 rounded-lg transition-colors cursor-pointer {{ $selectedRoleId === $role->id ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-300 dark:border-primary-600' : 'hover:bg-gray-50 dark:hover:bg-dark-700 border border-transparent' }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-10 w-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <x-icon :name="$role->icon ?? 'shield-check'" class="w-5 h-5 text-white" />
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="font-semibold text-dark-900 dark:text-dark-50 truncate flex items-center gap-2">
                                            {{ ucfirst($role->name) }}
                                            @if ($selectedRoleId === $role->id)
                                                <x-icon name="chevron-right"
                                                    class="w-4 h-4 text-primary-600 dark:text-primary-400 flex-shrink-0" />
                                            @endif
                                        </div>
                                        <div class="text-xs text-dark-500 dark:text-dark-400">
                                            {{ $role->permissions->count() }} permissions
                                            @if ($role->users()->count() > 0)
                                                • {{ $role->users()->count() }} users
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </button>

                            {{-- Action Buttons (Show on Hover) --}}
                            @if ($this->canManagePermissions)
                                <div
                                    class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none group-hover:pointer-events-auto">
                                    {{-- Edit Button --}}
                                    <x-button.circle icon="pencil" color="blue" size="sm"
                                        wire:click.stop="$dispatch('load::role', { role: {{ $role->id }} })"
                                        title="Edit Role" />

                                    {{-- Delete Button --}}
                                    <div wire:click.stop>
                                        <livewire:roles.delete :role="$role" :key="'delete-role-' . $role->id"
                                            @deleted="$refresh" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Permission Panel (Full Height) --}}
        <div class="lg:col-span-9">
            @if ($this->selectedRole)
                <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 overflow-hidden flex flex-col"
                    :style="`height: ${panelHeight}px`">
                    {{-- Panel Header (Fixed) --}}
                    <div
                        class="flex-shrink-0 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 px-6 py-4 border-b border-dark-200 dark:border-dark-600">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            {{-- Title --}}
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-12 w-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-icon :name="$this->selectedRole->icon ?? 'shield-check'" class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">
                                        {{ ucfirst($this->selectedRole->name) }}
                                    </h3>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">
                                        {{ count($selectedPermissions) }} / {{ $this->totalPermissions }} permissions
                                        assigned
                                    </p>
                                </div>
                            </div>

                            {{-- Header Actions - Only show if can manage --}}
                            <div class="flex flex-wrap gap-2">
                                <x-input wire:model.live.debounce.300ms="searchPermission"
                                    placeholder="Search permissions..." icon="magnifying-glass" class="lg:w-64" />

                                @if ($this->canManagePermissions)
                                    <x-button wire:click="revokeAllPermissions" color="red" size="sm" outline
                                        icon="x-mark"
                                        wire:confirm="Remove all permissions from '{{ $this->selectedRole->name }}' role?">
                                        Revoke All
                                    </x-button>
                                    <x-button wire:click="grantAllPermissions" color="green" size="sm"
                                        icon="check"
                                        wire:confirm="Grant all permissions to '{{ $this->selectedRole->name }}' role?">
                                        Grant All
                                    </x-button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Scrollable Content Area --}}
                    <div class="flex-1 overflow-y-auto">
                        @if ($this->groupedPermissions->isEmpty())
                            {{-- Empty State --}}
                            <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                                <div
                                    class="h-16 w-16 bg-gray-100 dark:bg-dark-700 rounded-full flex items-center justify-center mb-4">
                                    <x-icon name="magnifying-glass" class="w-8 h-8 text-gray-400 dark:text-dark-500" />
                                </div>
                                <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">
                                    No permissions found
                                </h3>
                                <p class="text-dark-500 dark:text-dark-400">
                                    Try adjusting your search criteria
                                </p>
                            </div>
                        @else
                            {{-- Permissions Grouped by Module --}}
                            <div class="divide-y divide-dark-200 dark:divide-dark-600">
                                @foreach ($this->groupedPermissions as $module => $permissions)
                                    <div>
                                        {{-- Module Header --}}
                                        <div
                                            class="bg-gray-50 dark:bg-dark-700 px-6 py-3 flex items-center justify-between sticky top-0 z-10">
                                            <div class="flex items-center gap-2">
                                                <x-icon name="folder"
                                                    class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                                <h4 class="font-semibold text-dark-900 dark:text-dark-50">
                                                    {{ $module }}
                                                </h4>
                                                <x-badge :text="$permissions->count() . ' permissions'" color="gray" size="sm" />
                                            </div>

                                            @if ($this->canManagePermissions)
                                                <div class="flex gap-2">
                                                    <x-button wire:click="selectModule('{{ $module }}')"
                                                        color="green" size="sm" flat icon="check">
                                                        All
                                                    </x-button>
                                                    <x-button wire:click="deselectModule('{{ $module }}')"
                                                        color="red" size="sm" flat icon="x-mark">
                                                        None
                                                    </x-button>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Permissions List --}}
                                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach ($permissions as $permission)
                                                <div class="relative group">
                                                    <label
                                                        class="flex items-center justify-between p-3 rounded-lg border transition-colors {{ in_array($permission->id, $selectedPermissions) ? 'border-primary-300 dark:border-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-dark-600 hover:border-primary-400 dark:hover:border-primary-500' }} {{ !$this->canManagePermissions ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                                                        <span
                                                            class="text-sm flex-1 {{ in_array($permission->id, $selectedPermissions) ? 'text-primary-700 dark:text-primary-300 font-medium' : 'text-dark-700 dark:text-dark-300' }}">
                                                            {{ $permission->name }}
                                                        </span>
                                                        <input type="checkbox"
                                                            @if ($this->canManagePermissions) wire:click="togglePermission({{ $permission->id }})" @endif
                                                            @checked(in_array($permission->id, $selectedPermissions)) @disabled(!$this->canManagePermissions)
                                                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-dark-800 focus:ring-2 dark:bg-dark-700 dark:border-dark-600 {{ !$this->canManagePermissions ? 'cursor-not-allowed' : '' }}">
                                                    </label>

                                                    {{-- Delete Button (only visible on hover and if user can manage permissions) --}}
                                                    @if ($this->canManagePermissions)
                                                        <div
                                                            class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                            <livewire:permissions.delete :permission="$permission"
                                                                :key="'delete-permission-' . $permission->id" @deleted="$refresh" />
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- No Role Selected State --}}
                <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 flex items-center justify-center"
                    :style="`height: ${panelHeight}px`">
                    <div class="text-center p-8">
                        <div
                            class="h-16 w-16 bg-gray-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-icon name="arrow-left" class="w-8 h-8 text-gray-400 dark:text-dark-500" />
                        </div>
                        <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">
                            Select a role
                        </h3>
                        <p class="text-dark-500 dark:text-dark-400">
                            Choose a role from the sidebar to manage its permissions
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <livewire:roles.update @updated="$refresh" />
</div>
