<div>
    <x-modal wire="modal" size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="pencil-square" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $user ? __('pages.edit_user_title', ['name' => $user->name]) : __('pages.edit_user_fallback') }}
                    </h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.user_management_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        @if ($user)
            <form id="user-edit" wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Left Column: Personal Info --}}
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.basic_info') }}</h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.user_full_name') }}</p>
                        </div>
                        <x-input :label="__('pages.user_full_name')" wire:model="name" required />
                        <x-input :label="__('pages.user_email')" type="email" wire:model="email" required />
                        <x-input :label="__('pages.user_phone')" wire:model="phone_number" />
                    </div>

                    {{-- Right Column: Role, Status & Password --}}
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.user_col_role') }} & {{ __('common.settings') }}</h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.assign_role') }}</p>
                        </div>
                        <x-select.styled :label="__('pages.user_role')" wire:model="role" :options="$this->roles" required />
                        <x-select.native :label="__('pages.user_status')" wire:model="status" :options="[
                            ['label' => __('pages.user_status_active'), 'value' => 'active'],
                            ['label' => __('pages.user_status_inactive'), 'value' => 'inactive'],
                        ]" required />
                        <x-password :label="__('pages.user_new_password')" wire:model="password"
                            :hint="__('pages.user_password_hint')" />
                        <x-password :label="__('pages.user_confirm_password')" wire:model="password_confirmation" />
                    </div>
                </div>
            </form>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)" color="zinc"
                        class="w-full sm:w-auto order-2 sm:order-1">
                        {{ __('common.cancel') }}
                    </x-button>
                    <x-button type="submit" form="user-edit" color="blue" icon="check" loading="save"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('common.save') }}
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
