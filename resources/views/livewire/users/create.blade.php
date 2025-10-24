<div>
    <x-button wire:click="$toggle('modal')" color="blue" icon="plus" class="w-full sm:w-auto">
        Add User
    </x-button>

    <x-modal title="Create New User" wire size="2xl">
        <form id="user-create" wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-input label="Full Name *" wire:model="name" required />
                </div>
                <div>
                    <x-input label="Email *" type="email" wire:model="email" required />
                </div>
                <div>
                    <x-input label="Phone Number" wire:model="phone_number" />
                </div>
                <div>
                    <x-select.styled label="Role *" wire:model="role" :options="$this->roles" required />
                </div>
                <div>
                    <x-select.native label="Status *" wire:model="status" :options="[
                        ['label' => 'Active', 'value' => 'active'],
                        ['label' => 'Inactive', 'value' => 'inactive'],
                    ]" required />
                </div>
                <div>
                    <x-password label="Password *" wire:model="password" required />
                </div>
                <div>
                    <x-password label="Confirm Password *" wire:model="password_confirmation" required />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button color="gray" wire:click="$set('modal', false)">Cancel</x-button>
                <x-button type="submit" form="user-create" color="blue" loading="save" icon="check">
                    Create User
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
