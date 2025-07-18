{{-- filepath: /Users/danssejahtera/Documents/Application/finance-management/resources/views/livewire/service-management.blade.php --}}
<section class="w-full p-6 bg-white dark:bg-zinc-800">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Service Management</h1>
        <p class="text-gray-500 dark:text-zinc-400">Manage your service offerings and pricing</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        @php
            $cards = [
                [
                    'filter' => '',
                    'color' => 'blue',
                    'count' => $serviceStats['total'],
                    'label' => 'Total Services',
                    'icon' =>
                        'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                ],
                [
                    'filter' => 'Perizinan',
                    'color' => 'emerald',
                    'count' => $serviceStats['perizinan'],
                    'label' => 'Perizinan',
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'filter' => 'Administrasi Perpajakan',
                    'color' => 'purple',
                    'count' => $serviceStats['administrasi'],
                    'label' => 'Administrasi Perpajakan',
                    'icon' =>
                        'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                ],
                [
                    'filter' => 'Digital Marketing',
                    'color' => 'orange',
                    'count' => $serviceStats['digital_marketing'],
                    'label' => 'Digital Marketing',
                    'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',
                ],
                [
                    'filter' => 'Sistem Digital',
                    'color' => 'teal',
                    'count' => $serviceStats['sistem_digital'],
                    'label' => 'Sistem Digital',
                    'icon' =>
                        'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div wire:click="$set('typeFilter', '{{ $card['filter'] }}')"
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-md dark:shadow-zinc-950/25 p-6 border-l-4 border-{{ $card['color'] }}-500 
                    transition-all duration-500 hover:shadow-lg dark:hover:shadow-zinc-950/50 transform hover:-translate-y-1 cursor-pointer
                    {{ $typeFilter === $card['filter'] ? 'ring-2 ring-' . $card['color'] . '-500 bg-' . $card['color'] . '-50 dark:bg-' . $card['color'] . '-900/20' : '' }}"
                x-data="{ isVisible: false }" x-init="setTimeout(() => { isVisible = true }, {{ $loop->index * 100 }})"
                :class="{ 'opacity-0': !isVisible, 'opacity-100': isVisible }">

                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $card['count'] }}</p>
                        @if ($typeFilter === $card['filter'])
                            <p class="text-xs text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-2">
                                Active Filter
                            </p>
                        @endif
                    </div>
                    <div class="bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-{{ $card['color'] }}-500 dark:text-{{ $card['color'] }}-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Search and Controls -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-md dark:shadow-zinc-950/25 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
            <!-- Search -->
            <div class="w-full lg:w-1/2">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search services..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 w-full lg:w-auto">
                @if ($typeFilter)
                    <button wire:click="$set('typeFilter', '')"
                        class="px-4 py-2 bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-600 transition-colors duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear Filter
                    </button>
                @endif

                <flux:modal.trigger name="service-form">
                    <flux:button wire:click='resetForm' icon="plus" variant="primary">Add Service</flux:button>
                </flux:modal.trigger>

                @if (count($selectedServices) > 0)
                    <flux:modal.trigger name="bulk-delete-confirm">
                        <flux:button icon="trash" variant="danger">
                            Delete ({{ count($selectedServices) }})
                        </flux:button>
                    </flux:modal.trigger>
                @endif
            </div>
        </div>
    </div>

    <!-- Services Table -->
    <div
        class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg dark:shadow-zinc-950/25 overflow-hidden border border-gray-200 dark:border-zinc-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <!-- Table Header -->
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-900">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 focus:ring-2">
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
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-zinc-300 uppercase tracking-wider cursor-pointer hover:text-gray-800 dark:hover:text-zinc-100 transition-colors duration-200"
                                wire:click="sortBy('{{ $header['field'] }}')">
                                <div class="flex items-center space-x-2">
                                    <span>{{ $header['label'] }}</span>
                                    @if ($sortField === $header['field'])
                                        <div
                                            class="flex items-center justify-center w-4 h-4 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3 text-blue-600 dark:text-blue-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                            </svg>
                                        </div>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-gray-400 dark:text-zinc-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                        @endforeach

                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-zinc-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse($services as $service)
                        <tr wire:key="service-{{ $service->id }}"
                            class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-zinc-800 dark:hover:to-zinc-700 transition-all duration-200 group">
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model.live="selectedServices" value="{{ $service->id }}"
                                    class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 focus:ring-2">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $service->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-zinc-400">Service ID:
                                            #{{ $service->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <flux:badge variant="default"
                                        class="bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 border border-emerald-200 dark:from-emerald-900/30 dark:to-green-900/30 dark:text-emerald-300 dark:border-emerald-700">
                                        Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </flux:badge>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeVariants = [
                                        'Perizinan' => [
                                            'variant' => 'default',
                                            'class' =>
                                                'bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 border border-blue-200 dark:from-blue-900/30 dark:to-cyan-900/30 dark:text-blue-300 dark:border-blue-700',
                                            'icon' => '📋',
                                        ],
                                        'Administrasi Perpajakan' => [
                                            'variant' => 'default',
                                            'class' =>
                                                'bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200 dark:from-purple-900/30 dark:to-pink-900/30 dark:text-purple-300 dark:border-purple-700',
                                            'icon' => '📊',
                                        ],
                                        'Digital Marketing' => [
                                            'variant' => 'default',
                                            'class' =>
                                                'bg-gradient-to-r from-orange-100 to-red-100 text-orange-800 border border-orange-200 dark:from-orange-900/30 dark:to-red-900/30 dark:text-orange-300 dark:border-orange-700',
                                            'icon' => '📱',
                                        ],
                                        'Sistem Digital' => [
                                            'variant' => 'default',
                                            'class' =>
                                                'bg-gradient-to-r from-teal-100 to-emerald-100 text-teal-800 border border-teal-200 dark:from-teal-900/30 dark:to-emerald-900/30 dark:text-teal-300 dark:border-teal-700',
                                            'icon' => '💻',
                                        ],
                                    ];
                                    $badgeConfig = $typeVariants[$service->type] ?? [
                                        'variant' => 'secondary',
                                        'class' =>
                                            'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200 dark:from-gray-900/30 dark:to-slate-900/30 dark:text-gray-300 dark:border-gray-700',
                                        'icon' => '🔧',
                                    ];
                                @endphp
                                <flux:badge variant="{{ $badgeConfig['variant'] }}"
                                    class="{{ $badgeConfig['class'] }}">
                                    {{ $badgeConfig['icon'] }} {{ $service->type }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <flux:button wire:click="editService({{ $service->id }})" size="sm"
                                        variant="ghost"
                                        class="group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </flux:button>
                                    <flux:button wire:click="confirmDelete({{ $service->id }})" size="sm"
                                        variant="ghost"
                                        class="group-hover:bg-red-100 dark:group-hover:bg-red-900/30 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
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
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-6">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-20">
                                        </div>
                                        <div
                                            class="relative p-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-700 dark:to-zinc-800 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-16 w-16 text-gray-400 dark:text-zinc-500" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-center space-y-2">
                                        <p class="text-gray-600 dark:text-zinc-300 text-xl font-semibold">No services
                                            found</p>
                                        <p class="text-gray-400 dark:text-zinc-500 text-sm max-w-sm">Create your first
                                            service to get started with managing your business offerings</p>
                                    </div>
                                    <flux:modal.trigger name="service-form">
                                        <flux:button variant="primary"
                                            class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Add First Service
                                        </flux:button>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        @if ($services->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-zinc-700 border-t border-slate-200 dark:border-zinc-600">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center text-sm text-slate-600 dark:text-zinc-400">
                        <flux:badge variant="outline"
                            class="text-slate-600 dark:text-zinc-400 border-slate-300 dark:border-zinc-600">
                            Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of
                            {{ $services->total() }} results
                        </flux:badge>
                    </div>

                    <div class="flex items-center space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($services->onFirstPage())
                            <flux:button variant="subtle" size="sm" disabled icon="chevron-left">
                                Previous
                            </flux:button>
                        @else
                            <flux:button wire:click="previousPage" variant="subtle" size="sm"
                                icon="chevron-left">
                                Previous
                            </flux:button>
                        @endif

                        {{-- Page Numbers --}}
                        <div class="flex items-center space-x-1">
                            @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                                @if ($page == $services->currentPage())
                                    <flux:badge variant="default" class="bg-blue-600 text-white px-3 py-1">
                                        {{ $page }}
                                    </flux:badge>
                                @elseif (
                                    $page == 1 ||
                                        $page == $services->lastPage() ||
                                        ($page >= $services->currentPage() - 2 && $page <= $services->currentPage() + 2))
                                    <flux:button wire:click="gotoPage({{ $page }})" variant="ghost"
                                        size="sm" class="min-w-[32px] h-8">
                                        {{ $page }}
                                    </flux:button>
                                @elseif ($page == $services->currentPage() - 3 || $page == $services->currentPage() + 3)
                                    <span class="px-2 text-slate-400 dark:text-zinc-500">...</span>
                                @endif
                            @endforeach
                        </div>

                        {{-- Next Page Link --}}
                        @if ($services->hasMorePages())
                            <flux:button wire:click="nextPage" variant="subtle" size="sm"
                                icon:trailing="chevron-right">
                                Next
                            </flux:button>
                        @else
                            <flux:button variant="subtle" size="sm" disabled icon:trailing="chevron-right">
                                Next
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Service Form Modal -->
    <flux:modal name="service-form" wire:model.self="showServiceModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editMode ? 'Edit Service' : 'Create New Service' }}</flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    {{ $editMode ? 'Update service information' : 'Fill in the service details to create a new offering' }}
                </flux:text>
            </div>

            <form wire:submit="saveService" class="space-y-4">
                <flux:input wire:model="name" label="Service Name" placeholder="Enter service name"
                    error="{{ $errors->first('name') }}" />

                <flux:input wire:model="price" type="number" step="0.01" label="Price" placeholder="0.00"
                    error="{{ $errors->first('price') }}" />

                <div>
                    <x-select.styled label="Service Type" wire:model="type" :options="$serviceTypes" searchable />
                    @error('type')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <flux:modal.close>
                        <flux:button wire:click="closeModal" variant="subtle">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        {{ $editMode ? 'Update' : 'Create' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-confirm" wire:model.self="showDeleteModal">
        <div class="space-y-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <flux:heading size="lg">Confirm Delete</flux:heading>
            </div>

            <flux:text>
                Are you sure you want to delete this service? This action cannot be undone.
            </flux:text>

            <div class="flex justify-end space-x-3">
                <flux:modal.close>
                    <flux:button variant="subtle">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteService" variant="danger">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Bulk Delete Confirmation Modal -->
    <flux:modal name="bulk-delete-confirm" wire:model.self="showDeleteModal">
        <div class="space-y-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <flux:heading size="lg">Confirm Bulk Delete</flux:heading>
            </div>

            <flux:text>
                Are you sure you want to delete {{ count($selectedServices) }} selected services? This action cannot be
                undone.
            </flux:text>

            <div class="flex justify-end space-x-3">
                <flux:modal.close>
                    <flux:button variant="subtle">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="bulkDeleteServices" variant="danger">Delete Services</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</section>
