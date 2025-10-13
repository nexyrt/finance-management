<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Testing Page - Combined Income</h1>
        <p class="text-gray-600 dark:text-gray-400">Gabungan Payment & Bank Transaction (Income)</p>
    </div>

    {{-- Manual Search --}}
    <div class="mb-4">
        <x-input wire:model.live.debounce.500ms="search"
            placeholder="Cari berdasarkan referensi, deskripsi, atau nama klien..." icon="magnifying-glass" clearable />
    </div>

    <x-card>
        <x-table :$headers :rows="$this->rows" :$sort loading>
            @interact('column_source_type', $row)
                <x-badge :color="$row['source_type'] === 'payment' ? 'green' : 'blue'" :text="ucfirst($row['source_type'])" />
            @endinteract

            @interact('column_amount', $row)
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ $row['formatted_amount'] }}
                </span>
            @endinteract

            @interact('column_date', $row)
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $row['date_formatted'] }}
                </span>
            @endinteract

            @interact('column_action', $row)
                <x-button.circle icon="paper-clip" :color="$row['has_attachment'] ? 'blue' : 'gray'" size="sm"
                    wire:click="viewAttachment({{ $row['original_id'] }}, '{{ $row['source_type'] }}')" :disabled="!$row['has_attachment']"
                    title="{{ $row['has_attachment'] ? 'Lihat Attachment' : 'Tidak ada attachment' }}" />
            @endinteract
        </x-table>
    </x-card>

    <x-modal title="Attachment Detail" wire size="2xl">
        @if ($selectedItem)
            <div class="space-y-4">
                {{-- Info --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Referensi:</span>
                        <span class="font-semibold ml-2">{{ $selectedItem['reference'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Tanggal:</span>
                        <span class="font-semibold ml-2">{{ $selectedItem['date'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Jumlah:</span>
                        <span class="font-semibold ml-2">{{ $selectedItem['amount'] }}</span>
                    </div>
                </div>

                <hr class="dark:border-gray-700">

                {{-- Attachment --}}
                @if ($selectedItem['has_attachment'])
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            File: {{ $selectedItem['attachment_name'] }}
                        </p>

                        @if ($selectedItem['is_image'])
                            <img src="{{ $selectedItem['attachment_url'] }}" alt="Attachment"
                                class="max-w-full h-auto rounded-lg border">
                        @elseif($selectedItem['is_pdf'])
                            <embed src="{{ $selectedItem['attachment_url'] }}" type="application/pdf" width="100%"
                                height="600px" class="rounded-lg border">
                        @else
                            <a href="{{ $selectedItem['attachment_url'] }}" target="_blank"
                                class="text-blue-600 hover:underline">
                                Download File
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <x-icon name="document" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>Tidak ada attachment</p>
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
