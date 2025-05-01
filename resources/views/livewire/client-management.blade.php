<div class="py-8">
    {{-- Header section with search and filters --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 px-4">
        <h1 class="text-2xl font-bold text-zinc-100 mb-4 md:mb-0">Client Management</h1>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search clients..."
                    class="w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="w-full sm:w-44">
                <x-inputs.select wire:model.live="type" :options="[
                    ['value' => '', 'label' => 'All Types'],
                    ['value' => 'individual', 'label' => 'Individual'],
                    ['value' => 'company', 'label' => 'Company'],
                ]" placeholder="Filter by type" />
            </div>

            <flux:modal.trigger name="create-client" wire:click="prepareCreate">
                <x-shared.button icon="M12 4v16m8-8H4" variant="primary">
                    New Client
                </x-shared.button>
            </flux:modal.trigger>
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
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Name</span>
                                <span class="text-zinc-400">
                                    @if ($sortField === 'name')
                                        @if ($sortDirection === 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Contact Information
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Tax ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
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
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->type === 'individual' ? 'bg-blue-900 text-blue-200' : 'bg-purple-900 text-purple-200' }}">
                                    {{ ucfirst($client->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-zinc-300">
                                    @if ($client->email)
                                        <div class="flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $client->email }}</span>
                                        </div>
                                    @endif
                                    @if ($client->phone)
                                        <div class="flex items-center space-x-1 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
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
                                    <flux:modal.trigger name="view-client-{{ $client->id }}"
                                        wire:click="loadClientDetails({{ $client->id }})">
                                        <x-shared.icon-button variant="info">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </x-shared.icon-button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="edit-client"
                                        wire:click="editClient({{ $client->id }})">
                                        <button
                                            class="text-amber-400 hover:text-amber-300 transition-colors duration-150 ease-in-out">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="delete-client"
                                        wire:click="confirmDelete({{ $client->id }})">
                                        <button
                                            class="text-red-400 hover:text-red-300 transition-colors duration-150 ease-in-out">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3 text-zinc-600"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p>No clients found. Start by adding a new client.</p>
                                    <flux:modal.trigger name="create-client" wire:click="prepareCreate">
                                        <x-shared.button variant="primary" class="mt-3">
                                            Add First Client
                                        </x-shared.button>
                                    </flux:modal.trigger>
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

    {{-- Create/Edit Client Modal --}}
    <flux:modal name="create-client" title="{{ $isEdit ? 'Edit Client' : 'Add New Client' }}" size="lg">
        <h3 class="font-semibold text-lg text-zinc-100 pb-5">{{ $isEdit ? 'Edit Client' : 'Add New Client' }}</h3>
        <form wire:submit.prevent="saveClient">
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <flux:input label="Client Name" wire:model="name" placeholder="Enter client name"
                            :required="true" />
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Updated client type radio buttons for create/edit modals --}}
                    <div x-data="{}" class="w-full">
                        <label class="block text-sm font-medium text-zinc-300 mb-1">Client Type <span
                                class="text-red-500">*</span></label>
                        <div class="client-type-container">
                            <label class="relative inline-flex items-center cursor-pointer client-type-radio">
                                <input type="radio" wire:model.live="clientType" value="individual"
                                    class="sr-only peer">
                                <div
                                    class="w-full px-4 py-2 bg-zinc-800 border peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-zinc-700 rounded-md text-zinc-300 peer-checked:text-white transition-colors duration-150 ease-in-out">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="whitespace-nowrap">Individual</span>
                                    </div>
                                </div>
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer client-type-radio">
                                <input type="radio" wire:model.live="clientType" value="company"
                                    class="sr-only peer">
                                <div
                                    class="w-full px-4 py-2 bg-zinc-800 border peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-zinc-700 rounded-md text-zinc-300 peer-checked:text-white transition-colors duration-150 ease-in-out">
                                    <div class="flex items-center justify-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="whitespace-nowrap">Company</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('clientType')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <flux:input label="Email" type="email" wire:model="email"
                            placeholder="Enter email address" />
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:input label="Phone Number" wire:model="phone" placeholder="Enter phone number" />
                        @error('phone')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <flux:input label="Tax ID" wire:model="taxId"
                            placeholder="Enter tax identification number" />
                        @error('taxId')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <flux:textarea label="Address" wire:model="address" rows="3"
                        placeholder="Enter client address" />
                    @error('address')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Updated relationships section for create/edit modal --}}
                <div x-data="{ clientType: @entangle('clientType').live }">
                    <!-- Associated Companies section (for individual clients) -->
                    <div x-show="clientType === 'individual'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-2">
                            <label class="block text-sm font-medium text-zinc-300">Associated Companies</label>
                            <flux:modal.trigger name="company-selector" wire:click="openCompanySelector">
                                <x-shared.button variant="secondary" size="xs" icon="M12 4v16m8-8H4">
                                    Manage
                                </x-shared.button>
                            </flux:modal.trigger>
                        </div>

                        <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                            @if (count($displayedCompanies) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($displayedCompanies as $company)
                                        <span
                                            class="inline-flex items-center px-2 py-1 bg-indigo-900/50 border border-indigo-700 rounded text-indigo-200 text-xs">
                                            {{ $company['name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-zinc-500 text-sm">No companies associated. Click "Manage" to add.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Individual Owners section (for company clients) -->
                    <div x-show="clientType === 'company'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-2">
                            <label class="block text-sm font-medium text-zinc-300">Individual Owners</label>
                            <flux:modal.trigger name="owner-selector" wire:click="openOwnerSelector">
                                <x-shared.button variant="secondary" size="xs" icon="M12 4v16m8-8H4">
                                    Manage
                                </x-shared.button>
                            </flux:modal.trigger>
                        </div>

                        <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                            @if (count($displayedOwners) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($displayedOwners as $owner)
                                        <span
                                            class="inline-flex items-center px-2 py-1 bg-blue-900/50 border border-blue-700 rounded text-blue-200 text-xs">
                                            {{ $owner['name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-zinc-500 text-sm">No individual owners associated. Click "Manage" to
                                    add.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <flux:modal.close>
                    <x-shared.button variant="secondary" type="button">
                        Cancel
                    </x-shared.button>
                </flux:modal.close>

                <x-shared.button variant="primary" type="submit">
                    {{ $isEdit ? 'Update Client' : 'Create Client' }}
                </x-shared.button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit Client Modal - Using same modal structure but with edit name for better behavior --}}
    <flux:modal name="edit-client" title="Edit Client" size="lg">
        <h3 class="font-semibold text-lg text-zinc-100 pb-5">Edit Client</h3>
        @if ($isEdit && $clientId)
            <form wire:submit.prevent="saveClient">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <flux:input label="Client Name" wire:model="name" placeholder="Enter client name"
                                :required="true" />
                            @error('name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Updated client type radio buttons for create/edit modals --}}
                        <div x-data="{}" class="w-full">
                            <label class="block text-sm font-medium text-zinc-300 mb-1">Client Type <span
                                    class="text-red-500">*</span></label>
                            <div class="client-type-container">
                                <label class="relative inline-flex items-center cursor-pointer client-type-radio">
                                    <input type="radio" wire:model.live="clientType" value="individual"
                                        class="sr-only peer">
                                    <div
                                        class="w-full px-4 py-2 bg-zinc-800 border peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-zinc-700 rounded-md text-zinc-300 peer-checked:text-white transition-colors duration-150 ease-in-out">
                                        <div class="flex items-center justify-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="whitespace-nowrap">Individual</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer client-type-radio">
                                    <input type="radio" wire:model.live="clientType" value="company"
                                        class="sr-only peer">
                                    <div
                                        class="w-full px-4 py-2 bg-zinc-800 border peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-zinc-700 rounded-md text-zinc-300 peer-checked:text-white transition-colors duration-150 ease-in-out">
                                        <div class="flex items-center justify-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="whitespace-nowrap">Company</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('clientType')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <flux:input label="Email" type="email" wire:model="email"
                                placeholder="Enter email address" />
                            @error('email')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <flux:input label="Phone Number" wire:model="phone" placeholder="Enter phone number" />
                            @error('phone')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <flux:input label="Tax ID" wire:model="taxId"
                                placeholder="Enter tax identification number" />
                            @error('taxId')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <flux:textarea label="Address" wire:model="address" rows="3"
                            placeholder="Enter client address" />
                        @error('address')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Updated relationships section for create/edit modal --}}
                    <div x-data="{ clientType: @entangle('clientType').live }">
                        <!-- Associated Companies section (for individual clients) -->
                        <div x-show="clientType === 'individual'"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-2">
                                <label class="block text-sm font-medium text-zinc-300">Associated Companies</label>
                                <flux:modal.trigger name="company-selector" wire:click="openCompanySelector">
                                    <x-shared.button variant="secondary" size="xs" icon="M12 4v16m8-8H4">
                                        Manage
                                    </x-shared.button>
                                </flux:modal.trigger>
                            </div>

                            <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                                @if (count($displayedCompanies) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($displayedCompanies as $company)
                                            <span
                                                class="inline-flex items-center px-2 py-1 bg-indigo-900/50 border border-indigo-700 rounded text-indigo-200 text-xs">
                                                {{ $company['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-zinc-500 text-sm">No companies associated. Click "Manage" to add.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Individual Owners section (for company clients) -->
                        <div x-show="clientType === 'company'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-2">
                                <label class="block text-sm font-medium text-zinc-300">Individual Owners</label>
                                <flux:modal.trigger name="owner-selector" wire:click="openOwnerSelector">
                                    <x-shared.button variant="secondary" size="xs" icon="M12 4v16m8-8H4">
                                        Manage
                                    </x-shared.button>
                                </flux:modal.trigger>
                            </div>

                            <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                                @if (count($displayedOwners) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($displayedOwners as $owner)
                                            <span
                                                class="inline-flex items-center px-2 py-1 bg-blue-900/50 border border-blue-700 rounded text-blue-200 text-xs">
                                                {{ $owner['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-zinc-500 text-sm">No individual owners associated. Click "Manage" to
                                        add.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <flux:modal.close>
                        <x-shared.button variant="secondary" type="button">
                            Cancel
                        </x-shared.button>
                    </flux:modal.close>

                    <flux:modal.close>
                        <x-shared.button variant="primary" type="submit">
                            Update Client
                        </x-shared.button>
                    </flux:modal.close>
                </div>
            </form>
        @else
            <div class="p-16 flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
                <p class="mt-4 text-zinc-400">Loading client data...</p>
            </div>
        @endif

    </flux:modal>


    <flux:modal name="company-selector" title="Select Companies" size="md">
        <div class="mb-4">
            <flux:input wire:model.live.debounce.300ms="companySearch" placeholder="Search companies..." />
        </div>

        <div class="max-h-60 overflow-y-auto">
            @if (count($availableCompanies) > 0)
                <ul class="divide-y divide-zinc-700">
                    @foreach ($availableCompanies as $company)
                        <li class="py-2">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" value="{{ $company['id'] }}"
                                    wire:click="toggleCompany({{ $company['id'] }})"
                                    {{ in_array($company['id'], $selectedCompanies) ? 'checked' : '' }}
                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
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

        <div class="mt-6 flex justify-end">
            <flux:modal.close wire:click="closeCompanySelector">
                <x-shared.button variant="primary">
                    Done
                </x-shared.button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="owner-selector" title="Select Individual Owners" size="md">
        <div class="mb-4">
            <flux:input wire:model.live.debounce.300ms="ownerSearch" placeholder="Search individuals..." />
        </div>

        <div class="max-h-60 overflow-y-auto">
            @if (count($availableOwners) > 0)
                <ul class="divide-y divide-zinc-700">
                    @foreach ($availableOwners as $owner)
                        <li class="py-2">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" value="{{ $owner['id'] }}"
                                    wire:click="toggleOwner({{ $owner['id'] }})"
                                    {{ in_array($owner['id'], $selectedOwners) ? 'checked' : '' }}
                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
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

        <div class="mt-6 flex justify-end">
            <flux:modal.close wire:click="closeOwnerSelector">
                <x-shared.button variant="primary">
                    Done
                </x-shared.button>
            </flux:modal.close>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-client" title="Delete Client" size="md">
        @if ($clientToDelete)
            <div class="flex items-center justify-center mb-4">
                <div
                    class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900 text-red-200 sm:mx-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <div class="text-center">
                @if (isset($hasDependencies) && $hasDependencies)
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
                <flux:modal.close>
                    <x-shared.button variant="secondary">
                        Cancel
                    </x-shared.button>
                </flux:modal.close>

                @if (!isset($hasDependencies) || !$hasDependencies)
                    <x-shared.button wire:click="deleteClient" variant="danger">
                        Delete
                    </x-shared.button>
                @endif
            </div>
        @else
            <div class="p-16 flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
                <p class="mt-4 text-zinc-400">Loading client information...</p>
            </div>
        @endif

    </flux:modal>

    {{-- Client Detail View Modals (Dynamic) --}}
    @foreach ($clients as $client)
        <flux:modal name="view-client-{{ $client->id }}" class="xl:max-w-5xl">
            @if ($viewingClient && $viewingClient->id === $client->id)
                <!-- Enhanced responsive design for the client detail view -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                    <!-- Basic Information -->
                    <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                        <div class="bg-zinc-700 px-4 py-2 flex justify-between items-center">
                            <h4 class="font-medium text-zinc-100">Contact Information</h4>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $viewingClient->type === 'individual' ? 'bg-blue-900 text-blue-200' : 'bg-purple-900 text-purple-200' }} lg:hidden">
                                {{ ucfirst($viewingClient->type) }}
                            </span>
                        </div>
                        <div class="p-4 space-y-3">
                            @if ($viewingClient->email)
                                <div>
                                    <div class="text-xs text-zinc-400">Email</div>
                                    <div class="text-zinc-200 break-all">{{ $viewingClient->email }}</div>
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
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 text-indigo-400 flex-shrink-0" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="text-zinc-200 truncate">{{ $company->name }}</span>
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
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 text-blue-400 flex-shrink-0" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="text-zinc-200 truncate">{{ $owner->name }}</span>
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
                                        <div class="text-lg font-bold text-zinc-100">
                                            {{ $viewingClient->invoices->count() }}</div>
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
                                            <div
                                                class="flex justify-between items-center p-2 bg-zinc-700/30 rounded-md">
                                                <div class="flex items-center space-x-2 min-w-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 text-zinc-400 flex-shrink-0" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span
                                                        class="text-sm text-zinc-200 truncate">{{ $invoice->invoice_number }}</span>
                                                </div>
                                                <div class="flex items-center flex-shrink-0">
                                                    <span
                                                        class="text-sm text-zinc-300 mr-2 hidden sm:inline">{{ $invoice->total_amount }}</span>
                                                    <span
                                                        class="px-2 py-0.5 text-xs rounded-full whitespace-nowrap
                                        {{ $invoice->status === 'paid'
                                            ? 'bg-green-900 text-green-200'
                                            : ($invoice->status === 'partially_paid'
                                                ? 'bg-amber-900 text-amber-200'
                                                : ($invoice->status === 'overdue'
                                                    ? 'bg-red-900 text-red-200'
                                                    : 'bg-blue-900 text-blue-200')) }}">
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

                <!-- Services Section - Responsive table -->
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
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                                    Service</th>
                                                <th
                                                    class="px-3 py-2 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider hidden sm:table-cell">
                                                    Date</th>
                                                <th
                                                    class="px-3 py-2 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                                    Amount</th>
                                                <th
                                                    class="px-3 py-2 text-center text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                                    Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-700">
                                            @foreach ($viewingClient->serviceClients->sortByDesc('service_date') as $serviceClient)
                                                <tr class="hover:bg-zinc-700/30">
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-zinc-200">
                                                        {{ $serviceClient->service->name }}
                                                        <!-- Show date on mobile -->
                                                        <div class="text-xs text-zinc-400 sm:hidden mt-1">
                                                            {{ $serviceClient->service_date->format('M d, Y') }}
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 whitespace-nowrap text-sm text-zinc-300 hidden sm:table-cell">
                                                        {{ $serviceClient->service_date->format('M d, Y') }}
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 whitespace-nowrap text-sm text-right text-zinc-300">
                                                        {{ number_format($serviceClient->amount, 2) }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                                                        @if ($serviceClient->invoiceItems->count() > 0)
                                                            <span
                                                                class="px-2 py-0.5 text-xs rounded-full bg-green-900 text-green-200">
                                                                Invoiced
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 py-0.5 text-xs rounded-full bg-yellow-900 text-yellow-200">
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

                <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3">
                    <flux:modal.trigger name="edit-client" wire:click="editClient({{ $viewingClient->id }})">
                        <x-shared.button variant="warning"
                            icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            class="w-full sm:w-auto">
                            Edit Client
                        </x-shared.button>
                    </flux:modal.trigger>

                    <flux:modal.close>
                        <x-shared.button variant="secondary" class="w-full sm:w-auto">
                            Close
                        </x-shared.button>
                    </flux:modal.close>
                </div>
            @else
                <div class="p-16 flex flex-col items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
                    <p class="mt-4 text-zinc-400">Loading client details...</p>
                </div>
            @endif
        </flux:modal>
    @endforeach
</div>
