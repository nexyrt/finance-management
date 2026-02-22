<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.cat_page_title') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.cat_page_description') }}
            </p>
        </div>
        <livewire:transactions-categories.create @created="$refresh" />
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="tag" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.cat_total_categories') }}</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $this->stats['total'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="folder" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.cat_parent_categories') }}</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $this->stats['parents'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="squares-2x2" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.cat_child_categories') }}</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $this->stats['children'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.cat_in_use') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $this->stats['with_transactions'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {{-- Type Filter --}}
            <div>
                <x-select.styled wire:model.live="typeFilter" :label="__('pages.cat_type_filter_label')" :options="[
                    ['label' => __('pages.cat_income'), 'value' => 'income'],
                    ['label' => __('pages.cat_expense'), 'value' => 'expense'],
                    ['label' => __('pages.cat_adjustment'), 'value' => 'adjustment'],
                    ['label' => __('pages.cat_transfer'), 'value' => 'transfer'],
                ]"
                    :placeholder="__('pages.cat_type_all_placeholder')" />
            </div>

            {{-- Search --}}
            <div class="sm:col-span-2">
                <x-input wire:model.live.debounce.300ms="search"
                    :label="__('pages.cat_search_label')"
                    :placeholder="__('pages.cat_search_placeholder')"
                    icon="magnifying-glass" />
            </div>
        </div>

        {{-- Active Filter Indicator --}}
        @php
            $activeFilters = collect([$typeFilter, $search])
                ->filter()
                ->count();
        @endphp

        @if ($activeFilters > 0)
            <div class="flex items-center justify-between">
                <x-badge text="{{ $activeFilters }} {{ __('pages.filter_active') }}" color="primary" size="sm" />
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('pages.showing') }} {{ $this->rows->count() }} {{ __('pages.of') }} {{ $this->rows->total() }} {{ __('pages.cat_result_count') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->rows" paginate loading>

        {{-- Type Column --}}
        @interact('column_type', $row)
            <x-badge :text="ucfirst($row->type)" :color="match ($row->type) {
                'income' => 'green',
                'expense' => 'red',
                'adjustment' => 'yellow',
                'transfer' => 'blue',
                default => 'gray',
            }" :icon="match ($row->type) {
                'income' => 'arrow-trending-up',
                'expense' => 'arrow-trending-down',
                'adjustment' => 'adjustments-horizontal',
                'transfer' => 'arrows-right-left',
                default => 'tag',
            }" />
        @endinteract

        {{-- Label Column --}}
        @interact('column_label', $row)
            <div class="font-medium text-dark-900 dark:text-dark-50">
                {{ translate_text($row->label) }}
            </div>
        @endinteract

        {{-- Parent Column --}}
        @interact('column_parent', $row)
            @if ($row->parent)
                <div class="flex items-center gap-2">
                    <x-icon name="arrow-turn-down-right" class="w-4 h-4 text-gray-400 dark:text-gray-600" />
                    <span class="text-sm text-dark-600 dark:text-dark-400">{{ translate_text($row->parent->label) }}</span>
                </div>
            @else
                <x-badge text="Parent" color="gray" light size="sm" />
            @endif
        @endinteract

        {{-- Usage Column --}}
        @interact('column_usage', $row)
            <div class="text-sm text-dark-600 dark:text-dark-400">
                <div class="flex items-center gap-1">
                    <x-icon name="document-text" class="w-4 h-4" />
                    <span>{{ __('pages.cat_transactions_count', ['count' => $row->transactions_count]) }}</span>
                </div>
                @if ($row->children_count > 0)
                    <div class="flex items-center gap-1 mt-1">
                        <x-icon name="squares-2x2" class="w-4 h-4" />
                        <span>{{ __('pages.cat_children_count', ['count' => $row->children_count]) }}</span>
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Actions Column --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="pencil" color="blue" size="sm"
                    wire:click="$dispatch('load::category', { category: '{{ $row->id }}' })"
                    :title="__('common.edit')" />
                <livewire:transactions-categories.delete :category="$row" :key="uniqid()" @deleted="$refresh" />
            </div>
        @endinteract

    </x-table>

    {{-- Child Components --}}
    <livewire:transactions-categories.update @updated="$refresh" />
</div>
