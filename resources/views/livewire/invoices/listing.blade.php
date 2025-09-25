<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
            <div>
                <x-select.styled wire:model.live="statusFilter" label="Status" :options="[
                    ['label' => 'Draft', 'value' => 'draft'],
                    ['label' => 'Terkirim', 'value' => 'sent'],
                    ['label' => 'Dibayar', 'value' => 'paid'],
                    ['label' => 'Sebagian', 'value' => 'partially_paid'],
                    ['label' => 'Terlambat', 'value' => 'overdue'],
                ]"
                    placeholder="Semua status..." />
            </div>

            <div>
                <x-select.styled wire:model.live="clientFilter" label="Klien" :options="$this->clients
                    ->map(
                        fn($client) => [
                            'label' => $client->name,
                            'value' => $client->id,
                        ],
                    )
                    ->toArray()"
                    placeholder="Semua klien..." searchable />
            </div>

            <div>
                <x-date month-year-only wire:model.live="selectedMonth" label="Bulan" placeholder="Pilih bulan..." />
            </div>

            <div>
                <x-date wire:model.live="dateRange" label="Range Tanggal" range placeholder="Pilih range..." />
            </div>
        </div>

        <div class="flex gap-2">
            @if ($statusFilter || $clientFilter || !empty($dateRange) || $selectedMonth)
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

    {{-- Table --}}
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
                <x-button.circle icon="eye" color="blue" size="sm" wire:click="showInvoice({{ $row->id }})"
                    loading="showInvoice({{ $row->id }})" title="Lihat Detail" />

                <x-button.circle icon="pencil" color="green" size="sm" href="{{ route('invoices.edit', $row->id) }}"
                    wire:navigate title="Edit" />

                @if ($row->status === 'draft')
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click='sendInvoice({{ $row->id }})' loading="sendInvoice({{ $row->id }})"
                        title="Kirim" />
                @endif

                @if ($row->status === 'sent')
                    <x-button.circle icon="arrow-uturn-left" color="orange" size="sm"
                        wire:click='rollbackTodraft({{ $row->id }})' loading="rollbackToraft({{ $row->id }})"
                        title="Kembali ke Draft" />
                @endif

                @if (in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                    <x-button.circle icon="currency-dollar" color="yellow" size="sm"
                        wire:click="recordPayment({{ $row->id }})" loading="recordPayment({{ $row->id }})"
                        title="Catat Pembayaran" />
                @endif

                <x-button.circle icon="printer" color="gray" size="sm" onclick="printInvoice({{ $row->id }})"
                    title="Print" />

                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deleteInvoice({{ $row->id }})" loading="deleteInvoice({{ $row->id }})" />
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
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="bulkDelete" class="whitespace-nowrap">
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
</div>

<script>
    function printInvoice(invoiceId) {
        window.open(`/invoice/${invoiceId}/preview`, '_blank');
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
                downloads,
                delay
            } = data[0];
            let currentIndex = 0;

            function downloadNext() {
                if (currentIndex >= downloads.length) return;

                const current = downloads[currentIndex];
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = current.url;

                iframe.onload = () => {
                    setTimeout(() => document.body.removeChild(iframe), 1000);
                };

                document.body.appendChild(iframe);
                currentIndex++;

                if (currentIndex < downloads.length) {
                    setTimeout(downloadNext, delay || 2000);
                }
            }

            downloadNext();
        });
    });
</script>
