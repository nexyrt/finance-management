<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
            <x-select.styled wire:model.live="statusFilter" label="Status" :options="$this->statusOptions" placeholder="All status..." />

            <x-select.styled wire:model.live="categoryFilter" label="Category" :options="$this->categoryOptions"
                placeholder="All categories..." />

            <x-date wire:model.live="dateRange" label="Date Range" range placeholder="Select range..." />
        </div>

        <div class="flex gap-2">
            @if ($statusFilter || $categoryFilter || !empty($dateRange))
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                    Clear
                </x-button>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <x-table :headers="$this->headers" :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

        {{-- Title Column --}}
        @interact('column_title', $row)
            <div>
                <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->title }}</div>
                @if ($row->description)
                    <div class="text-xs text-dark-500 dark:text-dark-400 line-clamp-1">{{ $row->description }}</div>
                @endif
            </div>
        @endinteract

        {{-- Amount Column --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-dark-900 dark:text-dark-50">
                    {{ $row->formatted_amount }}
                </div>
            </div>
        @endinteract

        {{-- Category Column --}}
        @interact('column_category', $row)
            <x-badge :text="$row->category_label" :color="match ($row->category) {
                'transport' => 'blue',
                'meals' => 'orange',
                'office_supplies' => 'green',
                'communication' => 'purple',
                'accommodation' => 'pink',
                'medical' => 'red',
                default => 'gray',
            }" />
        @endinteract

        {{-- Date Column --}}
        @interact('column_expense_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->expense_date->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->expense_date->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Status Column --}}
        @interact('column_status', $row)
            <x-badge :text="$row->status_label" :color="$row->status_badge_color" />
        @endinteract

        {{-- Actions Column --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                {{-- View --}}
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="$dispatch('load::reimbursement', { id: {{ $row->id }} })" title="View" />

                {{-- Edit (Draft/Rejected only) --}}
                @if ($row->canEdit())
                    <x-button.circle icon="pencil" color="green" size="sm"
                        wire:click="$dispatch('edit::reimbursement', { id: {{ $row->id }} })" title="Edit" />
                @endif

                {{-- Submit (Draft only) --}}
                @if ($row->canSubmit())
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click="submitRequest({{ $row->id }})" title="Submit for Approval" />
                @endif

                {{-- Delete (Draft only) --}}
                @if ($row->canDelete())
                    <livewire:reimbursements.delete :reimbursement="$row" :key="uniqid()" @deleted="$refresh" />
                @endif
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-dark-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50" x-text="`${show.length} selected`">
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">Choose action for selected items</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash"
                        class="whitespace-nowrap">
                        Delete
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
