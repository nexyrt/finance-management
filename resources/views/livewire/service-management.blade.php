<section class="w-full">
    <div class="p-6 space-y-6">
        <!-- Page Title and Subtitle -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Service Management</h1>
            <p class="text-zinc-400 mt-1">Create and manage your service offerings</p>
        </div>

        <!-- Statistics Overview - Cards with subtle hover effects and click functionality for filtering -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div wire:click="$set('typeFilter', '')"
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col items-center justify-center hover:border-blue-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md cursor-pointer {{ $typeFilter === '' ? 'ring-2 ring-blue-500 bg-zinc-800/80' : '' }}">
                <span class="text-3xl font-bold text-white mb-1">{{ $this->serviceStats['total'] }}</span>
                <span class="text-zinc-400 text-sm font-medium">Total Services</span>
                @if ($typeFilter === '')
                    <flux:badge class="mt-2 bg-blue-900/60 text-blue-100 border border-blue-700/50">Active Filter
                    </flux:badge>
                @endif
            </div>

            <div wire:click="$set('typeFilter', 'Perizinan')"
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col items-center justify-center hover:border-green-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md cursor-pointer {{ $typeFilter === 'Perizinan' ? 'ring-2 ring-green-500 bg-zinc-800/80' : '' }}">
                <span class="text-3xl font-bold text-white mb-1">{{ $this->serviceStats['perizinan'] }}</span>
                <span class="text-zinc-400 text-sm font-medium">Perizinan</span>
                @if ($typeFilter === 'Perizinan')
                    <flux:badge class="mt-2 bg-green-900/60 text-green-100 border border-green-700/50">Active Filter
                    </flux:badge>
                @endif
            </div>

            <div wire:click="$set('typeFilter', 'Administrasi Perpajakan')"
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col items-center justify-center hover:border-purple-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md cursor-pointer {{ $typeFilter === 'Administrasi Perpajakan' ? 'ring-2 ring-purple-500 bg-zinc-800/80' : '' }}">
                <span class="text-3xl font-bold text-white mb-1">{{ $this->serviceStats['administrasi'] }}</span>
                <span class="text-zinc-400 text-sm font-medium">Administrasi Perpajakan</span>
                @if ($typeFilter === 'Administrasi Perpajakan')
                    <flux:badge class="mt-2 bg-purple-900/60 text-purple-100 border border-purple-700/50">Active Filter
                    </flux:badge>
                @endif
            </div>

            <div wire:click="$set('typeFilter', 'Digital Marketing')"
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col items-center justify-center hover:border-orange-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md cursor-pointer {{ $typeFilter === 'Digital Marketing' ? 'ring-2 ring-orange-500 bg-zinc-800/80' : '' }}">
                <span class="text-3xl font-bold text-white mb-1">{{ $this->serviceStats['digital_marketing'] }}</span>
                <span class="text-zinc-400 text-sm font-medium">Digital Marketing</span>
                @if ($typeFilter === 'Digital Marketing')
                    <flux:badge class="mt-2 bg-orange-900/60 text-orange-100 border border-orange-700/50">Active Filter
                    </flux:badge>
                @endif
            </div>

            <div wire:click="$set('typeFilter', 'Sistem Digital')"
                class="bg-zinc-800/50 backdrop-blur-sm border border-zinc-700/50 rounded-xl p-4 flex flex-col items-center justify-center hover:border-teal-500/50 hover:bg-zinc-800/80 transition-all duration-300 shadow-md cursor-pointer {{ $typeFilter === 'Sistem Digital' ? 'ring-2 ring-teal-500 bg-zinc-800/80' : '' }}">
                <span class="text-3xl font-bold text-white mb-1">{{ $this->serviceStats['sistem_digital'] }}</span>
                <span class="text-zinc-400 text-sm font-medium">Sistem Digital</span>
                @if ($typeFilter === 'Sistem Digital')
                    <flux:badge class="mt-2 bg-teal-900/60 text-teal-100 border border-teal-700/50">Active Filter
                    </flux:badge>
                @endif
            </div>
        </div>

        <!-- Search & Filter Controls - With enhanced styling and filter reset button if filtered -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-1/2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Search services..." class="rounded-lg" />
            </div>

            <div class="flex gap-4 w-full md:w-auto">
                @if ($typeFilter)
                    <flux:button wire:click="$set('typeFilter', '')" variant="subtle" class="rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filter
                        </div>
                    </flux:button>
                @else
                    <flux:select wire:model.live="typeFilter" placeholder="All Types"
                        class="w-full md:w-auto rounded-lg">
                        <flux:select.option value="">All Types</flux:select.option>
                        @foreach ($serviceTypes as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <flux:button wire:click="createService" icon="plus" variant="primary"
                    class="px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Add Service
                </flux:button>

                @if (count($selectedServices) > 0)
                    <flux:button wire:click="confirmBulkDelete" icon="trash" variant="danger"
                        class="px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        Delete
                        <flux:badge class="ml-1 bg-zinc-700/70 text-white">{{ count($selectedServices) }}</flux:badge>
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Services Table - Modern table with better spacing -->
        <div class="bg-zinc-800/70 rounded-xl shadow-lg backdrop-blur-sm border border-zinc-700/50 overflow-hidden">
            <div class="min-w-full divide-y divide-zinc-700/70">
                <table class="min-w-full divide-y divide-zinc-700/70">
                    <thead class="bg-zinc-900/70 backdrop-blur-sm">
                        <tr>
                            <th scope="col" class="px-3 py-4 text-left">
                                <flux:checkbox wire:model.live="selectAll" label="" class="mt-0" />
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group" wire:click="sortBy('name')">
                                    <span class="group-hover:text-white transition-colors duration-200">Name</span>
                                    @if ($sortField === 'name')
                                        <span class="ml-1 text-blue-400">
                                            @if ($sortDirection === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        </span>
                                    @else
                                        <span
                                            class="ml-1 text-zinc-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group" wire:click="sortBy('price')">
                                    <span class="group-hover:text-white transition-colors duration-200">Price</span>
                                    @if ($sortField === 'price')
                                        <span class="ml-1 text-blue-400">
                                            @if ($sortDirection === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        </span>
                                    @else
                                        <span
                                            class="ml-1 text-zinc-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                <div class="flex cursor-pointer items-center group" wire:click="sortBy('type')">
                                    <span class="group-hover:text-white transition-colors duration-200">Type</span>
                                    @if ($sortField === 'type')
                                        <span class="ml-1 text-blue-400">
                                            @if ($sortDirection === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        </span>
                                    @else
                                        <span
                                            class="ml-1 text-zinc-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-zinc-800/30 backdrop-blur-sm divide-y divide-zinc-700/50">
                        @forelse($this->services as $service)
                            <tr class="hover:bg-zinc-700/30 transition-colors">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <flux:checkbox wire:model.live="selectedServices" value="{{ $service->id }}"
                                        label="" class="mt-0" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                    {{ $service->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-200">
                                    <flux:badge variant="outline" class="font-mono">
                                        {{ number_format($service->price, 2) }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <flux:badge
                                        class="@if ($service->type === 'Perizinan') bg-green-900/40 text-green-100 border border-green-700/50
                                        @elseif($service->type === 'Administrasi Perpajakan') bg-purple-900/40 text-purple-100 border border-purple-700/50
                                        @elseif($service->type === 'Digital Marketing') bg-orange-900/40 text-orange-100 border border-orange-700/50
                                        @else bg-teal-900/40 text-teal-100 border border-teal-700/50 @endif">
                                        {{ $service->type }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end">
                                        <flux:button wire:click="editService({{ $service->id }})" size="sm"
                                            variant="ghost" class="mr-2 text-zinc-400 hover:text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="confirmDelete({{ $service->id }})" size="sm"
                                            variant="ghost" class="text-zinc-400 hover:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-12 whitespace-nowrap text-sm text-zinc-400 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-600 mb-4"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-zinc-500 mb-1">No services found</p>
                                        <p class="text-zinc-600 text-xs">Click "Add Service" to create one</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-zinc-900/50 backdrop-blur-sm border-t border-zinc-700/50">
                {{ $this->services->links() }}
            </div>
        </div>
    </div>

    <!-- Service Form Modal - Fixed with wire:model.live for type selection -->
    <flux:modal wire:model.self="showServiceFormModal">
        <div class="space-y-6 p-2">
            <div>
                <flux:heading size="lg" class="text-white">
                    {{ $editMode ? 'Edit Service' : 'Add New Service' }}
                </flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    {{ $editMode ? 'Edit service details.' : 'Fill in the service details.' }}
                </flux:text>
            </div>

            <form wire:submit="saveService" class="space-y-5">
                <flux:input wire:model="name" label="Service Name" placeholder="Enter service name"
                    error="{{ $errors->first('name') }}" class="rounded-lg" />

                <flux:input wire:model="price" type="number" step="0.01" label="Price" placeholder="0.00"
                    error="{{ $errors->first('price') }}" class="rounded-lg" />

                <flux:select wire:model.live="type" label="Service Type" placeholder="Select service type"
                    error="{{ $errors->first('type') }}" class="rounded-lg">
                    @foreach ($serviceTypes as $option)
                        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex justify-end space-x-3 pt-2">
                    <flux:button wire:click="cancelServiceForm" variant="subtle" class="rounded-lg">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary" class="rounded-lg shadow-md">
                        {{ $editMode ? 'Update Service' : 'Create Service' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal - Enhanced with better styling -->
    <flux:modal wire:model.self="showDeleteConfirmModal">
        <div class="space-y-6 p-2">
            <div>
                <flux:heading size="lg" class="text-white">Confirm Deletion</flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    Are you sure you want to delete this service?
                    @if (count($affectedClients) === 0)
                        This action cannot be undone.
                    @else
                        <span class="text-red-400 font-medium block mt-2">This will affect
                            {{ count($affectedClients) }} client(s)!</span>
                    @endif
                </flux:text>
            </div>

            @if (count($affectedClients) > 0)
                <div class="mt-4">
                    <div class="flex items-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <flux:text class="font-semibold text-red-400">Affected Clients</flux:text>
                    </div>

                    <div class="mt-2 max-h-60 overflow-y-auto">
                        <div class="bg-zinc-900/70 backdrop-blur-sm rounded-lg p-4 space-y-4 border border-zinc-800">
                            @foreach ($affectedClients as $client)
                                <div class="border-b border-zinc-800 pb-3 last:border-0 last:pb-0">
                                    <div class="flex justify-between">
                                        <p class="font-medium text-zinc-200">{{ $client['name'] }}</p>
                                        <p class="text-zinc-500 text-sm">{{ $client['service_date'] }}</p>
                                    </div>
                                    <p class="text-zinc-400 text-sm">Amount: <span
                                            class="font-mono">{{ number_format($client['amount'], 2) }}</span></p>

                                    @if (count($client['invoices']) > 0)
                                        <div class="mt-2">
                                            <p class="text-xs text-zinc-500 font-medium">Related Invoices:</p>
                                            <div class="mt-1 space-y-1">
                                                @foreach ($client['invoices'] as $invoice)
                                                    <div
                                                        class="flex justify-between items-center bg-zinc-800/50 rounded-md px-2 py-1.5 text-xs">
                                                        <span
                                                            class="font-mono text-zinc-300">#{{ $invoice['number'] }}</span>
                                                        <flux:badge
                                                            class="@if ($invoice['status'] === 'paid') bg-green-900/40 text-green-100 border border-green-700/50
                                                            @elseif($invoice['status'] === 'partially_paid') bg-blue-900/40 text-blue-100 border border-blue-700/50
                                                            @elseif($invoice['status'] === 'overdue') bg-red-900/40 text-red-100 border border-red-700/50
                                                            @else bg-zinc-700/40 text-zinc-300 border border-zinc-600/50 @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $invoice['status'])) }}
                                                        </flux:badge>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 bg-zinc-900/50 rounded-lg p-3 border border-zinc-800">
                        <flux:checkbox wire:model.live="forceDelete"
                            label="I understand the consequences and want to force delete this service" />
                    </div>
                </div>
            @endif

            <div class="flex justify-end space-x-3 pt-2">
                <flux:button wire:click="$set('showDeleteConfirmModal', false)" variant="subtle" class="rounded-lg">
                    Cancel
                </flux:button>
                <flux:button wire:click="deleteService" variant="danger" class="rounded-lg shadow-md"
                    :disabled="count($affectedClients) > 0 && !$forceDelete">
                    Delete Service
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Bulk Delete Confirmation Modal -->
    <flux:modal wire:model.self="showBulkDeleteConfirmModal">
        <div class="space-y-6 p-2">
            <div>
                <flux:heading size="lg" class="text-white">Confirm Bulk Deletion</flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    Are you sure you want to delete {{ count($selectedServices) }} selected services?
                    @if (count($bulkAffectedClients) === 0)
                        This action cannot be undone.
                    @else
                        <span class="text-red-400 font-medium block mt-2">This will affect client relationships for
                            {{ count($bulkAffectedClients) }} service(s)!</span>
                    @endif
                </flux:text>
            </div>

            @if (count($bulkAffectedClients) > 0)
                <div class="mt-4">
                    <div class="flex items-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <flux:text class="font-semibold text-red-400">Affected Services and Clients</flux:text>
                    </div>

                    <div class="mt-2 max-h-60 overflow-y-auto">
                        <div class="bg-zinc-900/70 backdrop-blur-sm rounded-lg p-4 space-y-4 border border-zinc-800">
                            @foreach ($bulkAffectedClients as $serviceId => $serviceData)
                                <div class="border-b border-zinc-800 pb-3 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-medium text-zinc-100">{{ $serviceData['service_name'] }}</p>
                                        <flux:badge
                                            class="@if ($serviceData['service_type'] === 'Perizinan') bg-green-900/40 text-green-100 border border-green-700/50
                                            @elseif($serviceData['service_type'] === 'Administrasi Perpajakan') bg-purple-900/40 text-purple-100 border border-purple-700/50
                                            @elseif($serviceData['service_type'] === 'Digital Marketing') bg-orange-900/40 text-orange-100 border border-orange-700/50
                                            @else bg-teal-900/40 text-teal-100 border border-teal-700/50 @endif">
                                            {{ $serviceData['service_type'] }}
                                        </flux:badge>
                                    </div>

                                    <div class="space-y-2">
                                        <p class="text-xs text-zinc-500 font-medium">Clients
                                            ({{ count($serviceData['affected_clients']) }})
                                            :</p>

                                        @foreach ($serviceData['affected_clients'] as $client)
                                            <div
                                                class="bg-zinc-800/70 backdrop-blur-sm rounded-md p-2 border border-zinc-700/50">
                                                <div class="flex justify-between">
                                                    <p class="text-sm font-medium text-zinc-200">{{ $client['name'] }}
                                                    </p>
                                                    <p class="text-zinc-500 text-xs">{{ $client['service_date'] }}</p>
                                                </div>

                                                @if (count($client['invoices']) > 0)
                                                    <div class="mt-1">
                                                        <p class="text-xs text-zinc-500">Invoices:</p>
                                                        <div class="mt-0.5 flex flex-wrap gap-1">
                                                            @foreach ($client['invoices'] as $invoice)
                                                                <flux:badge
                                                                    class="@if ($invoice['status'] === 'paid') bg-green-900/40 text-green-100 border border-green-700/50
                                                                    @elseif($invoice['status'] === 'partially_paid') bg-blue-900/40 text-blue-100 border border-blue-700/50
                                                                    @elseif($invoice['status'] === 'overdue') bg-red-900/40 text-red-100 border border-red-700/50
                                                                    @else bg-zinc-700/40 text-zinc-300 border border-zinc-600/50 @endif">
                                                                    #{{ $invoice['number'] }}
                                                                </flux:badge>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 bg-zinc-900/50 rounded-lg p-3 border border-zinc-800">
                        <flux:checkbox wire:model.live="bulkForceDelete"
                            label="I understand the consequences and want to force delete these services" />
                    </div>
                </div>
            @endif

            <div class="flex justify-end space-x-3 pt-2">
                <flux:button wire:click="$set('showBulkDeleteConfirmModal', false)" variant="subtle"
                    class="rounded-lg">
                    Cancel
                </flux:button>
                <flux:button wire:click="bulkDeleteServices" variant="danger" class="rounded-lg shadow-md"
                    :disabled="count($bulkAffectedClients) > 0 && !$bulkForceDelete">
                    Delete Services
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Include flash message component -->
    @include('components.shared.flash-message')
</section>
