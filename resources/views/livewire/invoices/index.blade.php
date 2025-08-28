{{-- resources/views/livewire/invoices/index.blade.php --}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Manajemen Klien
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Kelola klien Anda dan lacak hubungan bisnis mereka
            </p>
        </div>
        <x-button wire:click="createInvoice" loading="createInvoice" color="primary" icon="plus">
            Buat Invoice Baru
        </x-button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total COGS</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($stats['total_cogs'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Gross Profit</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($stats['gross_profit'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-500 dark:text-green-400">
                        {{ number_format($stats['gross_profit_margin'], 1) }}% margin
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Outstanding</p>
                    <p class="text-xl font-bold text-orange-600 dark:text-orange-400">
                        Rp {{ number_format($stats['outstanding_amount'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Row --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4 flex-1">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <x-select.styled wire:model.live="statusFilter" :options="[
                    ['label' => 'Draft', 'value' => 'draft'],
                    ['label' => 'Terkirim', 'value' => 'sent'],
                    ['label' => 'Dibayar', 'value' => 'paid'],
                    ['label' => 'Sebagian', 'value' => 'partially_paid'],
                    ['label' => 'Terlambat', 'value' => 'overdue'],
                ]" placeholder="Semua status..." />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Klien</label>
                <x-select.styled wire:model.live="clientFilter" :options="$clients
                    ->map(
                        fn($client) => [
                            'label' => $client->name,
                            'value' => $client->id,
                        ],
                    )
                    ->toArray()" placeholder="Semua klien..."
                    searchable />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode</label>
                <x-date wire:model.live="dateRange" range placeholder="Pilih periode..." />
            </div>
        </div>

        <div class="flex gap-2">
            @if ($statusFilter || $clientFilter || !empty($dateRange))
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                    Clear
                </x-button>
            @endif
            <x-button wire:click="exportExcel" size="sm" color="green" icon="document-text" outline>
                Excel
            </x-button>
            <x-button wire:click="exportPdf" size="sm" color="red" icon="document" outline>
                PDF
            </x-button>
        </div>
    </div>

    {{-- Invoice Table --}}
    <x-table :$headers :$sort :rows="$this->invoices" selectable wire:model="selected" paginate filter loading>

        {{-- Invoice Number Column --}}
        @interact('column_invoice_number', $row)
            <div>
                <div class="font-mono font-bold text-zinc-600 dark:text-zinc-400">
                    {{ $row->invoice_number }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->issue_date->format('d/m/Y') }}
                </div>
            </div>
        @endinteract

        {{-- Client Column --}}
        @interact('column_client_name', $row)
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 {{ $row->client_type === 'individual'
                        ? 'bg-gradient-to-br from-zinc-400 to-zinc-600'
                        : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                    rounded-2xl flex items-center justify-center shadow-lg">
                    <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                        class="w-5 h-5 text-white" />
                </div>
                <div>
                    <p class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->client_name }}</p>
                    <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">
                        {{ $row->client_type === 'individual' ? 'Individu' : 'Perusahaan' }}
                    </div>
                </div>
            </div>
        @endinteract

        {{-- Issue Date Column --}}
        @interact('column_issue_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->issue_date->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->issue_date->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Due Date Column --}}
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
                    {{ $row->due_date->format('d M Y') }}
                </div>
                @if ($isOverdue)
                    <div class="text-xs text-red-600 dark:text-red-400">
                        {{ (int) abs($row->due_date->diffInDays(now())) }} hari lewat
                    </div>
                @elseif($isDueSoon)
                    <div class="text-xs text-yellow-600 dark:text-yellow-400">
                        {{ (int) $row->due_date->diffInDays(now()) }} hari lagi
                    </div>
                @else
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->due_date->diffForHumans() }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Amount Column --}}
        @interact('column_total_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                </div>
                @if ($row->amount_paid > 0)
                    @php
                        $paymentPercentage = ($row->amount_paid / $row->total_amount) * 100;
                    @endphp
                    <div class="mt-1">
                        <div class="text-xs text-green-600 dark:text-green-400">
                            {{ number_format($paymentPercentage, 1) }}% Dibayar
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-dark-700 rounded-full h-1 mt-1">
                            <div class="bg-green-500 h-1 rounded-full" style="width: {{ min($paymentPercentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-dark-500 dark:text-dark-400">Belum dibayar</div>
                @endif
            </div>
        @endinteract

        {{-- Status Column --}}
        @interact('column_status', $row)
            <x-badge :text="match ($row->status) {
                'draft' => 'Draft',
                'sent' => 'Terkirim',
                'paid' => 'Dibayar',
                'partially_paid' => 'Sebagian',
                'overdue' => 'Terlambat',
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

        {{-- Actions Column --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="$dispatch('show-invoice', { invoiceId: {{ $row->id }} })" title="Lihat Detail" />

                <x-button.circle icon="pencil" color="green" size="sm" href="{{ route('invoices.edit', $row->id) }}"
                    wire:navigate title="Edit" />

                @if ($row->status === 'draft')
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click='sendInvoice({{ $row->id }})' title="Kirim" />
                @endif

                @if (in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                    <x-button.circle icon="currency-dollar" color="yellow" size="sm"
                        wire:click="$dispatch('record-payment', { invoiceId: {{ $row->id }} })"
                        title="Catat Pembayaran" />
                @endif

                <x-button.circle icon="printer" color="gray" size="sm"
                    onclick="printInvoice({{ $row->id }})" title="Print" />

                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="$dispatch('delete-invoice', { invoiceId: {{ $row->id }} })" />
            </div>
        @endinteract

    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} invoice dipilih`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            Pilih aksi untuk invoice yang dipilih
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="bulkPrintInvoices" size="sm" color="blue" icon="printer"
                        loading="bulkPrintInvoices" class="whitespace-nowrap">
                        Print All
                    </x-button>
                    <x-button wire:click="openBulkDeleteModal" size="sm" color="red" icon="trash"
                        class="whitespace-nowrap">
                        Hapus
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        Batal
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Modal --}}
    <x-modal wire:model="showBulkDeleteModal" size="lg" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Konfirmasi Bulk Delete</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Hapus beberapa invoice sekaligus</p>
                </div>
            </div>
        </x-slot:title>

        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800">
            <div class="flex items-start gap-3">
                <div
                    class="h-8 w-8 bg-red-100 dark:bg-red-800/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h4 class="font-semibold text-red-900 dark:text-red-100 mb-2">Perhatian!</h4>
                    <p class="text-sm text-red-800 dark:text-red-200">
                        Anda akan menghapus <strong>{{ count($selected) }}</strong> invoice secara permanen.
                        Invoice, item, dan pembayaran terkait akan dihapus dan tidak dapat dikembalikan.
                    </p>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('showBulkDeleteModal', false)" color="gray" class="w-full sm:w-auto">
                    Batal
                </x-button>
                <x-button wire:click="bulkDelete" color="red" icon="trash" loading="bulkDelete"
                    class="w-full sm:w-auto">
                    Hapus Semua Invoice
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Livewire Components --}}
    <livewire:invoices.show />
    <livewire:invoices.create />
    <livewire:invoices.delete />
    <livewire:payments.create />
    <livewire:payments.edit />
</div>

<script>
    // Print function with preview + auto download
    function printInvoice(invoiceId) {
        // Open preview in new tab
        window.open(`/invoice/${invoiceId}/preview`, '_blank');

        // Auto download after small delay
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = `/invoice/${invoiceId}/download`;
            link.download = `Invoice-${invoiceId}.pdf`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 500);
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('start-bulk-download', (data) => {
            const {
                urls,
                delay
            } = data[0];
            let currentIndex = 0;

            function downloadNext() {
                if (currentIndex >= urls.length) return;

                const current = urls[currentIndex];
                const link = document.createElement('a');
                link.href = current.url;
                link.download = `Invoice-${current.invoice_number}.pdf`;
                link.style.display = 'none';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                currentIndex++;

                if (currentIndex < urls.length) {
                    setTimeout(downloadNext, delay || 1000);
                }
            }

            downloadNext();
        });
    });
</script>
