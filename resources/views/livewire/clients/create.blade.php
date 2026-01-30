{{-- resources/views/livewire/clients/create.blade.php --}}

<x-modal wire="showModal" title="{{ __('pages.create_new_client') }}" size="2xl" center>
    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                {{ __('pages.basic_information') }}
            </h4>

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
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                {{ __('pages.tax_information') }}
            </h4>

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
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                {{ __('pages.contact_information') }}
            </h4>

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
    </div>

    <x-slot:footer>
        <x-button wire:click="close" color="secondary">{{ __('common.cancel') }}</x-button>
        <x-button wire:click="save" color="primary" spinner="save">{{ __('pages.add_client') }}</x-button>
    </x-slot:footer>
</x-modal>