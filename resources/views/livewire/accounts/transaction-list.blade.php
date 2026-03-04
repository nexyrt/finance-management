<div class="space-y-6">
    {{-- Filter Section — wire:key forces full re-create on account switch (prevents TallStackUI Alpine morph errors) --}}
    <div class="space-y-4" wire:key="trx-filters-{{ $selectedAccountId }}">
        <div class="flex flex-col gap-4">
            {{-- Main Filters Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                {{-- Transaction Type --}}
                <x-select.styled wire:model.live="transaction_type" :label="__('pages.transaction_type_filter_label')" :options="[
                    ['label' => __('pages.all_types_placeholder'), 'value' => ''],
                    ['label' => __('pages.income_option'), 'value' => 'credit'],
                    ['label' => __('pages.expense_option'), 'value' => 'debit'],
                ]" :placeholder="__('pages.search_type_placeholder')" />

                {{-- Category Filter (API) --}}
                <x-select.styled wire:model.live="category_id"
                    :label="__('pages.category_filter_label')"
                    :placeholder="__('pages.search_category_placeholder')"
                    :request="route('api.transaction-categories')"
                    searchable />

                {{-- Month Picker --}}
                <x-date month-year-only wire:model.live="selected_month"
                    :label="__('pages.month_filter_label')"
                    :placeholder="__('pages.select_month_placeholder')" />
            </div>

            {{-- Search + Filter Status Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                    {{-- Search --}}
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                            :placeholder="__('pages.search_transaction_placeholder')"
                            icon="magnifying-glass" class="h-8" />
                    </div>

                    {{-- Active Filters + Result Count --}}
                    @php
                        $activeFilters = collect([
                            $transaction_type && $transaction_type !== '',
                            $category_id,
                            $selected_month,
                            $search,
                        ])->filter()->count();
                    @endphp

                    <div class="flex items-center gap-3">
                        @if ($activeFilters > 0)
                            <x-badge :text="__('pages.filters_active_count', ['count' => $activeFilters])" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.showing_from') }}</span>{{ $this->transactions->count() }}<span class="hidden sm:inline">{{ __('pages.showing_of') }}{{ $this->transactions->total() }}</span>{{ __('pages.transactions_unit') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->transactions" selectable wire:model="selected" paginate loading>

        {{-- Transaction Description with Icon --}}
        @interact('column_description', $row)
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 {{ $row->transaction_type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="{{ $row->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                        class="w-5 h-5 {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-dark-900 dark:text-dark-50 truncate">{{ $row->description ?: __('pages.no_description') }}</p>
                    @if ($row->reference_number)
                        <p class="text-xs text-dark-500 dark:text-dark-400 font-mono">{{ $row->reference_number }}</p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Category --}}
        @interact('column_category_id', $row)
            @if ($row->category)
                <x-badge :text="$row->category->label" :color="match ($row->category->type) {
                    'income' => 'green',
                    'expense' => 'red',
                    'adjustment' => 'yellow',
                    'transfer' => 'blue',
                    default => 'gray',
                }" :icon="match ($row->category->type) {
                    'income' => 'arrow-trending-up',
                    'expense' => 'arrow-trending-down',
                    'adjustment' => 'adjustments-horizontal',
                    'transfer' => 'arrows-right-left',
                    default => 'tag',
                }" size="sm" />
                @if ($row->category->parent)
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">{{ $row->category->parent->label }}</p>
                @endif
            @else
                <x-badge :text="__('pages.uncategorized')" color="gray" size="sm" />
            @endif
        @endinteract

        {{-- Transaction Date --}}
        @interact('column_transaction_date', $row)
            <div>
                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->transaction_date->format('d M Y') }}
                </p>
                <p class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->created_at->format('H:i') }}
                </p>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <p class="font-bold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp {{ number_format($row->amount, 0, ',', '.') }}
                </p>
                <p class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->transaction_type === 'credit' ? __('pages.income_label') : __('pages.expense_label') }}
                </p>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex justify-center gap-1">
                @if ($row->attachment_path)
                    <a href="{{ asset('storage/' . $row->attachment_path) }}" target="_blank" rel="noopener">
                        <x-button.circle color="blue" icon="paper-clip" size="sm" />
                    </a>
                @endif
                <x-button.circle wire:click="deleteTransaction({{ $row->id }})"
                    loading="deleteTransaction({{ $row->id }})" color="red" icon="trash" size="sm" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        wire:key="trx-bulk-{{ $selectedAccountId }}"
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length}{{ __('pages.bulk_selected') }}`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.bulk_action_hint') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash" class="whitespace-nowrap">
                        {{ __('pages.bulk_delete_btn') }}
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark" class="whitespace-nowrap">
                        {{ __('pages.bulk_cancel_btn') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
