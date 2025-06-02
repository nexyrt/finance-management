{{-- filepath: /Users/danssejahtera/Documents/Application/finance-management/resources/views/livewire/service-management.blade.php --}}
<section class="w-full min-h-screen bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900">
    <div class="p-6 space-y-8">
        {{-- Enhanced Header Section --}}
        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 rounded-2xl blur-xl"></div>
            <div class="relative bg-zinc-800/60 backdrop-blur-xl border border-zinc-700/50 rounded-2xl p-6">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-zinc-300 bg-clip-text text-transparent">
                    Service Management
                </h1>
                <p class="text-zinc-400 mt-2 text-lg">Create and manage your service offerings with ease</p>
            </div>
        </div>

        {{-- Enhanced Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
            @php
                $cards = [
                    [
                        'filter' => '',
                        'color' => 'blue',
                        'count' => $this->serviceStats['total'],
                        'label' => 'Total Services',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'filter' => 'Perizinan',
                        'color' => 'emerald',
                        'count' => $this->serviceStats['perizinan'],
                        'label' => 'Perizinan',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'filter' => 'Administrasi Perpajakan',
                        'color' => 'purple',
                        'count' => $this->serviceStats['administrasi'],
                        'label' => 'Administrasi Perpajakan',
                        'icon' =>
                            'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                    ],
                    [
                        'filter' => 'Digital Marketing',
                        'color' => 'orange',
                        'count' => $this->serviceStats['digital_marketing'],
                        'label' => 'Digital Marketing',
                        'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',
                    ],
                    [
                        'filter' => 'Sistem Digital',
                        'color' => 'teal',
                        'count' => $this->serviceStats['sistem_digital'],
                        'label' => 'Sistem Digital',
                        'icon' =>
                            'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div wire:click="$set('typeFilter', '{{ $card['filter'] }}')"
                    class="group relative overflow-hidden bg-zinc-800/40 backdrop-blur-xl border border-zinc-700/50 rounded-2xl p-6 
                           hover:border-{{ $card['color'] }}-500/50 hover:bg-zinc-800/60 transition-all duration-500 
                           cursor-pointer transform hover:scale-105 hover:shadow-2xl hover:shadow-{{ $card['color'] }}-500/20
                           {{ $typeFilter === $card['filter'] ? 'ring-2 ring-' . $card['color'] . '-500 bg-zinc-800/60 shadow-lg shadow-' . $card['color'] . '-500/20' : '' }}">

                    {{-- Background Gradient --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-{{ $card['color'] }}-600/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    {{-- Content --}}
                    <div class="relative flex flex-col items-center text-center space-y-3">
                        {{-- Icon --}}
                        <div
                            class="p-3 bg-{{ $card['color'] }}-500/10 rounded-xl group-hover:bg-{{ $card['color'] }}-500/20 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-{{ $card['color'] }}-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $card['icon'] }}" />
                            </svg>
                        </div>

                        {{-- Count --}}
                        <span
                            class="text-4xl font-bold bg-gradient-to-br from-white to-zinc-300 bg-clip-text text-transparent">
                            {{ $card['count'] }}
                        </span>

                        {{-- Label --}}
                        <span class="text-zinc-400 text-sm font-medium leading-tight">{{ $card['label'] }}</span>

                        {{-- Active Filter Badge --}}
                        @if ($typeFilter === $card['filter'])
                            <flux:badge
                                class="bg-{{ $card['color'] }}-500/20 text-{{ $card['color'] }}-100 border border-{{ $card['color'] }}-500/30 animate-pulse">
                                Active Filter
                            </flux:badge>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Enhanced Search & Controls --}}
        <div class="bg-zinc-800/40 backdrop-blur-xl border border-zinc-700/50 rounded-2xl p-6">
            <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
                {{-- Search Input --}}
                <div class="w-full lg:w-1/2">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                            placeholder="Search services..."
                            class="rounded-xl bg-zinc-900/50 border-zinc-600/50 focus:border-blue-500/50 focus:ring-blue-500/20" />
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4 w-full lg:w-auto">
                    @if ($typeFilter)
                        <flux:button wire:click="$set('typeFilter', '')" variant="subtle"
                            class="rounded-xl bg-zinc-700/50 hover:bg-zinc-600/50 border border-zinc-600/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filter
                        </flux:button>
                    @else
                        <flux:select wire:model.live="typeFilter" placeholder="All Types"
                            class="rounded-xl bg-zinc-900/50 border-zinc-600/50">
                            <flux:select.option value="">All Types</flux:select.option>
                            @foreach ($serviceTypes as $option)
                                <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    <flux:button wire:click="createService" icon="plus" variant="primary"
                        class="px-6 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                               shadow-lg hover:shadow-xl hover:shadow-blue-500/25 transition-all duration-300">
                        Add Service
                    </flux:button>

                    @if (count($selectedServices) > 0)
                        <flux:button wire:click="confirmBulkDelete" icon="trash" variant="danger"
                            class="px-6 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 
                                   shadow-lg hover:shadow-xl hover:shadow-red-500/25 transition-all duration-300">
                            Delete
                            <flux:badge class="ml-2 bg-white/20 text-white animate-bounce">
                                {{ count($selectedServices) }}</flux:badge>
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modern Services Table --}}
        <div class="bg-zinc-800/40 backdrop-blur-xl border border-zinc-700/50 rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    {{-- Enhanced Table Header --}}
                    <thead class="bg-gradient-to-r from-zinc-900/90 to-zinc-800/90 backdrop-blur-xl">
                        <tr class="border-b border-zinc-700/50">
                            <th scope="col" class="px-6 py-5 text-left">
                                <flux:checkbox wire:model.live="selectAll" label=""
                                    class="rounded-lg bg-zinc-800/50 border-zinc-600/50" />
                            </th>

                            @php
                                $headers = [
                                    ['field' => 'name', 'label' => 'Service Name'],
                                    ['field' => 'price', 'label' => 'Price'],
                                    ['field' => 'type', 'label' => 'Type'],
                                ];
                            @endphp

                            @foreach ($headers as $header)
                                <th scope="col"
                                    class="px-6 py-5 text-left text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                    <div class="flex cursor-pointer items-center group hover:text-white transition-colors duration-200"
                                        wire:click="sortBy('{{ $header['field'] }}')">
                                        <span>{{ $header['label'] }}</span>
                                        <span
                                            class="ml-2 opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                            @if ($sortField === $header['field'])
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4 4" />
                                                </svg>
                                            @endif
                                        </span>
                                    </div>
                                </th>
                            @endforeach

                            <th scope="col"
                                class="px-6 py-5 text-left text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    {{-- Enhanced Table Body --}}
                    <tbody class="divide-y divide-zinc-700/30">
                        @forelse($this->services as $service)
                            <tr class="group hover:bg-zinc-700/20 transition-all duration-300">
                                <td class="px-6 py-5">
                                    <flux:checkbox wire:model.live="selectedServices" value="{{ $service->id }}"
                                        label="" class="rounded-lg bg-zinc-800/50 border-zinc-600/50" />
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm font-semibold text-white group-hover:text-blue-300 transition-colors duration-200">
                                            {{ $service->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <flux:badge variant="outline"
                                        class="font-mono bg-zinc-800/50 border-zinc-600/50 text-emerald-300">
                                        Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-5">
                                    @php
                                        $typeColors = [
                                            'Perizinan' => 'emerald',
                                            'Administrasi Perpajakan' => 'purple',
                                            'Digital Marketing' => 'orange',
                                            'Sistem Digital' => 'teal',
                                        ];
                                        $color = $typeColors[$service->type] ?? 'zinc';
                                    @endphp
                                    <flux:badge
                                        class="bg-{{ $color }}-500/20 text-{{ $color }}-100 border border-{{ $color }}-500/30">
                                        {{ $service->type }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end space-x-2">
                                        <flux:button wire:click="editService({{ $service->id }})" size="sm"
                                            variant="ghost"
                                            class="p-2 rounded-lg text-zinc-400 hover:text-blue-400 hover:bg-blue-500/10 transition-all duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="confirmDelete({{ $service->id }})" size="sm"
                                            variant="ghost"
                                            class="p-2 rounded-lg text-zinc-400 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200">
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
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="p-4 bg-zinc-700/20 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-zinc-400 text-lg font-medium">No services found</p>
                                            <p class="text-zinc-500 text-sm mt-1">Create your first service to get
                                                started</p>
                                        </div>
                                        <flux:button wire:click="createService" variant="primary"
                                            class="mt-4 rounded-xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add First Service
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Enhanced Pagination --}}
            <div
                class="px-6 py-4 bg-gradient-to-r from-zinc-900/50 to-zinc-800/50 backdrop-blur-xl border-t border-zinc-700/30">
                {{ $this->services->links() }}
            </div>
        </div>
    </div>

    {{-- Enhanced Modals with better styling --}}
    <flux:modal wire:model.self="showServiceFormModal" class="max-w-2xl">
        <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl p-8 border border-zinc-700/50">
            {{-- ...existing modal content with enhanced styling... --}}
            <div class="space-y-6">
                <div class="text-center">
                    <flux:heading size="xl"
                        class="bg-gradient-to-r from-white to-zinc-300 bg-clip-text text-transparent">
                        {{ $editMode ? 'Edit Service' : 'Create New Service' }}
                    </flux:heading>
                    <flux:text class="mt-2 text-zinc-400">
                        {{ $editMode ? 'Update service information' : 'Fill in the service details to create a new offering' }}
                    </flux:text>
                </div>

                <form wire:submit="saveService" class="space-y-6">
                    <flux:input wire:model="name" label="Service Name" placeholder="Enter service name"
                        error="{{ $errors->first('name') }}"
                        class="rounded-xl bg-zinc-900/50 border-zinc-600/50 focus:border-blue-500/50 focus:ring-blue-500/20" />

                    <flux:input wire:model="price" type="number" step="0.01" label="Price" placeholder="0.00"
                        error="{{ $errors->first('price') }}"
                        class="rounded-xl bg-zinc-900/50 border-zinc-600/50 focus:border-blue-500/50 focus:ring-blue-500/20" />

                    <flux:select wire:model.live="type" label="Service Type" placeholder="Select service type"
                        error="{{ $errors->first('type') }}" class="rounded-xl bg-zinc-900/50 border-zinc-600/50">
                        @foreach ($serviceTypes as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="flex justify-end space-x-4 pt-6">
                        <flux:button wire:click="cancelServiceForm" variant="subtle"
                            class="rounded-xl bg-zinc-700/50 hover:bg-zinc-600/50">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg">
                            {{ $editMode ? 'Update Service' : 'Create Service' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </flux:modal>

    {{-- ...existing delete confirmation modals with enhanced styling... --}}
    {{-- Enhanced Delete Confirmation Modal --}}
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

    {{-- Enhanced Bulk Delete Confirmation Modal --}}
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

    @include('components.shared.flash-message')
</section>
