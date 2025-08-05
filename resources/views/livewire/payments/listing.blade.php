<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Total Payments Card -->
        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
            x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-secondary-900 dark:text-dark-50'>Total Pembayaran</h4><p class='text-xs text-secondary-600 dark:text-dark-300 mb-3'>Jumlah seluruh transaksi pembayaran yang telah diterima</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Transfer Bank: {{ $stats['bank_transfer_count'] ?? 0 }}x</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Tunai: {{ $stats['cash_count'] ?? 0 }}x</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Rata-rata per transaksi: Rp {{ $stats['total_payments'] > 0 ? number_format(($stats['total_amount'] ?? 0) / $stats['total_payments'], 0, ',', '.') : '0' }}</span></div></div></div>">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Total Pembayaran</p>
                    <p class="text-xl font-bold text-secondary-900 dark:text-dark-50">
                        {{ number_format($stats['total_payments']) }}
                    </p>
                </div>
                <div class="h-10 w-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                    <x-icon name="banknotes" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <!-- Total Amount Card -->
        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
            x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-secondary-900 dark:text-dark-50'>Total Nilai Pembayaran</h4><p class='text-xs text-secondary-600 dark:text-dark-300 mb-3'>Akumulasi seluruh nilai pembayaran yang telah diterima</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Via Transfer: Rp {{ number_format($stats['bank_transfer_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Via Tunai: Rp {{ number_format($stats['cash_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Pembayaran terbesar: Rp {{ number_format($stats['max_payment'] ?? 0, 0, ',', '.') }}</span></div></div></div>">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Total Nilai</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400 break-all">
                        Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                </div>
            </div>
        </div>

        <!-- This Month Count Card -->
        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
            x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-secondary-900 dark:text-dark-50'>Pembayaran Bulan Ini</h4><p class='text-xs text-secondary-600 dark:text-dark-300 mb-3'>Jumlah transaksi pembayaran yang diterima pada bulan {{ now()->format('F Y') }}</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Minggu ini: {{ $stats['this_week_count'] ?? 0 }}x</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Hari ini: {{ $stats['today_count'] ?? 0 }}x</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Pertumbuhan: {{ $stats['growth_percentage'] ?? 0 }}% vs bulan lalu</span></div></div></div>">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Bulan Ini</p>
                    <p class="text-xl font-bold text-secondary-900 dark:text-dark-50">
                        {{ number_format($stats['this_month_count']) }}
                    </p>
                </div>
                <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                    <x-icon name="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <!-- This Month Amount Card -->
        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
            x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-secondary-900 dark:text-dark-50'>Nilai Pembayaran Bulan Ini</h4><p class='text-xs text-secondary-600 dark:text-dark-300 mb-3'>Total nilai pembayaran yang diterima pada bulan {{ now()->format('F Y') }}</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Minggu ini: Rp {{ number_format($stats['this_week_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Hari ini: Rp {{ number_format($stats['today_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-secondary-600 dark:text-dark-400'>Rata-rata harian: Rp {{ number_format(($stats['this_month_amount'] ?? 0) / now()->day, 0, ',', '.') }}</span></div></div></div>">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-secondary-600 dark:text-dark-400">Nilai Bulan Ini</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 break-all">
                        Rp {{ number_format($stats['this_month_amount'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Filters Section --}}
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 sm:p-6 pb-4 border-b border-secondary-200 dark:border-dark-600">
            <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="funnel" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-50">Filter Pembayaran</h3>
                    <p class="text-sm text-secondary-500 dark:text-dark-400">Gunakan filter untuk mempersempit pencarian</p>
                </div>
            </div>

            {{-- Export Action --}}
            <x-dropdown icon="document-arrow-down" outline color="secondary" class="w-full sm:w-auto">
                <x-dropdown.items text="Export Excel" icon="document-text" wire:click="exportExcel" />
                <x-dropdown.items text="Export PDF" icon="document" wire:click="exportPdf" />
            </x-dropdown>
        </div>

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                {{-- Payment Method Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-700 dark:text-dark-300">Metode</label>
                    <x-select.styled wire:model.live="paymentMethodFilter" :options="[
                        ['label' => '💳 Transfer Bank', 'value' => 'bank_transfer'],
                        ['label' => '💵 Tunai', 'value' => 'cash'],
                    ]" placeholder="Semua metode..." class="w-full" />
                </div>

                {{-- Bank Account Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-700 dark:text-dark-300">Rekening</label>
                    <x-select.styled wire:model.live="bankAccountFilter" :options="$bankAccounts
                        ->map(
                            fn($account) => [
                                'label' => $account->bank_name . ' - ' . $account->account_name,
                                'value' => $account->id,
                            ],
                        )
                        ->toArray()" placeholder="Semua rekening..." searchable class="w-full" />
                </div>

                {{-- Invoice Status Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-700 dark:text-dark-300">Status Invoice</label>
                    <x-select.styled wire:model.live="invoiceStatusFilter" :options="[
                        ['label' => '✅ Dibayar', 'value' => 'paid'],
                        ['label' => '💰 Sebagian', 'value' => 'partially_paid'],
                        ['label' => '📤 Terkirim', 'value' => 'sent'],
                        ['label' => '⏰ Terlambat', 'value' => 'overdue'],
                    ]" placeholder="Semua status..." class="w-full" />
                </div>

                {{-- Date Range Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-700 dark:text-dark-300">Periode</label>
                    <x-date wire:model.live="dateRange" range placeholder="Pilih periode..." class="w-full" />
                </div>

                {{-- Clear Filters --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-700 dark:text-dark-300">Reset</label>
                    <x-button wire:click="clearFilters" color="secondary" icon="x-mark" outline class="w-full">
                        Hapus Filter
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments Table --}}
    <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable wire:model="selected">

        {{-- Payment Date Column --}}
        @interact('column_payment_date', $row)
            <div class="space-y-1">
                <div class="text-sm font-medium text-secondary-900 dark:text-dark-50">
                    {{ \Carbon\Carbon::parse($row->payment_date)->format('d M Y') }}
                </div>
                <div class="text-xs text-secondary-500 dark:text-dark-400 flex items-center gap-1">
                    <x-icon name="calendar" class="w-3 h-3" />
                    {{ \Carbon\Carbon::parse($row->payment_date)->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Invoice Number Column --}}
        @interact('column_invoice_number', $row)
            <div class="group cursor-pointer" wire:click="viewInvoice({{ $row->invoice_id }})">
                <div class="font-mono font-bold text-primary-600 dark:text-primary-400 group-hover:text-primary-700 dark:group-hover:text-primary-300 transition-colors duration-200">
                    {{ $row->invoice_number }}
                </div>
                <div class="text-xs text-secondary-500 dark:text-dark-400 mt-1">
                    Status: 
                    @php
                        $statusConfig = [
                            'paid' => ['text' => 'Lunas', 'color' => 'green'],
                            'partially_paid' => ['text' => 'Sebagian', 'color' => 'yellow'],
                            'sent' => ['text' => 'Terkirim', 'color' => 'blue'],
                            'overdue' => ['text' => 'Terlambat', 'color' => 'red'],
                        ];
                        $config = $statusConfig[$row->invoice_status] ?? ['text' => ucfirst($row->invoice_status), 'color' => 'gray'];
                    @endphp
                    <span class="text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400 font-medium">
                        {{ $config['text'] }}
                    </span>
                </div>
            </div>
        @endinteract

        {{-- Client Column --}}
        @interact('column_client_name', $row)
            <div class="flex items-center space-x-3">
                <div class="relative flex-shrink-0">
                    <div class="w-8 h-8 {{ $row->client_type === 'individual'
                        ? 'bg-gradient-to-br from-primary-400 to-primary-600'
                        : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                rounded-lg flex items-center justify-center shadow-sm">
                        <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                            class="w-4 h-4 text-white" />
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-secondary-900 dark:text-dark-50 truncate text-sm">
                        {{ $row->client_name }}
                    </p>
                    <div class="flex items-center gap-1 text-xs text-secondary-500 dark:text-dark-400">
                        <span class="capitalize">{{ $row->client_type === 'individual' ? 'Individu' : 'Perusahaan' }}</span>
                    </div>
                </div>
            </div>
        @endinteract

        {{-- Amount Column --}}
        @interact('column_amount', $row)
            <div class="text-right space-y-1">
                <div class="font-bold text-lg text-green-600 dark:text-green-400">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                @if($row->reference_number)
                    <div class="text-xs text-secondary-500 dark:text-dark-400 font-mono">
                        Ref: {{ $row->reference_number }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Payment Method Column --}}
        @interact('column_payment_method', $row)
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg 
                {{ $row->payment_method === 'bank_transfer' 
                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' 
                    : 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300' }}">
                <x-icon name="{{ $row->payment_method === 'bank_transfer' ? 'credit-card' : 'banknotes' }}" class="w-4 h-4" />
                <span class="font-medium text-sm">
                    {{ $row->payment_method === 'bank_transfer' ? 'Transfer' : 'Tunai' }}
                </span>
            </div>
        @endinteract

        {{-- Bank Account Column --}}
        @interact('column_bank_account', $row)
            <div class="space-y-1">
                <div class="font-medium text-secondary-900 dark:text-dark-50 text-sm">
                    {{ $row->bank_name }}
                </div>
                <div class="text-xs text-secondary-500 dark:text-dark-400">
                    {{ $row->account_name }}
                </div>
            </div>
        @endinteract

        {{-- Actions Column --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-2">
                <x-dropdown icon="ellipsis-vertical" class="relative">
                    {{-- Header --}}
                    <div class="px-4 py-2 border-b border-secondary-100 dark:border-dark-700 bg-secondary-50 dark:bg-dark-800">
                        <div class="font-mono text-sm font-medium text-secondary-900 dark:text-dark-50">
                            Rp {{ number_format($row->amount, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-secondary-500 dark:text-dark-400">
                            {{ $row->invoice_number }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="py-1">
                        <x-dropdown.items text="Lihat Invoice" icon="eye"
                            wire:click="viewInvoice({{ $row->invoice_id }})" />
                        <x-dropdown.items text="Edit Payment" icon="pencil"
                            wire:click="editPayment({{ $row->id }})"
                            class="text-primary-600 dark:text-primary-400" />
                    </div>

                    {{-- Danger Actions --}}
                    <div class="border-t border-secondary-100 dark:border-dark-700 py-1">
                        <x-dropdown.items text="Hapus Payment" icon="trash"
                            wire:click="$dispatch('delete-payment', { paymentId: {{ $row->id }} })"
                            class="text-red-600 dark:text-red-400" />
                    </div>
                </x-dropdown>
            </div>
        @endinteract

    </x-table>
</div>