<div class="space-y-6">
    {{-- Filters Section --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 lg:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Date Range --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <x-date wire:model.live="dateRange" label="Periode" range placeholder="Pilih range tanggal..." />
            </div>

            {{-- Client Filter (Multiple) --}}
            <div class="lg:col-span-2">
                <x-select.styled wire:model.live="clientFilters" label="Klien" :options="$this->clients"
                    placeholder="Semua klien..." multiple searchable />
            </div>

            {{-- Category Filter (Multiple) --}}
            <div class="lg:col-span-1">
                <x-select.styled wire:model.live="categoryFilters" label="Kategori" :options="$this->incomeCategories"
                    placeholder="Semua kategori..." multiple searchable />
            </div>

            {{-- Search --}}
            <div class="lg:col-span-1">
                <x-input wire:model.live.debounce.300ms="search" label="Cari" placeholder="Cari data..."
                    icon="magnifying-glass" />
            </div>
        </div>

        {{-- Active Filters Indicator --}}
        @php
            $activeFilters = collect([
                !empty($dateRange) && count($dateRange) >= 1,
                !empty($clientFilters),
                !empty($categoryFilters),
                $search,
            ])
                ->filter()
                ->count();
        @endphp

        @if ($activeFilters > 0)
            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-dark-600">
                <div class="flex items-center justify-between">
                    <x-badge text="{{ $activeFilters }} filter aktif" color="primary" size="sm" />
                    <div class="text-sm text-dark-500 dark:text-dark-400">
                        Menampilkan {{ $this->incomeData->count() }} dari {{ $this->incomeData->total() }} pemasukan
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Income Table --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
        {{-- Table Header --}}
        <div class="px-4 lg:px-6 py-4 border-b border-zinc-200 dark:border-dark-600">
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Daftar Pemasukan</h3>
            <p class="text-sm text-dark-600 dark:text-dark-400">Gabungan pembayaran invoice dan transaksi langsung</p>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-dark-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-dark-600 dark:text-dark-400 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-dark-600 dark:text-dark-400 uppercase tracking-wider">
                            Sumber
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-dark-600 dark:text-dark-400 uppercase tracking-wider">
                            Klien/Deskripsi
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-dark-600 dark:text-dark-400 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-dark-600 dark:text-dark-400 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th
                            class="px-4 py-3 text-center text-xs font-medium text-dark-600 dark:text-dark-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-dark-600">
                    @forelse($this->incomeData as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                            {{-- Date --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">
                                    {{ \Carbon\Carbon::parse($item->date)->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Source Type --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if ($item->source_type === 'payment')
                                    <x-badge text="Payment" color="blue" icon="document-text" size="sm" />
                                    @if ($item->invoice_number)
                                        <div class="text-xs text-dark-500 dark:text-dark-400 font-mono mt-1">
                                            {{ $item->invoice_number }}
                                        </div>
                                    @endif
                                @else
                                    <x-badge text="Direct Income" color="green" icon="arrow-trending-up"
                                        size="sm" />
                                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                                        {{ $item->bank_name }}
                                    </div>
                                @endif
                            </td>

                            {{-- Client/Description --}}
                            <td class="px-4 py-4">
                                <div class="max-w-xs">
                                    @if ($item->source_type === 'payment')
                                        <div class="font-medium text-dark-900 dark:text-dark-50">
                                            {{ $item->client_name }}
                                        </div>
                                        @if ($item->reference_number)
                                            <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                                                Ref: {{ $item->reference_number }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-sm text-dark-900 dark:text-dark-50">
                                            {{ $item->description ?? '-' }}
                                        </div>
                                        @if ($item->reference_number)
                                            <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                                                Ref: {{ $item->reference_number }}
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if ($item->category_label)
                                    <x-badge text="{{ $item->category_label }}" color="purple" outline
                                        size="sm" />
                                @else
                                    <span class="text-xs text-dark-400 dark:text-dark-500">-</span>
                                @endif
                            </td>

                            {{-- Amount --}}
                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- View Attachment --}}
                                    @if ($item->attachment_path)
                                        <x-button.circle icon="paper-clip" color="blue" size="sm"
                                            wire:click="viewAttachment('{{ $item->source_type }}', {{ $item->id }})"
                                            title="Lihat Lampiran" />
                                    @endif

                                    {{-- Edit (Payment only) --}}
                                    @if ($item->source_type === 'payment')
                                        <x-button.circle icon="pencil" color="green" size="sm"
                                            wire:click="editPayment({{ $item->id }})" title="Edit Payment" />
                                    @endif

                                    {{-- Delete --}}
                                    <x-button.circle icon="trash" color="red" size="sm"
                                        wire:click="deleteItem('{{ $item->source_type }}', {{ $item->id }})"
                                        title="Hapus" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="h-16 w-16 bg-dark-100 dark:bg-dark-700 rounded-full flex items-center justify-center mb-4">
                                        <x-icon name="inbox" class="w-8 h-8 text-dark-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-dark-900 dark:text-dark-50 mb-2">
                                        Tidak ada data pemasukan
                                    </h3>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">
                                        @if ($activeFilters > 0)
                                            Coba ubah filter untuk melihat data lain
                                        @else
                                            Belum ada pembayaran atau transaksi pemasukan
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->incomeData->hasPages())
            <div class="px-4 lg:px-6 py-4 border-t border-zinc-200 dark:border-dark-600">
                {{ $this->incomeData->links() }}
            </div>
        @endif
    </div>

    {{-- Child Components --}}
    <livewire:cash-flow.attachment-viewer />
    <livewire:payments.edit @payment-updated="$refresh" />
    <livewire:payments.delete @payment-deleted="$refresh" />
    <livewire:transactions.delete @transaction-deleted="$refresh" />
</div>
