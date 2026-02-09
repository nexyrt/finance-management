{{-- resources/views/livewire/services/edit.blade.php --}}

<x-modal wire size="2xl" center persistent>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="pencil" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.edit_service') }}</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.edit_service_description') }}</p>
            </div>
        </div>
    </x-slot:title>

    @if ($service)
        <form id="edit-form" wire:submit="save" class="space-y-4">
            <x-input wire:model="name" label="{{ __('pages.service_name') }}"
                     hint="{{ __('pages.service_name_hint') }}"
                     placeholder="{{ __('pages.service_name_placeholder') }}" required />

            <x-select.styled wire:model="type" label="{{ __('common.category') }}" :options="[
                ['label' => translate_text('Perizinan'), 'value' => 'Perizinan'],
                ['label' => translate_text('Administrasi Perpajakan'), 'value' => 'Administrasi Perpajakan'],
                ['label' => translate_text('Digital Marketing'), 'value' => 'Digital Marketing'],
                ['label' => translate_text('Sistem Digital'), 'value' => 'Sistem Digital'],
            ]"
                placeholder="{{ __('pages.select_category') }}" required />

            <x-currency-input wire:model="price" label="{{ __('pages.service_price') }}"
                hint="{{ __('pages.service_price_hint') }}" placeholder="0" prefix="Rp" required />
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="edit-form" color="blue" loading="save" icon="check" class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('common.save') }}
                </x-button>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
