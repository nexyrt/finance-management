<section class="p-6 space-y-6">
    <div class="max-w-7xl mx-auto">
        <!-- Enhanced Header with Glass Effect -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
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

        <!-- Enhanced Filters with Improved Layout -->
        <div class="mb-8 backdrop-blur-md bg-zinc-900/50 rounded-xl border border-zinc-800 p-4 shadow-xl">
            <div class="flex flex-wrap gap-4">
                <div class="w-full sm:w-48 z-50">
                    <x-inputs.select wire:model.live="perPage" @change="$wire.resetPageAfterChange()" :options="[
                        ['value' => '10', 'label' => '10 per page'],
                        ['value' => '25', 'label' => '25 per page'],
                        ['value' => '50', 'label' => '50 per page'],
                        ['value' => '100', 'label' => '100 per page'],
                        ['value' => '-1', 'label' => 'Show all'],
                    ]"
                        label="Per Page" />
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
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-zinc-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main container with Alpine.js -->
        <div x-data="clientManager()" x-init="init()" wire:key="client-manager">

            <div x-show="selectedClients.length > 0" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
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

            <!-- Table Container with z-index protection -->
            <div class="relative z-10">
                <!-- Enhanced Table with Modern Design -->
                <div
                    class="overflow-hidden rounded-xl border border-zinc-800 shadow-2xl bg-zinc-900/50 backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-800">
                            <thead class="bg-gradient-to-r from-zinc-900 to-zinc-900/80">
                                <tr>
                                    <th class="w-16 px-6 py-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll"
                                                class="w-4 h-4 bg-zinc-900 border-zinc-600 rounded focus:ring-2 focus:ring-offset-0 focus:ring-blue-500 transition-all">
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                        Phone</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                        Related</th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800">
                                @forelse($this->clients as $client)
                                    <tr class="hover:bg-zinc-800/40 transition-all duration-150 group"
                                        wire:key="client-{{ $client->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" value="{{ $client->id }}"
                                                x-model="selectedClients" @change="updateSelectAll"
                                                class="w-4 h-4 bg-zinc-900 border-zinc-600 rounded focus:ring-2 focus:ring-offset-0 focus:ring-blue-500 transition-all">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                            {{ $client->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $client->type === 'individual' ? 'bg-emerald-900/30 text-emerald-300 border border-emerald-800/30' : 'bg-blue-900/30 text-blue-300 border border-blue-800/30' }}">
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
                                                                class="px-2 py-1 bg-zinc-800/50 text-zinc-300 text-xs rounded-md border border-zinc-700/50">
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
                                                                class="px-2 py-1 bg-zinc-800/50 text-zinc-300 text-xs rounded-md border border-zinc-700/50">
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
                                            <div
                                                class="flex justify-end gap-1 opacity-50 group-hover:opacity-100 transition-opacity">
                                                <flux:modal.trigger name="view-modal">
                                                    <x-shared.icon-button @click="openViewModal({{ $client->id }})"
                                                        variant="info"
                                                        class="hover:shadow-lg hover:shadow-blue-500/20">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </x-shared.icon-button>
                                                </flux:modal.trigger>

                                                <flux:modal.trigger name="client-form">
                                                    <x-shared.icon-button
                                                        @click="$wire.openEditModal({{ $client->id }})"
                                                        variant="warning"
                                                        class="hover:shadow-lg hover:shadow-yellow-500/20">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </x-shared.icon-button>
                                                </flux:modal.trigger>

                                                <flux:modal.trigger name="delete-modal">
                                                    <x-shared.icon-button
                                                        @click="prepareDeleteSingle({{ $client->id }})"
                                                        variant="danger"
                                                        class="hover:shadow-lg hover:shadow-red-500/20">
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
                                            <div class="text-zinc-400 flex flex-col items-center justify-center gap-3">
                                                <svg class="w-12 h-12 text-zinc-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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

                                <div>
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-300 mb-2">Type</label>
                                        <div x-data="{
                                            type: '{{ $form['type'] }}',
                                            updateType(value) {
                                                this.type = value;
                                                // This tells Livewire to update the model
                                                @this.set('form.type', value);
                                                // This triggers your relationships reset function
                                                @this.resetRelationships();
                                            }
                                        }">
                                            <div class="relative">
                                                <select x-model="type" @change="updateType($event.target.value)"
                                                    class="w-full px-3 py-2 text-left bg-zinc-900 border border-zinc-700 rounded-md shadow-sm hover:border-zinc-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150 ease-in-out text-sm text-zinc-200">
                                                    <option value="individual">Individual</option>
                                                    <option value="company">Company</option>
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-zinc-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        @error('form.type')
                                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
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

            <!-- View Details Modal with Enhanced Design -->
            <flux:modal name="view-modal" class="w-full max-w-4xl">
                <div class="p-6" x-data="viewClientModal()">
                    <template x-if="loading">
                        <div class="flex justify-center items-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        </div>
                    </template>

                    <template x-if="!loading && client">
                        <div>
                            <flux:heading class="mb-6 flex items-center gap-3">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Client Details
                            </flux:heading>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div class="p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
                                        <label class="text-sm font-medium text-zinc-400">Name</label>
                                        <p class="mt-1 text-lg font-medium text-zinc-200" x-text="client.name"></p>
                                    </div>

                                    <div class="p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
                                        <label class="text-sm font-medium text-zinc-400">Type</label>
                                        <p class="mt-1">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="client.type === 'individual' ?
                                                    'bg-emerald-900/30 text-emerald-300 border border-emerald-800/30' :
                                                    'bg-blue-900/30 text-blue-300 border border-blue-800/30'"
                                                x-text="client.type.charAt(0).toUpperCase() + client.type.slice(1)">
                                            </span>
                                        </p>
                                    </div>

                                    <div class="p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
                                        <label class="text-sm font-medium text-zinc-400">Contact Information</label>
                                        <div class="mt-3 space-y-2">
                                            <p class="text-zinc-200 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-zinc-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <span x-text="client.email || 'No email'"></span>
                                            </p>
                                            <p class="text-zinc-200 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-zinc-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <span x-text="client.phone || 'No phone'"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
                                        <label class="text-sm font-medium text-zinc-400">Address</label>
                                        <p class="mt-1 text-base text-zinc-200 whitespace-pre-wrap"
                                            x-text="client.address || 'No address'"></p>
                                    </div>

                                    <div class="p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
                                        <label class="text-sm font-medium text-zinc-400">Tax ID</label>
                                        <p class="mt-1 text-base text-zinc-200" x-text="client.tax_id || 'No tax ID'">
                                        </p>
                                    </div>

                                    <div class="p-6 bg-zinc-800/30 rounded-lg border border-zinc-700/50">
                                        <label class="text-sm font-medium text-zinc-400">Relationships</label>
                                        <div class="mt-3 space-y-2">
                                            <template
                                                x-if="client.type === 'individual' && client.owned_companies && client.owned_companies.length > 0">
                                                <div>
                                                    <p class="text-sm text-zinc-300 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-blue-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        Owns:
                                                    </p>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        <template x-for="company in client.owned_companies"
                                                            :key="company.id">
                                                            <span
                                                                class="px-3 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-full border border-zinc-700/50"
                                                                x-text="company.name"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            <template
                                                x-if="client.type === 'company' && client.owners && client.owners.length > 0">
                                                <div>
                                                    <p class="text-sm text-zinc-300 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-emerald-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        Owned by:
                                                    </p>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        <template x-for="owner in client.owners"
                                                            :key="owner.id">
                                                            <span
                                                                class="px-3 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-full border border-zinc-700/50"
                                                                x-text="owner.name"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            <template
                                                x-if="(client.type === 'individual' && (!client.owned_companies || !client.owned_companies.length)) || (client.type === 'company' && (!client.owners || !client.owners.length))">
                                                <p class="text-sm text-zinc-400 italic">No relationships</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Invoices Table -->
                            <div class="mt-8 border-t border-zinc-700 pt-6">
                                <h4 class="text-sm font-medium text-zinc-300 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Related Invoices
                                </h4>
                                <template x-if="client.invoices && client.invoices.length > 0">
                                    <div class="overflow-hidden rounded-xl border border-zinc-700 shadow-lg">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-zinc-700">
                                                <thead class="bg-gradient-to-r from-zinc-900 to-zinc-900/80">
                                                    <tr>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                            Invoice #</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                            Due Date</th>
                                                        <th
                                                            class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                            Amount</th>
                                                        <th
                                                            class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">
                                                            Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-zinc-700 bg-zinc-800/30">
                                                    <template x-for="invoice in client.invoices"
                                                        :key="invoice.id">
                                                        <tr
                                                            class="hover:bg-zinc-800/70 transition-colors duration-150">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200"
                                                                x-text="invoice.invoice_number"></td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300"
                                                                x-text="invoice.due_date"></td>
                                                            <td
                                                                class="px-6 py-4 whitespace-nowrap text-sm text-zinc-200 text-right font-medium">
                                                                $<span
                                                                    x-text="parseFloat(invoice.total_amount).toFixed(2)"></span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                                <span class="px-3 py-1 text-xs rounded-full"
                                                                    :class="{
                                                                        'bg-green-900/50 text-green-300 border border-green-800/50': invoice
                                                                            .status === 'paid',
                                                                        'bg-yellow-900/50 text-yellow-300 border border-yellow-800/50': invoice
                                                                            .status === 'partially_paid',
                                                                        'bg-red-900/50 text-red-300 border border-red-800/50': invoice
                                                                            .status === 'overdue',
                                                                        'bg-zinc-800 text-zinc-300 border border-zinc-700/50': [
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
                                    </div>
                                </template>

                                <template x-if="!client.invoices || !client.invoices.length">
                                    <div class="bg-zinc-800/30 border border-zinc-700/50 rounded-lg p-6 text-center">
                                        <svg class="w-10 h-10 text-zinc-600 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm text-zinc-400">No invoices found for this client</p>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <flux:modal.close>
                                    <x-shared.button variant="secondary" class="shadow-lg">Close</x-shared.button>
                                </flux:modal.close>
                            </div>
                        </div>
                    </template>
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

                        <template x-if="loading">
                            <div class="flex justify-center items-center py-6">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                            </div>
                        </template>

                        <template x-if="!loading && invoices.length > 0">
                            <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-zinc-200 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    These invoices will also be deleted:
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
                                        <tbody class="divide-y divide-zinc-700/50">
                                            <template x-for="invoice in invoices" :key="invoice.invoice_number">
                                                <tr>
                                                    <td class="py-2 text-sm text-zinc-300"
                                                        x-text="invoice.invoice_number"></td>
                                                    <td class="py-2 text-sm text-zinc-300 text-right">$<span
                                                            x-text="parseFloat(invoice.total_amount).toFixed(2)"></span>
                                                    </td>
                                                    <td class="py-2 text-center">
                                                        <span
                                                            class="px-2 py-1 text-xs rounded-full inline-block min-w-20"
                                                            :class="{
                                                                'bg-green-900/40 text-green-300': invoice
                                                                    .status === 'paid',
                                                                'bg-yellow-900/40 text-yellow-300': invoice
                                                                    .status === 'partially_paid',
                                                                'bg-red-900/40 text-red-300': invoice
                                                                    .status === 'overdue',
                                                                'bg-zinc-700/40 text-zinc-300': ['draft', 'sent']
                                                                    .includes(invoice.status)
                                                            }"
                                                            x-text="invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1).replace('_', ' ')">
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
                                <div class="flex items-center justify-center gap-2 text-sm text-zinc-300">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>No invoices will be affected by this deletion.</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-zinc-700 pt-6">
                        <flux:modal.close>
                            <x-shared.button variant="secondary" class="shadow-lg">Cancel</x-shared.button>
                        </flux:modal.close>
                        <flux:modal.close>
                            <x-shared.button @click="confirmDelete" variant="danger"
                                class="shadow-lg hover:shadow-red-500/25 transition-shadow">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
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

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('clientManager', () => ({
                    selectedClients: [],
                    selectAll: false,

                    init() {
                        // Listen for Livewire events to clear selections
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
                        const deleteModal = document.querySelector('[x-data="deleteClientModal()"]');
                        if (deleteModal && deleteModal._x_dataStack) {
                            const deleteData = deleteModal._x_dataStack[0];
                            deleteData.clientsToDelete = [...this.selectedClients];
                            deleteData.loading = true;
                            deleteData.invoices = [];
                            deleteData.loadDeletedInvoices();
                        }
                    },

                    prepareDeleteSingle(clientId) {
                        const deleteModal = document.querySelector('[x-data="deleteClientModal()"]');
                        if (deleteModal && deleteModal._x_dataStack) {
                            const deleteData = deleteModal._x_dataStack[0];
                            deleteData.clientsToDelete = [clientId.toString()];
                            deleteData.loading = true;
                            deleteData.invoices = [];
                            deleteData.loadDeletedInvoices();
                        }
                    },

                    openViewModal(clientId) {
                        const viewModal = document.querySelector('[x-data="viewClientModal()"]');
                        if (viewModal && viewModal._x_dataStack) {
                            const viewData = viewModal._x_dataStack[0];
                            viewData.clientId = clientId;
                            viewData.loading = true;
                            viewData.client = null;
                            viewData.loadClientDetails();
                        }
                    }
                }));

                Alpine.data('viewClientModal', () => ({
                    loading: false,
                    client: null,
                    clientId: null,

                    async loadClientDetails() {
                        if (!this.clientId) return;

                        this.loading = true;
                        this.client = null;

                        try {
                            this.client = await this.$wire.getClientDetails(this.clientId);
                        } catch (error) {
                            console.error('Error loading client details:', error);
                        } finally {
                            this.loading = false;
                        }
                    }
                }));

                Alpine.data('deleteClientModal', () => ({
                    loading: false,
                    invoices: [],
                    clientsToDelete: [],

                    async confirmDelete() {
                        if (this.clientsToDelete.length === 0) return;

                        try {
                            if (this.clientsToDelete.length === 1) {
                                await this.$wire.deleteClient(this.clientsToDelete[0]);
                            } else {
                                await this.$wire.deleteMultiple(this.clientsToDelete);
                            }
                        } catch (error) {
                            console.error('Error deleting client(s):', error);
                        }
                    },

                    async loadDeletedInvoices() {
                        this.loading = true;
                        this.invoices = [];

                        try {
                            if (this.clientsToDelete.length > 0) {
                                this.invoices = await this.$wire.getDeletedInvoices(this
                                    .clientsToDelete);
                            }
                        } catch (error) {
                            console.error('Error loading invoices:', error);
                        } finally {
                            this.loading = false;
                        }
                    }
                }));
            });
        </script>
    @endpush

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
</section>
