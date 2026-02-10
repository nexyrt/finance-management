<div class="space-y-6">
    {{-- Filter Section (NO TITLE, NO BORDER!) --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">

            {{-- Main Filters Grid (Responsive) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                {{-- Status Filter --}}
                <x-select.styled wire:model.live="statusFilter"
                                 :label="__('pages.status')"
                                 :options="[
                                     ['label' => __('pages.all_statuses'), 'value' => ''],
                                     ['label' => __('pages.status_draft'), 'value' => 'draft'],
                                     ['label' => __('pages.status_pending'), 'value' => 'pending'],
                                     ['label' => __('pages.status_approved'), 'value' => 'approved'],
                                     ['label' => __('pages.status_rejected'), 'value' => 'rejected'],
                                     ['label' => __('pages.status_disbursed'), 'value' => 'disbursed'],
                                 ]" />

                {{-- Priority Filter --}}
                <x-select.styled wire:model.live="priorityFilter"
                                 :label="__('pages.priority')"
                                 :options="[
                                     ['label' => __('pages.all_priorities'), 'value' => ''],
                                     ['label' => __('pages.priority_low'), 'value' => 'low'],
                                     ['label' => __('pages.priority_medium'), 'value' => 'medium'],
                                     ['label' => __('pages.priority_high'), 'value' => 'high'],
                                     ['label' => __('pages.priority_urgent'), 'value' => 'urgent'],
                                 ]" />

                {{-- Month Filter --}}
                <div>
                    <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1.5">{{ __('pages.filter_month') }}</label>
                    <input type="month"
                           wire:model.live="monthFilter"
                           class="w-full h-10 px-3 rounded-xl border border-secondary-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                </div>
            </div>

            {{-- Search Bar + Filter Status Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                {{-- Left: Search + Status Info --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">

                    {{-- Search Field (Fixed Width) --}}
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                                 placeholder="{{ __('pages.search_fund_requests') }}"
                                 icon="magnifying-glass"
                                 class="h-8" />
                    </div>

                    {{-- Active Filters Badge + Result Count --}}
                    <div class="flex items-center gap-3">
                        @if ($this->activeFilters > 0)
                            <x-badge text="{{ $this->activeFilters }} {{ __('pages.filter_active') }}" color="primary" size="sm" />
                        @endif

                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.showing') }} </span>{{ $this->rows->count() }}
                            <span class="hidden sm:inline">{{ __('pages.of') }} {{ $this->rows->total() }}</span> {{ __('pages.results') }}
                        </div>
                    </div>
                </div>

                {{-- Right: Export PDF + Clear Filters --}}
                <div class="flex items-center gap-2">
                    {{-- Export PDF --}}
                    <a href="{{ $this->getExportUrl() }}"
                       target="_blank"
                       wire:navigate.hover
                       class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <x-icon name="document-arrow-down" class="w-4 h-4" />
                        {{ __('pages.export_pdf') }}
                    </a>

                    {{-- Clear Filters --}}
                    @if ($this->activeFilters > 0)
                        <x-button wire:click="clearFilters" color="zinc" size="sm">
                            <x-slot:left>
                                <x-icon name="x-mark" class="w-4 h-4" />
                            </x-slot:left>
                            {{ __('pages.clear_filters') }}
                        </x-button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :rows="$this->rows" :$sort paginate loading>
        @interact('column_request_number', $row)
            <div class="font-mono font-semibold text-primary-600 dark:text-primary-400">
                {{ $row->request_number ?? '-' }}
            </div>
        @endinteract

        @interact('column_title', $row)
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <x-icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->title }}</span>
                    <span class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ Str::limit($row->purpose, 60) }}</span>
                </div>
            </div>
        @endinteract

        @interact('column_total_amount', $row)
            <div class="flex items-center gap-2">
                <div class="flex flex-col">
                    <span class="text-lg font-bold text-dark-900 dark:text-dark-50">
                        Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                    </span>
                    @if ($row->items_count ?? $row->items->count() ?? 0)
                        <span class="text-xs text-dark-500 dark:text-dark-400">
                            {{ __('pages.items_count', ['count' => $row->items_count ?? $row->items->count()]) }}
                        </span>
                    @endif
                </div>
            </div>
        @endinteract

        @interact('column_priority', $row)
            <div class="flex items-center gap-2">
                @if ($row->priority === 'low')
                    <div class="h-8 w-8 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-down" class="w-4 h-4 text-green-600 dark:text-green-400" />
                    </div>
                @elseif ($row->priority === 'medium')
                    <div class="h-8 w-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="minus" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                @elseif ($row->priority === 'high')
                    <div class="h-8 w-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="arrow-up" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                    </div>
                @else {{-- urgent --}}
                    <div class="h-8 w-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="fire" class="w-4 h-4 text-red-600 dark:text-red-400" />
                    </div>
                @endif
                <span class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    @if ($row->priority === 'low')
                        {{ __('pages.priority_low') }}
                    @elseif ($row->priority === 'medium')
                        {{ __('pages.priority_medium') }}
                    @elseif ($row->priority === 'high')
                        {{ __('pages.priority_high') }}
                    @else
                        {{ __('pages.priority_urgent') }}
                    @endif
                </span>
            </div>
        @endinteract

        @interact('column_needed_by_date', $row)
            @php
                $neededDate = \Carbon\Carbon::parse($row->needed_by_date);
                $isOverdue = $neededDate->isPast() && in_array($row->status, ['pending', 'approved']);
                $daysUntil = now()->diffInDays($neededDate, false);
            @endphp
            <div class="flex items-center gap-2">
                @if ($isOverdue)
                    <div class="h-8 w-8 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="calendar" class="w-4 h-4 text-red-600 dark:text-red-400" />
                    </div>
                @else
                    <div class="h-8 w-8 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="calendar" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                @endif
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-dark-900 dark:text-dark-50">
                        {{ $neededDate->format('d M Y') }}
                    </span>
                    @if ($isOverdue)
                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">
                            <x-icon name="exclamation-triangle" class="w-3 h-3 inline" /> {{ __('pages.overdue_label') }}
                        </span>
                    @elseif ($daysUntil >= 0 && $daysUntil <= 7 && in_array($row->status, ['pending', 'approved']))
                        <span class="text-xs text-yellow-600 dark:text-yellow-400">
                            {{ __('pages.days_left', ['count' => $daysUntil]) }}
                        </span>
                    @endif
                </div>
            </div>
        @endinteract

        @interact('column_status', $row)
            @if ($row->status === 'draft')
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-secondary-50 dark:bg-secondary-900/20 rounded-lg border border-secondary-200 dark:border-secondary-800">
                    <x-icon name="document" class="w-4 h-4 text-secondary-600 dark:text-secondary-400" />
                    <span class="text-sm font-medium text-secondary-700 dark:text-secondary-300">{{ __('pages.status_draft') }}</span>
                </div>
            @elseif ($row->status === 'pending')
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <x-icon name="clock" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                    <span class="text-sm font-medium text-yellow-700 dark:text-yellow-300">{{ __('pages.status_pending') }}</span>
                </div>
            @elseif ($row->status === 'approved')
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">{{ __('pages.status_approved') }}</span>
                </div>
            @elseif ($row->status === 'rejected')
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <x-icon name="x-circle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                    <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ __('pages.status_rejected') }}</span>
                </div>
            @else {{-- disbursed --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                    <x-icon name="banknotes" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ __('pages.status_disbursed') }}</span>
                </div>
            @endif
        @endinteract

        @interact('column_created_at', $row)
            <div class="flex flex-col">
                <span class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}
                </span>
                <span class="text-xs text-dark-500 dark:text-dark-400">
                    {{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}
                </span>
            </div>
        @endinteract

        @interact('column_actions', $row)
            <div class="flex items-center gap-1.5">
                {{-- View --}}
                <x-button wire:click="$dispatch('show::fund-request', { id: {{ $row->id }} })"
                          color="zinc"
                          size="sm"
                          class="!px-2.5 !py-2"
                          title="{{ __('pages.view_details_tooltip') }}">
                    <x-icon name="eye" class="w-4 h-4" />
                </x-button>

                {{-- Edit (only draft or rejected) --}}
                @if ($row->canEdit())
                    <x-button wire:click="$dispatch('edit::fund-request', { id: {{ $row->id }} })"
                              color="blue"
                              size="sm"
                              class="!px-2.5 !py-2"
                              title="{{ __('pages.edit_request_tooltip') }}">
                        <x-icon name="pencil-square" class="w-4 h-4" />
                    </x-button>
                @endif

                {{-- Submit (only draft with items) --}}
                @if ($row->canSubmit())
                    <x-button wire:click="submitRequest({{ $row->id }})"
                              color="green"
                              size="sm"
                              class="!px-2.5 !py-2"
                              title="{{ __('pages.submit_for_approval_tooltip') }}">
                        <x-icon name="paper-airplane" class="w-4 h-4" />
                    </x-button>
                @endif

                {{-- Delete (only draft or rejected) --}}
                @if ($row->canDelete())
                    <x-button wire:click="$dispatch('delete::fund-request', { id: {{ $row->id }} })"
                              color="red"
                              size="sm"
                              class="!px-2.5 !py-2"
                              title="{{ __('pages.delete_request_tooltip') }}">
                        <x-icon name="trash" class="w-4 h-4" />
                    </x-button>
                @endif
            </div>
        @endinteract
    </x-table>
</div>

@script
<script>
    $wire.on('submit-request', (data) => {
        $wire.dispatch('confirm', {
            title: '{{ __('pages.submit_fund_request_title') }}',
            description: '{{ __('pages.submit_fund_request_description') }}',
            icon: 'question',
            accept: {
                label: '{{ __('pages.submit_for_approval') }}',
                color: 'green',
                execute: () => {
                    $wire.call('confirmSubmit', data[0].id);
                }
            }
        });
    });
</script>
@endscript
