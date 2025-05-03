<flux:modal name="delete-client" title="Delete Client" size="md">
    @if ($clientToDelete)
        <div class="flex items-center justify-center mb-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900 text-red-200 sm:mx-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <div class="text-center">
            @if ($hasDependencies)
                <p class="text-sm text-red-300">
                    This client cannot be deleted because it has dependencies:
                </p>
                <div class="mt-2 text-left bg-zinc-800 rounded-md p-3">
                    <ul class="list-disc pl-5 text-sm text-zinc-300">
                        @if (isset($clientDependencies['serviceClients']) && $clientDependencies['serviceClients'] > 0)
                            <li>{{ $clientDependencies['serviceClients'] }} service(s) associated</li>
                        @endif

                        @if (isset($clientDependencies['invoices']) && $clientDependencies['invoices'] > 0)
                            <li>{{ $clientDependencies['invoices'] }} invoice(s) associated</li>
                        @endif
                    </ul>
                </div>
                <p class="mt-3 text-sm text-zinc-300">
                    Please remove these dependencies before deleting this client.
                </p>
            @else
                <p class="text-sm text-zinc-300">
                    Are you sure you want to delete this client? This action cannot be undone.
                </p>
            @endif
        </div>

        <div class="mt-6 flex justify-center gap-3">
            <flux:modal.close wire:click="clearDeleteConfirmation">
                <x-shared.button variant="secondary">
                    Cancel
                </x-shared.button>
            </flux:modal.close>

            @if (!$hasDependencies)
                <flux:modal.close wire:click="deleteClient">
                    <x-shared.button variant="danger">
                        Delete
                    </x-shared.button>
                </flux:modal.close>
            @endif
        </div>
    @else
        <div class="p-16 flex flex-col items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
            <p class="mt-4 text-zinc-400">Loading client information...</p>
        </div>
    @endif
</flux:modal>