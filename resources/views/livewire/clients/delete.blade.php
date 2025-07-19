{{-- resources/views/livewire/client/delete.blade.php --}}

<div>
    <x-dropdown.items text="Delete" icon="trash" wire:click="$set('showDeleteModal', true)" />

    <x-modal wire="showDeleteModal" title="Delete Client">
        <p>Are you sure you want to delete <strong>{{ $client->name }}</strong>?</p>

        <x-slot:footer>
            <x-button wire:click="$set('showDeleteModal', false)" color="secondary">
                Cancel
            </x-button>
            <x-button wire:click="deleteClient" color="red">
                Delete
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
