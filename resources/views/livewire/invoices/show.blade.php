<div>
    <x-modal wire :title="__('invoice.invoice_details')" size="5xl" center>

        @if ($invoice)
            @php
                $statusColor = match($invoice->status) {
                    'paid'           => 'emerald',
                    'partially_paid' => 'amber',
                    'overdue'        => 'red',
                    'sent'           => 'blue',
                    default          => 'zinc',
                };
                $pctPaid = $invoice->total_amount > 0
                    ? min(($invoice->amount_paid / $invoice->total_amount) * 100, 100)
                    : 0;
            @endphp

            {{-- ═══ MODAL TITLE ═══ --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 rounded-xl flex-shrink-0 flex items-center justify-center {{ match($statusColor) {
                        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20',
                        'amber'   => 'bg-amber-50 dark:bg-amber-900/20',
                        'red'     => 'bg-red-50 dark:bg-red-900/20',
                        'blue'    => 'bg-blue-50 dark:bg-blue-900/20',
                        default   => 'bg-zinc-100 dark:bg-dark-700',
                    } }}">
                        <x-icon name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}"
                            class="w-6 h-6 {{ match($statusColor) {
                                'emerald' => 'text-emerald-600 dark:text-emerald-400',
                                'amber'   => 'text-amber-600 dark:text-amber-400',
                                'red'     => 'text-red-600 dark:text-red-400',
                                'blue'    => 'text-blue-600 dark:text-blue-400',
                                default   => 'text-zinc-500 dark:text-dark-400',
                            } }}" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50 font-mono tracking-tight">
                                {{ $invoice->invoice_number ?? __('invoice.draft_no_number') }}
                            </h3>
                            <x-badge :text="match ($invoice->status) {
                                'draft'          => __('common.draft'),
                                'sent'           => __('invoice.sent'),
                                'paid'           => __('common.paid'),
                                'partially_paid' => __('common.partially_paid'),
                                'overdue'        => __('common.overdue'),
                                default          => ucfirst($invoice->status),
                            }" :color="match ($invoice->status) {
                                'draft'          => 'zinc',
                                'sent'           => 'blue',
                                'paid'           => 'green',
                                'partially_paid' => 'yellow',
                                'overdue'        => 'red',
                                default          => 'zinc',
                            }" />
                        </div>
                        <p class="text-sm text-dark-600 dark:text-dark-400 mt-0.5 flex items-center gap-2">
                            <span>{{ $invoice->client->name }}</span>
                            <span class="opacity-30">·</span>
                            <span class="tabular-nums font-semibold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </span>
                        </p>
                    </div>
                </div>
            </x-slot:title>

            {{-- ═══ BODY ═══ --}}
            <div class="space-y-4">

                {{-- ROW 1: Date meta + Payment status bar (side by side) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Issue date --}}
                    <div class="bg-zinc-50 dark:bg-dark-700/40 border border-zinc-200 dark:border-dark-600 rounded-xl p-3">
                        <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-1">{{ __('invoice.invoice_date') }}</p>
                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $invoice->issue_date->translatedFormat('d M Y') }}</p>
                    </div>
                    {{-- Due date --}}
                    <div class="border {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'border-red-200 dark:border-red-800/50 bg-red-50 dark:bg-red-900/10' : 'bg-zinc-50 dark:bg-dark-700/40 border-zinc-200 dark:border-dark-600' }} rounded-xl p-3">
                        <p class="text-xs uppercase tracking-widest {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-400 dark:text-red-500' : 'text-dark-400 dark:text-dark-500' }} mb-1">{{ __('invoice.due_date') }}</p>
                        <p class="text-sm font-semibold {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-600 dark:text-red-400' : 'text-dark-900 dark:text-dark-50' }}">
                            {{ $invoice->due_date->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    {{-- Gross profit --}}
                    <div class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/40 rounded-xl p-3">
                        <p class="text-xs uppercase tracking-widest text-emerald-600 dark:text-emerald-500 mb-1">{{ __('pages.gross_profit') }}</p>
                        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 tabular-nums">
                            Rp {{ number_format($this->grossProfit, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- ROW 2: Invoice Items (full width) --}}
                <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                        <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold">
                            {{ __('pages.invoice_items') }}
                        </h4>
                        <span class="text-xs text-dark-400 dark:text-dark-500">{{ $invoice->items->count() }} {{ strtolower(__('pages.items')) }}</span>
                    </div>

                    {{-- Items --}}
                    <div class="divide-y divide-zinc-100 dark:divide-dark-700">
                        @foreach ($invoice->items as $item)
                            <div class="px-4 py-2.5 flex items-center justify-between gap-4 hover:bg-zinc-50 dark:hover:bg-dark-700/30 transition-colors">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-dark-700 flex items-center justify-center flex-shrink-0">
                                        <x-icon name="{{ $item->client->type === 'individual' ? 'user' : 'building-office' }}" class="w-3.5 h-3.5 text-dark-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $item->service_name }}</span>
                                            @if ($item->is_tax_deposit)
                                                <x-badge :text="__('invoice.tax_deposit')" color="amber" size="xs" />
                                            @endif
                                        </div>
                                        <p class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">
                                            {{ $item->client->name }}
                                            <span class="mx-1 opacity-30">·</span>{{ __('invoice.qty') }}: {{ $item->quantity }}
                                            <span class="mx-1 opacity-30">·</span>Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            @if (!$item->is_tax_deposit && $item->cogs_amount > 0)
                                                <span class="mx-1 opacity-30">·</span>
                                                <span class="text-emerald-600 dark:text-emerald-400">+Rp {{ number_format($item->profit_amount, 0, ',', '.') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-dark-900 dark:text-dark-50 tabular-nums flex-shrink-0">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals footer --}}
                    <div class="px-4 py-3 border-t border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                        <div class="flex items-center justify-between gap-6">
                            {{-- Left: subtotal + discount --}}
                            <div class="flex items-center gap-6 text-sm text-dark-500 dark:text-dark-400">
                                <span>{{ __('invoice.subtotal') }}: <span class="font-medium text-dark-900 dark:text-dark-50 tabular-nums">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span></span>
                                @if ($invoice->discount_amount > 0)
                                    <span>{{ __('invoice.discount') }}: <span class="font-medium text-emerald-600 dark:text-emerald-400 tabular-nums">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span></span>
                                @endif
                            </div>
                            {{-- Right: total --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('invoice.total_invoice') }}:</span>
                                <span class="text-base font-bold text-dark-900 dark:text-dark-50 tabular-nums">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ROW 3: Payment status (left) + Financial breakdown (right) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Payment Status --}}
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="h-1.5 bg-zinc-100 dark:bg-dark-700">
                            <div class="h-1.5 {{ $invoice->status === 'paid' ? 'bg-emerald-500' : 'bg-amber-500' }} transition-all duration-700"
                                style="width: {{ $pctPaid }}%"></div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-0.5">{{ __('pages.total_paid') }}</p>
                                    <p class="text-lg font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                                        Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if ($invoice->amount_remaining > 0)
                                    <div class="text-right">
                                        <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-0.5">{{ __('pages.remaining_bill') }}</p>
                                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                            Rp {{ number_format($invoice->amount_remaining, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                        <x-icon name="check-circle" class="w-5 h-5" />
                                        <span class="text-sm font-semibold">{{ __('common.paid') }}</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-dark-400 dark:text-dark-500 mb-3">{{ number_format($pctPaid, 1) }}% {{ __('pages.of_total_invoice') }}</p>
                            @if (in_array($invoice->status, ['sent', 'overdue', 'partially_paid']))
                                <x-button wire:click="recordPayment" color="green" icon="currency-dollar" size="sm" class="w-full">
                                    {{ __('pages.record_payment') }}
                                </x-button>
                            @endif
                        </div>
                    </div>

                    {{-- Financial Breakdown --}}
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                            <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold">{{ __('pages.financial_breakdown') }}</h4>
                        </div>
                        <div class="px-4 py-3 space-y-2.5">
                            <div class="flex justify-between text-sm">
                                <span class="text-dark-500 dark:text-dark-400">{{ __('invoice.total_invoice') }}</span>
                                <span class="font-medium text-dark-900 dark:text-dark-50 tabular-nums">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-dark-500 dark:text-dark-400">{{ __('invoice.tax_deposit') }}</span>
                                <span class="font-medium text-dark-900 dark:text-dark-50 tabular-nums">Rp {{ number_format($this->totalTaxDeposits, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-dark-500 dark:text-dark-400">{{ __('pages.total_cogs_label') }}</span>
                                <span class="font-medium text-dark-900 dark:text-dark-50 tabular-nums">Rp {{ number_format($this->totalCogs, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-zinc-200 dark:border-dark-600">
                                <span class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.gross_profit') }}</span>
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">Rp {{ number_format($this->grossProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ROW 4: Payment history + Client info + Timeline (3 kolom) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- Payment History --}}
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                            <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold">{{ __('pages.payments_tab') }}</h4>
                            <span class="text-xs text-dark-400 dark:text-dark-500">{{ $invoice->payments->count() }}</span>
                        </div>
                        @if ($invoice->payments->count() > 0)
                            <div class="divide-y divide-zinc-100 dark:divide-dark-700">
                                @foreach ($invoice->payments as $payment)
                                    <div class="px-3 py-2.5 hover:bg-zinc-50 dark:hover:bg-dark-700/30 transition-colors">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center flex-shrink-0">
                                                    <x-icon name="banknotes" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-dark-900 dark:text-dark-50 tabular-nums leading-tight">
                                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-dark-400 dark:text-dark-500 truncate">
                                                        {{ $payment->payment_date->translatedFormat('d M Y') }}
                                                        <span class="opacity-40 mx-0.5">·</span>{{ $payment->bankAccount->bank_name }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                @if ($payment->hasAttachment())
                                                    <x-button.circle wire:click="showPaymentAttachment({{ $payment->id }})"
                                                        loading="showPaymentAttachment({{ $payment->id }})"
                                                        color="blue" icon="eye" size="sm" outline
                                                        :title="__('pages.view_attachment')" />
                                                @endif
                                                <x-button.circle wire:click="$dispatch('delete-payment', { paymentId: {{ $payment->id }} })"
                                                    color="red" icon="trash" size="sm" outline
                                                    :title="__('pages.delete_payment')" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-4 py-6 text-center">
                                <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-dark-700 flex items-center justify-center mx-auto mb-2">
                                    <x-icon name="credit-card" class="w-4 h-4 text-dark-400 dark:text-dark-500" />
                                </div>
                                <p class="text-xs text-dark-400 dark:text-dark-500">{{ __('pages.no_payments_yet') }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Client Info --}}
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                            <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold">{{ __('pages.client_information') }}</h4>
                        </div>
                        <div class="px-4 py-3 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">{{ __('common.name') }}</span>
                                <span class="text-xs font-medium text-dark-900 dark:text-dark-50 text-right">{{ $invoice->client->name }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">{{ __('common.type') }}</span>
                                <span class="text-xs font-medium text-dark-900 dark:text-dark-50">{{ __('pages.' . $invoice->client->type) }}</span>
                            </div>
                            @if ($invoice->client->email)
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">{{ __('common.email') }}</span>
                                    <span class="text-xs font-medium text-dark-900 dark:text-dark-50 text-right break-all">{{ $invoice->client->email }}</span>
                                </div>
                            @endif
                            @if ($invoice->client->NPWP)
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">NPWP</span>
                                    <span class="text-xs font-medium font-mono text-dark-900 dark:text-dark-50">{{ $invoice->client->NPWP }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                            <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold">{{ __('pages.timeline') }}</h4>
                        </div>
                        <div class="px-4 py-3">
                            <div class="space-y-0">
                                <div class="flex gap-3 pb-3">
                                    <div class="flex flex-col items-center pt-0.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-dark-500 flex-shrink-0"></div>
                                        <div class="w-px flex-1 bg-zinc-200 dark:bg-dark-600 mt-1"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-dark-900 dark:text-dark-50">{{ __('pages.invoice_created') }}</p>
                                        <p class="text-xs text-dark-400 dark:text-dark-500">{{ $invoice->created_at->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                </div>

                                @if ($invoice->status !== 'draft')
                                    <div class="flex gap-3 pb-3">
                                        <div class="flex flex-col items-center pt-0.5">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></div>
                                            <div class="w-px flex-1 bg-zinc-200 dark:bg-dark-600 mt-1"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-dark-900 dark:text-dark-50">{{ __('pages.invoice_sent') }}</p>
                                            <p class="text-xs text-dark-400 dark:text-dark-500">{{ $invoice->issue_date->translatedFormat('d M Y') }}</p>
                                        </div>
                                    </div>
                                @endif

                                @foreach ($invoice->payments as $payment)
                                    <div class="flex gap-3 pb-3">
                                        <div class="flex flex-col items-center pt-0.5">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></div>
                                            <div class="w-px flex-1 bg-zinc-200 dark:bg-dark-600 mt-1"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-dark-900 dark:text-dark-50">{{ __('pages.payment_received') }}</p>
                                            <p class="text-xs text-dark-400 dark:text-dark-500">
                                                {{ $payment->payment_date->translatedFormat('d M Y') }}
                                                <span class="opacity-40 mx-0.5">·</span>Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                    <div class="flex gap-3">
                                        <div class="pt-0.5">
                                            <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-red-600 dark:text-red-400">{{ __('pages.past_due_date') }}</p>
                                            <p class="text-xs text-red-400 dark:text-red-500">
                                                {{ $invoice->due_date->translatedFormat('d M Y') }}
                                                <span class="opacity-40 mx-0.5">·</span>{{ abs($invoice->due_date->diffInDays(now())) }} {{ __('pages.days_ago') }}
                                            </p>
                                        </div>
                                    </div>
                                @elseif ($invoice->status === 'paid')
                                    <div class="flex gap-3">
                                        <div class="pt-0.5">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ __('common.paid') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ROW 5: Faktur Attachment (only if exists) --}}
                @if ($invoice->faktur)
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                            <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold flex items-center gap-2">
                                <x-icon name="paper-clip" class="w-3.5 h-3.5" />
                                {{ __('invoice.faktur') }}
                            </h4>
                            <a href="{{ Storage::url($invoice->faktur) }}" download
                                class="text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center gap-1 transition-colors">
                                <x-icon name="arrow-down-tray" class="w-3.5 h-3.5" />
                                {{ __('common.download') }}
                            </a>
                        </div>
                        <div class="p-4">
                            @php $isPdf = strtolower(pathinfo($invoice->faktur, PATHINFO_EXTENSION)) === 'pdf'; @endphp
                            @if ($isPdf)
                                <div class="aspect-[8.5/11] border border-zinc-200 dark:border-dark-600 rounded-lg overflow-hidden">
                                    <iframe src="{{ Storage::url($invoice->faktur) }}" class="w-full h-full"></iframe>
                                </div>
                            @else
                                <img src="{{ Storage::url($invoice->faktur) }}" alt="Faktur" class="w-full h-auto rounded-lg border border-zinc-200 dark:border-dark-600">
                            @endif
                            <p class="text-xs text-dark-400 dark:text-dark-500 mt-2">{{ basename($invoice->faktur) }}</p>
                        </div>
                    </div>
                @endif

            </div>

        @endif

        {{-- ═══ FOOTER ═══ --}}
        <x-slot:footer>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
                {{-- Left: Print + Send --}}
                <div class="flex flex-wrap items-center gap-2" x-data="{
                    templateOpen: false,
                    selectedTemplate: 'kisantra-invoice',
                    templates: [
                        { value: 'kisantra-invoice', label: @js(__('invoice.template_kisantra')), desc: @js(__('invoice.template_kisantra_desc')) },
                        { value: 'semesta-invoice',  label: @js(__('invoice.template_semesta')),  desc: @js(__('invoice.template_semesta_desc')) },
                        { value: 'agsa-invoice',     label: @js(__('invoice.template_agsa')),     desc: @js(__('invoice.template_agsa_desc')) },
                        { value: 'invoice',          label: @js(__('invoice.template_generic')),  desc: @js(__('invoice.template_generic_desc')) }
                    ]
                }">
                    @if ($invoice)
                        <div class="relative">
                            <x-button @click="templateOpen = !templateOpen" color="primary" icon="printer" outline size="sm">
                                {{ __('pages.print_pdf') }}
                            </x-button>
                            <div x-show="templateOpen"
                                 @click.away="templateOpen = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute left-0 mt-2 w-72 bg-white dark:bg-dark-800 rounded-xl shadow-xl border border-zinc-200 dark:border-dark-600 z-50 overflow-hidden">
                                <div class="px-3.5 py-2 border-b border-zinc-100 dark:border-dark-700">
                                    <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500">{{ __('pages.select_invoice_template') }}</p>
                                </div>
                                <div class="p-1.5">
                                    <template x-for="template in templates" :key="template.value">
                                        <button @click="selectedTemplate = template.value; templateOpen = false; printInvoiceWithTemplate({{ $invoice->id }}, template.value)"
                                            class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors flex items-start gap-2.5">
                                            <svg class="w-4 h-4 mt-0.5 text-primary-600 dark:text-primary-400 flex-shrink-0 transition-opacity"
                                                 :class="selectedTemplate === template.value ? 'opacity-100' : 'opacity-0'"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50" x-text="template.label"></p>
                                                <p class="text-xs text-dark-400 dark:text-dark-500 mt-0.5" x-text="template.desc"></p>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        @if ($invoice->status === 'draft')
                            <x-button wire:click="prepareSendInvoice" color="blue" icon="paper-airplane" size="sm"
                                loading="prepareSendInvoice">
                                {{ __('pages.send') }}
                            </x-button>
                        @endif
                    @endif
                </div>

                {{-- Right: Edit + Close --}}
                <div class="flex items-center gap-2">
                    @if ($invoice)
                        <x-button href="{{ route('invoices.edit', $invoice->id) }}" wire:navigate icon="pencil" outline size="sm">
                            {{ __('common.edit') }}
                        </x-button>
                    @endif
                    <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto">
                        {{ __('common.close') }}
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>

    <livewire:payments.delete @payment-deleted="$refresh" @invoice-updated="$refresh" />
    <livewire:payments.attachment-viewer />

    @script
    <script>
        window.printInvoiceWithTemplate = function(invoiceId, template = 'kisantra-invoice') {
            const previewUrl  = @js(route('invoice.preview',  ['invoice' => '__ID__'])).replace('__ID__', invoiceId) + '?template=' + template;
            const downloadUrl = @js(route('invoice.download', ['invoice' => '__ID__'])).replace('__ID__', invoiceId) + '?template=' + template;
            window.open(previewUrl, '_blank');
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };

        $wire.on('print-invoice', (data) => {
            const { invoiceId } = data[0];
            window.printInvoiceWithTemplate(invoiceId, 'kisantra-invoice');
        });
    </script>
    @endscript

    {{-- Send Invoice Modal --}}
    <x-modal wire:model="sendModal" size="md" center persistent>
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
