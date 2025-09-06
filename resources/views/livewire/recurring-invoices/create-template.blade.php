<div>
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary" size="sm">
        Buat Template
    </x-button>

    <x-modal wire title="Buat Template Recurring Invoice" size="6xl" center>
        <form id="template-form" wire:submit="save" class="space-y-4">
            <!-- Template Basic Info -->
            <div class="grid grid-cols-3 gap-3">
                <x-input wire:model="template.template_name" label="Nama Template" placeholder="Template name"
                    required />

                <x-select.styled wire:model="template.client_id" :options="$this->clientOptions" label="Client Utama" searchable
                    placeholder="Pilih client" required />

                <x-select.styled wire:model="template.frequency" :options="[
                    ['label' => 'Bulanan', 'value' => 'monthly'],
                    ['label' => 'Triwulan', 'value' => 'quarterly'],
                    ['label' => 'Semester', 'value' => 'semi_annual'],
                    ['label' => 'Tahunan', 'value' => 'annual'],
                ]" label="Frekuensi" required />
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-3">
                <x-date wire:model.live="template.start_date" label="Mulai" required />
                <x-date wire:model.live="template.end_date" label="Berakhir" required />
            </div>

            <!-- Items Section -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <h3 class="font-medium text-gray-900 dark:text-zinc-100">Items</h3>
                    <!-- Bulk Delete for selected items -->
                    <div x-data="{ selected: @entangle('selectedItems').live }" x-show="selected.length > 0" class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="`${selected.length} terpilih`"></span>
                        <x-button wire:click="bulkDeleteItems" color="red" size="xs" outline icon="trash">
                            Hapus Terpilih
                        </x-button>
                    </div>
                </div>

                @if (count($items) > 0)
                    <div class="space-y-2">
                        @foreach ($items as $index => $item)
                            <div
                                class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-600">
                                <!-- Mobile Layout -->
                                <div class="block lg:hidden space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" wire:model="selectedItems"
                                                value="{{ $index }}"
                                                class="rounded border-gray-300 dark:border-zinc-600">
                                            <div
                                                class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                                <span
                                                    class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                            </div>
                                        </div>
                                        <x-button wire:click="removeItem({{ $index }})" color="red"
                                            size="xs" outline icon="trash" loading="removeItem"
                                            wire:target="removeItem({{ $index }})" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <x-select.styled wire:model="items.{{ $index }}.client_id"
                                            :options="$this->clientOptions" searchable placeholder="Client" />

                                        <x-select.styled wire:model="items.{{ $index }}.service_id"
                                            :options="$this->serviceOptions" searchable placeholder="Layanan..."
                                            x-on:select="$wire.fillServiceData({{ $index }})" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <x-input wire:model="items.{{ $index }}.service_name"
                                            placeholder="Custom" />
                                        <x-input wire:model="items.{{ $index }}.quantity" type="number"
                                            min="1" placeholder="Qty" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <x-input wire:model="items.{{ $index }}.unit_price" prefix="Rp"
                                            x-mask:dynamic="$money($input, ',')" placeholder="Harga" />
                                        <x-input wire:model="items.{{ $index }}.cogs_amount" prefix="Rp"
                                            x-mask:dynamic="$money($input, ',')" placeholder="COGS" />
                                    </div>

                                    <div class="text-center">
                                        <x-badge text="Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}"
                                            color="primary" light />
                                    </div>
                                </div>

                                <!-- Desktop Layout -->
                                <div class="hidden lg:grid grid-cols-25 gap-2 items-center">
                                    <!-- Checkbox -->
                                    <div class="col-span-1 flex justify-center">
                                        <input type="checkbox" wire:model="selectedItems" value="{{ $index }}"
                                            class="rounded border-gray-300 dark:border-zinc-600">
                                    </div>

                                    <!-- Number -->
                                    <div class="col-span-1 flex justify-center">
                                        <div
                                            class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                        </div>
                                    </div>

                                    <!-- Client -->
                                    <div class="col-span-5">
                                        <x-select.styled wire:model="items.{{ $index }}.client_id"
                                            :options="$this->clientOptions" searchable placeholder="Client" />
                                    </div>

                                    <!-- Service -->
                                    <div class="col-span-6">
                                        <x-select.styled wire:model="items.{{ $index }}.service_id"
                                            :options="$this->serviceOptions" searchable placeholder="Layanan..."
                                            x-on:select="$wire.fillServiceData({{ $index }})" />
                                        <x-input wire:model="items.{{ $index }}.service_name"
                                            placeholder="Custom" />
                                    </div>

                                    <!-- Qty -->
                                    <div class="col-span-2">
                                        <x-input wire:model.blur="items.{{ $index }}.quantity" type="number"
                                            min="1" placeholder="1" />
                                    </div>

                                    <!-- Price & COGS -->
                                    <div class="col-span-4 space-y-1">
                                        <x-input wire:model.blur="items.{{ $index }}.unit_price" prefix="Rp"
                                            x-mask:dynamic="$money($input, ',')" placeholder="Harga" />
                                        <x-input wire:model.blur="items.{{ $index }}.cogs_amount" prefix="Rp"
                                            x-mask:dynamic="$money($input, ',')" placeholder="COGS" />
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-span-4 flex justify-center">
                                        <x-badge text="Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}"
                                            color="primary" light />
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-span-2 flex justify-center">
                                        <x-button wire:click="removeItem({{ $index }})" color="red"
                                            size="xs" outline icon="trash" loading="removeItem"
                                            wire:target="removeItem({{ $index }})" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Items Controls -->
                    <div class="flex justify-end items-center gap-2">
                        <!-- Bulk Delete Button -->
                        <div x-data="{ selected: @entangle('selectedItems').live }" x-show="selected.length > 0">
                            <x-button wire:click="bulkDeleteItems" color="red" size="xs" outline
                                icon="trash" loading="bulkDeleteItems">
                                Hapus Terpilih (<span x-text="selected.length"></span>)
                            </x-button>
                        </div>

                        <x-input wire:model="itemsToAdd" type="number" min="1" placeholder="1"
                            class="w-16" />
                        <x-button wire:click="addMultipleItems" color="primary" size="xs" icon="plus"
                            loading="addMultipleItems">
                            Tambah Item
                        </x-button>
                    </div>
                @else
                    <div
                        class="text-center py-6 text-gray-500 dark:text-zinc-400 border border-dashed border-gray-300 dark:border-zinc-600 rounded-lg">
                        <p class="text-sm">Belum ada item</p>
                        <x-button wire:click="addItem" color="primary" size="xs" icon="plus"
                            loading="addItem" class="mt-2">
                            Tambah Item Pertama
                        </x-button>
                    </div>
                @endif
            </div>

            <!-- Discount (Compact) -->
            <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model.live="hasDiscount" id="hasDiscount"
                        class="rounded border-gray-300 dark:border-zinc-600">
                    <label for="hasDiscount" class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                        Diskon
                    </label>
                    <div wire:loading wire:target="hasDiscount" class="ml-1">
                        <svg class="animate-spin h-3 w-3 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    @if ($hasDiscount)
                        <div class="flex gap-2 flex-1">
                            <x-select.styled wire:model="discount.type" :options="[
                                ['label' => 'Tetap', 'value' => 'fixed'],
                                ['label' => '%', 'value' => 'percentage'],
                            ]" class="w-20" />

                            <x-input wire:model="discount.value" :prefix="$discount['type'] === 'percentage' ? '' : 'Rp'" :suffix="$discount['type'] === 'percentage' ? '%' : ''" placeholder="0"
                                class="w-24" />

                            <x-input wire:model="discount.reason" placeholder="Alasan diskon" class="flex-1" />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary (Compact) -->
            <div
                class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-primary-200 dark:border-primary-700">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <x-badge text="{{ $this->estimatedInvoices }} Invoice" color="primary" />
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4l6-6m0 0v6m0-6H8"></path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-zinc-400 font-medium">
                                {{ \Carbon\Carbon::parse($template['start_date'])->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($template['end_date'])->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-3 items-center">
                        @if ($hasDiscount)
                            <x-badge text="-Rp {{ number_format($this->discountAmount, 0, ',', '.') }}"
                                color="red" light />
                        @endif
                        <div class="text-right">
                            <div class="text-xs text-gray-500 dark:text-zinc-400">Total</div>
                            <div class="font-bold text-xl text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="gray" size="sm">
                    Batal
                </x-button>
                <x-button type="submit" form="template-form" color="primary" loading="save" icon="check"
                    size="sm">
                    Simpan Template
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
