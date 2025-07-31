<div>
    <x-modal wire="showModal" size="6xl" center id="invoice-edit-modal" x-on:close="$wire.closeModal()" persistent>
        <x-slot:header>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon name="pencil" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Invoice</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Edit invoice dengan fleksibilitas penuh</p>
                </div>
            </div>
        </x-slot:header>

        @if($invoice)
            {{-- ✅ STATUS CHANGE PREVIEW ALERT --}}
            @if($statusWillChange)
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl p-4 border border-yellow-200/70 dark:border-yellow-700/50 mb-6 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 bg-yellow-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <x-icon name="arrow-path" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-1">
                                Perubahan Status Otomatis
                            </h4>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                                {{ $statusChangeMessage }}
                            </p>
                            
                            {{-- Status Comparison --}}
                            <div class="flex items-center gap-4 bg-yellow-100 dark:bg-yellow-800/30 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Status Saat Ini:</span>
                                    <x-badge text="{{ ucfirst($currentStatus) }}" color="gray" />
                                </div>
                                <x-icon name="arrow-right" class="w-4 h-4 text-yellow-600" />
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Status Baru:</span>
                                    @php
                                        $statusColors = [
                                            'draft' => 'gray',
                                            'sent' => 'blue', 
                                            'paid' => 'green',
                                            'partially_paid' => 'yellow',
                                            'overdue' => 'red'
                                        ];
                                    @endphp
                                    <x-badge text="{{ ucfirst($previewStatus) }}" color="{{ $statusColors[$previewStatus] ?? 'gray' }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ OVERPAYMENT WARNING --}}
            @if($invoice && $this->overpaymentAmount > 0)
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-purple-200/70 dark:border-purple-700/50 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 bg-purple-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <x-icon name="banknotes" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-1">Overpayment Detected</h4>
                            <p class="text-sm text-purple-800 dark:text-purple-200 mb-2">
                                Total pembayaran melebihi jumlah invoice sebesar:
                            </p>
                            <div class="bg-purple-100 dark:bg-purple-800/30 rounded-lg p-3">
                                <span class="text-lg font-bold text-purple-900 dark:text-purple-100">
                                    Rp {{ number_format($this->overpaymentAmount, 0, ',', '.') }}
                                </span>
                                <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">
                                    Status akan otomatis menjadi "Paid" meskipun ada overpayment
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Invoice Header --}}
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl p-6 border border-orange-200/50 dark:border-orange-700/50 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Invoice Number --}}
                    <div>
                        <x-input 
                            wire:model.live="invoice_number" 
                            label="Nomor Invoice *" 
                            icon="hashtag"
                            hint="Ubah nomor invoice jika diperlukan"
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
                    
                    {{-- Status Display with Preview --}}
                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Preview</label>
                            <div class="mt-1 flex items-center gap-2">
                                @if($statusWillChange)
                                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-200 rounded-lg flex-1">
                                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin" />
                                        <span class="font-medium text-sm">{{ ucfirst($previewStatus) }}</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg flex-1">
                                        <x-icon name="document" class="w-4 h-4" />
                                        <span class="font-medium text-sm">{{ ucfirst($currentStatus) }}</span>
                                    </div>
                                @endif
                            </div>
                            @if($statusWillChange)
                                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Status akan berubah otomatis</p>
                            @endif
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
                            hint="Perubahan due date dapat mempengaruhi status"
                        />
                    </div>
                </div>
            </div>

            {{-- ✅ PAYMENT SUMMARY SECTION --}}
            @if($invoice && $invoice->payments && $invoice->payments->count() > 0)
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200/50 dark:border-green-700/50 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <x-icon name="credit-card" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Pembayaran</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->payments->count() }} pembayaran tercatat</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Total Paid --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-200 dark:border-green-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Terbayar</p>
                            <p class="text-xl font-bold text-green-700 dark:text-green-300">
                                Rp {{ number_format($this->paymentSummary['paid'], 0, ',', '.') }}
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $this->paymentSummary['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-green-600">{{ number_format($this->paymentSummary['percentage'], 1) }}%</span>
                            </div>
                        </div>

                        {{-- Remaining Amount --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-orange-200 dark:border-orange-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Sisa Tagihan</p>
                            <p class="text-xl font-bold {{ $this->paymentSummary['remaining'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                                Rp {{ number_format($this->paymentSummary['remaining'], 0, ',', '.') }}
                            </p>
                            @if($this->paymentSummary['remaining'] <= 0)
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">✅ Lunas</p>
                            @endif
                        </div>

                        {{-- Overpayment Info --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Overpayment</p>
                            <p class="text-xl font-bold {{ $this->paymentSummary['overpaid'] > 0 ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400' }}">
                                Rp {{ number_format($this->paymentSummary['overpaid'], 0, ',', '.') }}
                            </p>
                            @if($this->paymentSummary['overpaid'] > 0)
                                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">💰 Kelebihan bayar</p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">Tidak ada</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Items Section --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <x-icon name="list-bullet" class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Item Invoice</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($items) }} item - Edit bebas semua field</p>
                        </div>
                    </div>
                    
                    <x-button wire:click="addItem" color="blue" icon="plus" size="sm">
                        Tambah Item
                    </x-button>
                </div>

                {{-- Items List --}}
                <div class="space-y-4">
                    @forelse($items as $index => $item)
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white">Item #{{ $index + 1 }}</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Layanan atau produk</p>
                                    </div>
                                </div>
                                
                                @if(count($items) > 1)
                                    <x-button 
                                        wire:click="removeItem({{ $item['id'] }})" 
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
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-purple-200/50 dark:border-purple-700/50">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-8 w-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                        <x-icon name="receipt-percent" class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Diskon (Opsional)</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Atur diskon untuk invoice - akan mempengaruhi total dan status</p>
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
                        <div class="flex justify-between items-center py-2 text-purple-600 dark:text-purple-400">
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
                        <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            Rp {{ number_format($total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- ✅ COMPARISON WITH PAYMENTS --}}
                    @if($invoice->payments->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Dibayar:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        Rp {{ number_format($this->paymentSummary['paid'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $this->paymentSummary['remaining'] > 0 ? 'Sisa Tagihan:' : 'Kelebihan:' }}
                                    </span>
                                    <span class="font-medium {{ $this->paymentSummary['remaining'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-purple-600 dark:text-purple-400' }}">
                                        Rp {{ number_format(abs($this->paymentSummary['remaining'] > 0 ? $this->paymentSummary['remaining'] : $this->paymentSummary['overpaid']), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Footer Actions --}}
        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                {{-- Left: Status & Info --}}
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <x-icon name="information-circle" class="w-4 h-4" />
                        <span>Edit bebas untuk semua status</span>
                    </div>
                    
                    @if($statusWillChange)
                        <div class="flex items-center gap-2 px-3 py-1 bg-yellow-100 dark:bg-yellow-800 rounded-lg">
                            <x-icon name="arrow-path" class="w-3 h-3 text-yellow-600" />
                            <span class="text-xs font-medium text-yellow-700 dark:text-yellow-300">
                                Status akan berubah
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-3">
                    <x-button x-on:click="$modalClose('invoice-edit-modal')" color="secondary">
                        Batal
                    </x-button>
                    
                    <x-button wire:click="save" color="orange" icon="check" spinner="save">
                        <span wire:loading.remove wire:target="save">Update Invoice</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>