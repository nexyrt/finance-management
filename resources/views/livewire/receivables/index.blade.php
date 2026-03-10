<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.receivable_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.manage_receivables_tracking') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Workflow Guide Button --}}
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.client_guide_btn') }}
            </button>

            <livewire:receivables.create @created="$refresh" />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_stat_total') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['total'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_stat_active') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['active'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_stat_pending') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['pending'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_stat_total_active_value') }}</p>
                    <p class="text-xl font-bold text-dark-900 dark:text-dark-50">
                        Rp {{ number_format($this->stats['total_principal_active'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filter Section --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">
            {{-- Filter Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-select.styled wire:model.live="typeFilter"
                    label="{{ __('common.type') }}"
                    :options="$this->typeOptions"
                    placeholder="{{ __('pages.all') }} {{ strtolower(__('common.type')) }}..." />

                <x-select.styled wire:model.live="statusFilter"
                    label="{{ __('common.status') }}"
                    :options="$this->statusOptions"
                    placeholder="{{ __('pages.all') }} {{ strtolower(__('common.status')) }}..." />
            </div>

            {{-- Search + Status Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('pages.rcv_search_placeholder') }}"
                            icon="magnifying-glass"
                            class="h-8" />
                    </div>
                    <div class="flex items-center gap-3">
                        @php $activeFilters = (int)!!$typeFilter + (int)!!$statusFilter + (int)!!$search; @endphp
                        @if ($activeFilters > 0)
                            <x-badge :text="$activeFilters . ' ' . __('pages.rcv_active_filters')" color="primary" size="sm" />
                            <x-button wire:click="clearFilters" icon="x-mark" color="zinc" outline size="sm">
                                {{ __('pages.clear_filter') }}
                            </x-button>
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">{{ __('pages.rcv_showing') }} </span>{{ $this->rows->count() }}
                            <span class="hidden sm:inline">{{ __('pages.rcv_of') }} {{ $this->rows->total() }}</span> {{ __('pages.rcv_results') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate>

        @interact('column_receivable_number', $row)
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->receivable_number }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->type === 'employee_loan' ? __('pages.rcv_type_employee') : __('pages.rcv_type_company') }}
                    </div>
                </div>
            </div>
        @endinteract

        @interact('column_debtor', $row)
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-secondary-100 dark:bg-dark-700 rounded-lg flex items-center justify-center shrink-0">
                    <x-icon name="{{ $row->type === 'employee_loan' ? 'user' : 'building-office' }}"
                        class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                </div>
                <div>
                    <div class="font-medium text-dark-900 dark:text-dark-50">{{ $row->debtor?->name }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 truncate max-w-[180px]">{{ $row->purpose }}</div>
                </div>
            </div>
        @endinteract

        @interact('column_principal_amount', $row)
            @php
                $totalPrincipalPaid = $row->payments_sum_principal_paid ?? 0;
                $remainingPrincipal = $row->principal_amount - $totalPrincipalPaid;
                $percentage = $row->principal_amount > 0 ? ($totalPrincipalPaid / $row->principal_amount) * 100 : 0;
            @endphp
            <div>
                <div class="font-semibold text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($row->principal_amount, 0, ',', '.') }}
                </div>
                @if ($totalPrincipalPaid > 0)
                    <div class="mt-1.5">
                        <div class="w-full bg-dark-200 dark:bg-dark-700 rounded-full h-1.5">
                            <div class="bg-green-500 h-1.5 rounded-full transition-all"
                                style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">
                            {{ __('pages.rcv_paid_label') }}: Rp {{ number_format($totalPrincipalPaid, 0, ',', '.') }}
                        </div>
                    </div>
                @endif
                <div class="text-xs mt-1">
                    <span class="text-dark-500 dark:text-dark-400">{{ __('pages.rcv_remaining') }}:</span>
                    <span class="font-medium text-orange-600 dark:text-orange-400">
                        Rp {{ number_format($remainingPrincipal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endinteract

        @interact('column_interest_amount', $row)
            @php
                $totalInterest = round(($row->principal_amount * $row->interest_rate) / 100);
                $totalInterestPaid = $row->payments_sum_interest_paid ?? 0;
                $remainingInterest = $totalInterest - $totalInterestPaid;
            @endphp
            <div>
                <div class="font-semibold text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($totalInterest, 0, ',', '.') }}
                </div>
                @if ($row->interest_rate > 0)
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->interest_rate }}% / {{ __('pages.rcv_per_year') }}
                    </div>
                @endif
                @if ($remainingInterest > 0)
                    <div class="text-xs mt-1">
                        <span class="text-dark-500 dark:text-dark-400">{{ __('pages.rcv_remaining') }}:</span>
                        <span class="font-medium text-orange-600 dark:text-orange-400">
                            Rp {{ number_format($remainingInterest, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_installment_months', $row)
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-secondary-100 dark:bg-dark-700 rounded-lg">
                    <x-icon name="calendar" class="w-3.5 h-3.5 text-dark-500 dark:text-dark-400" />
                    <span class="font-semibold text-dark-900 dark:text-dark-50 text-sm">{{ $row->installment_months ?? '-' }}</span>
                    <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_months') }}</span>
                </div>
                @if ($row->installment_amount)
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        Rp {{ number_format($row->installment_amount, 0, ',', '.') }}/{{ __('pages.rcv_month_abbr') }}
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_loan_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->loan_date?->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">
                    {{ __('pages.rcv_due_date_label') }}: {{ $row->due_date?->format('d M Y') }}
                </div>
                @if ($row->due_date?->isPast() && $row->status === 'active')
                    <div class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-red-50 dark:bg-red-900/20 rounded text-xs text-red-600 dark:text-red-400 font-medium">
                        <x-icon name="exclamation-triangle" class="w-3 h-3" />
                        {{ __('pages.rcv_overdue') }}
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_status', $row)
            <x-badge :text="match ($row->status) {
                'draft'            => __('pages.rcv_status_draft'),
                'pending_approval' => __('pages.rcv_status_pending'),
                'active'           => __('pages.rcv_status_active'),
                'paid_off'         => __('pages.rcv_status_paid_off'),
                'rejected'         => __('pages.rcv_status_rejected'),
                default            => ucfirst($row->status),
            }" :color="match ($row->status) {
                'draft'            => 'gray',
                'pending_approval' => 'yellow',
                'active'           => 'blue',
                'paid_off'         => 'green',
                'rejected'         => 'red',
                default            => 'gray',
            }" />
        @endinteract

        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                @if ($row->status === 'draft')
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click="submitReceivable({{ $row->id }})"
                        title="{{ __('pages.rcv_action_submit') }}" />
                @endif

                @if ($row->status === 'pending_approval' && auth()->user()->can('approve receivables'))
                    <x-button.circle icon="check" color="green" size="sm"
                        wire:click="$dispatch('approve::receivable', { receivable: '{{ $row->id }}' })"
                        title="{{ __('pages.rcv_action_approve') }}" />
                @endif

                @if ($row->status === 'active')
                    <x-button.circle icon="currency-dollar" color="green" size="sm"
                        wire:click="$dispatch('load::pay-receivable', { receivable: '{{ $row->id }}' })"
                        title="{{ __('pages.rcv_action_pay') }}" />
                @endif

                @if (in_array($row->status, ['draft', 'rejected']))
                    <x-button.circle icon="pencil" color="blue" size="sm"
                        wire:click="$dispatch('load::receivable', { receivable: '{{ $row->id }}' })"
                        title="{{ __('common.edit') }}" />
                @endif

                @if ($row->status === 'draft')
                    <livewire:receivables.delete :receivable="$row" :key="uniqid()" @deleted="$refresh" />
                @endif
            </div>
        @endinteract
    </x-table>

    {{-- Child Components --}}
    <livewire:receivables.update @updated="$refresh" />
    <livewire:receivables.approve @approved="$refresh" />
    <livewire:receivables.pay-receivable @paid="$refresh" />

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="4xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="map" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.rcv_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        {{-- Tab-based Guide --}}
        <div x-data="{ tab: 'workflow' }" class="space-y-5">

            {{-- Tab Navigation --}}
            <div class="flex gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <button
                    @click="tab = 'workflow'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'workflow'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrow-path" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.rcv_guide_tab_flow') }}</span>
                </button>
                <button
                    @click="tab = 'status'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'status'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="tag" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.rcv_guide_tab_status') }}</span>
                </button>
            </div>

            {{-- Tab: Alur Kerja --}}
            <div x-show="tab === 'workflow'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0">

                <div class="relative">
                    {{-- Timeline connector --}}
                    <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 via-amber-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:via-amber-700 dark:to-emerald-700 hidden sm:block"></div>

                    <div class="space-y-4">
                        {{-- Step 1 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                <span class="text-white font-bold text-sm">1</span>
                            </div>
                            <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="document-plus" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50 text-sm">{{ __('pages.rcv_guide_step1_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-2">{{ __('pages.rcv_guide_step1_desc') }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-md text-xs">
                                        <x-icon name="user" class="w-3 h-3" />
                                        {{ __('pages.rcv_guide_step1_tip1') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-md text-xs">
                                        <x-icon name="currency-dollar" class="w-3 h-3" />
                                        {{ __('pages.rcv_guide_step1_tip2') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                <span class="text-white font-bold text-sm">2</span>
                            </div>
                            <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="paper-airplane" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50 text-sm">{{ __('pages.rcv_guide_step2_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.rcv_guide_step2_desc') }}</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-amber-900/40 z-10">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <div class="flex-1 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="clipboard-document-check" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50 text-sm">{{ __('pages.rcv_guide_step3_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-2">{{ __('pages.rcv_guide_step3_desc') }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-md text-xs">
                                        <x-icon name="check-circle" class="w-3 h-3" />
                                        {{ __('pages.rcv_guide_step3_approved') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-md text-xs">
                                        <x-icon name="x-circle" class="w-3 h-3" />
                                        {{ __('pages.rcv_guide_step3_rejected') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                <span class="text-white font-bold text-sm">4</span>
                            </div>
                            <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="banknotes" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    <h4 class="font-semibold text-dark-900 dark:text-dark-50 text-sm">{{ __('pages.rcv_guide_step4_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.rcv_guide_step4_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab: Status & Tipe --}}
            <div x-show="tab === 'status'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-5">

                {{-- Status Legend --}}
                <div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.rcv_guide_status_title') }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-xl">
                            <div class="w-2.5 h-2.5 rounded-full bg-gray-400 shrink-0"></div>
                            <div>
                                <div class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_status_draft') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_guide_status_draft_desc') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-900/40 rounded-xl">
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-400 shrink-0"></div>
                            <div>
                                <div class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_status_pending') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_guide_status_pending_desc') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></div>
                            <div>
                                <div class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_status_active') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_guide_status_active_desc') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/40 rounded-xl">
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500 shrink-0"></div>
                            <div>
                                <div class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_status_paid_off') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_guide_status_paid_desc') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/40 rounded-xl sm:col-span-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0"></div>
                            <div>
                                <div class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_status_rejected') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.rcv_guide_status_rejected_desc') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tipe Piutang --}}
                <div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.rcv_guide_type_title') }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <x-icon name="user" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_guide_type_employee') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.rcv_guide_type_employee_desc') }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <x-icon name="building-office" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.rcv_guide_type_company') }}</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.rcv_guide_type_company_desc') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Role Access --}}
                <div class="p-4 bg-zinc-50 dark:bg-dark-700 border border-zinc-200 dark:border-dark-600 rounded-xl">
                    <h4 class="text-xs font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.rcv_guide_role_title') }}</h4>
                    <div class="space-y-1.5 text-xs text-dark-600 dark:text-dark-400">
                        <div class="flex items-center gap-2">
                            <x-icon name="shield-check" class="w-3.5 h-3.5 text-indigo-500 shrink-0" />
                            <span><strong>Finance Manager / Admin:</strong> {{ __('pages.rcv_guide_role_finance') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="user" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                            <span><strong>Staff:</strong> {{ __('pages.rcv_guide_role_staff') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-modal>
</div>
