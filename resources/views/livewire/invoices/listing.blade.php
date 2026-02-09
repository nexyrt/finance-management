<div class="space-y-6">
    {{-- Filters (unchanged) --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
            <x-select.styled wire:model.live="statusFilter" :label="__('common.status')" :options="[
                ['label' => __('common.draft'), 'value' => 'draft'],
                ['label' => __('invoice.sent'), 'value' => 'sent'],
                ['label' => __('common.paid'), 'value' => 'paid'],
                ['label' => __('common.partially_paid'), 'value' => 'partially_paid'],
                ['label' => __('common.overdue'), 'value' => 'overdue'],
            ]"
                :placeholder="__('pages.all') . ' ' . strtolower(__('common.status')) . '...'" />
            <x-select.styled wire:model.live="clientFilter" :label="__('common.clients')" :options="$this->clients
                ->map(fn($client) => ['label' => $client->name, 'value' => $client->id])
                ->toArray()" :placeholder="__('pages.all') . ' ' . strtolower(__('common.clients')) . '...'"
                searchable />
            <x-date month-year-only wire:model.live="selectedMonth" :label="__('pages.month')" :placeholder="__('common.select') . ' ' . strtolower(__('pages.month')) . '...'" />
            <x-date wire:model.live="dateRange" :label="__('pages.date_range')" range :placeholder="__('common.select') . ' ' . strtolower(__('pages.date_range')) . '...'" />
        </div>
        <div class="flex gap-2">
            @if ($statusFilter || $clientFilter || !empty($dateRange) || $selectedMonth)
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline
                    size="sm">{{ __('pages.clear_filter') }}</x-button>
            @endif
            <x-button wire:click="exportExcel" size="sm" color="green" icon="document-text"
                outline>{{ __('pages.export_excel') }}</x-button>
            <x-button wire:click="exportPdf" size="sm" color="red" icon="document" outline>{{ __('pages.export_pdf') }}</x-button>
        </div>
    </div>

    {{-- Table --}}
    <x-table :headers="$this->headers" :$sort :rows="$this->invoices" selectable wire:model="selected" paginate filter>
        @interact('column_invoice_number', $row)
            <div>
                <div class="font-mono font-bold text-zinc-600 dark:text-zinc-400">{{ $row->invoice_number }}</div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->issue_date->translatedFormat('d/m/Y') }}</div>
            </div>
        @endinteract

        @interact('column_client_name', $row)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-zinc-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                        class="w-5 h-5 text-dark-600 dark:text-dark-400" />
                </div>
                <div>
                    <p class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->client_name }}</p>
                    <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">
                        {{ $row->client_type === 'individual' ? __('pages.individual') : __('pages.company') }}</div>
                </div>
            </div>
        @endinteract

        @interact('column_issue_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $row->issue_date->translatedFormat('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->issue_date->diffForHumans() }}</div>
            </div>
        @endinteract

        @interact('column_due_date', $row)
            @php
                $isOverdue = $row->due_date->isPast() && !in_array($row->status, ['paid']);
                $isDueSoon =
                    $row->due_date->diffInDays(now()) <= 7 &&
                    !$row->due_date->isPast() &&
                    !in_array($row->status, ['paid']);
            @endphp
            <div>
                <div
                    class="text-sm font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isDueSoon ? 'text-yellow-600 dark:text-yellow-400' : 'text-dark-900 dark:text-dark-50') }}">
                    {{ $row->due_date->translatedFormat('d M Y') }}
                </div>
                @if ($isOverdue)
                    <div class="text-xs text-red-600 dark:text-red-400">{{ (int) abs($row->due_date->diffInDays(now())) }}
                        {{ __('pages.days_overdue') }}</div>
                @elseif($isDueSoon)
                    <div class="text-xs text-yellow-600 dark:text-yellow-400">{{ (int) $row->due_date->diffInDays(now()) }}
                        {{ __('pages.days_left') }}</div>
                @else
                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->due_date->diffForHumans() }}</div>
                @endif
            </div>
        @endinteract

        @interact('column_total_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-dark-900 dark:text-dark-50">Rp
                    {{ number_format($row->total_amount, 0, ',', '.') }}</div>
                @if ($row->amount_paid > 0)
                    @php $paymentPercentage = ($row->amount_paid / $row->total_amount) * 100; @endphp
                    <div class="mt-1">
                        <div class="text-xs text-green-600 dark:text-green-400">{{ number_format($paymentPercentage, 1) }}%
                            {{ __('invoice.paid') }}</div>
                        <div class="w-full bg-zinc-200 dark:bg-dark-700 rounded-full h-1 mt-1">
                            <div class="bg-green-500 h-1 rounded-full" style="width: {{ min($paymentPercentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('invoice.unpaid') }}</div>
                @endif
            </div>
        @endinteract

        @interact('column_status', $row)
            <x-badge :text="match ($row->status) {
                'draft' => __('common.draft'),
                'sent' => __('invoice.sent'),
                'paid' => __('common.paid'),
                'partially_paid' => __('common.partially_paid'),
                'overdue' => __('common.overdue'),
                default => ucfirst($row->status),
            }" :color="match ($row->status) {
                'draft' => 'gray',
                'sent' => 'blue',
                'paid' => 'green',
                'partially_paid' => 'yellow',
                'overdue' => 'red',
                default => 'gray',
            }" />
        @endinteract

        @interact('column_faktur', $row)
            @if ($row->faktur)
                <button wire:click="showInvoice({{ $row->id }}, 'overview')"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 hover:underline transition group">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="truncate max-w-[150px]" title="{{ basename($row->faktur) }}">{{ basename($row->faktur) }}</span>
                </button>
            @else
                <span class="text-sm text-dark-400 dark:text-dark-500">-</span>
            @endif
        @endinteract

        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm" wire:click="showInvoice({{ $row->id }})"
                    loading="showInvoice({{ $row->id }})" :title="__('common.view')" />
                <x-button.circle icon="pencil" color="green" size="sm" href="{{ route('invoices.edit', $row->id) }}"
                    wire:navigate :title="__('common.edit')" />

                @if ($row->status === 'draft')
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click='sendInvoice({{ $row->id }})' loading="sendInvoice({{ $row->id }})"
                        :title="__('invoice.send_invoice')" />
                @endif

                @if ($row->status === 'sent')
                    <x-button.circle icon="arrow-uturn-left" color="orange" size="sm"
                        wire:click='rollbackTodraft({{ $row->id }})' loading="rollbackToraft({{ $row->id }})"
                        :title="__('pages.back_to_draft')" />
                @endif

                @if (in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                    <x-button.circle icon="currency-dollar" color="yellow" size="sm"
                        wire:click="recordPayment({{ $row->id }})" loading="recordPayment({{ $row->id }})"
                        :title="__('pages.record_payment')" />
                @endif

                {{-- Print Button - Trigger Modal --}}
                <x-button.circle icon="printer" color="gray" size="sm"
                    wire:click="openPrintModal({{ $row->id }}, {{ $row->total_amount }}, {{ $row->amount_paid }})"
                    :title="__('common.print')" />

                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deleteInvoice({{ $row->id }})" loading="deleteInvoice({{ $row->id }})" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar (unchanged) --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-zinc-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-dark-600 dark:text-dark-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} {{ __('common.invoices') }} {{ __('pages.selected') }}`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.select_action_for_selected') }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="bulkPrintInvoices" size="sm" color="blue" icon="printer"
                        loading="bulkPrintInvoices" class="whitespace-nowrap">{{ __('pages.print_all') }}</x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="bulkDelete" class="whitespace-nowrap">{{ __('common.delete') }}</x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">{{ __('common.cancel') }}</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Options Modal --}}
    <x-modal wire="printModal" size="md" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div
                    class="h-12 w-12 bg-zinc-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                    <x-icon name="printer" class="w-6 h-6 text-dark-600 dark:text-dark-400" />
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50">{{ __('invoice.print_invoice') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.select_invoice_type_to_print') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-4">
            {{-- Full Invoice Option --}}
            <div wire:click="$set('printType', 'full')"
                class="p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-zinc-400 {{ $printType === 'full' ? 'border-zinc-600 bg-zinc-50 dark:bg-dark-700' : 'border-gray-200 dark:border-dark-600' }}">
                <div class="flex items-start gap-3">
                    <div class="pt-1">
                        <div
                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $printType === 'full' ? 'bg-zinc-600 border-zinc-600' : 'border-gray-300 dark:border-dark-500' }}">
                            @if ($printType === 'full')
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.full_payment_invoice') }}</div>
                        <div class="text-sm text-dark-600 dark:text-dark-400 mb-2">{{ __('pages.print_full_invoice') }}
                        </div>
                        <div class="text-lg font-semibold text-dark-900 dark:text-dark-50">
                            Rp {{ number_format($printTotalAmount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Down Payment Option --}}
            <div wire:click="$set('printType', 'dp')"
                class="p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-zinc-400 {{ $printType === 'dp' ? 'border-zinc-600 bg-zinc-50 dark:bg-dark-700' : 'border-gray-200 dark:border-dark-600' }}">
                <div class="flex items-start gap-3">
                    <div class="pt-1">
                        <div
                            class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $printType === 'dp' ? 'bg-zinc-600 border-zinc-600' : 'border-gray-300 dark:border-dark-500' }}">
                            @if ($printType === 'dp')
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.down_payment') }} (DP)</div>
                        <div class="text-sm text-dark-600 dark:text-dark-400 mb-3">{{ __('pages.print_dp_invoice') }}
                        </div>
                        @if ($printType === 'dp')
                            <x-currency-input wire:model="dpAmount" :label="__('pages.dp_amount') . ' *'" placeholder="0"
                                prefix="Rp" />
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pelunasan Option --}}
            @if ($printAmountPaid > 0 && $printAmountPaid < $printTotalAmount)
                <div wire:click="$set('printType', 'pelunasan')"
                    class="p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-zinc-400 {{ $printType === 'pelunasan' ? 'border-zinc-600 bg-zinc-50 dark:bg-dark-700' : 'border-gray-200 dark:border-dark-600' }}">
                    <div class="flex items-start gap-3">
                        <div class="pt-1">
                            <div
                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $printType === 'pelunasan' ? 'bg-zinc-600 border-zinc-600' : 'border-gray-300 dark:border-dark-500' }}">
                                @if ($printType === 'pelunasan')
                                    <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.settlement') }}</div>
                            <div class="text-sm text-dark-600 dark:text-dark-400 mb-2">{{ __('pages.print_settlement_invoice') }}</div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                <div class="text-dark-500 dark:text-dark-400">{{ __('invoice.already_paid') }}:</div>
                                <div class="text-right text-dark-900 dark:text-dark-50 font-semibold">
                                    Rp {{ number_format($printAmountPaid, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="text-lg font-semibold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($printTotalAmount - $printAmountPaid, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Template Selection dengan TallStackUI --}}
            <div class="mt-6 pt-6 border-t border-dark-200 dark:border-dark-600">
                <label class="block text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">
                    {{ __('pages.select_template') }}
                </label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['value' => 'kisantra-invoice', 'label' => __('invoice.template_kisantra'), 'desc' => __('invoice.template_kisantra_desc')],
                        ['value' => 'semesta-invoice', 'label' => __('invoice.template_semesta'), 'desc' => __('invoice.template_semesta_desc')],
                        ['value' => 'agsa-invoice', 'label' => __('invoice.template_agsa'), 'desc' => __('invoice.template_agsa_desc')],
                        ['value' => 'invoice', 'label' => __('invoice.template_generic'), 'desc' => __('invoice.template_generic_desc')],
                    ] as $tpl)
                        <div wire:click="$set('printTemplate', '{{ $tpl['value'] }}')"
                            class="p-3 border-2 rounded-lg cursor-pointer transition {{ $printTemplate === $tpl['value'] ? 'border-zinc-600 bg-zinc-50 dark:bg-dark-700' : 'border-gray-200 dark:border-dark-600 hover:border-zinc-400' }}">
                            <div class="flex items-start gap-2">
                                <div class="pt-0.5">
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $printTemplate === $tpl['value'] ? 'bg-zinc-600 border-zinc-600' : 'border-gray-300 dark:border-dark-500' }}">
                                        @if ($printTemplate === $tpl['value'])
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-sm text-dark-900 dark:text-dark-50">{{ $tpl['label'] }}</div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ $tpl['desc'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('printModal', false)" color="secondary" outline
                    class="w-full sm:w-auto">{{ __('common.cancel') }}</x-button>
                <x-button wire:click="executePrint" color="primary" icon="printer" loading="executePrint"
                    class="w-full sm:w-auto">{{ __('common.print') }}</x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

<script>
    function setupPrintListeners() {
        Livewire.on('execute-print', (data) => {
            const {
                previewUrl,
                downloadUrl
            } = data[0];
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

        Livewire.on('start-bulk-download', (data) => {
            const {
                downloads,
                delay
            } = data[0];
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
    }

    // Setup ulang setiap navigate
    document.addEventListener('livewire:navigated', () => {
        setupPrintListeners();
    });
</script>
