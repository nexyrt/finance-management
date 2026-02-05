<div>
    <x-modal wire :title="__('invoice.invoice_details')" size="5xl" center>
        @if ($invoice)
            {{-- Header --}}
            <div class="bg-white dark:bg-dark-800 -m-6 mb-6 p-6 border-b border-zinc-200 dark:border-dark-600">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    {{-- Left: Invoice Info --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-zinc-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                            <x-icon name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-6 h-6 text-dark-600 dark:text-dark-400" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-dark-900 dark:text-dark-50 font-mono">
                                {{ $invoice->invoice_number }}
                            </h2>
                            <p class="text-sm text-dark-600 dark:text-dark-400">{{ $invoice->client->name }}</p>
                        </div>
                    </div>

                    {{-- Right: Amount & Status --}}
                    <div class="flex flex-col items-end gap-2">
                        <x-badge :text="match ($invoice->status) {
                            'draft' => __('common.draft'),
                            'sent' => __('invoice.sent'),
                            'paid' => __('common.paid'),
                            'partially_paid' => __('common.partially_paid'),
                            'overdue' => __('common.overdue'),
                            default => ucfirst($invoice->status),
                        }" :color="match ($invoice->status) {
                            'draft' => 'zinc',
                            'sent' => 'blue',
                            'paid' => 'green',
                            'partially_paid' => 'yellow',
                            'overdue' => 'red',
                            default => 'zinc',
                        }" />

                        <div class="text-right">
                            <p class="text-xl font-semibold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </p>
                            @if ($this->grossProfit > 0)
                                <p class="text-sm text-dark-600 dark:text-dark-400">
                                    {{ __('pages.profit') }}: Rp {{ number_format($this->grossProfit, 0, ',', '.') }}
                                </p>
                            @endif
                            @if ($invoice->amount_paid > 0)
                                @php $percentage = ($invoice->amount_paid / $invoice->total_amount) * 100; @endphp
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="w-16 bg-zinc-200 dark:bg-dark-700 rounded-full h-1.5">
                                        <div class="bg-zinc-600 dark:bg-zinc-400 h-1.5 rounded-full"
                                            style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-dark-600 dark:text-dark-400">{{ number_format($percentage, 0) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Content --}}
            <x-tab :selected="$selectedTab">
                {{-- Tab 1: Overview --}}
                <x-tab.items tab="overview">
                    <x-slot:left>
                        <x-icon name="document-text" class="w-4 h-4" />
                    </x-slot:left>

                    <div class="space-y-6">
                        {{-- Quick Stats Grid --}}
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('invoice.invoice_date') }}</p>
                                <p class="font-medium text-dark-900 dark:text-dark-50">
                                    {{ $invoice->issue_date->format('d M Y') }}
                                </p>
                            </div>
                            <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('invoice.due_date') }}</p>
                                <p
                                    class="font-medium {{ $invoice->due_date->isPast() && $invoice->status !== 'paid'
                                        ? 'text-red-600 dark:text-red-400'
                                        : 'text-dark-900 dark:text-dark-50' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </p>
                            </div>
                            <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.total_items') }}</p>
                                <p class="font-medium text-dark-900 dark:text-dark-50">{{ $invoice->items->count() }}
                                    {{ __('pages.items') }}</p>
                            </div>
                            <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.gross_profit') }}</p>
                                <p class="font-medium text-dark-900 dark:text-dark-50">
                                    Rp {{ number_format($this->grossProfit, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Faktur Attachment --}}
                        @if ($invoice->faktur)
                            <div class="border border-zinc-200 dark:border-dark-600 rounded-lg overflow-hidden">
                                <div class="bg-white dark:bg-dark-800 px-4 py-3 border-b border-zinc-200 dark:border-dark-600">
                                    <h4 class="font-medium text-dark-900 dark:text-dark-50 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Faktur
                                    </h4>
                                </div>
                                <div class="p-4">
                                    @php
                                        $fileExtension = pathinfo($invoice->faktur, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($fileExtension) === 'pdf';
                                    @endphp

                                    @if ($isPdf)
                                        <div class="aspect-[8.5/11] border border-zinc-200 dark:border-dark-600 rounded-lg overflow-hidden">
                                            <iframe src="{{ Storage::url($invoice->faktur) }}" class="w-full h-full"></iframe>
                                        </div>
                                    @else
                                        <div class="border border-zinc-200 dark:border-dark-600 rounded-lg overflow-hidden">
                                            <img src="{{ Storage::url($invoice->faktur) }}" alt="Faktur" class="w-full h-auto">
                                        </div>
                                    @endif

                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-sm text-dark-600 dark:text-dark-400">{{ basename($invoice->faktur) }}</span>
                                        <a href="{{ Storage::url($invoice->faktur) }}" download class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Invoice Items --}}
                        <div class="border border-zinc-200 dark:border-dark-600 rounded-lg overflow-hidden">
                            <div
                                class="bg-white dark:bg-dark-800 px-4 py-3 border-b border-zinc-200 dark:border-dark-600">
                                <h4 class="font-medium text-dark-900 dark:text-dark-50 flex items-center gap-2">
                                    <x-icon name="list-bullet" class="w-4 h-4" />
                                    {{ __('pages.invoice_items') }}
                                </h4>
                            </div>
                            <div class="divide-y divide-zinc-200 dark:divide-dark-600">
                                @foreach ($invoice->items as $item)
                                    <div
                                        class="px-4 py-3 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-dark-800">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-8 h-8 bg-zinc-100 dark:bg-dark-700 text-dark-600 dark:text-dark-400 rounded-lg flex items-center justify-center">
                                                <x-icon
                                                    name="{{ $item->client->type === 'individual' ? 'user' : 'building-office' }}"
                                                    class="w-4 h-4" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <p
                                                        class="font-medium text-dark-900 dark:text-dark-50 text-sm truncate">
                                                        {{ $item->service_name }}
                                                    </p>
                                                    @if ($item->is_tax_deposit)
                                                        <x-badge :text="__('invoice.tax_deposit')" color="amber" size="xs" />
                                                    @endif
                                                </div>
                                                <p class="text-xs text-dark-600 dark:text-dark-400 truncate">
                                                    {{ $item->client->name }} • {{ __('invoice.qty') }}: {{ $item->quantity }}
                                                    @if (!$item->is_tax_deposit && $item->cogs_amount > 0)
                                                        • {{ __('pages.profit') }}: Rp
                                                        {{ number_format($item->profit_amount, 0, ',', '.') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0 ml-3">
                                            <p class="font-medium text-dark-900 dark:text-dark-50 text-sm">
                                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-dark-600 dark:text-dark-400">
                                                @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div
                                class="bg-white dark:bg-dark-800 px-4 py-3 border-t border-zinc-200 dark:border-dark-600">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-dark-600 dark:text-dark-400">{{ __('invoice.subtotal') }}</span>
                                        <span class="text-dark-900 dark:text-dark-50">
                                            Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @if ($invoice->discount_amount > 0)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-dark-600 dark:text-dark-400">{{ __('invoice.discount') }}</span>
                                            <span class="text-green-600 dark:text-green-400">
                                                -Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div
                                        class="flex justify-between items-center border-t border-zinc-200 dark:border-dark-600 pt-2">
                                        <span class="font-medium text-dark-900 dark:text-dark-50">{{ __('invoice.total_invoice') }}</span>
                                        <span class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab.items>

                {{-- Tab 2: Payments --}}
                <x-tab.items tab="payments">
                    <x-slot:left>
                        <x-icon name="credit-card" class="w-4 h-4" />
                    </x-slot:left>

                    @if ($invoice->payments->count() > 0)
                        {{-- Payment Summary --}}
                        @php
                            $totalPaid = $invoice->amount_paid;
                            $remaining = $invoice->amount_remaining;
                            $percentage = ($totalPaid / $invoice->total_amount) * 100;
                        @endphp

                        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-lg p-4 mb-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                <div>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_paid') }}</p>
                                    <p class="text-xl font-semibold text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if ($remaining > 0)
                                    <div class="text-left sm:text-right">
                                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.remaining_bill') }}</p>
                                        <p class="text-xl font-semibold text-dark-900 dark:text-dark-50">
                                            Rp {{ number_format($remaining, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-dark-700 rounded-full h-2 mb-2">
                                <div class="bg-zinc-600 dark:bg-zinc-400 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-dark-600 dark:text-dark-400">
                                {{ number_format($percentage, 1) }}% {{ __('pages.of_total_invoice') }}
                            </p>
                        </div>

                        {{-- Payment List --}}
                        <div class="space-y-3">
                            @foreach ($invoice->payments as $payment)
                                <div
                                    class="border border-zinc-200 dark:border-dark-600 rounded-lg p-4 hover:bg-zinc-50 dark:hover:bg-dark-700 transition">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div
                                                class="w-10 h-10 bg-zinc-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                                                <x-icon name="banknotes"
                                                    class="w-5 h-5 text-dark-600 dark:text-dark-400" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-medium text-gray-900 dark:text-gray-50">
                                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $payment->payment_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <div class="text-left sm:text-right">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-50">
                                                    {{ $payment->bankAccount->bank_name }}
                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                                </p>
                                                @if ($payment->reference_number)
                                                    <p class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                                        {{ $payment->reference_number }}
                                                    </p>
                                                @endif
                                                {{-- Attachment indicator --}}
                                                @if ($payment->hasAttachment())
                                                    <div class="flex items-center gap-1 mt-1">
                                                        <x-icon name="paper-clip" class="w-3 h-3 text-blue-500" />
                                                        <span class="text-xs text-blue-600 dark:text-blue-400">
                                                            {{ $payment->attachment_name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Action buttons --}}
                                            <div class="flex items-center gap-1">
                                                @if ($payment->hasAttachment())
                                                    <x-button.circle
                                                        wire:click="showPaymentAttachment({{ $payment->id }})"
                                                        loading="showPaymentAttachment({{ $payment->id }})"
                                                        color="blue" icon="eye" size="sm" outline
                                                        :title="__('pages.view_attachment')" />
                                                @endif

                                                <x-button.circle
                                                    wire:click="$dispatch('delete-payment', { paymentId: {{ $payment->id }} })"
                                                    color="red" icon="trash" size="sm" outline
                                                    :title="__('pages.delete_payment')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="bg-zinc-100 dark:bg-dark-700 rounded-lg w-12 h-12 flex items-center justify-center mx-auto mb-4">
                                <x-icon name="credit-card" class="w-6 h-6 text-dark-600 dark:text-dark-400" />
                            </div>
                            <h3 class="font-medium text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.no_payments_yet') }}</h3>
                            <p class="text-dark-600 dark:text-dark-400 text-sm mb-4">
                                {{ __('pages.invoice_no_payments_received') }}
                            </p>
                            @if (in_array($invoice->status, ['sent', 'overdue', 'partially_paid']))
                                <x-button wire:click="recordPayment" color="green" icon="plus" size="sm">
                                    {{ __('pages.record_payment') }}
                                </x-button>
                            @endif
                        </div>
                    @endif
                </x-tab.items>

                {{-- Tab 3: Details --}}
                <x-tab.items tab="details">
                    <x-slot:left>
                        <x-icon name="information-circle" class="w-4 h-4" />
                    </x-slot:left>

                    <div class="space-y-6">
                        {{-- Client Details --}}
                        <div class="border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                            <h4 class="font-medium text-dark-900 dark:text-dark-50 mb-3 flex items-center gap-2">
                                <x-icon
                                    name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}"
                                    class="w-4 h-4" />
                                {{ __('pages.client_information') }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-dark-600 dark:text-dark-400">{{ __('pages.name') }}</p>
                                    <p class="font-medium text-dark-900 dark:text-dark-50">
                                        {{ $invoice->client->name }}</p>
                                </div>
                                <div>
                                    <p class="text-dark-600 dark:text-dark-400">{{ __('pages.type') }}</p>
                                    <p class="font-medium text-dark-900 dark:text-dark-50">
                                        {{ ucfirst($invoice->client->type) }}</p>
                                </div>
                                @if ($invoice->client->email)
                                    <div>
                                        <p class="text-dark-600 dark:text-dark-400">Email</p>
                                        <p class="font-medium text-dark-900 dark:text-dark-50 break-all">
                                            {{ $invoice->client->email }}</p>
                                    </div>
                                @endif
                                @if ($invoice->client->NPWP)
                                    <div>
                                        <p class="text-dark-600 dark:text-dark-400">NPWP</p>
                                        <p class="font-medium text-dark-900 dark:text-dark-50 font-mono">
                                            {{ $invoice->client->NPWP }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Financial Breakdown --}}
                        <div class="border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                            <h4 class="font-medium text-dark-900 dark:text-dark-50 mb-3 flex items-center gap-2">
                                <x-icon name="calculator" class="w-4 h-4" />
                                {{ __('pages.financial_breakdown') }}
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-dark-600 dark:text-dark-400">{{ __('invoice.total_invoice') }}</span>
                                    <span class="text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-dark-600 dark:text-dark-400">{{ __('invoice.tax_deposit') }}</span>
                                    <span class="text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($this->totalTaxDeposits, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-dark-600 dark:text-dark-400">{{ __('pages.total_cogs_label') }}</span>
                                    <span class="text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($this->totalCogs, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between border-t border-zinc-200 dark:border-dark-600 pt-2">
                                    <span class="font-medium text-dark-900 dark:text-dark-50">{{ __('pages.gross_profit') }}</span>
                                    <span class="font-medium text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($this->grossProfit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="border border-zinc-200 dark:border-dark-600 rounded-lg p-4">
                            <h4 class="font-medium text-dark-900 dark:text-dark-50 mb-3 flex items-center gap-2">
                                <x-icon name="clock" class="w-4 h-4" />
                                {{ __('pages.timeline') }}
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 bg-zinc-500 dark:bg-zinc-400 rounded-full flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.invoice_created') }}
                                        </p>
                                        <p class="text-xs text-dark-600 dark:text-dark-400">
                                            {{ $invoice->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                @if ($invoice->status !== 'draft')
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-zinc-500 dark:bg-zinc-400 rounded-full flex-shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.invoice_sent') }}</p>
                                            <p class="text-xs text-dark-600 dark:text-dark-400">
                                                {{ $invoice->issue_date->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @foreach ($invoice->payments as $payment)
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-zinc-500 dark:bg-zinc-400 rounded-full flex-shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                                {{ __('pages.payment_received') }}
                                            </p>
                                            <p class="text-xs text-dark-600 dark:text-dark-400">
                                                {{ $payment->payment_date->format('d M Y') }} •
                                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse flex-shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-red-600">{{ __('pages.past_due_date') }}</p>
                                            <p class="text-xs text-red-500">
                                                {{ $invoice->due_date->format('d M Y') }} •
                                                {{ abs($invoice->due_date->diffInDays(now())) }} {{ __('pages.days_ago') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-tab.items>
            </x-tab>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
                {{-- Quick Actions --}}
                <div class="flex flex-wrap items-center gap-2" x-data="{
                    templateOpen: false,
                    selectedTemplate: 'kisantra-invoice',
                    templates: [
                        { value: 'kisantra-invoice', label: 'Kisantra (Default)', desc: 'Template default dengan branding Kisantra' },
                        { value: 'semesta-invoice', label: 'Semesta (Mining)', desc: 'Template untuk mining/trading dengan PPN + PPH 22' },
                        { value: 'agsa-invoice', label: 'AGSA', desc: 'Template alternatif AGSA' },
                        { value: 'invoice', label: 'Generic', desc: 'Template sederhana' }
                    ]
                }">
                    @if ($invoice)
                        {{-- Print with Template Selection --}}
                        <div class="relative">
                            <x-button @click="templateOpen = !templateOpen" color="primary" icon="printer" outline size="sm">
                                {{ __('pages.print_pdf') }}
                            </x-button>

                            {{-- Template Dropdown --}}
                            <div x-show="templateOpen"
                                 @click.away="templateOpen = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-dark-800 rounded-lg shadow-xl border border-dark-200 dark:border-dark-700 z-50">
                                <div class="p-3 border-b border-dark-200 dark:border-dark-700">
                                    <h3 class="font-semibold text-sm text-dark-900 dark:text-dark-50">{{ __('pages.select_invoice_template') }}</h3>
                                </div>
                                <div class="p-2 max-h-96 overflow-y-auto">
                                    <template x-for="template in templates" :key="template.value">
                                        <button
                                            @click="selectedTemplate = template.value; templateOpen = false; printInvoiceWithTemplate({{ $invoice->id }}, template.value)"
                                            class="w-full text-left px-3 py-2 rounded hover:bg-primary-50 dark:hover:bg-primary-900/20 transition group">
                                            <div class="flex items-start gap-2">
                                                <div class="mt-1">
                                                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" :class="{ 'opacity-100': selectedTemplate === template.value, 'opacity-0': selectedTemplate !== template.value }" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-medium text-dark-900 dark:text-dark-50 text-sm" x-text="template.label"></div>
                                                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5" x-text="template.desc"></div>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        @if ($invoice->status === 'draft')
                            <x-button wire:click="sendInvoice" color="blue" icon="paper-airplane" size="sm">
                                {{ __('pages.send') }}
                            </x-button>
                        @endif

                        @if (in_array($invoice->status, ['sent', 'overdue', 'partially_paid']))
                            <x-button wire:click="recordPayment" color="green" icon="currency-dollar"
                                size="sm">
                                {{ __('pages.pay') }}
                            </x-button>
                        @endif
                    @endif
                </div>

                {{-- Main Actions --}}
                <div class="flex items-center gap-2">
                    @if ($invoice)
                        <x-button href="{{ route('invoices.edit', $invoice->id) }}" wire:navigate icon="pencil"
                            outline size="sm">
                            {{ __('common.edit') }}
                        </x-button>
                    @endif
                    <x-button wire:click="$set('modal', false)" color="zinc">
                        {{ __('common.close') }}
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>

    <livewire:payments.delete @payment-deleted="$refresh" @invoice-updated="$refresh" />
    <livewire:payments.attachment-viewer />
</div>

<script>
    // Global function for printing with template selection
    function printInvoiceWithTemplate(invoiceId, template = 'kisantra-invoice') {
        const previewUrl = `/invoice/${invoiceId}/preview?template=${template}`;
        const downloadUrl = `/invoice/${invoiceId}/download?template=${template}`;

        window.open(previewUrl, '_blank');
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `Invoice-${invoiceId}.pdf`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 500);
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('print-invoice', (data) => {
            const {
                invoiceId,
                invoiceNumber
            } = data[0];

            // Use default template if called from wire:click
            printInvoiceWithTemplate(invoiceId, 'kisantra-invoice');
        });
    });
</script>
