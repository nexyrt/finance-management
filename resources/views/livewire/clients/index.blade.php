{{-- resources/views/livewire/clients/index.blade.php --}}
<section class="space-y-6">

    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                    {{ __('pages.client_management') }}
                </h1>
                <p class="text-gray-600 dark:text-zinc-400 text-lg">
                    {{ __('pages.client_management_description') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-button wire:click="$dispatch('create-client')" icon="plus" color="primary">
                    {{ __('pages.add_client') }}
                </x-button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('pages.total_clients') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $rows->total() ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('pages.active_clients') }}</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ $rows->where('status', 'Active')->count() ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-green-500/10 dark:bg-green-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('pages.companies') }}</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $rows->where('type', 'company')->count() ?? 0 }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-purple-500/10 dark:bg-purple-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="building-office" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('pages.individuals') }}</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $rows->where('type', 'individual')->count() ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="user" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-gradient-to-r from-white/90 via-white/95 to-white/90 dark:from-zinc-800/90 dark:via-zinc-800/95 dark:to-zinc-800/90 backdrop-blur-sm rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 shadow-lg shadow-zinc-500/5 mb-8">
        <!-- Filter Header -->
        <div class="flex items-center justify-between p-6 pb-4 border-b border-zinc-200/50 dark:border-zinc-700/50">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon name="funnel" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('pages.filter_clients') }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('pages.use_filters_narrow_search') }}</p>
                </div>
            </div>
            
            <!-- Active Filters Count -->
            @if($typeFilter || $statusFilter)
                <div class="flex items-center space-x-2">
                    <div class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full text-sm font-medium">
                        {{ collect([$typeFilter, $statusFilter])->filter()->count() }} {{ __('pages.active_filters') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Filter Controls -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Type Filter --}}
                <div class="space-y-2">
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="h-6 w-6 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <x-icon name="user-group" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('pages.client_type') }}</label>
                    </div>
                    <x-select.styled wire:model.live="typeFilter" :options="[
                        ['label' => '👤 ' . __('pages.individuals'), 'value' => 'individual'],
                        ['label' => '🏢 ' . __('pages.companies'), 'value' => 'company'],
                    ]" placeholder="{{ __('pages.all_types') }}"
                        class="w-full" />
                </div>

                {{-- Status Filter --}}
                <div class="space-y-2">
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="h-6 w-6 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <x-icon name="shield-check" class="w-4 h-4 text-green-600 dark:text-green-400" />
                        </div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('common.status') }}</label>
                    </div>
                    <x-select.styled wire:model.live="statusFilter" :options="[
                        ['label' => '✅ ' . __('common.active'), 'value' => 'Active'],
                        ['label' => '❌ ' . __('common.inactive'), 'value' => 'Inactive'],
                    ]"
                        placeholder="{{ __('pages.all_status') }}"
                        class="w-full" />
                </div>

                {{-- Clear Filters --}}
                <div>
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="h-6 w-6 bg-zinc-100 dark:bg-zinc-900/30 rounded-lg flex items-center justify-center">
                            <x-icon name="arrow-path" class="w-4 h-4 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('pages.reset') }}</label>
                    </div>
                    <x-button wire:click="clearFilters" color="secondary" icon="x-mark">
                        {{ __('pages.clear_all_filters') }}
                    </x-button>
                </div>
            </div>

            <!-- Active Filter Tags -->
            @if($typeFilter || $statusFilter)
                <div class="mt-6 pt-4 border-t border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-center space-x-2 mb-3">
                        <x-icon name="tag" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('pages.active_filters') }}:</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($typeFilter)
                            <div class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                                <span>{{ $typeFilter === 'individual' ? '👤 ' . __('pages.individuals') : '🏢 ' . __('pages.companies') }}</span>
                                <button wire:click="$set('typeFilter', '')" class="hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5 transition-colors">
                                    <x-icon name="x-mark" class="w-3 h-3" />
                                </button>
                            </div>
                        @endif
                        @if($statusFilter)
                            <div class="inline-flex items-center gap-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 px-3 py-1.5 rounded-lg border border-green-200 dark:border-green-800 text-sm">
                                <span>{{ $statusFilter === 'Active' ? '✅ ' . __('common.active') : '❌ ' . __('common.inactive') }}</span>
                                <button wire:click="$set('statusFilter', '')" class="hover:bg-green-200 dark:hover:bg-green-800 rounded-full p-0.5 transition-colors">
                                    <x-icon name="x-mark" class="w-3 h-3" />
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Main Table Card --}}
    <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable wire:model="selected">
        {{-- Client Name with Enhanced Avatar --}}
        @interact('column_name', $row)
            <div class="flex items-center space-x-4">
                <div class="relative">
                    @if ($row->logo)
                        <img class="h-12 w-12 rounded-2xl object-cover shadow-md" src="{{ $row->logo }}"
                            alt="{{ $row->name }}">
                    @else
                        <div
                            class="h-12 w-12 rounded-2xl flex items-center justify-center shadow-md
                                        {{ $row->type === 'individual'
                                            ? 'bg-gradient-to-br from-blue-400 to-blue-600'
                                            : 'bg-gradient-to-br from-purple-400 to-purple-600' }}">
                            <x-icon name="{{ $row->type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-6 h-6 text-white" />
                        </div>
                    @endif

                    {{-- Status indicator --}}
                    <div
                        class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-white dark:border-gray-800 
                                    {{ $row->status === 'Active' ? 'bg-green-400' : 'bg-gray-400' }}">
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ $row->name }}
                    </p>
                    @if ($row->NPWP)
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 truncate font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-md inline-block mt-1">
                            {{ $row->NPWP }}
                        </p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Enhanced Type Column --}}
        @interact('column_type', $row)
            <x-badge text="{{ $row->type === 'individual' ? '👤 ' . __('pages.individuals') : '🏢 ' . __('pages.companies') }}"
                color="{{ $row->type === 'individual' ? 'blue' : 'purple' }}" class="shadow-sm" />
        @endinteract

        {{-- Enhanced Contact Info --}}
        @interact('column_person_in_charge', $row)
            <div class="space-y-2">
                @if ($row->email)
                    <a href="mailto:{{ $row->email }}"
                        class="group flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        <x-icon name="envelope" class="w-4 h-4" />
                        <span class="truncate group-hover:underline">{{ $row->email }}</span>
                    </a>
                @endif

                @if ($row->ar_phone_number)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="phone" class="w-4 h-4" />
                        <span class="truncate">{{ $row->ar_phone_number }}</span>
                    </div>
                @endif

                @if (!$row->email && !$row->ar_phone_number)
                    <span class="text-gray-400 dark:text-gray-500 italic text-sm">{{ __('pages.no_contact_info') }}</span>
                @endif
            </div>
        @endinteract

        {{-- Enhanced Status Column --}}
        @interact('column_status', $row)
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full {{ $row->status === 'Active' ? 'bg-green-400' : 'bg-red-400' }}">
                </div>
                <x-badge text="{{ $row->status }}" color="{{ $row->status === 'Active' ? 'green' : 'red' }}"
                    class="shadow-sm" />
            </div>
        @endinteract

        {{-- Enhanced Invoices Count --}}
        @interact('column_invoices_count', $row)
            <div class="text-center">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full 
                                {{ $row->invoices_count > 0
                                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                    : 'bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                    <x-icon name="document-text" class="w-4 h-4" />
                    <span class="font-medium">{{ $row->invoices_count }}</span>
                </div>
            </div>
        @endinteract

        {{-- Enhanced Financial Summary --}}
        @interact('column_financial_summary', $row)
            <div class="text-right space-y-1">
                @php
                    $totalAmount = $row->invoices->sum('total_amount');
                    $paidAmount = $row->invoices->filter(fn($inv) => $inv->status === 'paid')->sum('total_amount');
                    $outstandingAmount = $totalAmount - $paidAmount;
                @endphp

                <div class="font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </div>

                @if ($outstandingAmount > 0)
                    <div
                        class="inline-flex items-center gap-1 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-full">
                        <x-icon name="exclamation-triangle" class="w-3 h-3" />
                        <span>Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</span>
                    </div>
                @elseif($totalAmount > 0)
                    <div
                        class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full">
                        <x-icon name="check-circle" class="w-3 h-3" />
                        <span>{{ __('pages.paid_off') }}</span>
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Enhanced Actions --}}
        @interact('column_actions', $row)
            <x-dropdown icon="ellipsis-vertical" class="shadow-lg">
                <x-dropdown.items text="{{ __('pages.view_details') }}" icon="eye"
                    wire:click="$dispatch('show-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('pages.edit_client') }}" icon="pencil"
                    wire:click="$dispatch('edit-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('pages.manage_relationships') }}" icon="users"
                    wire:click="$dispatch('manage-relationships', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('common.delete') }}" icon="trash"
                    wire:click="$dispatch('delete-client', { clientId: {{ $row->id }} })" />
            </x-dropdown>
        @endinteract

    </x-table>

    {{-- Bulk Actions Bar - Sticky positioned --}}
    <div x-data="{ show: @entangle('selected').live }" 
         x-show="show.length > 0"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-0 left-1/2 transform -translate-x-1/2 z-50 mb-6">
        
        <div class="bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-700/50 px-6 py-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                
                {{-- Selection Info --}}
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white" x-text="`${show.length} {{ __('pages.clients_selected') }}`"></p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('pages.select_action_all_clients') }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    <x-button
                        wire:click="clearSelection"
                        color="secondary"
                        size="sm"
                        icon="x-mark">
                        {{ __('common.cancel') }}
                    </x-button>

                    <x-button
                        wire:click="bulkDelete"
                        color="red"
                        size="sm"
                        icon="trash">
                        <span x-text="`{{ __('common.delete') }} ${show.length} {{ __('common.clients') }}`"></span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal components --}}
    <livewire:clients.edit />
    <livewire:clients.delete />
    <livewire:clients.show />
    <livewire:clients.create />
    <livewire:clients.relationship />
</section>