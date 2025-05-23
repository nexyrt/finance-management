<section class="w-full p-6 space-y-6">
    <!-- Enhanced Header with Glass Effect -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                Client Management
            </h1>
            <p class="mt-2 text-zinc-400">Manage your clients and their relationships effortlessly</p>
        </div>
        <flux:modal.trigger name="client-form">
            <x-shared.button @click="$wire.openCreateModal()" variant="primary"
                class="shadow-lg hover:shadow-blue-500/25 transition-shadow w-full sm:w-auto justify-center">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Client
            </x-shared.button>
        </flux:modal.trigger>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="w-full sm:w-48">
            <x-inputs.select wire:model.live="perPage" :options="[
                ['value' => '10', 'label' => '10 per page'],
                ['value' => '25', 'label' => '25 per page'],
                ['value' => '50', 'label' => '50 per page'],
                ['value' => 'all', 'label' => 'All'],
            ]" label="Per Page" />
        </div>

        <div class="w-full sm:w-48 z-40">
            <x-inputs.select wire:model.live="typeFilter" :options="[
                ['value' => '', 'label' => 'All Types'],
                ['value' => 'individual', 'label' => 'Individuals'],
                ['value' => 'company', 'label' => 'Companies'],
            ]" label="Filter by Type" />
        </div>

        <div class="flex-1 min-w-[300px]">
            <label class="block text-sm font-medium text-zinc-300 mb-1">Search Clients</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" placeholder="Search by name..." type="text"
                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-900/70 border border-zinc-700/50 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all placeholder:text-zinc-500" />
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-zinc-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main container with Alpine.js -->
    <div x-data="clientManager()" x-init="initClientManager()" wire:key="client-manager">

        <div x-show="selectedClients.length > 0" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mb-4 p-4 bg-gradient-to-r from-blue-900/20 to-indigo-900/20 border border-blue-700/50 rounded-lg backdrop-blur-sm shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-sm text-zinc-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="selectedClients.length"></span> client(s) selected
                </span>
                <flux:modal.trigger name="delete-modal">
                    <x-shared.button @click="prepareDeleteMultiple()" variant="danger"
                        class="shadow-lg hover:shadow-red-500/25 transition-shadow">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </x-shared.button>
                </flux:modal.trigger>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-auto rounded-lg border border-zinc-700 shadow-xl bg-zinc-900/50 backdrop-blur-sm">
            <table class="min-w-full divide-y divide-zinc-700">
                <thead class="bg-zinc-800/50">
                    <tr>
                        <th class="w-16 px-6 py-4">
                            <div class="flex items-center">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll"
                                    class="w-4 h-4 bg-zinc-900 border-zinc-600 rounded focus:ring-2 focus:ring-offset-0 focus:ring-blue-500 transition-all">
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Name</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Type</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Email</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Phone</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Related</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse($this->clients as $client)
                        <tr class="hover:bg-zinc-800/50 transition-all duration-150" wire:key="{{ $client->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $client->id }}" x-model="selectedClients"
                                    @change="updateSelectAll"
                                    class="w-4 h-4 bg-zinc-900 border-zinc-600 rounded focus:ring-2 focus:ring-offset-0 focus:ring-blue-500 transition-all">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                {{ $client->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $client->type === 'individual' ? 'bg-emerald-900/50 text-emerald-300 border border-emerald-800' : 'bg-blue-900/50 text-blue-300 border border-blue-800' }}">
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
                                @if ($client->type === 'individual')
                                    @php
                                        $companies = $client->ownedCompanies;
                                    @endphp
                                    @if ($companies->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($companies as $company)
                                                <span class="px-2 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-md">
                                                    {{ $company->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-zinc-500">No companies</span>
                                    @endif
                                @else
                                    @php
                                        $owners = $client->owners;
                                    @endphp
                                    @if ($owners->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($owners as $owner)
                                                <span class="px-2 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-md">
                                                    {{ $owner->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-zinc-500">No owners</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex justify-end gap-1">
                                    <!-- View button using Livewire method -->
                                    <flux:modal.trigger name="view-modal">
                                        <x-shared.icon-button wire:click="openViewModal({{ $client->id }})"
                                            variant="info">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </x-shared.icon-button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="client-form">
                                        <x-shared.icon-button @click="$wire.openEditModal({{ $client->id }})"
                                            variant="warning" class="hover:shadow-lg hover:shadow-yellow-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </x-shared.icon-button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="delete-modal">
                                        <x-shared.icon-button @click="prepareDeleteSingle({{ $client->id }})"
                                            variant="danger" class="hover:shadow-lg hover:shadow-red-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </x-shared.icon-button>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-zinc-400 flex flex-col items-center justify-center gap-3">
                                    <svg class="w-12 h-12 text-zinc-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p>No clients found.</p>
                                    <flux:modal.trigger name="client-form">
                                        <button class="text-blue-400 hover:text-blue-300 font-medium"
                                            @click="$wire.openCreateModal()">
                                            Create your first client
                                        </button>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Pagination -->
    <div class="mt-6">
        @if ($this->perPage != -1)
            {{ $this->clients->links() }}
        @else
            <div class="flex justify-between items-center text-sm text-zinc-400">
                <div>Showing all {{ count($this->clients) }} results</div>
            </div>
        @endif
    </div>

    <!-- Client Form Modal -->
    <flux:modal name="client-form" class="w-full max-w-5xl">
        <form wire:submit="save" class="overflow-hidden rounded-xl">
            <h2 class="text-2xl font-bold text-white tracking-tight mb-5">
                {{ $isEditing ? 'Edit Client' : 'Create New Client' }}
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">
                                Name
                            </label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 absolute left-3 top-2.5 text-zinc-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <input wire:model="form.name" placeholder="Enter client name"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                            </div>
                            @error('form.name')
                                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fixed Type Selection -->
                        <div wire:key="client-type-{{ $isEditing ? $editingClient?->id : 'new' }}">
                            <label class="block text-sm font-medium text-zinc-300 mb-2">
                                Type
                            </label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 absolute left-3 top-2.5 text-zinc-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <select wire:model.live="form.type" wire:change="resetRelationships"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="individual">Individual</option>
                                    <option value="company">Company</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('form.type')
                                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 absolute left-3 top-2.5 text-zinc-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <input wire:model="form.email" type="email" placeholder="Enter email address"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                            </div>
                            @error('form.email')
                                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">
                                Phone
                            </label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 absolute left-3 top-2.5 text-zinc-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <input wire:model="form.phone" placeholder="Enter phone number"
                                    class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                            </div>
                            @error('form.phone')
                                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">
                            Address
                        </label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 absolute left-3 top-2.5 text-zinc-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <textarea wire:model="form.address" placeholder="Enter address"
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                rows="3"></textarea>
                        </div>
                        @error('form.address')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">
                            Tax ID
                        </label>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 absolute left-3 top-2.5 text-zinc-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <input wire:model="form.tax_id" placeholder="Enter tax ID"
                                class="w-full pl-10 pr-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        </div>
                        @error('form.tax_id')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <div>
                        <div class="bg-zinc-800/70 rounded-xl border border-zinc-700 p-5 h-full">
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-zinc-200 mb-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-purple-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Relationships
                                </h3>
                                <p class="text-xs text-zinc-400 mb-4">
                                    {{ $form['type'] === 'individual' ? 'Select companies to own' : 'Select company owners' }}
                                </p>
                            </div>

                            <div class="space-y-2 max-h-80 overflow-y-auto custom-scrollbar">
                                @foreach ($this->availableConnections as $connection)
                                    <label
                                        class="flex items-center p-3 bg-zinc-800/50 border rounded-lg cursor-pointer hover:bg-zinc-700/50 transition-all
                                            {{ in_array($connection->id, $this->form['relationships'] ?? []) ? 'border-blue-500 bg-blue-900/20' : 'border-zinc-700' }}">
                                        <input type="checkbox" value="{{ $connection->id }}"
                                            {{ in_array($connection->id, $this->form['relationships'] ?? []) ? 'checked' : '' }}
                                            wire:click="toggleRelationship({{ $connection->id }})"
                                            class="w-4 h-4 mr-3 bg-zinc-900 border-zinc-600 rounded focus:ring-2 focus:ring-offset-0 focus:ring-blue-500">
                                        <span class="text-sm text-zinc-200">{{ $connection->name }}</span>
                                    </label>
                                @endforeach
                                @if ($this->availableConnections->isEmpty())
                                    <div class="p-4 text-center text-zinc-400 text-sm">
                                        No {{ $form['type'] === 'individual' ? 'companies' : 'individuals' }}
                                        available
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-8 py-4 bg-zinc-800 flex justify-end gap-3">
                <flux:modal.close>
                    <button type="button"
                        class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 rounded-lg font-medium text-sm transition-all shadow-sm">
                        Cancel
                    </button>
                </flux:modal.close>
                <flux:modal.close>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-medium text-sm transition-all shadow-sm">
                        {{ $isEditing ? 'Update Client' : 'Create Client' }}
                    </button>
                </flux:modal.close>
            </div>
        </form>
    </flux:modal>

    <!-- View Details Modal (Livewire-based) -->
    <flux:modal name="view-modal" class="w-full max-w-5xl" @close-modal="$wire.clearViewingClient()">
        <div class="p-0 overflow-hidden rounded-xl">
            @if (isset($viewingClient))
                <!-- Header with gradient background -->
                <div class="bg-gradient-to-r from-blue-900 via-indigo-800 to-purple-900 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white tracking-tight">
                                {{ $viewingClient['name'] }}
                            </h2>
                            <div class="flex items-center mt-2 space-x-2">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm
                                    {{ $viewingClient['type'] === 'individual'
                                        ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                                        : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' }}">
                                    {{ ucfirst($viewingClient['type']) }}
                                </span>
                                @if ($viewingClient['email'])
                                    <span class="text-sm text-gray-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 opacity-70"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $viewingClient['email'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <flux:modal.close>
                            <button
                                class="rounded-full p-2 text-gray-300 hover:text-white hover:bg-gray-800/30 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </flux:modal.close>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 bg-zinc-900 max-h-[calc(100vh-16rem)] overflow-y-auto custom-scrollbar">
                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="bg-zinc-800/70 rounded-xl shadow-sm border border-zinc-700 p-5 hover:border-zinc-600 transition-all">
                            <div class="space-y-5">
                                <!-- Contact Information Section -->
                                <div>
                                    <h3 class="text-sm font-medium text-zinc-400 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Contact Information
                                    </h3>
                                    <div class="grid grid-cols-1 gap-3">
                                        @if ($viewingClient['phone'])
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-zinc-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-zinc-200">
                                                        {{ $viewingClient['phone'] }}</p>
                                                    <p class="text-xs text-zinc-500">Phone</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($viewingClient['address'])
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-zinc-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-zinc-200">
                                                        {{ $viewingClient['address'] }}</p>
                                                    <p class="text-xs text-zinc-500">Address</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($viewingClient['tax_id'])
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-zinc-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-zinc-200">
                                                        {{ $viewingClient['tax_id'] }}</p>
                                                    <p class="text-xs text-zinc-500">Tax ID</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-zinc-800/70 rounded-xl shadow-sm border border-zinc-700 p-5 hover:border-zinc-600 transition-all">
                            <div>
                                <h3 class="text-sm font-medium text-zinc-400 mb-3 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-purple-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Relationships
                                </h3>

                                @if (
                                    $viewingClient['type'] === 'individual' &&
                                        isset($viewingClient['owned_companies']) &&
                                        count($viewingClient['owned_companies']) > 0)
                                    <div class="mb-3">
                                        <p class="text-xs text-indigo-400 uppercase tracking-wider mb-2 font-medium">
                                            Owns Companies</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($viewingClient['owned_companies'] as $company)
                                                <span
                                                    class="px-3 py-1 bg-indigo-900/30 text-indigo-300 text-xs rounded-md border border-indigo-800/50">
                                                    {{ $company['name'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($viewingClient['type'] === 'company' && isset($viewingClient['owners']) && count($viewingClient['owners']) > 0)
                                    <div class="mb-3">
                                        <p class="text-xs text-indigo-400 uppercase tracking-wider mb-2 font-medium">
                                            Owned By</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($viewingClient['owners'] as $owner)
                                                <span
                                                    class="px-3 py-1 bg-indigo-900/30 text-indigo-300 text-xs rounded-md border border-indigo-800/50">
                                                    {{ $owner['name'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="py-4 text-center bg-zinc-800/50 rounded-lg border border-zinc-700">
                                        <p class="text-sm text-zinc-400">No relationships found</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Invoices Section -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-medium text-zinc-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Related Invoices
                            </h3>
                            @if (isset($viewingClient['invoices']) && count($viewingClient['invoices']) > 0)
                                <span class="px-3 py-1 bg-zinc-800 rounded-full text-xs text-zinc-400">
                                    {{ count($viewingClient['invoices']) }} invoice(s)
                                </span>
                            @endif
                        </div>

                        @if (isset($viewingClient['invoices']) && count($viewingClient['invoices']) > 0)
                            <div class="overflow-hidden rounded-xl border border-zinc-700">
                                <table class="min-w-full divide-y divide-zinc-700">
                                    <thead class="bg-zinc-800/90">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                Invoice #
                                            </th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                Amount
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-zinc-900/50 divide-y divide-zinc-800">
                                        @foreach ($viewingClient['invoices'] as $invoice)
                                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                                    {{ $invoice['invoice_number'] }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-zinc-200 text-right">
                                                    <span
                                                        class="font-mono">${{ number_format($invoice['total_amount'], 2) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span
                                                        class="px-3 py-1 text-xs rounded-full 
                                                        {{ $invoice['status'] === 'paid'
                                                            ? 'bg-green-900/30 text-green-300 border border-green-800/30'
                                                            : ($invoice['status'] === 'partially_paid'
                                                                ? 'bg-yellow-900/30 text-yellow-300 border border-yellow-800/30'
                                                                : ($invoice['status'] === 'overdue'
                                                                    ? 'bg-red-900/30 text-red-300 border border-red-800/30'
                                                                    : 'bg-zinc-800/70 text-zinc-300 border border-zinc-700/50')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $invoice['status'])) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-16 text-center bg-zinc-800/30 rounded-xl border border-zinc-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-zinc-700 mb-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-zinc-400">No invoices found for this client</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-4 bg-zinc-800 border-t border-zinc-700 flex justify-end">
                    <flux:modal.close>
                        <button
                            class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 rounded-lg font-medium text-sm transition-all shadow-sm">
                            Close
                        </button>
                    </flux:modal.close>
                </div>
            @else
                <div class="flex justify-center items-center py-32 bg-zinc-900">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-4"></div>
                        <p class="text-zinc-400">Loading client information...</p>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal with Enhanced Design -->
    <flux:modal name="delete-modal" class="w-full max-w-xl">
        <div class="p-6" x-data="deleteClientModal()">
            <flux:heading class="mb-6 flex items-center gap-3 text-red-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Confirm Deletion
            </flux:heading>

            <div class="space-y-4">
                <div class="p-4 bg-red-900/20 border border-red-900/50 rounded-lg">
                    <p class="text-zinc-300">
                        Are you sure you want to delete <span x-text="clientsToDelete.length"
                            class="font-bold text-red-400"></span> client(s)?
                        This action cannot be undone.
                    </p>
                </div>

                <div x-show="loading" class="flex justify-center items-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                </div>

                <div x-show="!loading && invoices.length > 0"
                    class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-zinc-200 mb-3">
                        <span class="text-red-400">Warning:</span> These invoices will also be deleted:
                    </h4>
                    <div class="max-h-48 overflow-y-auto custom-scrollbar">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-700">
                                    <th class="text-left text-xs font-medium text-zinc-400 pb-2">Invoice #</th>
                                    <th class="text-right text-xs font-medium text-zinc-400 pb-2">Amount</th>
                                    <th class="text-center text-xs font-medium text-zinc-400 pb-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                <template x-for="invoice in invoices" :key="invoice.id">
                                    <tr>
                                        <td class="py-2 text-sm text-zinc-300" x-text="invoice.invoice_number"></td>
                                        <td class="py-2 text-sm text-zinc-300 text-right">
                                            $<span x-text="parseFloat(invoice.total_amount).toFixed(2)"></span>
                                        </td>
                                        <td class="py-2 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full"
                                                :class="{
                                                    'bg-green-900/50 text-green-300': invoice
                                                        .status === 'paid',
                                                    'bg-yellow-900/50 text-yellow-300': invoice
                                                        .status === 'partially_paid',
                                                    'bg-red-900/50 text-red-300': invoice
                                                        .status === 'overdue',
                                                    'bg-zinc-700 text-zinc-300': ['draft', 'sent'].includes(
                                                        invoice.status)
                                                }"
                                                x-text="invoice.status.replace('_', ' ').charAt(0).toUpperCase() + invoice.status.slice(1).replace('_', ' ')">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="!loading && invoices.length === 0"
                    class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
                    <p class="text-sm text-zinc-300 text-center">
                        <span class="text-green-400">No invoices</span> will be affected by this deletion.
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-zinc-700 pt-6">
                <flux:modal.close>
                    <x-shared.button variant="secondary" class="shadow-lg">Cancel</x-shared.button>
                </flux:modal.close>
                <flux:modal.close>
                    <x-shared.button @click="confirmDelete" variant="danger"
                        class="shadow-lg hover:shadow-red-500/25 transition-shadow">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </x-shared.button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <!-- Flash Messages -->
    <x-shared.flash-message />

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('clientManager', () => ({
                    selectedClients: [],
                    selectAll: false,

                    // Initialize client manager component
                    initClientManager() {
                        // Listen for Livewire events to clear selections
                        document.addEventListener('livewire:initialized', () => {
                            this.selectedClients = [];
                            this.selectAll = false;
                        });

                        document.addEventListener('livewire:updated', () => {
                            this.selectedClients = [];
                            this.selectAll = false;
                        });

                        document.addEventListener('clients-deleted', () => {
                            this.selectedClients = [];
                            this.selectAll = false;
                        });
                    },

                    toggleSelectAll() {
                        const checkboxes = document.querySelectorAll(
                            'input[type="checkbox"][x-model="selectedClients"]');
                        if (this.selectAll) {
                            this.selectedClients = Array.from(checkboxes).map(cb => cb.value);
                        } else {
                            this.selectedClients = [];
                        }
                    },

                    updateSelectAll() {
                        const checkboxes = document.querySelectorAll(
                            'input[type="checkbox"][x-model="selectedClients"]');
                        this.selectAll = checkboxes.length > 0 && this.selectedClients.length === checkboxes
                            .length;
                    },

                    prepareDeleteMultiple() {
                        if (this.selectedClients.length === 0) return;

                        const event = new CustomEvent('prepare-delete', {
                            detail: {
                                clients: [...this.selectedClients]
                            }
                        });
                        window.dispatchEvent(event);
                    },

                    prepareDeleteSingle(clientId) {
                        const event = new CustomEvent('prepare-delete', {
                            detail: {
                                clients: [clientId.toString()]
                            }
                        });
                        window.dispatchEvent(event);
                    }
                }));

                // Delete client modal data
                Alpine.data('deleteClientModal', () => ({
                    loading: false,
                    invoices: [],
                    clientsToDelete: [],

                    init() {
                        this.listenForEvents();
                    },

                    listenForEvents() {
                        window.addEventListener('prepare-delete', (event) => {
                            if (event.detail && event.detail.clients) {
                                this.clientsToDelete = event.detail.clients;
                                this.loadDeletedInvoices();
                            }
                        });

                        // Listen for modal open/close events
                        document.addEventListener('open-modal', (event) => {
                            if (event.detail && event.detail.name === 'delete-modal') {
                                // Make sure we have the latest data when the modal opens
                                this.loadDeletedInvoices();
                            }
                        });

                        document.addEventListener('close-modal', (event) => {
                            if (event.detail && event.detail.name === 'delete-modal') {
                                // Reset state when modal closes
                                this.invoices = [];
                                this.loading = false;
                            }
                        });
                    },

                    async confirmDelete() {
                        try {
                            if (this.clientsToDelete.length === 1) {
                                await this.$wire.deleteClient(this.clientsToDelete[0]);
                            } else if (this.clientsToDelete.length > 1) {
                                await this.$wire.deleteMultiple(this.clientsToDelete);
                            }
                        } catch (error) {
                            console.error('Error deleting clients:', error);
                        }
                    },

                    async loadDeletedInvoices() {
                        this.loading = true;
                        this.invoices = []; // Reset invoices array

                        try {
                            if (this.clientsToDelete && this.clientsToDelete.length > 0) {
                                this.invoices = await this.$wire.getDeletedInvoices(this
                                    .clientsToDelete);
                            }
                        } catch (error) {
                            console.error('Error loading invoices:', error);
                            this.invoices = [];
                        } finally {
                            this.loading = false;
                        }
                    }
                }));
            });

            // Handle modal closing event for view modal
            document.addEventListener('close-modal', (event) => {
                if (event.detail && event.detail.name === 'view-modal') {
                    // Call the Livewire method to clear the viewingClient
                    const livewireComponent = window.Livewire.find(
                        document.querySelector('[wire\\:id]')?.getAttribute('wire:id')
                    );
                    if (livewireComponent) {
                        livewireComponent.clearViewingClient();
                    }
                }
            });
        </script>

        <!-- Custom Scrollbar Styles -->
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #27272a;
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #52525b;
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #71717a;
            }

            /* Fix for ensuring dropdowns have proper z-index */
            .relative {
                position: relative;
            }

            /* Add responsive table styles */
            .overflow-x-auto {
                scrollbar-width: thin;
                scrollbar-color: #52525b #27272a;
            }

            .overflow-x-auto::-webkit-scrollbar {
                height: 6px;
            }

            .overflow-x-auto::-webkit-scrollbar-track {
                background: #27272a;
                border-radius: 3px;
            }

            .overflow-x-auto::-webkit-scrollbar-thumb {
                background: #52525b;
                border-radius: 3px;
            }

            .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                background: #71717a;
            }

            /* Ensure modals appear above everything */
            [name="view-modal"],
            [name="client-form"],
            [name="delete-modal"] {
                z-index: 999;
            }

            /* Make the page more responsive on small screens */
            @media (max-width: 640px) {
                .flex-wrap {
                    margin-bottom: 0.5rem;
                }

                th,
                td {
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
            }
        </style>
    @endpush
</section>
