<flux:modal name="delete-client">
    <div class="p-6" wire:key="delete-modal-{{ $clientToDelete ?? 'none' }}">
        @if($clientToDelete)
            <h2 class="text-xl font-semibold text-red-500">Delete Client</h2>
            <p class="mt-4 text-zinc-300">
                Are you sure you want to delete this client? This action cannot be undone.
            </p>
            
            @if($hasDependencies)
                <div class="mt-4 bg-red-900/40 border border-red-700 p-4 rounded-lg text-zinc-200">
                    <p class="font-medium mb-2 text-red-300">⚠️ Warning: Dependencies Found</p>
                    <p>This client has associated records that must be removed first.</p>
                </div>
            @endif
            
            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close wire:click="clearDeleteConfirmation">
                    <x-shared.button variant="secondary">
                        Cancel
                    </x-shared.button>
                </flux:modal.close>
                
                <flux:modal.close wire:click="deleteClient">
                    <x-shared.button 
                        variant="danger"
                        :disabled="$hasDependencies"
                    >
                        Delete Client
                    </x-shared.button>
                </flux:modal.close>
            </div>
        @else
            <p class="text-center text-zinc-400">No client selected for deletion.</p>
        @endif
    </div>
</flux:modal>