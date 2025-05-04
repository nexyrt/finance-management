<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-zinc-100">Client Management</h1>
            <x-shared.button wire:click="create" variant="primary" icon="M12 4v16m8-8H4">
                Add Client
            </x-shared.button>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-inputs.select 
                    wire:model.live="perPage" 
                    :options="[
                        ['value' => '10', 'label' => '10 per page'],
                        ['value' => '25', 'label' => '25 per page'],
                        ['value' => '50', 'label' => '50 per page'],
                        ['value' => '100', 'label' => '100 per page']
                    ]"
                    label="Per Page"
                />
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <x-inputs.select 
                    wire:model.live="typeFilter" 
                    :options="[
                        ['value' => '', 'label' => 'All Types'],
                        ['value' => 'individual', 'label' => 'Individuals'],
                        ['value' => 'company', 'label' => 'Companies']
                    ]"
                    label="Filter by Type"
                />
            </div>
            
            <div class="flex-1 min-w-[300px]">
                <label class="block text-sm font-medium text-zinc-300 mb-1">Search Clients</label>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search by name..." 
                    type="text"
                    class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedClients) > 0)
            <div class="mb-4 p-4 bg-zinc-800 border border-zinc-700 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-300">
                        {{ count($selectedClients) }} client(s) selected
                    </span>
                    <x-shared.button wire:click="confirmDelete" variant="danger" icon="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        Delete Selected
                    </x-shared.button>
                </div>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="w-16 px-6 py-3">
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectAll"
                                    class="w-4 h-4 bg-zinc-900 border-zinc-600 rounded focus:ring-indigo-500"
                                >
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">Related</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-zinc-900 divide-y divide-zinc-700">
                    @forelse($this->clients as $client)
                        <tr class="hover:bg-zinc-800 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    value="{{ $client->id }}"
                                    wire:model.live="selectedClients"
                                    class="w-4 h-4 bg-zinc-900 border-zinc-600 rounded focus:ring-indigo-500"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                {{ $client->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $client->type === 'individual' ? 'bg-green-900 text-green-300' : 'bg-blue-900 text-blue-300' }}">
                                    {{ ucfirst($client->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                {{ $client->email ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                {{ $client->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                @if($client->type === 'individual')
                                    @php
                                        $company = $client->ownedCompanies()->first()?->company;
                                    @endphp
                                    {{ $company ? 'Owner of: ' . $company->name : 'No company' }}
                                @else
                                    @php
                                        $owner = $client->owners()->first()?->owner;
                                    @endphp
                                    {{ $owner ? 'Owned by: ' . $owner->name : 'No owner' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <x-shared.icon-button 
                                        wire:click="edit({{ $client->id }})" 
                                        variant="warning"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </x-shared.icon-button>
                                    
                                    <x-shared.icon-button 
                                        wire:click="manageRelationships({{ $client->id }})" 
                                        variant="info"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                    </x-shared.icon-button>
                                    
                                    <x-shared.icon-button 
                                        wire:click="confirmDelete({{ $client->id }})" 
                                        variant="danger"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </x-shared.icon-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-zinc-400">
                                    No clients found. 
                                    <button wire:click="create" class="text-indigo-400 hover:text-indigo-300">
                                        Create one
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $this->clients->links() }}
        </div>

        <!-- Create/Edit Modal -->
        <flux:modal wire:model="showModal">
            <flux:heading>
                {{ $isEditing ? 'Edit Client' : 'Create Client' }}
            </flux:heading>
            
            <form wire:submit="save">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Name</label>
                        <input 
                            wire:model="form.name" 
                            placeholder="Enter client name"
                            class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        />
                        @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-inputs.select 
                            wire:model="form.type" 
                            :options="[
                                ['value' => 'individual', 'label' => 'Individual'],
                                ['value' => 'company', 'label' => 'Company']
                            ]"
                            label="Type"
                        />
                        @error('form.type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Email</label>
                        <input 
                            wire:model="form.email" 
                            type="email" 
                            placeholder="Enter email address"
                            class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        />
                        @error('form.email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Phone</label>
                        <input 
                            wire:model="form.phone" 
                            placeholder="Enter phone number"
                            class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        />
                        @error('form.phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Address</label>
                        <textarea 
                            wire:model="form.address" 
                            placeholder="Enter address"
                            class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            rows="3"
                        ></textarea>
                        @error('form.address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Tax ID</label>
                        <input 
                            wire:model="form.tax_id" 
                            placeholder="Enter tax ID"
                            class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        />
                        @error('form.tax_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-shared.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                        Cancel
                    </x-shared.button>
                    <x-shared.button type="submit" variant="primary">
                        {{ $isEditing ? 'Update' : 'Create' }}
                    </x-shared.button>
                </div>
            </form>
        </flux:modal>

        <!-- Delete Confirmation Modal -->
        <flux:modal wire:model="showDeleteModal">
            <flux:heading>
                Confirm Deletion
            </flux:heading>
            
            <p class="text-zinc-300">
                Are you sure you want to delete {{ count($selectedClients) }} client(s)? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <x-shared.button variant="secondary" wire:click="$set('showDeleteModal', false)">
                    Cancel
                </x-shared.button>
                <x-shared.button wire:click="deleteSelected" variant="danger">
                    Delete
                </x-shared.button>
            </div>
        </flux:modal>

        <!-- Relationship Management Modal -->
        <flux:modal wire:model="ownershipModal">
            <flux:heading>
                Manage Relationships - {{ $ownershipClient?->name }}
            </flux:heading>
            
            @if($ownershipClient)
                <div class="space-y-4">
                    @if($ownershipClient->type === 'individual')
                        <div>
                            <x-inputs.select 
                                wire:model="selectedCompany" 
                                :options="$this->companies"
                                label="Select Company to Own"
                                placeholder="Select a company"
                            />
                        </div>
                    @else
                        <div>
                            <x-inputs.select 
                                wire:model="selectedOwner" 
                                :options="$this->individuals"
                                label="Select Owner"
                                placeholder="Select an owner"
                            />
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-shared.button variant="secondary" wire:click="$set('ownershipModal', false)">
                        Cancel
                    </x-shared.button>
                    <x-shared.button wire:click="saveRelationship" variant="primary">
                        Save
                    </x-shared.button>
                </div>
            @endif
        </flux:modal>
    </div>
</div>