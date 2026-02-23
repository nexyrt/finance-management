<section class="mt-8 space-y-4">
    <div class="border-b border-red-200 dark:border-red-900/50 pb-4">
        <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-1">{{ __('common.delete_account') }}</h3>
        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.delete_account_description') }}</p>
    </div>

    <x-button color="red" wire:click="$set('confirmDeletion', true)">
        {{ __('common.delete_account') }}
    </x-button>

    <x-modal wire="confirmDeletion" size="md" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('common.delete_account') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('common.action_irreversible') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form id="delete-user-form" wire:submit="deleteUser" class="space-y-4">
            <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.delete_account_confirm_message') }}</p>
            <x-input wire:model="password" :label="__('common.password')" type="password" required />
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('confirmDeletion', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="delete-user-form" color="red" icon="trash"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('common.delete_account') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</section>
