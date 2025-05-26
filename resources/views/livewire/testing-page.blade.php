<section class="w-full p-6 space-y-6">
    <div class="overflow-x-auto rounded-lg shadow-lg">
        <table class="w-full border-collapse text-left text-sm bg-zinc-900 dark:bg-zinc-900">
            <thead class="bg-zinc-800 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">No. Faktur</th>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">Pelanggan</th>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">Total Faktur</th>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">Telah Dibayar</th>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">Tenggat</th>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">Status</th>
                    <th scope="col" class="px-6 py-4 font-medium text-white dark:text-white">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700 dark:divide-zinc-700 border-t border-zinc-700 dark:border-zinc-700">
                @forelse ($invoices as $invoice)
                    <tr x-data="{ hover: false }" x-on:mouseenter="hover = true" x-on:mouseleave="hover = false"
                        x-bind:class="{ 'bg-zinc-800/50': hover }"
                        class="text-gray-300 dark:text-gray-300 transition-all duration-200 ease-in-out">
                        <td class="px-6 py-4 font-medium">
                            {{ $invoice->invoice_number ?? 'FAK-' . str_pad($invoice->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span>{{ $invoice->client->name ?? ($invoice->customer_name ?? 'Tidak diketahui') }}</span>
                                @if (isset($invoice->client) && isset($invoice->client->type))
                                    <span
                                        class="text-xs text-zinc-500">{{ $invoice->client->type === 'company' ? 'Perusahaan' : 'Individu' }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            Rp{{ number_format($invoice->total_amount ?? ($invoice->total ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $totalAmount = $invoice->total_amount ?? ($invoice->total ?? 0);
                                $amountPaid = $invoice->amount_paid ?? 0;
                                $percentage = $totalAmount > 0 ? min(100, ($amountPaid / $totalAmount) * 100) : 0;
                            @endphp
                            <div class="w-full bg-zinc-700 rounded-full h-2.5 mb-1">
                                <div class="h-2.5 rounded-full bg-blue-600 transition-all duration-500 ease-out"
                                    style="width: {{ $percentage }}%">
                                </div>
                            </div>
                            <span class="text-xs">Rp{{ number_format($amountPaid, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if (isset($invoice->due_date))
                                <span class="{{ $invoice->due_date < now() ? 'text-red-400' : '' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show"
                                class="px-3 py-1 text-xs font-medium rounded-full inline-flex items-center gap-1"
                                :class="{
                                    'bg-green-900/70 text-green-300 border border-green-700': '{{ $invoice->status ?? '' }}'
                                    === 'paid',
                                    'bg-yellow-900/70 text-yellow-300 border border-yellow-700': '{{ $invoice->status ?? '' }}'
                                    === 'partially_paid',
                                    'bg-blue-900/70 text-blue-300 border border-blue-700': '{{ $invoice->status ?? '' }}'
                                    === 'pending' || '{{ $invoice->status ?? '' }}'
                                    === 'Pending',
                                    'bg-red-900/70 text-red-300 border border-red-700': '{{ $invoice->status ?? '' }}'
                                    === 'overdue' || '{{ $invoice->status ?? '' }}'
                                    === 'Overdue',
                                }">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                        :class="{
                                            'bg-green-400': '{{ $invoice->status ?? '' }}'
                                            === 'paid',
                                            'bg-yellow-400': '{{ $invoice->status ?? '' }}'
                                            === 'partially_paid',
                                            'bg-blue-400': '{{ $invoice->status ?? '' }}'
                                            === 'pending' || '{{ $invoice->status ?? '' }}'
                                            === 'Pending',
                                            'bg-red-400': '{{ $invoice->status ?? '' }}'
                                            === 'overdue' || '{{ $invoice->status ?? '' }}'
                                            === 'Overdue',
                                        }"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2"
                                        :class="{
                                            'bg-green-500': '{{ $invoice->status ?? '' }}'
                                            === 'paid',
                                            'bg-yellow-500': '{{ $invoice->status ?? '' }}'
                                            === 'partially_paid',
                                            'bg-blue-500': '{{ $invoice->status ?? '' }}'
                                            === 'pending' || '{{ $invoice->status ?? '' }}'
                                            === 'Pending',
                                            'bg-red-500': '{{ $invoice->status ?? '' }}'
                                            === 'overdue' || '{{ $invoice->status ?? '' }}'
                                            === 'Overdue',
                                        }"></span>
                                </span>
                                @php
                                    $status = $invoice->status ?? '';
                                    $statusText = match (strtolower($status)) {
                                        'paid' => 'Lunas',
                                        'partially_paid' => 'Bayar Sebagian',
                                        'pending' => 'Tertunda',
                                        'overdue' => 'Terlambat',
                                        default => 'Tertunda',
                                    };
                                @endphp
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <flux:modal.trigger name="invoice-detail">
                                    <flux:button wire:click='getInvoiceItems({{ $invoice->id }})' variant="outline"
                                        size="xs" icon="eye">Detail</flux:button>
                                </flux:modal.trigger>
                                <flux:button variant="outline" size="xs" icon="pencil-square">Edit</flux:button>
                                @if (($invoice->status ?? '') !== 'paid')
                                    <div x-cloak>
                                        <flux:button variant="primary" size="xs" icon="currency-dollar">Bayar
                                        </flux:button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-10 h-10 opacity-30" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="mt-1 font-medium">Belum ada faktur</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal name="invoice-detail" class="md:max-w-3xl">
        <div wire:loading wire:target="getInvoiceItems">Loading...</div>

        <div class="space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detail Faktur</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Berikut adalah rincian dari faktur yang dipilih.</p>
            
            @if($selectedInvoice)
                <div class="space-y-4">
                    <!-- Informasi Faktur -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">No. Faktur</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedInvoice->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Faktur</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedInvoice->issue_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $selectedInvoice->due_date->format('d M Y') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Faktur</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">
                            Rp{{ number_format($selectedInvoice->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Dibayar</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">
                            Rp{{ number_format($selectedInvoice->amount_paid, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sisa Pembayaran</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">
                            Rp{{ number_format($selectedInvoice->total_amount - $selectedInvoice->amount_paid, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Syarat Pembayaran</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $selectedInvoice->payment_terms ?? 'Pembayaran penuh' }}
                        </span>
                    </div>

                    <!-- Informasi Pelanggan -->
                    <div class="mt-6">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">Informasi Pelanggan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</span>
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $selectedInvoice->client->name ?? 'Tidak diketahui' }}
                                </div>
                            </div>
                            @if (isset($selectedInvoice->client) && isset($selectedInvoice->client->type))
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</span>
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $selectedInvoice->client->type === 'company' ? 'Perusahaan' : 'Individu' }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($selectedInvoice->client) && isset($selectedInvoice->client->email))
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $selectedInvoice->client->email }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($selectedInvoice->client) && isset($selectedInvoice->client->phone))
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Telepon</span>
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $selectedInvoice->client->phone }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($selectedInvoice->client) && isset($selectedInvoice->client->tax_id))
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">NPWP</span>
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $selectedInvoice->client->tax_id }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($selectedInvoice->client) && isset($selectedInvoice->client->address))
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</span>
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $selectedInvoice->client->address }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Item Faktur -->
                    <div class="mt-6">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">Item Faktur</h3>
                        <table class="w-full mt-2 text-sm text-gray-700 dark:text-gray-300">
                            <!-- Table header -->
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Deskripsi</th>
                                    <th class="px-4 py-2 text-right">Jumlah</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoiceItems as $item)
                                <tr>
                                    <td class="px-4 py-2">Layanan</td>
                                    <td class="px-4 py-2 text-right">1</td>
                                    <td class="px-4 py-2 text-right">Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if (count($invoicePayments) > 0)
                        <!-- Riwayat Pembayaran -->
                        <div class="mt-6">
                            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">Riwayat Pembayaran</h3>
                            <div class="overflow-x-auto rounded-lg">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs uppercase bg-zinc-700/50 text-gray-400 dark:text-gray-300">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 rounded-l-lg">Tanggal</th>
                                            <th scope="col" class="px-4 py-3">Metode</th>
                                            <th scope="col" class="px-4 py-3">Referensi</th>
                                            <th scope="col" class="px-4 py-3 text-right rounded-r-lg">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoicePayments as $payment)
                                            <tr class="border-b border-zinc-700/30">
                                                <td class="px-4 py-3">{{ $payment->payment_date->format('d M Y') }}</td>
                                                <td class="px-4 py-3">{{ $payment->payment_method }}</td>
                                                <td class="px-4 py-3">{{ $payment->reference_number ?? '-' }}</td>
                                                <td class="px-4 py-3 text-right font-medium">
                                                    Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="py-4 text-center text-gray-500">
                    Data faktur tidak ditemukan
                </div>
            @endif
        </div>

        <div wire:loading.remove wire:target="getInvoiceItems" class="mt-6 flex justify-between">
            <flux:modal.close>
                <flux:button variant="outline" size="sm">Kembali</flux:button>
            </flux:modal.close>
            @if ($selectedInvoice)
                @if ($selectedInvoice->status !== 'paid')
                    <flux:button variant="primary" size="sm" icon="currency-dollar">
                        Bayar Faktur
                    </flux:button>
                @endif
                <flux:button variant="outline" size="sm" icon="printer">Cetak Faktur</flux:button>
            @endif
        </div>
    </flux:modal>
</section>
