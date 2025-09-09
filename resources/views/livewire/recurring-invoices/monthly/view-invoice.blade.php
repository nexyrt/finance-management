<x-modal wire title="View Invoice Details" size="5xl" center>
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
                                <h3 class="font-bold text-primary-900 dark:text-primary-100">{{ $invoice->client->name }}
                                </h3>
                                <p class="text-sm text-primary-600 dark:text-primary-300">
                                    {{ $invoice->template->template_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <x-icon name="calendar" class="w-4 h-4 text-gray-500" />
                                <span class="text-gray-600 dark:text-gray-300">
                                    {{ $invoice->scheduled_date->format('d M Y') }}
                                </span>
                            </div>
                            <x-badge :text="ucfirst($invoice->status)" :color="$invoice->status === 'published' ? 'green' : 'amber'" />
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Amount</div>
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                        </div>
                        @if ($this->discountAmount > 0)
                            <div class="text-sm text-red-500">
                                Discount: -Rp {{ number_format($this->discountAmount, 0, ',', '.') }}
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
                                Published as Invoice #{{ $invoice->publishedInvoice->invoice_number }}
                            </span>
                        </div>
                        <x-button
                            wire:click="$dispatch('view-published-invoice', { invoiceId: {{ $invoice->publishedInvoice->id }} })"
                            color="green" size="xs" outline icon="eye">
                            View Invoice
                        </x-button>
                    </div>
                </div>
            @endif

            <!-- Items -->
            <div class="space-y-3">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Invoice Items</h4>

                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block">
                        <div
                            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                <div class="col-span-1">#</div>
                                <div class="col-span-2">Client</div>
                                <div class="col-span-3">Service</div>
                                <div class="col-span-1">Qty</div>
                                <div class="col-span-2">Unit Price</div>
                                <div class="col-span-2">COGS</div>
                                <div class="col-span-1">Total</div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($this->items as $index => $item)
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="grid grid-cols-12 gap-4 items-center text-sm">
                                        <div class="col-span-1">
                                            <x-badge :text="$index + 1" color="primary" size="sm" />
                                        </div>
                                        <div class="col-span-2">
                                            @php
                                                $itemClient = \App\Models\Client::find($item['client_id']);
                                            @endphp
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $itemClient?->name ?? 'Unknown Client' }}
                                            </div>
                                        </div>
                                        <div class="col-span-3">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $item['service_name'] }}
                                            </div>
                                        </div>
                                        <div class="col-span-1 text-center">
                                            {{ $item['quantity'] }}
                                        </div>
                                        <div class="col-span-2">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </div>
                                        <div class="col-span-2 text-red-600 dark:text-red-400">
                                            Rp {{ number_format($item['cogs_amount'], 0, ',', '.') }}
                                        </div>
                                        <div class="col-span-1 font-semibold text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($this->items as $index => $item)
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between items-start">
                                    <x-badge :text="'Item ' . ($index + 1)" color="primary" />
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Qty: {{ $item['quantity'] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    @php
                                        $itemClient = \App\Models\Client::find($item['client_id']);
                                    @endphp
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Client:</span>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $itemClient?->name ?? 'Unknown Client' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Service:</span>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item['service_name'] }}
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Unit Price:</span>
                                            <div>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">COGS:</span>
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
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Financial Summary</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-white dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Subtotal</div>
                        <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="text-center p-3 bg-white dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total COGS</div>
                        <div class="font-bold text-lg text-red-600 dark:text-red-400">
                            Rp {{ number_format($this->totalCogs, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="text-center p-3 bg-white dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Gross Profit</div>
                        <div class="font-bold text-lg text-green-600 dark:text-green-400">
                            Rp {{ number_format($this->grossProfit, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            ({{ number_format($this->grossProfitMargin, 1) }}%)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Info -->
            @if ($this->discountAmount > 0)
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
                    <h4 class="font-semibold text-red-800 dark:text-red-200 mb-2">Discount Applied</h4>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-red-700 dark:text-red-300">
                                {{ $invoice->invoice_data['discount_type'] === 'percentage' ? 'Percentage' : 'Fixed Amount' }}:
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
                            Reason: {{ $invoice->invoice_data['discount_reason'] }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$set('modal', false)" color="gray">
                    Close
                </x-button>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
