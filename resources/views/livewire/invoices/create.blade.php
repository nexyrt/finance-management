<div>
    <x-modal wire="showModal" size="6xl" center id="invoice-create-modal" x-on:close="$wire.resetData()" persistent>
        <x-slot:header>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon name="document-plus" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Buat Invoice Baru</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Buat invoice dengan multiple items dan klien</p>
                </div>
            </div>
        </x-slot:header>

        {{-- Invoice Form --}}
        <div class="space-y-8">
            {{-- Invoice Header Info --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200/50 dark:border-blue-700/50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Invoice Number --}}
                    <div>
                        <x-input 
                            wire:model="invoice_number" 
                            label="Nomor Invoice *" 
                            icon="hashtag"
                            hint="Nomor invoice otomatis generate"
                        />
                    </div>
                    
                    {{-- Client Selection --}}
                    <div>
                        <x-select.styled 
                            wire:model.live="billed_to_id" 
                            label="Klien *"
                            :options="$this->clients"
                            placeholder="Pilih klien..."
                            searchable
                        />
                    </div>
                    
                    {{-- Status Display --}}
                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <div class="mt-1">
                                <div class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg w-full">
                                    <x-icon name="document" class="w-4 h-4" />
                                    <span class="font-medium">Draft</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <x-input 
                            wire:model.live="issue_date" 
                            label="Tanggal Invoice *" 
                            type="date"
                            icon="calendar"
                        />
                    </div>
                    
                    <div>
                        <x-input 
                            wire:model.live="due_date" 
                            label="Jatuh Tempo *" 
                            type="date"
                            icon="calendar-days"
                        />
                    </div>
                </div>
            </div>

            {{-- Invoice Items Section --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <x-icon name="list-bullet" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Item Invoice</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tambahkan layanan dan produk</p>
                        </div>
                    </div>
                    
                    <x-button wire:click="addItem" color="purple" icon="plus" size="sm">
                        Tambah Item
                    </x-button>
                </div>

                {{-- Items List --}}
                <div class="space-y-4">
                    @forelse($items as $index => $item)
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-300 font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">Item #{{ $index + 1 }}</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Layanan atau produk</p>
                                    </div>
                                </div>
                                
                                @if(count($items) > 1)
                                    <x-button 
                                        wire:click="removeItem({{ $item['id'] ?? $index }})" 
                                        color="red" 
                                        icon="trash" 
                                        size="sm" 
                                        outline
                                        class="opacity-70 hover:opacity-100"
                                    >
                                    </x-button>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                {{-- Client Selection --}}
                                <div class="md:col-span-2">
                                    <x-select.styled 
                                        wire:model.live="items.{{ $index }}.client_id" 
                                        label="Klien *"
                                        :options="$this->clients"
                                        placeholder="Pilih klien..."
                                        searchable
                                    />
                                </div>
                                
                                {{-- Service Selection with Quick Load --}}
                                <div class="md:col-span-2 space-y-2">
                                    <x-input 
                                        wire:model.live="items.{{ $index }}.service_name" 
                                        label="Nama Layanan *" 
                                        placeholder="Masukkan nama layanan"
                                    />
                                    <x-select.styled 
                                        x-on:select="$wire.loadService({{ $index }}, $event.detail.select.value)" 
                                        :options="$this->services"
                                        placeholder="Atau pilih dari template..."
                                        searchable
                                        class="text-xs"
                                    />
                                </div>
                                
                                {{-- Quantity --}}
                                <div>
                                    <x-input 
                                        wire:model.live="items.{{ $index }}.quantity" 
                                        label="Qty *" 
                                        type="number"
                                        min="1"
                                        step="1"
                                    />
                                </div>
                                
                                {{-- Unit Price --}}
                                <div>
                                    <x-wireui-currency 
                                        wire:model.live="items.{{ $index }}.unit_price" 
                                        label="Harga Satuan *" 
                                        placeholder="0"
                                        prefix="Rp"
                                        thousands="."
                                        decimal=","
                                        precision="0"
                                    />
                                </div>
                            </div>
                            
                            {{-- Item Amount Display --}}
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Item:</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <x-icon name="document-plus" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                            <p class="text-gray-500 dark:text-gray-400">Belum ada item invoice</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Discount Section --}}
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-6 border border-yellow-200/50 dark:border-yellow-700/50">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-8 w-8 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                        <x-icon name="receipt-percent" class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Diskon (Opsional)</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Berikan diskon untuk invoice ini</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Discount Type --}}
                    <div>
                        <x-select.styled 
                            wire:model.live="discount_type" 
                            label="Tipe Diskon"
                            :options="[
                                ['label' => '💰 Nominal Tetap', 'value' => 'fixed'],
                                ['label' => '📊 Persentase', 'value' => 'percentage'],
                            ]"
                        />
                    </div>
                    
                    {{-- Discount Value --}}
                    <div>
                        @if($discount_type === 'percentage')
                            <x-input 
                                wire:model.live="discount_value" 
                                label="Persentase Diskon" 
                                type="number"
                                min="0"
                                max="100"
                                step="0.1"
                                suffix="%"
                                placeholder="0"
                            />
                        @else
                            <x-wireui-currency 
                                wire:model.live="discount_value" 
                                label="Nominal Diskon" 
                                placeholder="0"
                                prefix="Rp"
                                thousands="."
                                decimal=","
                                precision="0"
                            />
                        @endif
                    </div>
                    
                    {{-- Discount Reason --}}
                    <div>
                        <x-input 
                            wire:model="discount_reason" 
                            label="Alasan Diskon" 
                            placeholder="Diskon khusus, promo, dll..."
                        />
                    </div>
                </div>
            </div>

            {{-- Invoice Summary --}}
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-800 dark:to-slate-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="calculator" class="w-5 h-5" />
                    Ringkasan Invoice
                </h4>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal ({{ count($items) }} item):</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    @if($discount_amount > 0)
                        <div class="flex justify-between items-center py-2 text-orange-600 dark:text-orange-400">
                            <span>
                                Diskon 
                                @if($discount_type === 'percentage')
                                    ({{ $discount_value }}%)
                                @else
                                    (Tetap)
                                @endif
                                :
                            </span>
                            <span class="font-medium">
                                -Rp {{ number_format($discount_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                    
                    <hr class="border-gray-300 dark:border-gray-600">
                    
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Total Invoice:</span>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                {{-- Quick Actions --}}
                <div class="flex items-center gap-2">
                    <x-button 
                        wire:click="generateInvoiceNumber"
                        color="secondary" 
                        size="sm" 
                        outline
                        icon="arrow-path"
                    >
                        Generate Ulang No.
                    </x-button>
                    
                    @if(count($items) < 5)
                        <x-button 
                            wire:click="addItem"
                            color="purple" 
                            size="sm" 
                            outline
                            icon="plus"
                        >
                            Tambah Item
                        </x-button>
                    @endif
                </div>

                {{-- Main Actions --}}
                <div class="flex items-center gap-3">
                    <x-button x-on:click="$modalClose('invoice-create-modal')" color="secondary">
                        Batal
                    </x-button>
                    
                    <x-button wire:click="save" color="blue" icon="check" spinner="save">
                        Simpan Invoice
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>