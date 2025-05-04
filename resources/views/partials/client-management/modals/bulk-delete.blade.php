<flux:modal name="bulk-delete-confirmation">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-red-500">Delete Selected Clients</h2>
        <p class="mt-4 text-zinc-300">
            Are you sure you want to delete all selected clients? This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <x-shared.button variant="secondary">Cancel</x-shared.button>
            </flux:modal.close>

            <flux:modal.close wire:click="bulkDeleteClients">
                <x-shared.button variant="danger">Yes, Delete Clients</x-shared.button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>