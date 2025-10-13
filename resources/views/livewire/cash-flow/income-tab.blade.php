<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Income --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-16 h-16 bg-white/5 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-white" />
                    </div>
                    <div class="text-right">
                        <p class="text-green-100 text-sm font-medium">Total Income</p>
                        <p class="text-3xl font-bold">
                            Rp {{ number_format($this->stats['total_income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-green-100">
                    <x-icon name="arrow-trending-up" class="w-4 h-4" />
                    <span class="text-sm">All income sources combined</span>
                </div>
            </div>
        </div>

        {{-- Bank Income --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-icon name="building-library" class="w-6 h-6 text-white" />
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100 text-sm font-medium">Bank Income</p>
                        <p class="text-2xl font-bold">
                            Rp {{ number_format($this->stats['bank_income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-blue-100">
                    <x-icon name="credit-card" class="w-4 h-4" />
                    <span class="text-sm">Direct bank transactions</span>
                </div>
            </div>
        </div>

        {{-- Payment Profit --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-18 h-18 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-icon name="chart-pie" class="w-6 h-6 text-white" />
                    </div>
                    <div class="text-right">
                        <p class="text-purple-100 text-sm font-medium">Net Profit</p>
                        <p class="text-2xl font-bold">
                            Rp {{ number_format($this->stats['net_payment_profit'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-purple-100">
                    <x-icon name="receipt-percent" class="w-4 h-4" />
                    <span class="text-sm">After COGS & tax deposits</span>
                </div>
            </div>
        </div>

        {{-- Total Transactions --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl p-6 text-white">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-16 h-16 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-icon name="queue-list" class="w-6 h-6 text-white" />
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm font-medium">Transactions</p>
                        <p class="text-2xl font-bold">
                            {{ number_format($this->stats['total_transactions'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-gray-300">
                    <x-icon name="document-text" class="w-4 h-4" />
                    <span class="text-sm">Total records</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters following Invoice pattern --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
            <div>
                <x-select.styled wire:model.live="categoryFilter" label="Category" :options="$this->categories"
                    placeholder="All categories..." />
            </div>

            <div>
                <x-select.styled wire:model.live="sourceFilter" label="Source" :options="[
                    ['label' => 'Bank Income', 'value' => 'bank_transaction'],
                    ['label' => 'Invoice Payment', 'value' => 'payment'],
                ]"
                    placeholder="All sources..." />
            </div>

            <div>
                <x-date wire:model.live="dateRange" label="Date Range" range placeholder="Select period..." />
            </div>

            <div class="lg:col-span-1">
                <!-- Space for additional filter if needed -->
            </div>
        </div>

        <div class="flex gap-2">
            @if ($categoryFilter || $sourceFilter || !empty($dateRange) || $search)
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                    Clear
                </x-button>
            @endif
            <x-button wire:click="exportExcel" size="sm" color="green" icon="document-text" outline>
                Excel
            </x-button>
        </div>
    </div>

    {{-- Table following Invoice pattern --}}
    <x-table :headers="$headers" :sort="$sort" :rows="$this->incomeTransactions" selectable wire:model="selected" paginate filter
        loading="incomeTransactions">

        {{-- Date Column --}}
        @interact('column_date', $row)
            <div>
                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                    {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ \Carbon\Carbon::parse($row->date)->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Description Column --}}
        @interact('column_description', $row)
            <div>
                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                    {{ $row->description ?: 'No description' }}
                </div>
                @if ($row->reference_number)
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        Ref: {{ $row->reference_number }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Source Type Column --}}
        @interact('column_source_type', $row)
            <x-badge :text="$row->source_type" :color="$row->source === 'bank_transaction' ? 'blue' : 'green'" />
        @endinteract

        {{-- Bank Account Column --}}
        @interact('column_bank_account', $row)
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg flex items-center justify-center">
                    <x-icon name="building-library" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->account_name }}</p>
                </div>
            </div>
        @endinteract

        {{-- Category Column --}}
        @interact('column_category', $row)
            @if ($row->category_label)
                <x-badge :text="$row->category_label" color="emerald" />
            @else
                <span class="text-sm text-zinc-400 dark:text-zinc-500">Invoice Payment</span>
            @endif
        @endinteract

        {{-- Amount Column --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-green-600 dark:text-green-400">
                    +Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
            </div>
        @endinteract

    </x-table>

    {{-- Bulk Actions Bar following Invoice pattern --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-50"
                            x-text="`${show.length} transactions selected`"></div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            Choose action for selected transactions
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="exportExcel" size="sm" color="green" icon="document-arrow-down"
                        loading="exportExcel" class="whitespace-nowrap">
                        Export Selected
                    </x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="bulkDelete" class="whitespace-nowrap">
                        Delete
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
