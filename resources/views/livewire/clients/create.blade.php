{{-- resources/views/livewire/clients/create.blade.php --}}

<x-modal wire="showModal" title="Create New Client" size="2xl">
    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                Basic Information
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Client Name *" wire:model="name" icon="user" required 
                         placeholder="Enter client name" />
                <x-select.styled label="Client Type *" wire:model="type" :options="[
                    ['label' => 'Individual', 'value' => 'individual'],
                    ['label' => 'Company', 'value' => 'company'],
                ]" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Email" wire:model="email" type="email" icon="envelope" 
                         placeholder="client@example.com" />
                <x-select.styled label="Status *" wire:model="status" :options="[
                    ['label' => 'Active', 'value' => 'Active'],
                    ['label' => 'Inactive', 'value' => 'Inactive'],
                ]" required />
            </div>
        </div>

        <!-- Tax Information -->
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                Tax Information
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-input label="NPWP" wire:model="NPWP" icon="identification" 
                         hint="Nomor Pokok Wajib Pajak" placeholder="01.234.567.8-901.000" />
                <x-input label="KPP" wire:model="KPP" icon="building-office-2" 
                         hint="Kantor Pelayanan Pajak" placeholder="KPP Pratama Jakarta Selatan" />
                <x-input label="EFIN" wire:model="EFIN" icon="document-text" 
                         hint="Electronic Filing Number" placeholder="1234567890123456" />
            </div>
        </div>

        <!-- Contact Information -->
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                Contact Information
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Account Representative" wire:model="account_representative" 
                         icon="user-circle" placeholder="John Doe" />
                <x-input label="AR Phone Number" wire:model="ar_phone_number" 
                         icon="phone" placeholder="+62 812 3456 7890" />
            </div>

            <x-input label="Person in Charge" wire:model="person_in_charge" 
                     icon="user" placeholder="Jane Smith" />

            <x-input label="Address" wire:model="address" icon="map-pin" 
                     hint="Complete business address" 
                     placeholder="Jl. Sudirman No. 123, Jakarta Selatan 12190" />
        </div>
    </div>

    <x-slot:footer>
        <x-button wire:click="close" color="secondary">Cancel</x-button>
        <x-button wire:click="save" color="primary" spinner="save">Create Client</x-button>
    </x-slot:footer>
</x-modal>