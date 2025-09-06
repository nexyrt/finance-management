<div>
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary">
        Buat Template
    </x-button>

    <x-modal wire title="Buat Template Recurring Invoice" size="7xl">
        <form id="template-form" wire:submit="save" class="space-y-6">
            <!-- Template Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model="template.template_name" label="Nama Template"
                    placeholder="Misal: Retainer Bulanan PT ABC" required />

                <x-select.styled wire:model="template.client_id" :options="$this->clientOptions" label="Client Utama" searchable
                    placeholder="Pilih client" required />
            </div>

            <!-- Schedule Configuration -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-date wire:model="template.start_date" label="Tanggal Mulai" required />

                <x-date wire:model="template.end_date" label="Tanggal Berakhir" required />

                <x-select.styled wire:model="template.frequency" :options="[
                    ['label' => 'Bulanan', 'value' => 'monthly'],
                    ['label' => 'Triwulan', 'value' => 'quarterly'],
                    ['label' => 'Semester', 'value' => 'semi_annual'],
                    ['label' => 'Tahunan', 'value' => 'annual'],
                ]" label="Frekuensi" required />
            </div>

            <!-- Invoice Items -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Items Template</h3>

                @if (count($items) > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-2xl">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layanan
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">COGS
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <x-select.styled wire:model="items.{{ $index }}.client_id"
                                            :options="$this->clientOptions" searchable placeholder="Pilih client" />
                                    </td>
                                    <td class="px-3 py-4">
                                        <div class="space-y-2">
                                            <x-select.styled wire:model="items.{{ $index }}.service_id"
                                                :options="$this->serviceOptions" searchable placeholder="Cari layanan..."
                                                x-on:select="$wire.fillServiceData({{ $index }})" />
                                            <x-input wire:model="items.{{ $index }}.service_name"
                                                placeholder="Atau input manual" class="text-sm" />
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <x-input wire:model.blur="items.{{ $index }}.quantity" type="number"
                                            min="1" class="w-20" />
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <x-input wire:model.blur="items.{{ $index }}.unit_price"
                                            x-mask:dynamic="$money($input, ',')" placeholder="0" />
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <x-input wire:model.blur="items.{{ $index }}.cogs_amount"
                                            x-mask:dynamic="$money($input, ',')" placeholder="0" />
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="font-medium">
                                            Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        <x-button wire:click="removeItem({{ $index }})" color="red"
                                            size="sm" outline icon="trash" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-8 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                        <p>Belum ada item. Klik "Tambah Item" untuk mulai.</p>
                    </div>
                @endif

                <div class="flex justify-center">
                    <x-button wire:click="addItem" color="primary" size="sm" icon="plus" loading="addItem">
                        Tambah Item
                    </x-button>
                </div>
            </div>

            <!-- Discount Configuration -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.live="hasDiscount" id="hasDiscount"
                        class="rounded border-gray-300">
                    <label for="hasDiscount" class="text-sm font-medium">Tambah Diskon</label>
                </div>

                @if ($hasDiscount)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-select.styled wire:model="discount.type" :options="[
                            ['label' => 'Nominal Tetap', 'value' => 'fixed'],
                            ['label' => 'Persentase', 'value' => 'percentage'],
                        ]" label="Tipe Diskon" />

                        <x-currency wire:model="discount.value"
                            label="{{ $discount['type'] === 'percentage' ? 'Persentase (%)' : 'Nominal Diskon' }}"
                            :prefix="$discount['type'] === 'percentage' ? '' : 'Rp'" :suffix="$discount['type'] === 'percentage' ? '%' : ''" />

                        <x-input wire:model="discount.reason" label="Alasan Diskon"
                            placeholder="Misal: Diskon loyalitas" />
                    </div>
                @endif
            </div>

            <!-- Summary -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Ringkasan Template</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($hasDiscount)
                        <div class="flex justify-between text-red-600">
                            <span>Diskon:</span>
                            <span>- Rp {{ number_format($this->discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span>Rp {{ number_format($this->totalAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-4">
                        <p>Akan generate {{ $this->estimatedInvoices }} invoice</p>
                        <p>Periode: {{ $template['start_date'] }} - {{ $template['end_date'] }}</p>
                    </div>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="gray">
                    Batal
                </x-button>
                <x-button type="submit" form="template-form" color="primary" loading="save" icon="check">
                    Simpan Template
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
