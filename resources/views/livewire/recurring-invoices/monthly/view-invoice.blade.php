<x-modal wire :title="__('pages.ri_view_invoice_modal_title')" size="5xl" center>
    @if ($invoice)
        <div class="space-y-6">
            <!-- Header Info -->
            <div
                class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-primary-200 dark:border-primary-700">
                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-blue-600 flex items-center justify-center">
                                <span class="text-white font-bold text-lg">
                                    {{ strtoupper(substr($invoice->client->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="font-bold text-dark-900 dark:text-dark-50">{{ $invoice->client->name }}</h3>
                                <p class="text-sm text-dark-600 dark:text-dark-400">
                                    {{ $invoice->template->template_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <x-icon name="calendar" class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                                <span class="text-dark-600 dark:text-dark-400">
                                    {{ $invoice->scheduled_date->format('d M Y') }}
                                </span>
                            </div>
                            <x-badge :text="$invoice->status === 'published' ? __('pages.ri_published_label') : __('pages.ri_draft_label')" :color="$invoice->status === 'published' ? 'green' : 'amber'" />
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ri_total_amount_label') }}</div>
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                        </div>
                        @if ($this->discountAmount > 0)
                            <div class="text-sm text-red-500 dark:text-red-400">
                                {{ __('pages.ri_discount_prefix') }} -Rp {{ number_format($this->discountAmount, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Published Invoice Link -->
            @if ($invoice->status === 'published' && $invoice->publishedInvoice)
                <div
                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                            <span class="text-green-800 dark:text-green-200 font-medium">
                                {{ __('pages.ri_published_as', ['number' => $invoice->publishedInvoice->invoice_number]) }}
                            </span>
                        </div>
                        <x-button wire:click="viewPublishedInvoice({{ $invoice->publishedInvoice->id }})" color="green"
                            size="xs" outline icon="eye"
                            loading="viewPublishedInvoice({{ $invoice->publishedInvoice->id }})">
                            {{ __('pages.ri_view_invoice_btn') }}
                        </x-button>
                    </div>
                </div>
            @endif

            <!-- Items -->
            <div class="space-y-3">
                <h4 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_invoice_items_title') }}</h4>

                <div
                    class="bg-white dark:bg-dark-800 rounded-lg border border-zinc-200 dark:border-dark-600 overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block">
                        <div
                            class="bg-zinc-50 dark:bg-dark-700 px-4 py-3 border-b border-zinc-200 dark:border-dark-600">
                            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-dark-900 dark:text-dark-50">
                                <div class="col-span-1">{{ __('pages.ri_col_hash') }}</div>
                                <div class="col-span-2">{{ __('pages.ri_col_client') }}</div>
                                <div class="col-span-3">{{ __('pages.ri_col_service') }}</div>
                                <div class="col-span-1">{{ __('pages.ri_col_qty') }}</div>
                                <div class="col-span-2">{{ __('pages.ri_col_unit_price') }}</div>
                                <div class="col-span-2">{{ __('pages.ri_col_cogs') }}</div>
                                <div class="col-span-1">{{ __('pages.ri_col_total') }}</div>
                            </div>
                        </div>

                        <div class="divide-y divide-zinc-100 dark:divide-dark-700">
                            @foreach ($this->items as $index => $item)
                                <div class="px-4 py-3 hover:bg-zinc-50 dark:hover:bg-dark-700">
                                    <div class="grid grid-cols-12 gap-4 items-center text-sm">
                                        <div class="col-span-1">
                                            <x-badge :text="$index + 1" color="primary" size="sm" />
                                        </div>
                                        <div class="col-span-2">
                                            @php
                                                $itemClient = \App\Models\Client::find($item['client_id']);
                                            @endphp
                                            <div class="font-medium text-dark-900 dark:text-dark-100">
                                                {{ $itemClient?->name ?? __('pages.ri_unknown_client') }}
                                            </div>
                                        </div>
                                        <div class="col-span-3">
                                            <div class="font-medium text-dark-900 dark:text-dark-100">
                                                {{ $item['service_name'] }}
                                            </div>
                                        </div>
                                        <div class="col-span-1 text-center">
                                            {{ $item['quantity'] }}
                                        </div>
                                        <div class="col-span-2 text-dark-900 dark:text-dark-100">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </div>
                                        <div class="col-span-2 text-red-600 dark:text-red-400">
                                            Rp {{ number_format($item['cogs_amount'], 0, ',', '.') }}
                                        </div>
                                        <div class="col-span-1 font-semibold text-dark-900 dark:text-dark-100">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden divide-y divide-zinc-100 dark:divide-dark-700">
                        @foreach ($this->items as $index => $item)
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between items-start">
                                    <x-badge :text="__('pages.ri_item_label_mobile', ['number' => $index + 1])" color="primary" />
                                    <div class="text-right">
                                        <div class="font-semibold text-dark-900 dark:text-dark-100">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-dark-600 dark:text-dark-400">
                                            {{ __('pages.ri_qty_mobile') }} {{ $item['quantity'] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    @php
                                        $itemClient = \App\Models\Client::find($item['client_id']);
                                    @endphp
                                    <div>
                                        <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_client_mobile') }}</span>
                                        <div class="font-medium text-dark-900 dark:text-dark-100">
                                            {{ $itemClient?->name ?? __('pages.ri_unknown_client') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_service_mobile') }}</span>
                                        <div class="font-medium text-dark-900 dark:text-dark-100">
                                            {{ $item['service_name'] }}
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_unit_price_mobile') }}</span>
                                            <div class="text-dark-900 dark:text-dark-100">Rp
                                                {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                        </div>
                                        <div>
                                            <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_cogs_mobile') }}</span>
                                            <div class="text-red-600 dark:text-red-400">
                                                Rp {{ number_format($item['cogs_amount'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-zinc-50 dark:bg-dark-800 rounded-lg p-4 space-y-3">
                <h4 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_financial_summary_title') }}</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-white dark:bg-dark-700 rounded-lg">
                        <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ri_subtotal_stat') }}</div>
                        <div class="font-bold text-lg text-dark-900 dark:text-dark-100">
                            Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="text-center p-3 bg-white dark:bg-dark-700 rounded-lg">
                        <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ri_total_cogs_stat') }}</div>
                        <div class="font-bold text-lg text-red-600 dark:text-red-400">
                            Rp {{ number_format($this->totalCogs, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="text-center p-3 bg-white dark:bg-dark-700 rounded-lg">
                        <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ri_gross_profit_stat') }}</div>
                        <div class="font-bold text-lg text-green-600 dark:text-green-400">
                            Rp {{ number_format($this->grossProfit, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-dark-600 dark:text-dark-400">
                            ({{ number_format($this->grossProfitMargin, 1) }}%)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Info -->
            @if ($this->discountAmount > 0)
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
                    <h4 class="font-semibold text-red-800 dark:text-red-200 mb-2">{{ __('pages.ri_discount_applied_title') }}</h4>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-red-700 dark:text-red-300">
                                {{ $invoice->invoice_data['discount_type'] === 'percentage' ? __('pages.ri_discount_percentage') : __('pages.ri_discount_fixed_amount') }}:
                            </span>
                            <span class="font-medium text-red-800 dark:text-red-200">
                                @if ($invoice->invoice_data['discount_type'] === 'percentage')
                                    {{ number_format($invoice->invoice_data['discount_value'] / 100, 2) }}%
                                @else
                                    Rp {{ number_format($invoice->invoice_data['discount_value'], 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <div class="font-bold text-red-800 dark:text-red-200">
                            -Rp {{ number_format($this->discountAmount, 0, ',', '.') }}
                        </div>
                    </div>
                    @if (!empty($invoice->invoice_data['discount_reason']))
                        <div class="mt-2 text-sm text-red-600 dark:text-red-300">
                            {{ __('pages.ri_discount_reason_prefix') }} {{ $invoice->invoice_data['discount_reason'] }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$set('modal', false)" color="zinc" loading="$set('modal', false)">
                    {{ __('pages.ri_close_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    @endif

</x-modal>
