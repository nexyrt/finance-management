{{-- resources/views/livewire/clients/create.blade.php --}}

<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary">
        {{ __('pages.add_client') }}
    </x-button>

    {{-- Modal --}}
    <x-modal wire="modal" size="2xl" center persistent>
        <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="user-plus" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.create_new_client') }}</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.add_new_client_to_system') }}</p>
            </div>
        </div>
    </x-slot:title>

    <form wire:submit="save" class="space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.basic_information') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.client_basic_details') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('pages.client_name') }} *" wire:model="name" icon="user" required
                         placeholder="{{ __('pages.enter_client_name') }}" />
                <x-select.styled label="{{ __('pages.client_type') }} *" wire:model="type" :options="[
                    ['label' => __('pages.individual'), 'value' => 'individual'],
                    ['label' => __('pages.company'), 'value' => 'company'],
                ]" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('common.email') }}" wire:model="email" type="email" icon="envelope"
                         placeholder="client@example.com" />
                <x-select.styled label="{{ __('common.status') }} *" wire:model="status" :options="[
                    ['label' => __('common.active'), 'value' => 'Active'],
                    ['label' => __('common.inactive'), 'value' => 'Inactive'],
                ]" required />
            </div>
        </div>

        <!-- Tax Information -->
        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.tax_information') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.tax_compliance_details') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-input label="{{ __('pages.tax_id') }}" wire:model="NPWP" icon="identification"
                         hint="Nomor Pokok Wajib Pajak" placeholder="01.234.567.8-901.000" />
                <x-input label="{{ __('pages.kpp') }}" wire:model="KPP" icon="building-office-2"
                         hint="{{ __('pages.tax_service_office') }}" placeholder="KPP Pratama Jakarta Selatan" />
                <x-input label="{{ __('pages.efin') }}" wire:model="EFIN" icon="document-text"
                         hint="{{ __('pages.electronic_filing_number') }}" placeholder="1234567890123456" />
            </div>
        </div>

        <!-- Contact Information -->
        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.contact_information') }}</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.contact_person_details') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('pages.account_representative') }}" wire:model="account_representative"
                         icon="user-circle" placeholder="John Doe" />
                <x-input label="{{ __('pages.ar_phone_number') }}" wire:model="ar_phone_number"
                         icon="phone" placeholder="+62 812 3456 7890" />
            </div>

            <x-input label="{{ __('pages.person_in_charge') }}" wire:model="person_in_charge"
                     icon="user" placeholder="Jane Smith" />

            <x-input label="{{ __('common.address') }}" wire:model="address" icon="map-pin"
                     hint="{{ __('pages.complete_business_address') }}"
                     placeholder="Jl. Sudirman No. 123, Jakarta Selatan 12190" />
        </div>
    </form>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="close" color="zinc" class="w-full sm:w-auto order-2 sm:order-1">
                {{ __('common.cancel') }}
            </x-button>
            <x-button type="submit" wire:click="save" color="primary" icon="check" loading="save"
                class="w-full sm:w-auto order-1 sm:order-2">
                {{ __('pages.add_client') }}
            </x-button>
        </div>
    </x-slot:footer>
    </x-modal>
</div>