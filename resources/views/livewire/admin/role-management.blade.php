<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Role Management</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Manage permissions for each role</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Role List --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Roles</h3>
                <div class="space-y-2">
                    @foreach ($this->roles as $role)
                        <button wire:click="selectRole({{ $role->id }})"
                            class="w-full text-left px-4 py-3 rounded-lg transition-colors {{ $selectedRoleId === $role->id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <div class="flex items-center justify-between">
                                <span class="capitalize">{{ $role->name }}</span>
                                <span
                                    class="text-xs px-2 py-1 rounded-full {{ $selectedRoleId === $role->id ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    {{ $role->permissions->count() }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Permissions Panel --}}
        <div class="lg:col-span-3">
            @if ($this->selectedRole)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    {{-- Header --}}
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                                    {{ $this->selectedRole->name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                   {{ count($permissions) }} of {{ $this->totalPermissions }} permissions granted
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <x-button color="gray" wire:click="revokeAllPermissions" size="sm"
                                    wire:confirm="Remove all permissions from this role?">
                                    Revoke All
                                </x-button>
                                <x-button color="blue" wire:click="grantAllPermissions" size="sm"
                                    wire:confirm="Grant all permissions to this role?">
                                    Grant All
                                </x-button>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Grid --}}
                    <div class="p-6 space-y-6">
                        @foreach ($this->groupedPermissions as $module => $perms)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase mb-3">
                                    {{ ucfirst($module) }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach ($perms as $permission)
                                        <label
                                            class="flex items-center justify-between p-3 rounded-lg border {{ in_array($permission['id'], $permissions) ? 'border-blue-300 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }} cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                                            <span
                                                class="text-sm {{ in_array($permission['id'], $permissions) ? 'text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                                                {{ ucfirst($permission['action']) }}
                                            </span>
                                            <input type="checkbox" wire:click="togglePermission({{ $permission['id'] }})"
                                                {{ in_array($permission['id'], $permissions) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <p class="text-lg font-medium">Select a role to manage permissions</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>