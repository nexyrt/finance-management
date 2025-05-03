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