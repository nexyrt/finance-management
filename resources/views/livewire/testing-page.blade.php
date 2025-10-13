<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Combined Income</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Gabungan Payment & Bank Transaction (Income)</p>
        </div>

        {{-- Summary Cards --}}
        <div class="flex gap-3">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="text-xs opacity-90 mb-1">Total Transaksi</div>
                <div class="text-2xl font-bold">{{ $this->rows->count() }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="text-xs opacity-90 mb-1">Total Income</div>
                <div class="text-2xl font-bold">Rp {{ number_format($this->rows->sum('amount'), 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <x-card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">Filter & Search</h3>
                <x-button wire:click="resetFilters" size="sm" color="gray" icon="arrow-path">
                    Reset
                </x-button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-3">
                    <x-input wire:model.live.debounce.500ms="search"
                        placeholder="Cari berdasarkan referensi, deskripsi, atau nama klien..." icon="magnifying-glass"
                        clearable />
                </div>

                {{-- Date Range --}}
                <div>
                    <x-date wire:model.live="dateFrom" label="Dari Tanggal" helpers />
                </div>
                <div>
                    <x-date wire:model.live="dateTo" label="Sampai Tanggal" helpers />
                </div>

                {{-- Type Filter --}}
                <div>
                    <x-select.styled wire:model.live="filterTypes" label="Tipe" :options="$this->typeOptions"
                        placeholder="Semua Tipe" searchable multiple />
                </div>

                {{-- Category Filter --}}
                <div>
                    <x-select.styled wire:model.live="filterCategories" label="Kategori" :options="$this->categoryOptions"
                        placeholder="Semua Kategori" searchable multiple />
                </div>

                {{-- Bank Account Filter --}}
                <div class="lg:col-span-2">
                    <x-select.styled wire:model.live="filterBankAccounts" label="Rekening Bank" :options="$this->bankAccountOptions"
                        placeholder="Semua Rekening" searchable multiple />
                </div>
            </div>
        </div>
    </x-card>

    {{-- Table --}}
    <x-card>
        <x-table :$headers :rows="$this->rows" :$sort loading striped>
            @interact('column_date', $row)
                <div class="flex items-center gap-2">
                    <x-icon name="calendar" class="w-4 h-4 text-gray-400" />
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $row['date_formatted'] }}
                    </span>
                </div>
            @endinteract

            @interact('column_source_type', $row)
                <x-badge :color="$row['source_type'] === 'payment' ? 'green' : 'blue'" :text="ucfirst($row['source_type'])" light />
            @endinteract

            @interact('column_reference', $row)
                <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                    {{ $row['reference'] }}
                </span>
            @endinteract

            @interact('column_description', $row)
                <div class="max-w-xs">
                    <p class="text-sm text-gray-900 dark:text-white truncate" title="{{ $row['description'] }}">
                        {{ $row['description'] }}
                    </p>
                </div>
            @endinteract

            @interact('column_client_name', $row)
                <div class="flex items-center gap-2">
                    @if ($row['client_name'] !== '-')
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ substr($row['client_name'], 0, 2) }}
                        </div>
                    @endif
                    <span
                        class="text-sm {{ $row['client_name'] === '-' ? 'text-gray-400 italic' : 'text-gray-900 dark:text-white' }}">
                        {{ $row['client_name'] }}
                    </span>
                </div>
            @endinteract

            @interact('column_bank_account', $row)
                <div class="flex items-center gap-2">
                    <x-icon name="building-library" class="w-4 h-4 text-gray-400" />
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ $row['bank_account'] }}
                    </span>
                </div>
            @endinteract

            @interact('column_category', $row)
                <x-badge :color="$row['category'] === 'Payment' ? 'emerald' : 'cyan'" :text="$row['category']" outline />
            @endinteract

            @interact('column_amount', $row)
                <div class="flex items-center gap-2">
                    <x-icon name="banknotes" class="w-4 h-4 text-green-500" />
                    <span class="font-bold text-green-600 dark:text-green-400">
                        {{ $row['formatted_amount'] }}
                    </span>
                </div>
            @endinteract

            @interact('column_action', $row)
                <x-button.circle icon="paper-clip" :color="$row['has_attachment'] ? 'blue' : 'gray'" size="sm"
                    wire:click="viewAttachment({{ $row['original_id'] }}, '{{ $row['source_type'] }}')" :disabled="!$row['has_attachment']"
                    title="{{ $row['has_attachment'] ? 'Lihat Attachment' : 'Tidak ada attachment' }}" />
            @endinteract
        </x-table>

        {{-- Empty State --}}
        @if ($this->rows->isEmpty())
            <div class="text-center py-12">
                <x-icon name="inbox" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tidak ada data</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Tidak ada transaksi yang sesuai dengan filter Anda</p>
                <x-button wire:click="resetFilters" color="blue" size="sm">
                    Reset Filter
                </x-button>
            </div>
        @endif
    </x-card>

    {{-- Attachment Modal --}}
    <x-modal title="Detail Attachment" wire size="3xl">
        @if ($selectedItem)
            <div class="space-y-4">
                {{-- Info Header --}}
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block mb-1">Referensi</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white">{{ $selectedItem['reference'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block mb-1">Tanggal</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white">{{ $selectedItem['date'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block mb-1">Jumlah</span>
                            <span
                                class="font-semibold text-green-600 dark:text-green-400">{{ $selectedItem['amount'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Attachment Preview --}}
                @if ($selectedItem['has_attachment'])
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                📎 {{ $selectedItem['attachment_name'] }}
                            </p>
                            <a href="{{ $selectedItem['attachment_url'] }}" target="_blank"
                                class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Download
                            </a>
                        </div>

                        @if ($selectedItem['is_image'])
                            <img src="{{ $selectedItem['attachment_url'] }}" alt="Attachment"
                                class="max-w-full h-auto rounded-lg shadow-lg">
                        @elseif($selectedItem['is_pdf'])
                            <embed src="{{ $selectedItem['attachment_url'] }}" type="application/pdf" width="100%"
                                height="600px" class="rounded-lg">
                        @else
                            <div class="text-center py-8">
                                <x-icon name="document" class="w-16 h-16 mx-auto text-gray-400 mb-3" />
                                <p class="text-gray-500">Preview tidak tersedia untuk tipe file ini</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div
                        class="text-center py-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                        <x-icon name="document" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada attachment</p>
                    </div>
                @endif
            </div>

            <x-slot:footer>
                <x-button color="gray" wire:click="$set('modal', false)">
                    Tutup
                </x-button>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
