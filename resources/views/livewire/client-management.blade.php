<div 
    x-data="{
        selectAll: @entangle('selectAll').live,
        selectedClients: @entangle('selectedClients').live,
        
        toggleAll() {
            this.selectAll = !this.selectAll;
            $wire.call('selectAllClients', this.selectAll);
        },
        
        toggleClient(clientId, event) {
            clientId = parseInt(clientId);
            
            if (event.target.checked) {
                if (!this.selectedClients.includes(clientId)) {
                    this.selectedClients.push(clientId);
                }
            } else {
                this.selectedClients = this.selectedClients.filter(id => id !== clientId);
                this.selectAll = false;
            }
            
            $wire.call('selectClient', clientId, event.target.checked);
        },
        
        deselectAll() {
            this.selectedClients = [];
            this.selectAll = false;
            $wire.call('selectAllClients', false);
        },
        
        isSelected(clientId) {
            return this.selectedClients.includes(parseInt(clientId));
        }
    }" 
    class="py-8" 
    @clientPageChanged="$wire.call('refreshClientModals')" 
    x-init="$watch('selectedClients', value => {
        if (value.length === 0) {
            selectAll = false;
        }
    })"
>
    {{-- Header section with search and filters --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 px-4">
        <h1 class="text-2xl font-bold text-zinc-100 mb-4 md:mb-0">Client Management</h1>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search clients..."
                    class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="w-full sm:w-44">
                <x-inputs.select 
                    wire:model.live="type" 
                    :options="[
                        ['value' => '', 'label' => 'All Types'],
                        ['value' => 'individual', 'label' => 'Individual'],
                        ['value' => 'company', 'label' => 'Company'],
                    ]" 
                    placeholder="Filter by type" 
                />
            </div>

            <div class="w-full sm:w-44">
                <x-inputs.select 
                    wire:model.live="perPage" 
                    :options="[
                        ['value' => 10, 'label' => '10 per page'],
                        ['value' => 25, 'label' => '25 per page'],
                        ['value' => 50, 'label' => '50 per page'],
                        ['value' => 100, 'label' => '100 per page'],
                        ['value' => 'All', 'label' => 'All clients'],
                    ]" 
                    placeholder="Records per page" 
                />
            </div>

            <flux:modal.trigger name="create-client" wire:click="prepareCreate">
                <x-shared.button icon="M12 4v16m8-8H4" variant="primary">
                    New Client
                </x-shared.button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- Flash Messages --}}
    @include('partials.client-management.flash-messages')

    {{-- Bulk Actions Toolbar --}}
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

    {{-- Clients Table --}}
    @include('partials.client-management.clients-table')

    {{-- Modals --}}
    @include('partials.client-management.modals.bulk-delete')
    @include('partials.client-management.modals.create-client')
    @include('partials.client-management.modals.edit-client')
    @include('partials.client-management.modals.company-selector')
    @include('partials.client-management.modals.owner-selector')
    @include('partials.client-management.modals.delete-client')
    @include('partials.client-management.modals.view-client')
</div>