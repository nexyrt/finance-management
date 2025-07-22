{{-- resources/views/livewire/clients/edit.blade.php --}}

<x-modal wire="showModal" title="Edit Client" size="2xl">
    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Name" wire:model="name" icon="user" required />
            <x-select.styled label="Type" wire:model="type" :options="[
                ['label' => 'Individual', 'value' => 'individual'],
                ['label' => 'Company', 'value' => 'company'],
            ]" required />
        </div>

        <!-- Contact Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Email" wire:model="email" type="email" icon="envelope" />
            <x-input label="NPWP" wire:model="NPWP" icon="identification" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select.styled label="Status" wire:model="status" :options="[['label' => 'Active', 'value' => 'Active'], ['label' => 'Inactive', 'value' => 'Inactive']]" required />
            <x-input label="Account Representative" wire:model="account_representative" icon="user-circle" />
        </div>

        <x-input label="Address" wire:model="address" icon="map-pin" />
    </div>

    <x-slot:footer>
        <x-button wire:click="close" color="secondary">Cancel</x-button>
        <x-button wire:click="save" color="primary" spinner="save">Update</x-button>
    </x-slot:footer>
</x-modal>
