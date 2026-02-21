<div class="space-y-6">

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
            <x-select.styled wire:model.blur="statusFilter" :label="__('common.status')" :options="$this->statusOptions" :placeholder="__('pages.reimb_all_status_placeholder')" />

            <x-select.styled wire:model.blur="categoryFilter" :label="__('common.category')" :options="$this->categoryOptions"
                :placeholder="__('pages.reimb_all_categories_placeholder')" />

            <x-date wire:model.blur="dateRange" :label="__('pages.reimb_date_range_label')" range :placeholder="__('pages.reimb_date_range_placeholder')" />
        </div>

        <div class="flex gap-2">
            @if ($statusFilter || $categoryFilter || !empty($dateRange))
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                    {{ __('pages.reimb_clear_filters_btn') }}
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
        {{-- Replace column_amount section only --}}
        @interact('column_amount', $row)
            <div class="min-w-[180px]">
                <div class="font-bold text-base text-dark-900 dark:text-dark-50">
                    {{ $row->formatted_amount }}
                </div>
                @if ($row->amount_paid > 0)
                    @php $pct = ($row->amount_paid / $row->amount) * 100; @endphp
                    <div class="mt-2 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-green-600 dark:text-green-400">{{ $row->formatted_amount_paid }}</span>
                            @if ($row->amount_remaining > 0)
                                <span class="text-amber-600">{{ __('pages.reimb_remaining_label') }} {{ $row->formatted_amount_remaining }}</span>
                            @endif
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-dark-700 rounded-full h-2">
                            <div class="{{ $row->isFullyPaid() ? 'bg-green-500' : 'bg-amber-500' }} h-2 rounded-full"
                                style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <div class="text-xs text-center {{ $row->isFullyPaid() ? 'text-green-600' : 'text-amber-600' }}">
                            {{ number_format($pct, 1) }}%
                        </div>
                    </div>
                @else
                    <div class="text-xs text-gray-500 mt-1">{{ __('pages.reimb_not_paid_yet') }}</div>
                @endif
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
                    wire:click="$dispatch('load::reimbursement', { id: {{ $row->id }} })" :title="__('pages.reimb_view_tooltip')" />

                {{-- Review (Pending only) --}}
                @if ($row->canReview() && auth()->user()->can('approve reimbursements'))
                    <x-button.circle icon="clipboard-document-check" color="yellow" size="sm"
                        wire:click="$dispatch('review::reimbursement', { id: {{ $row->id }} })" :title="__('pages.reimb_review_btn')" />
                @endif

                {{-- Pay (Approved only) --}}
                @if ($row->canPay() && auth()->user()->can('pay reimbursements'))
                    <x-button.circle icon="banknotes" color="green" size="sm"
                        wire:click="$dispatch('pay::reimbursement', { id: {{ $row->id }} })"
                        :title="__('pages.reimb_process_pay_btn')" />
                @endif
            </div>
        @endinteract
    </x-table>
</div>
