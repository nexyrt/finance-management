{{-- resources/views/livewire/clients/index.blade.php --}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.client_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.client_management_description') }}
            </p>
        </div>

        <livewire:clients.create @client-created="$refresh" />
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Clients --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_clients') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->total() ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        {{-- Active Clients --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.active_clients') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->where('status', 'Active')->count() ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        {{-- Companies --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="building-office" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.companies') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->where('type', 'company')->count() ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        {{-- Individuals --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="user" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.individuals') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->where('type', 'individual')->count() ?? 0 }}</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filters --}}
    <div class="space-y-4">
        {{-- Filter Section --}}
        <div class="flex flex-col gap-4">
            {{-- Main Filters Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Type Filter --}}
                <x-select.styled wire:model.live="typeFilter" label="{{ __('pages.client_type') }}" :options="[
                    ['label' => __('pages.individuals'), 'value' => 'individual'],
                    ['label' => __('pages.companies'), 'value' => 'company'],
                ]" placeholder="{{ __('pages.all_types') }}" />

                {{-- Status Filter --}}
                <x-select.styled wire:model.live="statusFilter" label="{{ __('common.status') }}" :options="[
                    ['label' => __('common.active'), 'value' => 'Active'],
                    ['label' => __('common.inactive'), 'value' => 'Inactive'],
                ]" placeholder="{{ __('pages.all_status') }}" />

                {{-- Clear Filters --}}
                <div class="flex items-end">
                    <x-button wire:click="clearFilters" color="zinc" icon="x-mark" class="w-full">
                        {{ __('pages.clear_all_filters') }}
                    </x-button>
                </div>
            </div>

            {{-- Filter Status & Result Count --}}
            <div class="flex items-center gap-3">
                @php
                    $activeFilters = collect([$typeFilter, $statusFilter])->filter()->count();
                @endphp

                @if ($activeFilters > 0)
                    <x-badge text="{{ $activeFilters }} {{ __('pages.active_filters') }}" color="primary" size="sm" />
                @endif

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="hidden sm:inline">{{ __('common.showing') }} </span>{{ $rows->count() }}
                    <span class="hidden sm:inline">{{ __('common.of') }} {{ $rows->total() }}</span> {{ __('common.clients') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable
        wire:model="selected">
        {{-- Client Name --}}
        @interact('column_name', $row)
            <div class="flex items-center gap-3">
                @if ($row->logo)
                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $row->logo }}" alt="{{ $row->name }}">
                @else
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $row->type === 'individual' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' }}">
                        <x-icon name="{{ $row->type === 'individual' ? 'user' : 'building-office' }}" class="w-5 h-5" />
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $row->name }}</p>
                    @if ($row->NPWP)
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate font-mono mt-1">{{ $row->NPWP }}</p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Type --}}
        @interact('column_type', $row)
            <x-badge text="{{ $row->type === 'individual' ? __('pages.individuals') : __('pages.companies') }}"
                color="{{ $row->type === 'individual' ? 'blue' : 'purple' }}" light />
        @endinteract

        {{-- Contact Info --}}
        @interact('column_person_in_charge', $row)
            <div class="space-y-1">
                @if ($row->email)
                    <a href="mailto:{{ $row->email }}"
                        class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        <x-icon name="envelope" class="w-4 h-4" />
                        <span class="truncate">{{ $row->email }}</span>
                    </a>
                @endif

                @if ($row->ar_phone_number)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="phone" class="w-4 h-4" />
                        <span class="truncate">{{ $row->ar_phone_number }}</span>
                    </div>
                @endif

                @if (!$row->email && !$row->ar_phone_number)
                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('pages.no_contact_info') }}</span>
                @endif
            </div>
        @endinteract

        {{-- Status --}}
        @interact('column_status', $row)
            <x-badge text="{{ $row->status }}" color="{{ $row->status === 'Active' ? 'green' : 'red' }}" light />
        @endinteract

        {{-- Invoices Count --}}
        @interact('column_invoices_count', $row)
            <div class="text-center">
                <x-badge color="{{ $row->invoices_count > 0 ? 'blue' : 'gray' }}" light>
                    <x-icon name="document-text" class="w-4 h-4" />
                    <span class="ml-1">{{ $row->invoices_count }}</span>
                </x-badge>
            </div>
        @endinteract

        {{-- Financial Summary --}}
        @interact('column_financial_summary', $row)
            <div class="text-right space-y-1">
                @php
                    $totalAmount = $row->invoices->sum('total_amount');
                    $paidAmount = $row->invoices->filter(fn($inv) => $inv->status === 'paid')->sum('total_amount');
                    $outstandingAmount = $totalAmount - $paidAmount;
                @endphp

                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </p>

                @if ($outstandingAmount > 0)
                    <x-badge color="red" light>
                        <x-icon name="exclamation-triangle" class="w-3 h-3" />
                        <span class="ml-1">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</span>
                    </x-badge>
                @elseif($totalAmount > 0)
                    <x-badge color="green" light>
                        <x-icon name="check-circle" class="w-3 h-3" />
                        <span class="ml-1">{{ __('pages.paid_off') }}</span>
                    </x-badge>
                @endif
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_actions', $row)
            <x-dropdown icon="ellipsis-vertical">
                <x-dropdown.items text="{{ __('pages.view_details') }}" icon="eye"
                    wire:click="$dispatch('show-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('pages.edit_client') }}" icon="pencil"
                    wire:click="$dispatch('edit-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('common.delete') }}" icon="trash"
                    wire:click="$dispatch('delete-client', { clientId: {{ $row->id }} })" />
            </x-dropdown>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white" x-text="`${show.length} {{ __('pages.clients_selected') }}`"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('pages.select_action_all_clients') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-button wire:click="clearSelection" color="zinc" size="sm" icon="x-mark">
                        {{ __('common.cancel') }}
                    </x-button>

                    <x-button wire:click="bulkDelete" color="red" size="sm" icon="trash">
                        <span x-text="`{{ __('common.delete') }} ${show.length}`"></span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal components --}}
    <livewire:clients.edit />
    <livewire:clients.delete />
    <livewire:clients.show />
</div>
