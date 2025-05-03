<flux:modal name="bulk-delete-confirmation" size="md">
    <div class="text-center">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Confirm Bulk Deletion</h3>

        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                You are about to delete <span class="font-bold text-red-600" x-text="selectedClients.length"></span> selected clients.
                This action cannot be undone.
            </p>

            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg p-4 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700 dark:text-amber-200">
                            Note: Clients with dependencies (services or invoices) will be skipped automatically.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <flux:modal.close>
                <x-shared.button variant="secondary" class="w-full sm:w-auto">
                    <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </div>
                </x-shared.button>
            </flux:modal.close>

            <flux:modal.close wire:click="bulkDeleteClients">
                <x-shared.button variant="danger" class="w-full sm:w-auto">
                    <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Yes, Delete Selected
                    </div>
                </x-shared.button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>