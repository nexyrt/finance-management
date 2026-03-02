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
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.cf_income_guide_btn') }}
            </button>
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
                <x-select.styled wire:model.live="clientFilters" :request="route('api.clients')"
                    label="{{ __('common.clients') }}" placeholder="{{ __('pages.all_clients') }}" multiple searchable />
                <x-select.styled wire:model.live="categoryFilters"
                    :request="['url' => route('api.transaction-categories'), 'method' => 'get', 'params' => ['type' => 'income']]"
                    label="{{ __('common.category') }}" placeholder="{{ __('pages.all_categories') }}" multiple searchable />
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
    <x-table :$headers :$sort :rows="$this->rows" selectable selectable-property="uid" wire:model="selected" paginate>

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

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.cf_income_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.cf_income_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div x-data="{ tab: 'workflow' }" class="space-y-5">
            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <button
                    @click="tab = 'workflow'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'workflow'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrow-path" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.cf_income_guide_tab_workflow') }}</span>
                </button>
                <button
                    @click="tab = 'features'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'features'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="sparkles" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.cf_income_guide_tab_features') }}</span>
                </button>
            </div>

            {{-- TAB 1: ALUR KERJA --}}
            <div x-show="tab === 'workflow'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="relative">
                    <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-gradient-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block"></div>
                    <div class="space-y-4">
                        {{-- Step 1 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                <span class="text-white font-bold text-sm">1</span>
                            </div>
                            <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="plus-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.cf_income_guide_step1_title') }}</h4>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.cf_income_guide_step1_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_income_guide_step1_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_income_guide_step1_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                <span class="text-white font-bold text-sm">2</span>
                            </div>
                            <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="tag" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.cf_income_guide_step2_title') }}</h4>
                                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.cf_income_guide_step2_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_income_guide_step2_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_income_guide_step2_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="document-arrow-down" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.cf_income_guide_step3_title') }}</h4>
                                        <p class="text-sm text-emerald-700 dark:text-emerald-300 mb-2">{{ __('pages.cf_income_guide_step3_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_income_guide_step3_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.cf_income_guide_step3_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: FITUR & FILTER --}}
            <div x-show="tab === 'features'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">
                    {{-- Data Sources --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="circle-stack" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">{{ __('pages.cf_income_guide_source_title') }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.cf_income_guide_source_payment') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.cf_income_guide_source_payment_desc') }}</p>
                                    </div>
                                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.cf_income_guide_source_direct') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.cf_income_guide_source_direct_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="funnel" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.cf_income_guide_filter_title') }}</h4>
                                <div class="space-y-1.5 mt-2">
                                    <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_income_guide_filter1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_income_guide_filter2') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_income_guide_filter3') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bulk Actions --}}
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="squares-2x2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.cf_income_guide_bulk_title') }}</h4>
                                <div class="space-y-1.5 mt-2">
                                    <div class="flex items-start gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_income_guide_bulk1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.cf_income_guide_bulk2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.client_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
