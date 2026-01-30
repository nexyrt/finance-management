{{-- resources/views/livewire/clients/edit.blade.php --}}

<x-modal wire="clientEditModal" title="{{ __('pages.edit_client') }}" size="2xl" center>
    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="{{ __('common.name') }}" wire:model="name" icon="user" required />
            <x-select.styled label="{{ __('common.type') }}" wire:model="type" :options="[
                ['label' => __('pages.individual'), 'value' => 'individual'],
                ['label' => __('pages.company'), 'value' => 'company'],
            ]" required />
        </div>

        <!-- Contact Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="{{ __('common.email') }}" wire:model="email" type="email" icon="envelope" />
            <x-input label="{{ __('pages.tax_id') }}" wire:model="NPWP" icon="identification" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select.styled label="{{ __('common.status') }}" wire:model="status" :options="[['label' => __('common.active'), 'value' => 'Active'], ['label' => __('common.inactive'), 'value' => 'Inactive']]" required />
            <x-input label="{{ __('pages.account_representative') }}" wire:model="account_representative" icon="user-circle" />
        </div>

        <x-input label="{{ __('common.address') }}" wire:model="address" icon="map-pin" />
    </div>

    <x-slot:footer>
        <x-button wire:click="$toggle('clientEditModal')" color="secondary">{{ __('common.cancel') }}</x-button>
        <x-button wire:click="save" color="primary" spinner="save">{{ __('pages.update') }}</x-button>
    </x-slot:footer>
</x-modal>
