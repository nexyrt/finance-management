<section class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                    Manajemen Invoice
                </h1>
                <p class="text-gray-600 dark:text-zinc-400 text-lg">
                    Kelola invoice, pembayaran, dan buat invoice baru
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-button color="secondary" icon="document-arrow-down" outline>
                    Export
                </x-button>
                <x-button color="primary" icon="plus">
                    Buat Invoice Baru
                </x-button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Invoice</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">127</p>
                </div>
                <div class="h-12 w-12 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Outstanding</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">Rp 45.5M</p>
                </div>
                <div class="h-12 w-12 bg-red-500/10 dark:bg-red-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>

        <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paid This Month</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">Rp 125M</p>
                </div>
                <div class="h-12 w-12 bg-green-500/10 dark:bg-green-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overdue</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">8</p>
                </div>
                <div class="h-12 w-12 bg-orange-500/10 dark:bg-orange-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="clock" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabs Content --}}
    <x-tab selected="invoices">
        
        {{-- Tab 1: Invoice Management --}}
        <x-tab.items tab="invoices">
            <x-slot:left>
                <x-icon name="document-text" class="w-5 h-5" />
            </x-slot:left>
            <x-slot:right>
                <x-badge text="127" color="blue" />
            </x-slot:right>

            {{-- Filters Section --}}
            <div class="bg-gradient-to-r from-white/90 via-white/95 to-white/90 dark:from-zinc-800/90 dark:via-zinc-800/95 dark:to-zinc-800/90 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 shadow-lg shadow-zinc-500/5 mb-8">
                <div class="flex items-center justify-between p-6 pb-4 border-b border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-icon name="funnel" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Filter Invoice</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Gunakan filter untuk mempersempit pencarian</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        {{-- Status Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                            <x-select.styled :options="[
                                ['label' => '📄 Draft', 'value' => 'draft'],
                                ['label' => '📤 Terkirim', 'value' => 'sent'],
                                ['label' => '✅ Dibayar', 'value' => 'paid'],
                                ['label' => '⏰ Terlambat', 'value' => 'overdue'],
                            ]" placeholder="Semua status..." class="w-full" />
                        </div>

                        {{-- Client Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Klien</label>
                            <x-select.styled :options="[
                                ['label' => 'PT ABC Company', 'value' => '1'],
                                ['label' => 'John Doe', 'value' => '2'],
                                ['label' => 'PT XYZ Corp', 'value' => '3'],
                            ]" placeholder="Semua klien..." searchable class="w-full" />
                        </div>

                        {{-- Date Range --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Periode</label>
                            <x-input placeholder="Pilih tanggal..." icon="calendar" />
                        </div>

                        {{-- Search --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Cari</label>
                            <x-input placeholder="Nomor invoice..." icon="magnifying-glass" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Invoice Table --}}
            <x-table :headers="[
                ['index' => 'invoice_number', 'label' => 'No. Invoice'],
                ['index' => 'client', 'label' => 'Klien'],
                ['index' => 'issue_date', 'label' => 'Tanggal'],
                ['index' => 'due_date', 'label' => 'Jatuh Tempo'],
                ['index' => 'amount', 'label' => 'Jumlah'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
            ]" :rows="[
                (object)[
                    'id' => 1,
                    'invoice_number' => 'INV-000001',
                    'client' => (object)['name' => 'PT ABC Company', 'type' => 'company'],
                    'issue_date' => '2025-01-15',
                    'due_date' => '2025-02-15',
                    'total_amount' => 75000000,
                    'status' => 'sent',
                    'amount_paid' => 0
                ],
                (object)[
                    'id' => 2,
                    'invoice_number' => 'INV-000002',
                    'client' => (object)['name' => 'John Doe', 'type' => 'individual'],
                    'issue_date' => '2025-01-10',
                    'due_date' => '2025-02-10',
                    'total_amount' => 25000000,
                    'status' => 'paid',
                    'amount_paid' => 25000000
                ],
                (object)[
                    'id' => 3,
                    'invoice_number' => 'INV-000003',
                    'client' => (object)['name' => 'PT XYZ Corp', 'type' => 'company'],
                    'issue_date' => '2024-12-20',
                    'due_date' => '2025-01-20',
                    'total_amount' => 50000000,
                    'status' => 'overdue',
                    'amount_paid' => 0
                ]
            ]" filter paginate selectable>

                {{-- Invoice Number Column --}}
                @interact('column_invoice_number', $row)
                    <div class="font-mono font-semibold text-blue-600 dark:text-blue-400">
                        {{ $row->invoice_number }}
                    </div>
                @endinteract

                {{-- Client Column --}}
                @interact('column_client', $row)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                            <x-icon name="{{ $row->client->type === 'individual' ? 'user' : 'building-office' }}" 
                                    class="w-4 h-4 text-primary-600" />
                        </div>
                        <span class="font-medium">{{ $row->client->name }}</span>
                    </div>
                @endinteract

                {{-- Issue Date Column --}}
                @interact('column_issue_date', $row)
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ date('d M Y', strtotime($row->issue_date)) }}
                    </div>
                @endinteract

                {{-- Due Date Column --}}
                @interact('column_due_date', $row)
                    <div class="text-sm">
                        <span class="{{ strtotime($row->due_date) < time() && $row->status !== 'paid' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            {{ date('d M Y', strtotime($row->due_date)) }}
                        </span>
                        @if(strtotime($row->due_date) < time() && $row->status !== 'paid')
                            <div class="text-xs text-red-500 mt-1">
                                {{ floor((time() - strtotime($row->due_date)) / 86400) }} hari lewat
                            </div>
                        @endif
                    </div>
                @endinteract

                {{-- Amount Column --}}
                @interact('column_amount', $row)
                    <div class="text-right">
                        <div class="font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                        </div>
                        @if($row->amount_paid > 0 && $row->amount_paid < $row->total_amount)
                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                Dibayar: Rp {{ number_format($row->amount_paid, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                @endinteract

                {{-- Status Column --}}
                @interact('column_status', $row)
                    @php
                        $statusConfig = [
                            'draft' => ['color' => 'gray', 'text' => '📄 Draft'],
                            'sent' => ['color' => 'blue', 'text' => '📤 Terkirim'],
                            'paid' => ['color' => 'green', 'text' => '✅ Dibayar'],
                            'partially_paid' => ['color' => 'yellow', 'text' => '💰 Sebagian'],
                            'overdue' => ['color' => 'red', 'text' => '⏰ Terlambat'],
                        ];
                        $config = $statusConfig[$row->status] ?? ['color' => 'gray', 'text' => $row->status];
                    @endphp
                    <x-badge text="{{ $config['text'] }}" color="{{ $config['color'] }}" />
                @endinteract

                {{-- Actions Column --}}
                @interact('column_actions', $row)
                    <x-dropdown icon="ellipsis-vertical">
                        <x-dropdown.items text="Lihat Detail" icon="eye" />
                        @if($row->status === 'draft')
                            <x-dropdown.items text="Edit Invoice" icon="pencil" />
                            <x-dropdown.items text="Kirim Invoice" icon="paper-airplane" />
                        @endif
                        @if(in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                            <x-dropdown.items text="Catat Pembayaran" icon="currency-dollar" />
                        @endif
                        <x-dropdown.items text="Print PDF" icon="printer" />
                        <x-dropdown.items text="Duplikasi" icon="document-duplicate" />
                        @if($row->status === 'draft')
                            <x-dropdown.items text="Hapus" icon="trash" />
                        @endif
                    </x-dropdown>
                @endinteract

            </x-table>

        </x-tab.items>

        {{-- Tab 2: Payment Tracking --}}
        <x-tab.items tab="payments">
            <x-slot:left>
                <x-icon name="currency-dollar" class="w-5 h-5" />
            </x-slot:left>
            Payment Tracking
            <x-slot:right>
                <x-badge text="84" color="green" />
            </x-slot:right>

            <div class="text-center py-12">
                <div class="h-24 w-24 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="currency-dollar" class="w-12 h-12 text-green-600 dark:text-green-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Tracking</h3>
                <p class="text-gray-600 dark:text-gray-400">Kelola dan lacak semua pembayaran invoice</p>
            </div>

        </x-tab.items>

        {{-- Tab 3: Create Invoice Form --}}
        <x-tab.items tab="create">
            <x-slot:left>
                <x-icon name="document-plus" class="w-5 h-5" />
            </x-slot:left>
            Buat Invoice
            <x-slot:right>
                <x-badge text="New" color="purple" />
            </x-slot:right>

            <div class="text-center py-12">
                <div class="h-24 w-24 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-plus" class="w-12 h-12 text-purple-600 dark:text-purple-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Buat Invoice Baru</h3>
                <p class="text-gray-600 dark:text-gray-400">Form untuk membuat invoice baru dengan multiple items</p>
            </div>

        </x-tab.items>

    </x-tab>

    {{-- Bulk Actions Bar (sama seperti client management) --}}
    <div x-data="{ show: false }" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-0 left-1/2 transform -translate-x-1/2 z-50 mb-6">
        
        <div class="bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-700/50 px-6 py-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">3 invoice dipilih</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Pilih aksi untuk semua invoice terpilih</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-button color="secondary" size="sm" icon="x-mark">
                        Batal
                    </x-button>
                    <x-button color="blue" size="sm" icon="paper-airplane">
                        Kirim Semua
                    </x-button>
                    <x-button color="red" size="sm" icon="trash">
                        Hapus
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</section>
