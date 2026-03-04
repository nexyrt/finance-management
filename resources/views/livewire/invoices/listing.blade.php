<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Total Revenue --}}
        <x-card class="hover:shadow-lg transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="chart-bar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wide">{{ __('pages.total_revenue') }}</p>
                    <p class="text-xl font-bold text-dark-900 dark:text-dark-50 truncate mt-0.5">
                        Rp {{ number_format($this->stats['total_revenue'], 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="inline-flex items-center gap-1 text-xs text-dark-400 dark:text-dark-500">
                            <x-icon name="document-text" class="w-3 h-3" />
                            {{ $this->stats['invoice_count'] }} {{ strtolower(__('common.invoices')) }}
                        </span>
                        <span class="text-dark-200 dark:text-dark-600">·</span>
                        <span class="text-xs text-dark-400 dark:text-dark-500">
                            avg Rp {{ number_format($this->stats['average_invoice_value'] / 1000, 0) }}k
                        </span>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Total COGS --}}
        <x-card class="hover:shadow-lg transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <p class="text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wide">{{ __('pages.total_cogs_short') }}</p>
                        <x-tooltip :text="__('pages.total_cogs_tooltip')" position="top" color="zinc" />
                    </div>
                    <p class="text-xl font-bold text-dark-900 dark:text-dark-50 truncate mt-0.5">
                        Rp {{ number_format($this->stats['total_cogs'], 0, ',', '.') }}
                    </p>
                    @if ($this->stats['total_revenue'] > 0)
                        <div class="mt-1.5 flex items-center gap-2">
                            <div class="flex-1 bg-red-100 dark:bg-red-900/20 rounded-full h-1 max-w-[60px]">
                                <div class="bg-red-400 dark:bg-red-500 h-1 rounded-full"
                                    style="width: {{ min(($this->stats['total_cogs'] / $this->stats['total_revenue']) * 100, 100) }}%">
                                </div>
                            </div>
                            <span class="text-xs text-dark-400 dark:text-dark-500">
                                {{ number_format(($this->stats['total_cogs'] / $this->stats['total_revenue']) * 100, 1) }}% {{ __('pages.of_revenue') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Total Profit --}}
        <x-card class="hover:shadow-lg transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wide">{{ __('pages.total_profit') }}</p>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                            {{ number_format($this->stats['profit_margin'], 1) }}%
                        </span>
                    </div>
                    <p class="text-xl font-bold text-dark-900 dark:text-dark-50 truncate mt-0.5">
                        Rp {{ number_format($this->stats['total_profit'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-dark-400 dark:text-dark-500 mt-1.5">{{ __('pages.margin') }}</p>
                </div>
            </div>
        </x-card>

        {{-- Outstanding Profit --}}
        <x-card class="hover:shadow-lg transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wide">{{ __('pages.outstanding_profit') }}</p>
                    <p class="text-xl font-bold text-dark-900 dark:text-dark-50 truncate mt-0.5">
                        Rp {{ number_format($this->stats['outstanding_profit'], 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1 mt-1.5">
                        <x-icon name="check-circle" class="w-3 h-3 text-emerald-500 dark:text-emerald-400 flex-shrink-0" />
                        <span class="text-xs text-dark-400 dark:text-dark-500">
                            Rp {{ number_format($this->stats['paid_this_month'] / 1000, 0) }}k {{ strtolower(__('pages.paid_this_month')) }}
                        </span>
                    </div>
                </div>
            </div>
        </x-card>

    </div>

    {{-- Filter Section --}}
    <div class="flex flex-col gap-3">

        {{-- Filter Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <x-select.styled wire:model.live="statusFilter" :label="__('common.status')" :options="[
                ['label' => __('common.draft'), 'value' => 'draft'],
                ['label' => __('invoice.sent'), 'value' => 'sent'],
                ['label' => __('common.paid'), 'value' => 'paid'],
                ['label' => __('common.partially_paid'), 'value' => 'partially_paid'],
                ['label' => __('common.overdue'), 'value' => 'overdue'],
            ]" :placeholder="__('pages.all') . ' ' . strtolower(__('common.status')) . '...'" />
            <x-select.styled wire:model.live="clientFilter" :label="__('common.clients')"
                :request="route('api.clients')"
                :placeholder="__('pages.all') . ' ' . strtolower(__('common.clients')) . '...'" searchable />
            <x-date month-year-only wire:model.live="selectedMonth" :label="__('pages.month')"
                :placeholder="__('common.select') . ' ' . strtolower(__('pages.month')) . '...'" />
            <x-date wire:model.live="dateRange" :label="__('pages.date_range')" range
                :placeholder="__('common.select') . ' ' . strtolower(__('pages.date_range')) . '...'" />
        </div>

        {{-- Filter Status Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            {{-- Left: Badge + Count --}}
            <div class="flex items-center gap-3">
                @if ($this->activeFilters > 0)
                    <x-badge :text="__('pages.active_filters_count', ['count' => $this->activeFilters])" color="primary" sm />
                @endif
                <span class="text-sm text-dark-500 dark:text-dark-400">
                    {{ $this->invoices->total() }} {{ strtolower(__('common.invoices')) }}
                </span>
            </div>

            {{-- Right: Export + Clear --}}
            <div class="flex items-center gap-2">
                @if ($this->activeFilters > 0)
                    <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                        {{ __('pages.clear_filter') }}
                    </x-button>
                @endif
                <x-button wire:click="exportExcel" size="sm" color="green" icon="document-text" outline>
                    {{ __('pages.export_excel') }}
                </x-button>
                <x-button wire:click="exportPdf" size="sm" color="red" icon="document" outline>
                    {{ __('pages.export_pdf') }}
                </x-button>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <x-table :headers="$this->headers" :$sort :rows="$this->invoices" selectable wire:model="selected" paginate
        :filter="['search' => 'search', 'quantity' => 'quantity']">

        @interact('column_invoice_number', $row)
            <div>
                @if($row->invoice_number)
                    <div class="font-mono font-semibold text-sm text-dark-700 dark:text-dark-200">{{ $row->invoice_number }}</div>
                @else
                    <div class="text-sm text-dark-400 dark:text-dark-500 italic">{{ __('invoice.draft_no_number') }}</div>
                @endif
                <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ $row->issue_date->translatedFormat('d/m/Y') }}</div>
            </div>
        @endinteract

        @interact('column_client_name', $row)
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-zinc-100 dark:bg-dark-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                        class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-sm text-dark-900 dark:text-dark-50 truncate">{{ $row->client_name }}</p>
                    <p class="text-xs text-dark-400 dark:text-dark-500 capitalize">
                        {{ $row->client_type === 'individual' ? __('pages.individual') : __('pages.company') }}
                    </p>
                </div>
            </div>
        @endinteract

        @interact('column_issue_date', $row)
            <div>
                <div class="text-sm text-dark-700 dark:text-dark-200">{{ $row->issue_date->translatedFormat('d M Y') }}</div>
                <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ $row->issue_date->diffForHumans() }}</div>
            </div>
        @endinteract

        @interact('column_due_date', $row)
            @php
                $isOverdue = $row->due_date->isPast() && !in_array($row->status, ['paid']);
                $isDueSoon = $row->due_date->diffInDays(now()) <= 7 && !$row->due_date->isPast() && !in_array($row->status, ['paid']);
            @endphp
            <div>
                <div class="text-sm {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : ($isDueSoon ? 'text-amber-600 dark:text-amber-400 font-medium' : 'text-dark-700 dark:text-dark-200') }}">
                    {{ $row->due_date->translatedFormat('d M Y') }}
                </div>
                @if ($isOverdue)
                    <div class="text-xs text-red-500 dark:text-red-400 mt-0.5">
                        {{ (int) abs($row->due_date->diffInDays(now())) }} {{ __('pages.days_overdue') }}
                    </div>
                @elseif ($isDueSoon)
                    <div class="text-xs text-amber-500 dark:text-amber-400 mt-0.5">
                        {{ (int) $row->due_date->diffInDays(now()) }} {{ __('pages.days_left') }}
                    </div>
                @else
                    <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ $row->due_date->diffForHumans() }}</div>
                @endif
            </div>
        @endinteract

        @interact('column_total_amount', $row)
            <div class="text-right">
                <div class="font-semibold text-sm text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                </div>
                @if ($row->amount_paid > 0)
                    @php $pct = min(($row->amount_paid / $row->total_amount) * 100, 100); @endphp
                    <div class="mt-1.5 space-y-0.5">
                        <div class="w-full bg-zinc-100 dark:bg-dark-700 rounded-full h-1">
                            <div class="bg-green-500 h-1 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-xs text-green-600 dark:text-green-400">{{ number_format($pct, 0) }}% {{ __('invoice.paid') }}</div>
                    </div>
                @else
                    <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ __('invoice.unpaid') }}</div>
                @endif
            </div>
        @endinteract

        @interact('column_status', $row)
            <x-badge :text="match ($row->status) {
                'draft'          => __('common.draft'),
                'sent'           => __('invoice.sent'),
                'paid'           => __('common.paid'),
                'partially_paid' => __('common.partially_paid'),
                'overdue'        => __('common.overdue'),
                default          => ucfirst($row->status),
            }" :color="match ($row->status) {
                'draft'          => 'gray',
                'sent'           => 'blue',
                'paid'           => 'green',
                'partially_paid' => 'yellow',
                'overdue'        => 'red',
                default          => 'gray',
            }" />
        @endinteract

        @interact('column_faktur', $row)
            @if ($row->faktur)
                <button wire:click="showInvoice({{ $row->id }})"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                    <x-icon name="paper-clip" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span class="truncate max-w-[120px]" title="{{ basename($row->faktur) }}">{{ basename($row->faktur) }}</span>
                </button>
            @else
                <span class="text-xs text-dark-300 dark:text-dark-600">—</span>
            @endif
        @endinteract

        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="showInvoice({{ $row->id }})"
                    loading="showInvoice({{ $row->id }})"
                    :title="__('common.view')" />
                <x-button.circle icon="pencil" color="green" size="sm"
                    href="{{ route('invoices.edit', $row->id) }}" wire:navigate
                    :title="__('common.edit')" />

                @if ($row->status === 'draft')
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click="prepareSendInvoice({{ $row->id }})"
                        loading="prepareSendInvoice({{ $row->id }})"
                        :title="__('invoice.send_invoice')" />
                @endif

                @if ($row->status === 'sent')
                    <x-button.circle icon="arrow-uturn-left" color="orange" size="sm"
                        wire:click="rollbackTodraft({{ $row->id }})"
                        loading="rollbackTodraft({{ $row->id }})"
                        :title="__('pages.back_to_draft')" />
                @endif

                @if (in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                    <x-button.circle icon="currency-dollar" color="yellow" size="sm"
                        wire:click="recordPayment({{ $row->id }})"
                        loading="recordPayment({{ $row->id }})"
                        :title="__('pages.record_payment')" />
                @endif

                <x-button.circle icon="printer" color="gray" size="sm"
                    wire:click="openPrintModal({{ $row->id }}, {{ $row->total_amount }}, {{ $row->amount_paid }})"
                    :title="__('common.print')" />

                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deleteInvoice({{ $row->id }})"
                    loading="deleteInvoice({{ $row->id }})" />
            </div>
        @endinteract

    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }"
         x-show="show.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-xl border border-zinc-200 dark:border-dark-600 px-4 sm:px-5 py-3.5 sm:min-w-[28rem]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="check-circle" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} {{ __('common.invoices') }} {{ __('pages.selected') }}`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.select_action_for_selected') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="bulkPrintInvoices" size="sm" color="blue" icon="printer"
                        loading="bulkPrintInvoices" class="whitespace-nowrap">{{ __('pages.print_all') }}</x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="bulkDelete" class="whitespace-nowrap">{{ __('common.delete') }}</x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="zinc" class="whitespace-nowrap">
                        {{ __('common.cancel') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Options Modal --}}
    <x-modal wire="printModal" size="md" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-3 my-3">
                <div class="h-11 w-11 bg-zinc-100 dark:bg-dark-700 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="printer" class="w-5 h-5 text-dark-600 dark:text-dark-400" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-dark-900 dark:text-dark-50">{{ __('invoice.print_invoice') }}</h3>
                    <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.select_invoice_type_to_print') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-3">

            {{-- Full Invoice Option --}}
            <div wire:click="$set('printType', 'full')"
                class="group p-4 border-2 rounded-xl cursor-pointer transition-all
                    {{ $printType === 'full'
                        ? 'border-primary-500 bg-primary-50/60 dark:bg-primary-900/10'
                        : 'border-zinc-200 dark:border-dark-600 hover:border-zinc-300 dark:hover:border-dark-500' }}">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0
                        {{ $printType === 'full' ? 'border-primary-500 bg-primary-500' : 'border-zinc-300 dark:border-dark-500' }}">
                        @if ($printType === 'full')
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-sm text-dark-900 dark:text-dark-50">{{ __('invoice.full_payment_invoice') }}</div>
                        <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.print_full_invoice') }}</div>
                        <div class="text-base font-bold text-dark-900 dark:text-dark-50 mt-2">
                            Rp {{ number_format($printTotalAmount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Down Payment Option --}}
            <div wire:click="$set('printType', 'dp')"
                class="group p-4 border-2 rounded-xl cursor-pointer transition-all
                    {{ $printType === 'dp'
                        ? 'border-primary-500 bg-primary-50/60 dark:bg-primary-900/10'
                        : 'border-zinc-200 dark:border-dark-600 hover:border-zinc-300 dark:hover:border-dark-500' }}">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0
                        {{ $printType === 'dp' ? 'border-primary-500 bg-primary-500' : 'border-zinc-300 dark:border-dark-500' }}">
                        @if ($printType === 'dp')
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-sm text-dark-900 dark:text-dark-50">{{ __('invoice.down_payment') }} (DP)</div>
                        <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.print_dp_invoice') }}</div>
                        @if ($printType === 'dp')
                            <div class="mt-3">
                                <x-currency-input wire:model="dpAmount" :label="__('pages.dp_amount') . ' *'" placeholder="0" prefix="Rp" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pelunasan Option --}}
            @if ($printAmountPaid > 0 && $printAmountPaid < $printTotalAmount)
                <div wire:click="$set('printType', 'pelunasan')"
                    class="group p-4 border-2 rounded-xl cursor-pointer transition-all
                        {{ $printType === 'pelunasan'
                            ? 'border-primary-500 bg-primary-50/60 dark:bg-primary-900/10'
                            : 'border-zinc-200 dark:border-dark-600 hover:border-zinc-300 dark:hover:border-dark-500' }}">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0
                            {{ $printType === 'pelunasan' ? 'border-primary-500 bg-primary-500' : 'border-zinc-300 dark:border-dark-500' }}">
                            @if ($printType === 'pelunasan')
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-sm text-dark-900 dark:text-dark-50">{{ __('invoice.settlement') }}</div>
                            <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.print_settlement_invoice') }}</div>
                            <div class="mt-2 grid grid-cols-2 gap-x-4 text-xs">
                                <span class="text-dark-500 dark:text-dark-400">{{ __('invoice.already_paid') }}</span>
                                <span class="text-right font-medium text-dark-700 dark:text-dark-200">Rp {{ number_format($printAmountPaid, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-base font-bold text-dark-900 dark:text-dark-50 mt-1">
                                Rp {{ number_format($printTotalAmount - $printAmountPaid, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Template Selection --}}
            <div class="pt-3 border-t border-zinc-100 dark:border-dark-600">
                <p class="text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wider mb-3">
                    {{ __('pages.select_template') }}
                </p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['value' => 'kisantra-invoice', 'label' => __('invoice.template_kisantra'), 'desc' => __('invoice.template_kisantra_desc')],
                        ['value' => 'semesta-invoice',  'label' => __('invoice.template_semesta'),  'desc' => __('invoice.template_semesta_desc')],
                        ['value' => 'agsa-invoice',     'label' => __('invoice.template_agsa'),     'desc' => __('invoice.template_agsa_desc')],
                        ['value' => 'invoice',          'label' => __('invoice.template_generic'),  'desc' => __('invoice.template_generic_desc')],
                    ] as $tpl)
                        <div wire:click="$set('printTemplate', '{{ $tpl['value'] }}')"
                            class="p-3 border-2 rounded-xl cursor-pointer transition-all
                                {{ $printTemplate === $tpl['value']
                                    ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-900/10'
                                    : 'border-zinc-200 dark:border-dark-600 hover:border-zinc-300 dark:hover:border-dark-500' }}">
                            <div class="flex items-start gap-2">
                                <div class="mt-0.5 w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                    {{ $printTemplate === $tpl['value'] ? 'border-primary-500 bg-primary-500' : 'border-zinc-300 dark:border-dark-500' }}">
                                    @if ($printTemplate === $tpl['value'])
                                        <div class="w-1 h-1 bg-white rounded-full"></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-dark-800 dark:text-dark-100">{{ $tpl['label'] }}</div>
                                    <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ $tpl['desc'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('printModal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">{{ __('common.cancel') }}</x-button>
                <x-button wire:click="executePrint" color="primary" icon="printer" loading="executePrint"
                    class="w-full sm:w-auto order-1 sm:order-2">{{ __('common.print') }}</x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    @script
    <script>
        $wire.on('execute-print', (data) => {
            const { previewUrl, downloadUrl } = data[0];
            window.open(previewUrl, '_blank');
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        });

        $wire.on('start-bulk-download', (data) => {
            const { downloads, delay } = data[0];
            let currentIndex = 0;
            function downloadNext() {
                if (currentIndex >= downloads.length) return;
                const current = downloads[currentIndex];
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = current.url;
                iframe.onload = () => setTimeout(() => document.body.removeChild(iframe), 1000);
                document.body.appendChild(iframe);
                currentIndex++;
                if (currentIndex < downloads.length) setTimeout(downloadNext, delay || 2000);
            }
            downloadNext();
        });
    </script>
    @endscript

{{-- Send Invoice Modal --}}
<x-modal wire="sendModal" size="md" center persistent>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="paper-airplane" class="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('invoice.send_invoice_title') }}</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('invoice.send_invoice_subtitle') }}</p>
            </div>
        </div>
    </x-slot:title>

    <div class="space-y-4">
        <x-input wire:model="pendingInvoiceNumber"
            :label="__('invoice.invoice_number') . ' *'"
            :hint="__('invoice.number_auto_generated_hint')" />
    </div>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="$set('sendModal', false)" color="zinc"
                class="w-full sm:w-auto order-2 sm:order-1">
                {{ __('common.cancel') }}
            </x-button>
            <x-button wire:click="confirmSendInvoice" color="primary" icon="paper-airplane"
                loading="confirmSendInvoice" class="w-full sm:w-auto order-1 sm:order-2">
                {{ __('invoice.send_invoice') }}
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>

</div>
