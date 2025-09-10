<x-modal wire title="Edit Template Recurring Invoice" size="6xl" center>
    @if ($template && count($items) > 0)
        <form id="edit-template-form" wire:submit="save" class="space-y-4">
            <!-- Template Status Alert -->
            @if ($templateData['status'] === 'inactive')
                <div
                    class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        <span class="text-sm text-amber-800 dark:text-amber-200">Template ini sedang tidak aktif</span>
                    </div>
                </div>
            @endif

            <!-- Template Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <x-input wire:model="templateData.template_name" label="Nama Template" required />
                <x-select.styled wire:model.live="templateData.client_id" :options="$this->clientOptions" label="Client Utama"
                    searchable required />
                <x-select.styled wire:model="templateData.frequency" :options="[
                    ['label' => 'Bulanan', 'value' => 'monthly'],
                    ['label' => 'Triwulan', 'value' => 'quarterly'],
                    ['label' => 'Semester', 'value' => 'semi_annual'],
                    ['label' => 'Tahunan', 'value' => 'annual'],
                ]" label="Frekuensi" required />
            </div>

            <!-- Date Range & Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <x-date wire:model.live="templateData.start_date" label="Mulai" required />
                <x-date wire:model.live="templateData.end_date" label="Berakhir" required />
                <x-select.styled wire:model="templateData.status" :options="[
                    ['label' => 'Aktif', 'value' => 'active'],
                    ['label' => 'Tidak Aktif', 'value' => 'inactive'],
                ]" label="Status" required />
            </div>

            <!-- Template Stats -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icon name="document-text" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        <span class="text-blue-800 dark:text-blue-200">
                            {{ $template->recurringInvoices->count() }} invoice telah dibuat
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                        <span class="text-green-800 dark:text-green-200">
                            {{ $template->recurringInvoices->where('status', 'published')->count() }} dipublish
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        <span class="text-amber-800 dark:text-amber-200">
                            {{ $template->recurringInvoices->where('status', 'draft')->count() }} draft
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <h3 class="font-medium text-gray-900 dark:text-zinc-100">Items</h3>
                    <div x-data="{ selected: @entangle('selectedItems').live }" x-show="selected.length > 0" class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="`${selected.length} terpilih`"></span>
                        <x-button wire:click="bulkDeleteItems" loading="bulkDeleteItems" color="red" size="xs"
                            outline icon="trash">
                            Hapus Terpilih
                        </x-button>
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach ($items as $index => $item)
                        <div
                            class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-600">
                            <!-- Mobile Layout -->
                            <div class="block lg:hidden space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model="selectedItems" value="{{ $index }}"
                                            class="rounded border-gray-300 dark:border-zinc-600">
                                        <div
                                            class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                        </div>
                                    </div>
                                    <x-button wire:click="removeItem({{ $index }})" color="red" size="xs"
                                        outline icon="trash" loading="removeItem({{ $index }})" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <x-select.styled wire:model="items.{{ $index }}.client_id" :options="$this->clientOptions"
                                        searchable placeholder="Client" />
                                    <x-select.styled wire:model="items.{{ $index }}.service_id" :options="$this->serviceOptions"
                                        searchable placeholder="Layanan..." />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <x-input wire:model="items.{{ $index }}.service_name" placeholder="Custom" />
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
                            <div class="hidden lg:flex gap-2 items-center">
                                <div class="w-8 flex justify-center">
                                    <input type="checkbox" wire:model="selectedItems" value="{{ $index }}"
                                        class="rounded border-gray-300 dark:border-zinc-600">
                                </div>
                                <div class="w-8 flex justify-center">
                                    <div
                                        class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                        <span
                                            class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                    </div>
                                </div>

                                <div class="flex-1 w-20">
                                    <x-select.styled wire:model="items.{{ $index }}.client_id"
                                        :options="$this->clientOptions" searchable placeholder="Client" />
                                </div>

                                <div class="flex-1 space-y-1 w-20">
                                    <x-select.styled wire:model="items.{{ $index }}.service_id"
                                        :options="$this->serviceOptions" searchable placeholder="Layanan..."
                                        x-on:select="$wire.fillServiceData({{ $index }})" />
                                    <x-input wire:model="items.{{ $index }}.service_name"
                                        placeholder="Custom" />
                                </div>

                                <div class="w-20">
                                    <x-input wire:model.blur="items.{{ $index }}.quantity" type="number"
                                        min="1" placeholder="1" />
                                </div>

                                <div class="flex-1 space-y-1">
                                    <x-input wire:model.blur="items.{{ $index }}.unit_price" prefix="Rp"
                                        x-mask:dynamic="$money($input, ',')" placeholder="Harga" />
                                    <x-input wire:model.blur="items.{{ $index }}.cogs_amount" prefix="Rp"
                                        x-mask:dynamic="$money($input, ',')" placeholder="COGS" />
                                </div>

                                <div class="w-24 flex justify-center">
                                    <x-badge text="Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}"
                                        color="primary" light />
                                </div>

                                <div class="w-16 flex justify-center">
                                    <x-button wire:click="removeItem({{ $index }})" color="red"
                                        size="xs" outline icon="trash"
                                        loading="removeItem({{ $index }})" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Add Items Controls -->
                <div class="flex justify-end items-center gap-2">
                    <x-input wire:model="itemsToAdd" type="number" min="1" placeholder="1"
                        class="w-fit" />
                    <x-button wire:click="addMultipleItems" color="primary" size="xs" icon="plus"
                        loading="addMultipleItems">
                        Tambah Item
                    </x-button>
                </div>
            </div>

            <!-- Discount Section -->
            <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model.live="hasDiscount" id="hasDiscount"
                        class="rounded border-gray-300 dark:border-zinc-600">
                    <label for="hasDiscount" class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                        Diskon
                    </label>

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

            <!-- Summary -->
            <div
                class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-primary-200 dark:border-primary-700">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="check-circle" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                            <x-badge text="{{ $this->estimatedInvoices }} Invoice" color="primary" />
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="calendar" class="w-4 h-4 text-gray-500 dark:text-zinc-400" />
                            <span class="text-sm text-gray-600 dark:text-zinc-400 font-medium">
                                {{ \Carbon\Carbon::parse($templateData['start_date'])->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($templateData['end_date'])->format('d M Y') }}
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
                <x-button type="submit" form="edit-template-form" color="primary" loading="save" icon="check"
                    size="sm">
                    Perbarui Template
                </x-button>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
