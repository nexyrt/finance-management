<div 
    x-show="selectedClients.length > 0" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95" 
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200" 
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
    class="mb-6 px-4 py-3 bg-gradient-to-r from-indigo-900 to-blue-900 rounded-lg shadow-lg border border-indigo-700"
    x-cloak
>
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-800 bg-opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                </svg>
            </div>
            <div>
                <span class="text-white font-semibold"><span x-text="selectedClients.length"></span> clients selected</span>
                <button 
                    @click="deselectAll()"
                    class="block text-xs text-blue-200 hover:text-white transition-colors duration-150"
                >
                    Deselect All
                </button>
            </div>
        </div>

        <div class="flex flex-wrap justify-center sm:justify-end gap-2">
            <flux:modal.trigger name="bulk-delete-confirmation">
                <x-shared.button variant="danger" class="w-full sm:w-auto">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </div>
                </x-shared.button>
            </flux:modal.trigger>
            
            {{-- You can add more bulk actions here in the future --}}
        </div>
    </div>
</div>