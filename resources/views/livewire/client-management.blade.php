<section class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">Client Management</h1>
                <p class="mt-2 text-zinc-400">Manage your clients and their relationships</p>
            </div>
            <flux:modal.trigger name="client-form">
                <x-shared.button x-on:click="$wire.openCreateModal()" variant="primary" icon="M12 4v16m8-8H4">
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
                    ['value' => '100', 'label' => '100 per page'],
                ]" label="Per Page" />
            </div>

            <div class="w-full sm:w-48">
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
                        class="w-full pl-10 pr-4 py-2.5 bg-zinc-900/50 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                    <svg class="absolute left-3 top-3 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Alpine.js for Client Selection -->
        <div x-data="clientManager()">
            <!-- Bulk Actions -->
            <div x-show="selectedClients.length > 0" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak class="mb-4 p-4 bg-zinc-800/50 border border-zinc-700 rounded-lg backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-300">
                        <span x-text="selectedClients.length"></span> client(s) selected
                    </span>
                    <flux:modal.trigger name="delete-modal" @click="prepareDeleteMultiple">
                        <x-shared.button variant="danger"
                            icon="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            Delete Selected
                        </x-shared.button>
                    </flux:modal.trigger>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-lg border border-zinc-700 shadow-xl bg-zinc-900/50 backdrop-blur-sm">
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
                            <tr class="hover:bg-zinc-800/50 transition-all duration-150">
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
                                                    <span
                                                        class="px-2 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-md">
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
                                                    <span
                                                        class="px-2 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-md">
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
                                        <flux:modal.trigger name="view-modal"
                                            @click="openViewModal({{ $client->id }})">
                                            <x-shared.icon-button variant="info">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </x-shared.icon-button>
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="client-form">
                                            <x-shared.icon-button @click="$wire.openEditModal({{ $client->id }})"
                                                variant="warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </x-shared.icon-button>
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="delete-modal">
                                            <x-shared.icon-button @click="prepareDeleteSingle({{ $client->id }})"
                                                variant="danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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
                                    <div class="text-zinc-400">
                                        No clients found.
                                        <flux:modal.trigger name="client-form">
                                            <button class="text-blue-400 hover:text-blue-300"
                                                @click="$wire.openCreateModal()">
                                                Create one
                                            </button>
                                        </flux:modal.trigger>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $this->clients->links() }}
            </div>

            <!-- Client Form Modal -->
            <flux:modal name="client-form" class="w-full max-w-5xl">
                <form wire:submit="save" class="p-6">
                    <flux:heading class="mb-6">
                        {{ $isEditing ? 'Edit Client' : 'Create New Client' }}
                    </flux:heading>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Name
                                    </label>
                                    <input wire:model="form.name" placeholder="Enter client name"
                                        class="w-full px-4 py-2.5 bg-zinc-900/70 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                                    @error('form.name')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div wire:key="client-type-{{ $isEditing ? $editingClient?->id : 'new' }}">
                                    <x-inputs.select wire:model.live="form.type"
                                        x-on:change="$wire.resetRelationships()" :options="[
                                            ['value' => 'individual', 'label' => 'Individual'],
                                            ['value' => 'company', 'label' => 'Company'],
                                        ]" label="Type"
                                        :selected="$form['type']" />
                                    @error('form.type')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Email
                                    </label>
                                    <input wire:model="form.email" type="email" placeholder="Enter email address"
                                        class="w-full px-4 py-2.5 bg-zinc-900/70 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                                    @error('form.email')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Phone
                                    </label>
                                    <input wire:model="form.phone" placeholder="Enter phone number"
                                        class="w-full px-4 py-2.5 bg-zinc-900/70 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                                    @error('form.phone')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">
                                    Address
                                </label>
                                <textarea wire:model="form.address" placeholder="Enter address"
                                    class="w-full px-4 py-2.5 bg-zinc-900/70 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    rows="3"></textarea>
                                @error('form.address')
                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">
                                    Tax ID
                                </label>
                                <input wire:model="form.tax_id" placeholder="Enter tax ID"
                                    class="w-full px-4 py-2.5 bg-zinc-900/70 border border-zinc-700 rounded-lg text-zinc-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                                @error('form.tax_id')
                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <div class="lg:border-l lg:border-zinc-700 lg:pl-6 h-full">
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-zinc-300 mb-1">
                                        Relationships
                                    </h3>
                                    <p class="text-xs text-zinc-400 mb-6">
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
                                                @change="$wire.toggleRelationship({{ $connection->id }})"
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

                    <div class="mt-8 flex justify-end gap-3 border-t border-zinc-700 pt-6">
                        <flux:modal.close>
                            <x-shared.button type="button" variant="secondary">
                                Cancel
                            </x-shared.button>
                        </flux:modal.close>
                        <flux:modal.close>
                            <x-shared.button type="submit" variant="primary">
                                {{ $isEditing ? 'Update Client' : 'Create Client' }}
                            </x-shared.button>
                        </flux:modal.close>
                    </div>
                </form>
            </flux:modal>

            <!-- View Details Modal -->
            <flux:modal name="view-modal" class="w-full max-w-4xl">
                <div class="p-6" x-data="viewClientModal()" x-on:open-modal.window="handleModalOpen($event)">

                    <template x-if="loading">
                        <div class="flex justify-center items-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        </div>
                    </template>

                    <template x-if="!loading && client">
                        <div>
                            <flux:heading class="mb-6">
                                Client Details
                            </flux:heading>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Name</label>
                                        <p class="mt-1 text-base text-zinc-200" x-text="client.name"></p>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Type</label>
                                        <p class="mt-1">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="client.type === 'individual' ?
                                                    'bg-emerald-900/50 text-emerald-300 border border-emerald-800' :
                                                    'bg-blue-900/50 text-blue-300 border border-blue-800'"
                                                x-text="client.type.charAt(0).toUpperCase() + client.type.slice(1)">
                                            </span>
                                        </p>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Email</label>
                                        <p class="mt-1 text-base text-zinc-200" x-text="client.email || '-'"></p>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Phone</label>
                                        <p class="mt-1 text-base text-zinc-200" x-text="client.phone || '-'"></p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Address</label>
                                        <p class="mt-1 text-base text-zinc-200 whitespace-pre-wrap"
                                            x-text="client.address || '-'"></p>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Tax ID</label>
                                        <p class="mt-1 text-base text-zinc-200" x-text="client.tax_id || '-'"></p>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-zinc-400">Relationships</label>
                                        <div class="mt-2 space-y-2">
                                            <template
                                                x-if="client.type === 'individual' && client.owned_companies && client.owned_companies.length > 0">
                                                <div>
                                                    <p class="text-sm text-zinc-300">Owns:</p>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        <template x-for="company in client.owned_companies"
                                                            :key="company.id">
                                                            <span
                                                                class="px-3 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-md"
                                                                x-text="company.name"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            <template
                                                x-if="client.type === 'company' && client.owners && client.owners.length > 0">
                                                <div>
                                                    <p class="text-sm text-zinc-300">Owned by:</p>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        <template x-for="owner in client.owners"
                                                            :key="owner.id">
                                                            <span
                                                                class="px-3 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-md"
                                                                x-text="owner.name"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            <template
                                                x-if="(client.type === 'individual' && (!client.owned_companies || !client.owned_companies.length)) || (client.type === 'company' && (!client.owners || !client.owners.length))">
                                                <p class="text-sm text-zinc-400">No relationships</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 border-t border-zinc-700 pt-6">
                                <h4 class="text-sm font-medium text-zinc-300 mb-4">Related Invoices</h4>
                                <template x-if="client.invoices && client.invoices.length > 0">
                                    <div class="overflow-hidden rounded-lg border border-zinc-700">
                                        <table class="min-w-full divide-y divide-zinc-700">
                                            <thead class="bg-zinc-800">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                        Invoice #</th>
                                                    <th
                                                        class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                        Amount</th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                        Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-zinc-700">
                                                <template x-for="invoice in client.invoices" :key="invoice.id">
                                                    <tr class="hover:bg-zinc-800/50">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200"
                                                            x-text="invoice.invoice_number"></td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-zinc-200 text-right">
                                                            $<span
                                                                x-text="parseFloat(invoice.total_amount).toFixed(2)"></span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <span class="px-3 py-1 text-xs rounded-full"
                                                                :class="{
                                                                    'bg-green-900/50 text-green-300 border border-green-800': invoice
                                                                        .status === 'paid',
                                                                    'bg-yellow-900/50 text-yellow-300 border border-yellow-800': invoice
                                                                        .status === 'partially_paid',
                                                                    'bg-red-900/50 text-red-300 border border-red-800': invoice
                                                                        .status === 'overdue',
                                                                    'bg-zinc-800 text-zinc-300 border border-zinc-700': [
                                                                        'draft', 'sent'
                                                                    ].includes(invoice.status)
                                                                }"
                                                                x-text="invoice.status.replace('_', ' ').charAt(0).toUpperCase() + invoice.status.slice(1).replace('_', ' ')">
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>

                                <template x-if="!client.invoices || !client.invoices.length">
                                    <p class="text-sm text-zinc-400">No invoices found</p>
                                </template>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <flux:modal.close>
                                    <x-shared.button variant="secondary">Close</x-shared.button>
                                </flux:modal.close>
                            </div>
                        </div>
                    </template>
                </div>
            </flux:modal>

            <!-- Delete Confirmation Modal -->
            <flux:modal name="delete-modal" class="w-full max-w-xl">
                <div class="p-6" x-data="deleteClientModal()" x-on:open-modal.window="handleModalOpen($event)">
                    <flux:heading class="mb-6">
                        Confirm Deletion
                    </flux:heading>

                    <div class="space-y-4">
                        <p class="text-zinc-300">
                            Are you sure you want to delete <span x-text="clientsToDelete.length"></span> client(s)?
                            This action cannot be undone.
                        </p>

                        <template x-if="loading">
                            <div class="flex justify-center items-center py-4">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                            </div>
                        </template>

                        <template x-if="!loading && invoices.length > 0">
                            <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-zinc-200 mb-3">
                                    <span class="text-red-400">Warning:</span> These invoices will also be deleted:
                                </h4>
                                <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-zinc-700">
                                                <th class="text-left text-xs font-medium text-zinc-400 pb-2">Invoice #
                                                </th>
                                                <th class="text-right text-xs font-medium text-zinc-400 pb-2">Amount
                                                </th>
                                                <th class="text-center text-xs font-medium text-zinc-400 pb-2">Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-700">
                                            <template x-for="invoice in invoices" :key="invoice.invoice_number">
                                                <tr>
                                                    <td class="py-2 text-sm text-zinc-300"
                                                        x-text="invoice.invoice_number"></td>
                                                    <td class="py-2 text-sm text-zinc-300 text-right">$<span
                                                            x-text="parseFloat(invoice.total_amount).toFixed(2)"></span>
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
                                                            x-text="invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1)">
                                                        </span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <template x-if="!loading && invoices.length === 0">
                            <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
                                <p class="text-sm text-zinc-300 text-center">
                                    <span class="text-green-400">No invoices</span> will be affected by this deletion.
                                </p>
                            </div>
                        </template>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <flux:modal.close>
                            <x-shared.button variant="secondary">Cancel</x-shared.button>
                        </flux:modal.close>
                        <flux:modal.close>
                            <x-shared.button @click="confirmDelete" variant="danger">
                                Delete
                            </x-shared.button>
                        </flux:modal.close>
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>

    <!-- Flash Messages -->
    <x-shared.flash-message />

    <!-- Alpine.js Scripts -->
    <script>
        // Main client manager
        function clientManager() {
            return {
                selectedClients: [],
                selectAll: false,

                toggleSelectAll() {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"][x-model="selectedClients"]');
                    if (this.selectAll) {
                        this.selectedClients = Array.from(checkboxes).map(cb => cb.value);
                    } else {
                        this.selectedClients = [];
                    }
                },

                updateSelectAll() {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"][x-model="selectedClients"]');
                    this.selectAll = checkboxes.length > 0 && this.selectedClients.length === checkboxes.length;
                },

                prepareDeleteMultiple() {
                    const component = document.querySelector('[x-data="clientManager()"]');
                    const deleteModal = component.querySelector('[x-data="deleteClientModal()"]');
                    if (deleteModal && deleteModal._x_dataStack) {
                        deleteModal._x_dataStack[0].clientsToDelete = [...this.selectedClients];
                    }
                },

                prepareDeleteSingle(clientId) {
                    const component = document.querySelector('[x-data="clientManager()"]');
                    const deleteModal = component.querySelector('[x-data="deleteClientModal()"]');
                    if (deleteModal && deleteModal._x_dataStack) {
                        deleteModal._x_dataStack[0].clientsToDelete = [clientId.toString()];
                    }
                },

                openViewModal(clientId) {
                    console.log('Opening view modal for client:', clientId);
                    // Set the clientId in the viewModal component
                    const component = document.querySelector('[x-data="clientManager()"]');
                    const viewModal = component.querySelector('[x-data="viewClientModal()"]');
                    if (viewModal && viewModal._x_dataStack) {
                        const viewData = viewModal._x_dataStack[0];
                        viewData.clientId = clientId;
                        viewData.loading = true;
                        viewData.client = null;
                        // Load immediately
                        viewData.loadClientDetails();
                    }
                },

                init() {
                    document.addEventListener('clients-deleted', () => {
                        console.log('clients-deleted event received');
                        this.selectedClients = [];
                        this.selectAll = false;
                    });
                }
            }
        }

        // View client modal
        function viewClientModal() {
            return {
                loading: false, // Changed from true to false
                client: null,
                clientId: null,

                // Replace the loadClientDetails method:
                async loadClientDetails() {
                    console.log('Loading client details for:', this.clientId);
                    this.loading = true;
                    try {
                        this.client = await this.$wire.getClientDetails(this.clientId);
                        console.log('Client data loaded:', this.client);
                    } catch (error) {
                        console.error('Error loading client details:', error);
                    }
                    this.loading = false;
                },

                handleModalOpen(event) {
                    console.log('Modal opened:', event.detail.name);
                    console.log('Client ID:', this.clientId);
                    if (event.detail.name === 'view-modal' && this.clientId) {
                        this.loadClientDetails();
                    }
                }
            }
        }

        // Delete client modal
        function deleteClientModal() {
            return {
                loading: false,
                invoices: [],
                clientsToDelete: [],

                // Replace this part in the confirmDelete method:
                async confirmDelete() {
                    if (this.clientsToDelete.length === 1) {
                        await this.$wire.deleteClient(this.clientsToDelete[0]);
                    } else {
                        await this.$wire.deleteMultiple(this.clientsToDelete);
                    }
                },

                // Replace this part in the loadDeletedInvoices method:
                async loadDeletedInvoices() {
                    this.loading = true;
                    try {
                        if (this.clientsToDelete.length > 0) {
                            this.invoices = await this.$wire.getDeletedInvoices(this.clientsToDelete);
                        }
                    } catch (error) {
                        console.error('Error loading invoices:', error);
                    }
                    this.loading = false;
                },

                handleModalOpen(event) {
                    if (event.detail.name === 'delete-modal' && this.clientsToDelete.length > 0) {
                        this.loadDeletedInvoices();
                    }
                },

                init() {
                    this.$root.addEventListener('clients-deleted', () => {
                        const clientManager = this.$root.querySelector('[x-data="clientManager()"]');
                        if (clientManager && clientManager._x_dataStack) {
                            clientManager._x_dataStack[0].selectedClients = [];
                            clientManager._x_dataStack[0].selectAll = false;
                        }
                    });
                }
            }
        }
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
    </style>
</section>