<div class="space-y-6">

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
            <x-select.styled wire:model.blur="statusFilter" label="Status" :options="$this->statusOptions" placeholder="All status..." />

            <x-select.styled wire:model.blur="categoryFilter" label="Category" :options="$this->categoryOptions"
                placeholder="All categories..." />

            <x-date wire:model.blur="dateRange" label="Date Range" range placeholder="Select range..." />
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
    <x-table :$headers :$sort :rows="$this->rows" paginate filter loading>

        {{-- Title Column --}}
        @interact('column_title', $row)
            <div>
                <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->title }}</div>
                @if ($row->description)
                    <div class="text-xs text-dark-500 dark:text-dark-400 line-clamp-1">{{ $row->description }}</div>
                @endif
            </div>
        @endinteract

        {{-- User Column --}}
        @interact('column_user', $row)
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-semibold text-sm">{{ $row->user->initials() }}</span>
                </div>
                <div>
                    <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->user->name }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->user->email }}</div>
                </div>
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

                {{-- Review (Pending only) --}}
                @if ($row->canReview() && auth()->user()->can('approve reimbursements'))
                    <x-button.circle icon="clipboard-document-check" color="yellow" size="sm"
                        wire:click="$dispatch('review::reimbursement', { id: {{ $row->id }} })" title="Review" />
                @endif

                {{-- Pay (Approved only) --}}
                @if ($row->canPay() && auth()->user()->can('pay reimbursements'))
                    <x-button.circle icon="banknotes" color="green" size="sm"
                        wire:click="$dispatch('pay::reimbursement', { id: {{ $row->id }} })"
                        title="Process Payment" />
                @endif
            </div>
        @endinteract
    </x-table>
</div>
