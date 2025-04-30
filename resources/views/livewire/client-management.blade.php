<div class="py-8">
    {{-- Header section with search and filters --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 px-4">
        <h1 class="text-2xl font-bold text-zinc-100 mb-4 md:mb-0">Client Management</h1>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <input
                    type="text"
                    wire:model.debounce.300ms="search"
                    placeholder="Search clients..."
                    class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            
            <div class="w-full sm:w-44">
                <x-inputs.select
                    wire:model="type"
                    :options="[
                        ['value' => '', 'label' => 'All Types'],
                        ['value' => 'individual', 'label' => 'Individual'],
                        ['value' => 'company', 'label' => 'Company']
                    ]"
                    placeholder="Filter by type"
                />
            </div>
            
            <button
                wire:click="openForm"
                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-md text-white font-medium transition-colors duration-150 ease-in-out"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Client
            </button>
        </div>
    </div>
    
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 mx-4 px-4 py-3 bg-green-900/50 border border-green-700 rounded-md text-green-200">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-4 mx-4 px-4 py-3 bg-red-900/50 border border-red-700 rounded-md text-red-200">
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Clients Table --}}
    <div class="bg-zinc-900 border border-zinc-700 rounded-md shadow-sm overflow-hidden mx-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Name</span>
                                <span class="text-zinc-400">
                                    @if ($sortField === 'name')
                                        @if ($sortDirection === 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Contact Information
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Tax ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-zinc-800/60 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-100">{{ $client->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->type === 'individual' ? 'bg-blue-900 text-blue-200' : 'bg-purple-900 text-purple-200' }}">
                                    {{ ucfirst($client->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-300">
                                    @if ($client->email)
                                        <div class="flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $client->email }}</span>
                                        </div>
                                    @endif
                                    @if ($client->phone)
                                        <div class="flex items-center space-x-1 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <span>{{ $client->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                {{ $client->tax_id ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="viewClientDetails({{ $client->id }})" class="text-blue-400 hover:text-blue-300 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="editClient({{ $client->id }})" class="text-amber-400 hover:text-amber-300 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $client->id }})" class="text-red-400 hover:text-red-300 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p>No clients found. Start by adding a new client.</p>
                                    <button
                                        wire:click="openForm"
                                        class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-medium text-xs text-white tracking-wide transition-colors duration-150 ease-in-out"
                                    >
                                        Add First Client
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="border-t border-zinc-700 px-4 py-3">
            {{ $clients->links() }}
        </div>
    </div>
    
    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteConfirmation)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900 text-red-200 sm:mx-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <h3 class="text-lg font-medium text-zinc-100" id="modal-title">
                                Delete Client
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-zinc-300">
                                    Are you sure you want to delete this client? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-center gap-3">
                        <button
                            type="button"
                            wire:click="cancelDelete"
                            class="inline-flex justify-center px-4 py-2 bg-zinc-700 hover:bg-zinc-600 border border-zinc-600 rounded-md text-zinc-200 text-sm font-medium transition-colors duration-150 ease-in-out"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            wire:click="deleteClient"
                            class="inline-flex justify-center px-4 py-2 bg-red-600 hover:bg-red-700 border border-transparent rounded-md text-white text-sm font-medium transition-colors duration-150 ease-in-out"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Add/Edit Client Form Modal --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="saveClient">
                        <div class="px-6 py-5 border-b border-zinc-700">
                            <h3 class="text-lg font-medium text-zinc-100">
                                {{ $isEdit ? 'Edit Client' : 'Add New Client' }}
                            </h3>
                        </div>
                        
                        <div class="px-6 py-4 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-zinc-300 mb-1">Client Name <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        wire:model="name" 
                                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter client name"
                                    >
                                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-1">Client Type <span class="text-red-500">*</span></label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input 
                                                type="radio" 
                                                wire:model="clientType" 
                                                value="individual" 
                                                class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                            >
                                            <span class="ml-2 text-zinc-300">Individual</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input 
                                                type="radio" 
                                                wire:model="clientType" 
                                                value="company" 
                                                class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                            >
                                            <span class="ml-2 text-zinc-300">Company</span>
                                        </label>
                                    </div>
                                    @error('clientType') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">Email</label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        wire:model="email" 
                                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter email address"
                                    >
                                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-zinc-300 mb-1">Phone Number</label>
                                    <input 
                                        type="text" 
                                        id="phone" 
                                        wire:model="phone" 
                                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter phone number"
                                    >
                                    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="taxId" class="block text-sm font-medium text-zinc-300 mb-1">Tax ID</label>
                                    <input 
                                        type="text" 
                                        id="taxId" 
                                        wire:model="taxId" 
                                        class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter tax identification number"
                                    >
                                    @error('taxId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="address" class="block text-sm font-medium text-zinc-300 mb-1">Address</label>
                                <textarea 
                                    id="address" 
                                    wire:model="address" 
                                    rows="3"
                                    class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter client address"
                                ></textarea>
                                @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Relationship section based on client type -->
                            @if ($clientType === 'individual')
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium text-zinc-300">Associated Companies</label>
                                        <button 
                                            type="button" 
                                            wire:click="openCompanyModal"
                                            class="inline-flex items-center px-2 py-1 text-xs bg-zinc-800 hover:bg-zinc-700 border border-zinc-600 rounded text-zinc-300 transition-colors duration-150 ease-in-out"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Manage
                                        </button>
                                    </div>
                                    
                                    <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                                        @if (count($selectedCompanies) > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($selectedCompanies as $companyId)
                                                    @php
                                                        $company = \App\Models\Client::find($companyId);
                                                    @endphp
                                                    @if ($company)
                                                        <span class="inline-flex items-center px-2 py-1 bg-indigo-900/50 border border-indigo-700 rounded text-indigo-200 text-xs">
                                                            {{ $company->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-zinc-500 text-sm">No companies associated. Click "Manage" to add.</p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium text-zinc-300">Individual Owners</label>
                                        <button 
                                            type="button" 
                                            wire:click="openOwnerModal"
                                            class="inline-flex items-center px-2 py-1 text-xs bg-zinc-800 hover:bg-zinc-700 border border-zinc-600 rounded text-zinc-300 transition-colors duration-150 ease-in-out"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Manage
                                        </button>
                                    </div>
                                    
                                    <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                                        @if (count($selectedOwners) > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($selectedOwners as $ownerId)
                                                    @php
                                                        $owner = \App\Models\Client::find($ownerId);
                                                    @endphp
                                                    @if ($owner)
                                                        <span class="inline-flex items-center px-2 py-1 bg-blue-900/50 border border-blue-700 rounded text-blue-200 text-xs">
                                                            {{ $owner->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-zinc-500 text-sm">No individual owners associated. Click "Manage" to add.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-end">
                            <button
                                type="button"
                                wire:click="closeForm"
                                class="inline-flex items-center px-4 py-2 bg-zinc-700 hover:bg-zinc-600 border border-zinc-600 rounded-md text-zinc-200 text-sm font-medium transition-colors duration-150 ease-in-out mr-3"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md text-white text-sm font-medium transition-colors duration-150 ease-in-out"
                            >
                                {{ $isEdit ? 'Update Client' : 'Create Client' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Company Selection Modal --}}
    @if ($showCompanyModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-5 border-b border-zinc-700">
                        <h3 class="text-lg font-medium text-zinc-100">
                            Select Companies
                        </h3>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="mb-4">
                            <div class="relative">
                                <input
                                    type="text"
                                    wire:model.debounce.300ms="companySearch"
                                    placeholder="Search companies..."
                                    class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="max-h-60 overflow-y-auto">
                            @if (count($availableCompanies) > 0)
                                <ul class="divide-y divide-zinc-700">
                                    @foreach ($availableCompanies as $company)
                                        <li class="py-2">
                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    value="{{ $company['id'] }}"
                                                    wire:click="toggleCompany({{ $company['id'] }})"
                                                    {{ in_array($company['id'], $selectedCompanies) ? 'checked' : '' }}
                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                >
                                                <span class="text-zinc-200">{{ $company['name'] }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="py-6 text-center text-zinc-400">
                                    <p>No companies found. Try a different search term.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-end">
                        <button
                            type="button"
                            wire:click="closeCompanyModal"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md text-white text-sm font-medium transition-colors duration-150 ease-in-out"
                        >
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Owner Selection Modal --}}
    @if ($showOwnerModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-5 border-b border-zinc-700">
                        <h3 class="text-lg font-medium text-zinc-100">
                            Select Individual Owners
                        </h3>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="mb-4">
                            <div class="relative">
                                <input
                                    type="text"
                                    wire:model.debounce.300ms="ownerSearch"
                                    placeholder="Search individuals..."
                                    class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="max-h-60 overflow-y-auto">
                            @if (count($availableOwners) > 0)
                                <ul class="divide-y divide-zinc-700">
                                    @foreach ($availableOwners as $owner)
                                        <li class="py-2">
                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    value="{{ $owner['id'] }}"
                                                    wire:click="toggleOwner({{ $owner['id'] }})"
                                                    {{ in_array($owner['id'], $selectedOwners) ? 'checked' : '' }}
                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                >
                                                <span class="text-zinc-200">{{ $owner['name'] }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="py-6 text-center text-zinc-400">
                                    <p>No individuals found. Try a different search term.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-end">
                        <button
                            type="button"
                            wire:click="closeOwnerModal"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md text-white text-sm font-medium transition-colors duration-150 ease-in-out"
                        >
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Client Details Modal --}}
    @if ($showDetail && $viewingClient)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="px-6 py-4 border-b border-zinc-700 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-zinc-100 flex items-center space-x-2">
                            <span>Client Details:</span>
                            <span class="font-bold text-indigo-400">{{ $viewingClient->name }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $viewingClient->type === 'individual' ? 'bg-blue-900 text-blue-200' : 'bg-purple-900 text-purple-200' }}">
                                {{ ucfirst($viewingClient->type) }}
                            </span>
                        </h3>
                        <button
                            type="button"
                            wire:click="closeDetailView"
                            class="text-zinc-400 hover:text-white transition-colors duration-150 ease-in-out"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="p-6 overflow-y-auto max-h-[calc(100vh-200px)]">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Basic Information -->
                            <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                                <div class="bg-zinc-700 px-4 py-2">
                                    <h4 class="font-medium text-zinc-100">Contact Information</h4>
                                </div>
                                <div class="p-4 space-y-3">
                                    @if ($viewingClient->email)
                                        <div>
                                            <div class="text-xs text-zinc-400">Email</div>
                                            <div class="text-zinc-200">{{ $viewingClient->email }}</div>
                                        </div>
                                    @endif
                                    
                                    @if ($viewingClient->phone)
                                        <div>
                                            <div class="text-xs text-zinc-400">Phone</div>
                                            <div class="text-zinc-200">{{ $viewingClient->phone }}</div>
                                        </div>
                                    @endif
                                    
                                    @if ($viewingClient->address)
                                        <div>
                                            <div class="text-xs text-zinc-400">Address</div>
                                            <div class="text-zinc-200">{{ $viewingClient->address }}</div>
                                        </div>
                                    @endif
                                    
                                    @if ($viewingClient->tax_id)
                                        <div>
                                            <div class="text-xs text-zinc-400">Tax ID</div>
                                            <div class="text-zinc-200">{{ $viewingClient->tax_id }}</div>
                                        </div>
                                    @endif
                                    
                                    @if (!$viewingClient->email && !$viewingClient->phone && !$viewingClient->address && !$viewingClient->tax_id)
                                        <div class="text-zinc-500 italic">No contact information available</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Relationships -->
                            <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                                <div class="bg-zinc-700 px-4 py-2">
                                    <h4 class="font-medium text-zinc-100">
                                        {{ $viewingClient->type === 'individual' ? 'Associated Companies' : 'Individual Owners' }}
                                    </h4>
                                </div>
                                <div class="p-4">
                                    @if ($viewingClient->type === 'individual')
                                        @if ($viewingClient->ownedCompanies->count() > 0)
                                            <div class="space-y-2">
                                                @foreach ($viewingClient->ownedCompanies as $company)
                                                    <div class="flex items-center space-x-2 p-2 bg-zinc-700/50 rounded-md">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        <span class="text-zinc-200">{{ $company->name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-zinc-500 italic">No associated companies</div>
                                        @endif
                                    @else
                                        @if ($viewingClient->owners->count() > 0)
                                            <div class="space-y-2">
                                                @foreach ($viewingClient->owners as $owner)
                                                    <div class="flex items-center space-x-2 p-2 bg-zinc-700/50 rounded-md">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        <span class="text-zinc-200">{{ $owner->name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-zinc-500 italic">No individual owners</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Invoice Summary -->
                            <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                                <div class="bg-zinc-700 px-4 py-2">
                                    <h4 class="font-medium text-zinc-100">Invoice Summary</h4>
                                </div>
                                <div class="p-4">
                                    @if ($viewingClient->invoices->count() > 0)
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                                <div class="text-lg font-bold text-zinc-100">{{ $viewingClient->invoices->count() }}</div>
                                                <div class="text-xs text-zinc-400">Total Invoices</div>
                                            </div>
                                            
                                            <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                                <div class="text-lg font-bold text-green-400">
                                                    {{ $viewingClient->invoices->where('status', 'paid')->count() }}
                                                </div>
                                                <div class="text-xs text-zinc-400">Paid</div>
                                            </div>
                                            
                                            <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                                <div class="text-lg font-bold text-amber-400">
                                                    {{ $viewingClient->invoices->where('status', 'partially_paid')->count() }}
                                                </div>
                                                <div class="text-xs text-zinc-400">Partially Paid</div>
                                            </div>
                                            
                                            <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                                <div class="text-lg font-bold text-red-400">
                                                    {{ $viewingClient->invoices->where('status', 'overdue')->count() }}
                                                </div>
                                                <div class="text-xs text-zinc-400">Overdue</div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <div class="text-xs text-zinc-400 mb-2">Recent Invoices</div>
                                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                                @foreach ($viewingClient->invoices->sortByDesc('issue_date')->take(5) as $invoice)
                                                    <div class="flex justify-between items-center p-2 bg-zinc-700/30 rounded-md">
                                                        <div class="flex items-center space-x-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                            <span class="text-sm text-zinc-200">{{ $invoice->invoice_number }}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <span class="text-sm text-zinc-300 mr-2">{{ $invoice->total_amount }}</span>
                                                            <span class="px-2 py-0.5 text-xs rounded-full 
                                                                {{ $invoice->status === 'paid' ? 'bg-green-900 text-green-200' : 
                                                                   ($invoice->status === 'partially_paid' ? 'bg-amber-900 text-amber-200' : 
                                                                   ($invoice->status === 'overdue' ? 'bg-red-900 text-red-200' : 'bg-blue-900 text-blue-200')) }}">
                                                                {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-zinc-500 italic">No invoices found for this client</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Services Section -->
                        <div class="mt-6">
                            <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                                <div class="bg-zinc-700 px-4 py-2">
                                    <h4 class="font-medium text-zinc-100">Services History</h4>
                                </div>
                                <div class="p-4">
                                    @if ($viewingClient->serviceClients->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-zinc-700">
                                                <thead>
                                                    <tr>
                                                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Service</th>
                                                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Date</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">Amount</th>
                                                        <th class="px-3 py-2 text-center text-xs font-medium text-zinc-400 uppercase tracking-wider">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-zinc-700">
                                                    @foreach ($viewingClient->serviceClients->sortByDesc('service_date') as $serviceClient)
                                                        <tr class="hover:bg-zinc-700/30">
                                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-zinc-200">
                                                                {{ $serviceClient->service->name }}
                                                            </td>
                                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-zinc-300">
                                                                {{ $serviceClient->service_date->format('M d, Y') }}
                                                            </td>
                                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-zinc-300">
                                                                {{ number_format($serviceClient->amount, 2) }}
                                                            </td>
                                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                                                                @if ($serviceClient->invoiceItems->count() > 0)
                                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-green-900 text-green-200">
                                                                        Invoiced
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-900 text-yellow-200">
                                                                        Not Invoiced
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-zinc-500 italic">No services history found for this client</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-between">
                        <button
                            type="button"
                            wire:click="editClient({{ $viewingClient->id }})"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 border border-transparent rounded-md text-white text-sm font-medium transition-colors duration-150 ease-in-out"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Client
                        </button>
                        <button
                            type="button"
                            wire:click="closeDetailView"
                            class="inline-flex items-center px-4 py-2 bg-zinc-700 hover:bg-zinc-600 border border-zinc-600 rounded-md text-zinc-200 text-sm font-medium transition-colors duration-150 ease-in-out"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>