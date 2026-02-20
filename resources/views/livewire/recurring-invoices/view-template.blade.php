<x-modal wire :title="__('pages.ri_view_template_title')" size="6xl" center>
    @if ($template)
        <div class="space-y-6">
            <!-- Template Header -->
            <div
                class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-primary-200 dark:border-primary-700">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">{{ $template->template_name }}
                        </h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400 mt-1">{{ $template->client->name }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <x-badge :text="$template->status === 'active' ? __('pages.ri_status_active') : __('pages.ri_status_archived')" :color="$template->status === 'active' ? 'green' : 'gray'" />
                            <x-badge :text="__('pages.ri_freq_' . $template->frequency)" color="blue" />
                            <span class="text-xs text-dark-600 dark:text-dark-400">
                                {{ $template->start_date->format('d M Y') }} -
                                {{ $template->end_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_total_template_label') }}</div>
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ $template->formatted_total_amount }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-dark-800 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                    <div class="flex items-center gap-2">
                        <x-icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                                {{ $this->invoiceStats['total'] }}</div>
                            <div class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_total_invoice_stat') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-800 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                    <div class="flex items-center gap-2">
                        <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $this->invoiceStats['published'] }}</div>
                            <div class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_published_label') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-800 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        <div>
                            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                {{ $this->invoiceStats['draft'] }}</div>
                            <div class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_draft_label') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-800 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                    <div class="flex items-center gap-2">
                        <x-icon name="currency-dollar" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        <div>
                            <div class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($this->invoiceStats['total_revenue'], 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.ri_revenue_label') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Items -->
            <div class="bg-white dark:bg-dark-800 rounded-lg border border-zinc-200 dark:border-dark-600">
                <div class="p-4 border-b border-zinc-200 dark:border-dark-600">
                    <h4 class="font-medium text-dark-900 dark:text-dark-50">{{ __('pages.ri_template_items_title') }}</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-dark-700">
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-dark-600 dark:text-dark-400 uppercase">
                                    {{ __('pages.ri_col_service') }}</th>
                                <th
                                    class="px-4 py-2 text-center text-xs font-medium text-dark-600 dark:text-dark-400 uppercase">
                                    {{ __('pages.ri_col_qty') }}</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-dark-600 dark:text-dark-400 uppercase">
                                    {{ __('pages.ri_col_price') }}</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-dark-600 dark:text-dark-400 uppercase">
                                    {{ __('pages.ri_col_cogs') }}</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-dark-600 dark:text-dark-400 uppercase">
                                    {{ __('pages.ri_col_amount') }}</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-dark-600 dark:text-dark-400 uppercase">
                                    {{ __('pages.ri_col_profit') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-dark-600">
                            @foreach ($this->templateItems as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ $item['service_name'] }}</div>
                                        @if (isset($item['client_id']) && $item['client_id'] !== $template->client_id)
                                            <div class="text-xs text-blue-600 dark:text-blue-400">{{ __('pages.ri_different_client') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-dark-900 dark:text-dark-50">
                                        {{ $item['quantity'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($item['cogs_amount'], 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-right text-sm font-medium text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        @php $profit = $item['amount'] - $item['cogs_amount']; @endphp
                                        <span
                                            class="{{ $profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            Rp {{ number_format($profit, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-zinc-50 dark:bg-dark-700">
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-3 text-right text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ __('pages.ri_subtotal_label') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-dark-900 dark:text-dark-50">
                                    Rp {{ number_format($template->invoice_template['subtotal'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium">
                                    @php
                                        $totalProfit =
                                            $template->invoice_template['subtotal'] -
                                            collect($this->templateItems)->sum('cogs_amount');
                                    @endphp
                                    <span
                                        class="{{ $totalProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @if ($template->invoice_template['discount_amount'] > 0)
                                <tr>
                                    <td colspan="4"
                                        class="px-4 py-2 text-right text-sm text-dark-600 dark:text-dark-400">
                                        {{ __('pages.ri_discount_label_summary') }}
                                        ({{ $template->invoice_template['discount_type'] === 'percentage' ? $template->invoice_template['discount_value'] . '%' : __('pages.ri_discount_fixed') }}):
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm text-red-600 dark:text-red-400">
                                        -Rp
                                        {{ number_format($template->invoice_template['discount_amount'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-3 text-right text-lg font-bold text-dark-900 dark:text-dark-50">
                                    {{ __('pages.ri_total_label_row') }}
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-lg font-bold text-primary-600 dark:text-primary-400">
                                    {{ $template->formatted_total_amount }}
                                </td>
                                <td class="px-4 py-3 text-right text-lg font-bold">
                                    @php
                                        $finalProfit =
                                            $totalProfit - ($template->invoice_template['discount_amount'] ?? 0);
                                    @endphp
                                    <span
                                        class="{{ $finalProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        Rp {{ number_format($finalProfit, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Next Scheduled Invoices -->
            @if ($this->nextScheduledInvoices->count() > 0)
                <div class="bg-white dark:bg-dark-800 rounded-lg border border-zinc-200 dark:border-dark-600">
                    <div class="p-4 border-b border-zinc-200 dark:border-dark-600">
                        <h4 class="font-medium text-dark-900 dark:text-dark-50">{{ __('pages.ri_upcoming_invoices_title') }}</h4>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            @foreach ($this->nextScheduledInvoices as $invoice)
                                <div
                                    class="flex justify-between items-center p-3 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ $invoice->scheduled_date->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-dark-600 dark:text-dark-400">
                                            {{ $invoice->scheduled_date->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <x-badge :text="$invoice->formatted_total_amount" color="primary" light />
                                        <x-badge :text="__('pages.ri_draft_label')" color="amber" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="zinc" size="sm"
                    loading="$set('modal', false)">
                    {{ __('pages.ri_close_btn') }}
                </x-button>
                <div class="flex gap-2">
                    <x-button wire:click="editTemplate" color="green" size="sm" icon="pencil"
                        loading="editTemplate">
                        {{ __('pages.ri_edit_template_btn') }}
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
