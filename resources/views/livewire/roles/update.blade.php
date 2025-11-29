<div>
    {{-- Modal --}}
    <x-modal wire size="2xl" center persistent>
        {{-- Custom Title --}}
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="pencil" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Edit Role</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">
                        Update role: <span class="font-semibold">{{ $originalName ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </x-slot:title>

        {{-- Form --}}
        <form id="role-update" wire:submit="save" class="space-y-6">
            {{-- Section: Basic Information --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Basic Information</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Role name and identifier</p>
                </div>

                <x-input wire:model="name" label="Role Name *" placeholder="e.g., Project Manager"
                    hint="Role name will be stored in lowercase" />
            </div>

            {{-- Section: Icon Selection --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Icon Selection</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Choose an icon to represent this role</p>
                </div>

                {{-- Selected Icon Preview --}}
                <div
                    class="flex items-center gap-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-700">
                    <div
                        class="h-16 w-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <x-icon :name="$icon" class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">Selected Icon</div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ $icon }}</div>
                    </div>
                </div>

                {{-- Icon Grid --}}
                <div class="max-h-96 overflow-y-auto rounded-lg border border-dark-200 dark:border-dark-600 p-4">
                    <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2">
                        @foreach ($availableIcons as $iconName)
                            <button type="button" wire:click="selectIcon('{{ $iconName }}')"
                                class="group relative aspect-square p-3 rounded-lg border transition-all {{ $icon === $iconName ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30' : 'border-gray-200 dark:border-dark-600 hover:border-primary-400 dark:hover:border-primary-500 hover:bg-gray-50 dark:hover:bg-dark-700' }}"
                                title="{{ $iconName }}">
                                <x-icon :name="$iconName"
                                    class="w-full h-full transition-colors {{ $icon === $iconName ? 'text-primary-600 dark:text-primary-400' : 'text-dark-600 dark:text-dark-400 group-hover:text-primary-600 dark:group-hover:text-primary-400' }}" />

                                {{-- Checkmark for selected icon --}}
                                @if ($icon === $iconName)
                                    <div
                                        class="absolute -top-1 -right-1 h-5 w-5 bg-primary-600 rounded-full flex items-center justify-center shadow-lg">
                                        <x-icon name="check" class="w-3 h-3 text-white" />
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Helper Text --}}
                <p class="text-xs text-dark-500 dark:text-dark-400 flex items-center gap-2">
                    <x-icon name="information-circle" class="w-4 h-4" />
                    Click on an icon to select it for this role
                </p>
            </div>
        </form>

        {{-- Footer --}}
        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Cancel
                </x-button>
                <x-button type="submit" form="role-update" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Update Role
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
