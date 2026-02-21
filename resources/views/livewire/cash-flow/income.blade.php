<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.income') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.income_description') }}
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <x-button wire:click="exportPdf" color="red" icon="document-text" size="sm" loading="exportPdf">
                {{ __('pages.export_pdf') }}
            </x-button>
            <x-button wire:click="export" color="green" icon="arrow-down-tray" size="sm" loading="export">
                {{ __('pages.export_excel') }}
            </x-button>
            <livewire:transactions.create-income @transaction-created="$refresh" />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_income') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->totalIncome, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_transactions') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $this->rows->total() }}
                    </p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="calendar" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.period') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        @if (!empty($dateRange) && count($dateRange) >= 2)
                            {{ \Carbon\Carbon::parse($dateRange[0])->format('d M') }} -
                            {{ \Carbon\Carbon::parse($dateRange[1])->format('d M Y') }}
                        @else
                            {{ __('pages.all_time') }}
                        @endif
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filter Section --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <x-date wire:model.live="dateRange" label="{{ __('pages.period') }}" range placeholder="{{ __('pages.select_date_range') }}" />
                <x-select.styled wire:model.live="clientFilters" label="{{ __('common.clients') }}" :options="$this->clients"
                    placeholder="{{ __('pages.all_clients') }}" multiple searchable />
                <x-select.styled wire:model.live="categoryFilters" label="{{ __('common.category') }}" :options="$this->incomeCategories"
                    placeholder="{{ __('pages.all_categories') }}" multiple searchable />
                <x-input wire:model.live.debounce.300ms="search" label="{{ __('common.search') }}" placeholder="{{ __('pages.search_data') }}"
                    icon="magnifying-glass" />
            </div>

            @php
                $activeFilters = collect([
                    !empty($dateRange) && count($dateRange) >= 1,
                    !empty($clientFilters),
                    !empty($categoryFilters),
                    $search,
                ])->filter()->count();
            @endphp

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                    <div class="flex items-center gap-3">
                        @if ($activeFilters > 0)
                            <x-badge text="{{ $activeFilters }} {{ __('pages.filter_active') }}" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.showing') }} </span>{{ $this->rows->count() }}
                            <span class="hidden sm:inline">{{ __('pages.of') }} {{ $this->rows->total() }}</span> {{ __('pages.results') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable selectable-property="uid" wire:model="selected" paginate loading>

        @interact('column_date', $row)
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-dark-700 dark:to-dark-600 rounded-lg flex items-center justify-center">
                    <x-icon name="calendar" class="w-5 h-5 text-secondary-600 dark:text-secondary-400" />
                </div>
                <div>
                    <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ \Carbon\Carbon::parse($row->date)->diffForHumans() }}
                    </div>
                </div>
            </div>
        @endinteract

        @interact('column_source_type', $row)
            @if ($row->source_type === 'payment')
                <x-badge text="{{ __('common.invoices') }}" color="blue" icon="document-text" size="sm" />
            @else
                <x-badge text="{{ __('pages.direct') }}" color="green" icon="banknotes" size="sm" />
            @endif
        @endinteract

        @interact('column_client_description', $row)
            <div class="max-w-xs">
                @if ($row->source_type === 'payment')
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                        {{ $row->client_name }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->invoice_number }}
                    </div>
                @else
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 line-clamp-2">
                        {{ $row->description ?? '-' }}
                    </div>
                @endif
                @if ($row->reference_number)
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                        <span class="text-xs text-dark-500 dark:text-dark-400 font-mono">{{ $row->reference_number }}</span>
                    </div>
                @endif
                @if ($row->attachment_path)
                    <div class="flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 mt-1">
                        <x-icon name="paper-clip" class="w-3 h-3" />
                        <span class="font-medium">{{ __('pages.has_attachment') }}</span>
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_category_label', $row)
            @if ($row->category_label)
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                    <x-icon name="tag" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                    <span class="text-xs font-medium text-purple-700 dark:text-purple-300">{{ translate_category($row->category_label) }}</span>
                </div>
            @else
                <span class="text-xs text-dark-400">-</span>
            @endif
        @endinteract

        @interact('column_amount', $row)
            <div class="text-right">
                <div class="text-xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->bank_name }}
                </div>
            </div>
        @endinteract

        @interact('column_action', $row)
            <div class="flex items-center justify-center gap-1">
                @if ($row->attachment_path)
                    <x-button.circle icon="paper-clip" color="primary" size="sm"
                        wire:click="viewAttachment('{{ $row->source_type }}', {{ $row->id }})"
                        title="{{ __('pages.view_attachment') }}" />
                @endif
                @if ($row->source_type === 'payment')
                    <x-button.circle icon="pencil" color="blue" size="sm"
                        wire:click="editPayment({{ $row->id }})"
                        title="{{ __('common.edit') }}" />
                @endif
                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deleteItem('{{ $row->source_type }}', {{ $row->id }})"
                    title="{{ __('common.delete') }}" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} {{ __('pages.items_selected') }}`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.select_action_for_selected') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="exportSelected" size="sm" color="green" icon="arrow-down-tray"
                        loading="exportSelected" class="whitespace-nowrap">{{ __('common.export') }}</x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="executeBulkDelete" class="whitespace-nowrap">{{ __('common.delete') }}</x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="secondary" icon="x-mark"
                        class="whitespace-nowrap">{{ __('common.cancel') }}</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Child Components --}}
    <livewire:cash-flow.attachment-viewer />
    <livewire:payments.edit @payment-updated="$refresh" />
    <livewire:payments.delete @payment-deleted="$refresh" />
    <livewire:transactions.delete @transaction-deleted="$refresh" />
</div>
