<section class="w-full">
    <!-- Page Header -->
    <div class="border-b border-zinc-700 px-6 py-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-zinc-100">Financial Dashboard</h2>
                <p class="mt-1 text-sm text-zinc-400">Monitor your financial metrics and performance</p>
            </div>
            
            <!-- Date Range Picker -->
            <div class="w-full sm:w-64">
                <x-inputs.daterangepicker 
                    label="Date Range"
                    wire:model.live="dateRange"
                />
            </div>
        </div>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
        <!-- Total Revenue -->
        <div class="rounded-lg bg-blue-900 bg-opacity-25 p-5">
            <div class="text-sm font-medium text-zinc-400">Total Revenue</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-100">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-blue-300">
                Collected during selected period
            </div>
        </div>
        
        <!-- Pending Revenue -->
        <div class="rounded-lg bg-amber-900 bg-opacity-25 p-5">
            <div class="text-sm font-medium text-zinc-400">Pending Revenue</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-100">
                Rp {{ number_format($pendingRevenue, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-amber-300">
                Awaiting payment from sent invoices
            </div>
        </div>
        
        <!-- Collection Rate -->
        <div class="rounded-lg bg-green-900 bg-opacity-25 p-5">
            <div class="text-sm font-medium text-zinc-400">Collection Rate</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-100">
                {{ $totalRevenue + $pendingRevenue > 0 
                    ? number_format(($totalRevenue / ($totalRevenue + $pendingRevenue)) * 100, 1) 
                    : 0 }}%
            </div>
            <div class="mt-2 text-xs text-green-300">
                Percentage of billed revenue collected
            </div>
        </div>
    </div>
    
    <!-- Bank Accounts -->
    <div class="border-t border-zinc-700 p-6">
        <h3 class="mb-4 text-lg font-medium text-zinc-100">Bank Account Balances</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-700">
                        <th class="pb-3 text-sm font-medium text-zinc-400">Account Name</th>
                        <th class="pb-3 text-sm font-medium text-zinc-400">Bank</th>
                        <th class="pb-3 text-right text-sm font-medium text-zinc-400">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankBalances as $account)
                        <tr class="border-b border-zinc-800">
                            <td class="py-3 text-sm text-zinc-200">{{ $account->account_name }}</td>
                            <td class="py-3 text-sm text-zinc-300">{{ $account->bank_name }}</td>
                            <td class="py-3 text-right text-sm text-zinc-200">
                                {{ $account->currency == 'IDR' ? 'Rp' : $account->currency }} {{ number_format($account->current_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-center text-sm text-zinc-400">No bank accounts found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Accounts Receivable Aging -->
    <div class="border-t border-zinc-700 p-6">
        <h3 class="mb-4 text-lg font-medium text-zinc-100">Accounts Receivable Aging</h3>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <div class="rounded-lg border border-zinc-700 p-4 text-center">
                <div class="text-sm font-medium text-zinc-400">Current</div>
                <div class="mt-2 text-lg font-semibold text-green-400">
                    Rp {{ number_format($agingReceivables['current'], 0, ',', '.') }}
                </div>
            </div>
            <div class="rounded-lg border border-zinc-700 p-4 text-center">
                <div class="text-sm font-medium text-zinc-400">1-30 Days</div>
                <div class="mt-2 text-lg font-semibold text-blue-400">
                    Rp {{ number_format($agingReceivables['1_30'], 0, ',', '.') }}
                </div>
            </div>
            <div class="rounded-lg border border-zinc-700 p-4 text-center">
                <div class="text-sm font-medium text-zinc-400">31-60 Days</div>
                <div class="mt-2 text-lg font-semibold text-amber-400">
                    Rp {{ number_format($agingReceivables['31_60'], 0, ',', '.') }}
                </div>
            </div>
            <div class="rounded-lg border border-zinc-700 p-4 text-center">
                <div class="text-sm font-medium text-zinc-400">61-90 Days</div>
                <div class="mt-2 text-lg font-semibold text-orange-400">
                    Rp {{ number_format($agingReceivables['61_90'], 0, ',', '.') }}
                </div>
            </div>
            <div class="rounded-lg border border-zinc-700 p-4 text-center">
                <div class="text-sm font-medium text-zinc-400">90+ Days</div>
                <div class="mt-2 text-lg font-semibold text-red-400">
                    Rp {{ number_format($agingReceivables['over_90'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue Reports -->
    <div class="grid grid-cols-1 gap-6 border-t border-zinc-700 p-6 md:grid-cols-2">
        <!-- Revenue by Client -->
        <div>
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-zinc-100">Top Clients by Revenue</h3>
                <div class="w-32">
                    <x-inputs.select
                        placeholder="All Clients"
                        wire:model.live="clientFilter"
                        :options="$clientOptions"
                    />
                </div>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-700">
                            <th class="pb-3 text-sm font-medium text-zinc-400">Client</th>
                            <th class="pb-3 text-right text-sm font-medium text-zinc-400">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientRevenue as $client)
                            <tr class="border-b border-zinc-800">
                                <td class="py-3 text-sm text-zinc-200">{{ $client->name }}</td>
                                <td class="py-3 text-right text-sm text-zinc-200">
                                    Rp {{ number_format($client->revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-3 text-center text-sm text-zinc-400">No client revenue found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Revenue by Service -->
        <div>
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-zinc-100">Top Services by Revenue</h3>
                <div class="w-32">
                    <x-inputs.select
                        placeholder="All Services"
                        wire:model.live="serviceFilter"
                        :options="$serviceOptions"
                    />
                </div>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-700">
                            <th class="pb-3 text-sm font-medium text-zinc-400">Service</th>
                            <th class="pb-3 text-right text-sm font-medium text-zinc-400">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRevenue as $service)
                            <tr class="border-b border-zinc-800">
                                <td class="py-3 text-sm text-zinc-200">{{ $service->name }}</td>
                                <td class="py-3 text-right text-sm text-zinc-200">
                                    Rp {{ number_format($service->revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-3 text-center text-sm text-zinc-400">No service revenue found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Recent Data -->
    <div class="grid grid-cols-1 gap-6 border-t border-zinc-700 p-6 md:grid-cols-2">
        <!-- Recent Invoices -->
        <div>
            <h3 class="mb-4 text-lg font-medium text-zinc-100">Recent Invoices</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-700">
                            <th class="pb-3 text-sm font-medium text-zinc-400">Invoice #</th>
                            <th class="pb-3 text-sm font-medium text-zinc-400">Client</th>
                            <th class="pb-3 text-right text-sm font-medium text-zinc-400">Amount</th>
                            <th class="pb-3 text-right text-sm font-medium text-zinc-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $invoice)
                            <tr class="border-b border-zinc-800">
                                <td class="py-3 text-sm text-zinc-200">{{ $invoice->invoice_number }}</td>
                                <td class="py-3 text-sm text-zinc-300">{{ $invoice->client->name ?? 'Unknown Client' }}</td>
                                <td class="py-3 text-right text-sm text-zinc-200">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right">
                                    <span class="rounded px-2 py-1 text-xs font-medium 
                                        {{ $invoice->status === 'paid' ? 'bg-green-900 bg-opacity-40 text-green-400' : 
                                           ($invoice->status === 'partially_paid' ? 'bg-blue-900 bg-opacity-40 text-blue-400' : 
                                            'bg-amber-900 bg-opacity-40 text-amber-400') }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center text-sm text-zinc-400">No invoices found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Upcoming Installments -->
        <div>
            <h3 class="mb-4 text-lg font-medium text-zinc-100">Upcoming Installment Payments</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-zinc-700">
                            <th class="pb-3 text-sm font-medium text-zinc-400">Invoice #</th>
                            <th class="pb-3 text-sm font-medium text-zinc-400">Client</th>
                            <th class="pb-3 text-right text-sm font-medium text-zinc-400">Installment</th>
                            <th class="pb-3 text-right text-sm font-medium text-zinc-400">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingInstallments as $invoice)
                            <tr class="border-b border-zinc-800">
                                <td class="py-3 text-sm text-zinc-200">{{ $invoice->invoice_number }}</td>
                                <td class="py-3 text-sm text-zinc-300">{{ $invoice->client->name ?? 'Unknown Client' }}</td>
                                <td class="py-3 text-right text-sm text-zinc-200">
                                    Rp {{ number_format($invoice->total_amount / ($invoice->installment_count ?: 1), 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right text-sm text-zinc-200">
                                    {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center text-sm text-zinc-400">No upcoming installments</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>