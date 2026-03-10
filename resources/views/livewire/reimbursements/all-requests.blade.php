<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Requests --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_total') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['total'], 0, ',', '.') }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Rp {{ number_format($this->stats['total_amount'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </x-card>

        {{-- Pending Review --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_pending') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['pending_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">Rp {{ number_format($this->stats['pending_amount'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </x-card>

        {{-- Approved --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="check-badge" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_approved') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['approved_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400">Rp {{ number_format($this->stats['approved_amount'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </x-card>

        {{-- Paid --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="banknotes" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_paid') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['paid_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Rp {{ number_format($this->stats['total_paid'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filter Section --}}
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <x-select.styled wire:model.blur="statusFilter"
                :label="__('common.status')"
                :options="$this->statusOptions"
                :placeholder="__('pages.reimb_all_status_placeholder')" />

            <x-select.styled wire:model.blur="categoryFilter"
                :label="__('common.category')"
                :options="$this->categoryOptions"
                :placeholder="__('pages.reimb_all_categories_placeholder')" />

            <x-date wire:model.blur="dateRange"
                :label="__('pages.reimb_date_range_label')"
                range
                :placeholder="__('pages.reimb_date_range_placeholder')" />
        </div>

        {{-- Search + Status Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                <div class="w-full sm:w-64">
                    <x-input wire:model.live.debounce.300ms="search"
                        :placeholder="__('common.search') . '...'"
                        icon="magnifying-glass"
                        class="h-8" />
                </div>
                <div class="flex items-center gap-3">
                    @if ($statusFilter || $categoryFilter || !empty($dateRange) || $search)
                        <x-badge :text="collect([$statusFilter, $categoryFilter, !empty($dateRange) ? 'date' : null, $search ? 'search' : null])->filter()->count() . ' ' . __('pages.reimb_active_filters')" color="primary" size="sm" />
                    @endif
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="hidden sm:inline">{{ __('common.showing') }} </span>{{ $this->rows->count() }}
                        <span class="hidden sm:inline">{{ __('common.of') }} {{ $this->rows->total() }}</span>
                    </div>
                </div>
            </div>
            @if ($statusFilter || $categoryFilter || !empty($dateRange) || $search)
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
                    <div class="text-xs text-dark-500 dark:text-dark-400 line-clamp-1 mt-0.5">{{ $row->description }}</div>
                @endif
            </div>
        @endinteract

        {{-- User Column --}}
        @interact('column_user', $row)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-linear-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow shrink-0">
                    <span class="text-white font-semibold text-xs">{{ $row->user->initials() }}</span>
                </div>
                <div class="min-w-0">
                    <div class="font-medium text-dark-900 dark:text-dark-50 text-sm">{{ $row->user->name }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 truncate">{{ $row->user->email }}</div>
                </div>
            </div>
        @endinteract

        {{-- Amount Column --}}
        @interact('column_amount', $row)
            <div class="min-w-[160px]">
                <div class="font-bold text-base text-dark-900 dark:text-dark-50">
                    {{ $row->formatted_amount }}
                </div>
                @if ($row->amount_paid > 0)
                    @php $pct = min(($row->amount_paid / $row->amount) * 100, 100); @endphp
                    <div class="mt-1.5 space-y-1">
                        <div class="w-full bg-gray-100 dark:bg-[#27272a] rounded-full h-1.5">
                            <div class="{{ $row->isFullyPaid() ? 'bg-emerald-500' : 'bg-amber-500' }} h-1.5 rounded-full transition-all"
                                style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="{{ $row->isFullyPaid() ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ number_format($pct, 0) }}%
                            </span>
                            @if ($row->amount_remaining > 0)
                                <span class="text-dark-400 dark:text-dark-500">sisa {{ $row->formatted_amount_remaining }}</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ __('pages.reimb_not_paid_yet') }}</div>
                @endif
            </div>
        @endinteract

        {{-- Category Column --}}
        @interact('column_category', $row)
            <x-badge :text="$row->category_label" :color="match ($row->category_input) {
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
                <div class="text-xs text-dark-400 dark:text-dark-500">
                    {{ $row->expense_date->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Status Column --}}
        @interact('column_status', $row)
            <x-badge :text="$row->status_label" :color="$row->status_badge_color" />
        @endinteract

        {{-- Payment Status Column --}}
        @interact('column_payment_status', $row)
            <x-badge :text="$row->payment_status_label" :color="$row->payment_status_badge_color" />
        @endinteract

        {{-- Actions Column --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                {{-- View --}}
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="$dispatch('load::reimbursement', { id: {{ $row->id }} })"
                    :title="__('pages.reimb_view_tooltip')" />

                {{-- Review (Pending only) --}}
                @if ($row->canReview() && auth()->user()->can('approve reimbursements'))
                    <x-button.circle icon="clipboard-document-check" color="yellow" size="sm"
                        wire:click="$dispatch('review::reimbursement', { id: {{ $row->id }} })"
                        :title="__('pages.reimb_review_btn')" />
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
