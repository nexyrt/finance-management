<div>
    <x-dropdown.items text="Edit" icon="pencil" wire:click="openEditModal" />

    <x-modal wire="showEditModal" title="Edit Client" size="2xl" persistent>
        <form wire:submit="updateClient" class="space-y-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <h4
                    class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Basic Information
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="Name" wire:model="name" icon="user" required clearable />

                    <x-select.styled label="Type" wire:model="type" :options="[
                        ['label' => 'Individual', 'value' => 'individual'],
                        ['label' => 'Company', 'value' => 'company'],
                    ]" required />
                </div>
            </div>

            <!-- Contact Details -->
            <div class="space-y-4">
                <h4
                    class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Contact Details
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="Email" wire:model="email" type="email" icon="envelope" clearable />

                    <x-input label="NPWP" wire:model="NPWP" icon="identification" clearable />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select.styled label="Status" wire:model="status" :options="[
                        ['label' => 'Active', 'value' => 'Active'],
                        ['label' => 'Inactive', 'value' => 'Inactive'],
                    ]" required />

                    <x-input label="Account Representative" wire:model="account_representative" icon="user-circle"
                        clearable />
                </div>

                <x-input label="Address" wire:model="address" icon="map-pin" clearable />
            </div>
        </form>

        <x-slot:footer>
            <x-button wire:click="$set('showEditModal', false)" color="secondary">
                Cancel
            </x-button>
            <x-button wire:click="updateClient" color="primary">
                Update
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
