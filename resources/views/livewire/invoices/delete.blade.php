<div>
    <x-modal wire="showModal" size="2xl" center id="invoice-delete-modal" x-on:close="$wire.resetData()">
        <x-slot:header>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon name="trash" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Hapus Invoice</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Konfirmasi penghapusan invoice</p>
                </div>
            </div>
        </x-slot:header>

        @if($invoice)
            <div class="space-y-6">
                {{-- Invoice Info --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 {{ $invoice->client->type === 'individual' 
                            ? 'bg-gradient-to-br from-blue-400 to-blue-600' 
                            : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                            rounded-xl flex items-center justify-center shadow-lg">
                            <x-icon name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}" 
                                    class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white font-mono">
                                    {{ $invoice->invoice_number }}
                                </h4>
                                @php
                                    $statusConfig = [
                                        'draft' => ['color' => 'gray', 'text' => 'Draft'],
                                        'sent' => ['color' => 'blue', 'text' => 'Terkirim'],
                                        'paid' => ['color' => 'green', 'text' => 'Dibayar'],
                                        'partially_paid' => ['color' => 'yellow', 'text' => 'Sebagian'],
                                        'overdue' => ['color' => 'red', 'text' => 'Terlambat'],
                                    ];
                                    $config = $statusConfig[$invoice->status] ?? $statusConfig['draft'];
                                @endphp
                                <x-badge text="{{ $config['text'] }}" color="{{ $config['color'] }}" />
                            </div>
                            <p class="font-medium text-gray-700 dark:text-gray-300">{{ $invoice->client->name }}</p>
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>{{ $invoice->issue_date->format('d M Y') }}</span>
                                <span>•</span>
                                <span class="font-bold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Warning based on status and dependencies --}}
                @if($invoice->status !== 'draft' && $invoice->payments->count() > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200/50 dark:border-red-700/50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 bg-red-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <x-icon name="shield-exclamation" class="w-4 h-4 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-900 dark:text-red-100 mb-1">Perhatian Khusus!</h4>
                                <p class="text-sm text-red-800 dark:text-red-200 mb-3">
                                    Invoice ini memiliki status <strong>{{ $config['text'] }}</strong> dan sudah memiliki 
                                    <strong>{{ $invoice->payments->count() }} pembayaran</strong>. 
                                    Menghapus invoice ini akan juga menghapus semua data pembayaran yang terkait.
                                </p>
                                <div class="bg-red-100 dark:bg-red-800/30 rounded-lg p-3">
                                    <div class="text-sm text-red-800 dark:text-red-200">
                                        <div class="font-medium mb-1">Total pembayaran yang akan dihapus:</div>
                                        <div class="text-lg font-bold">Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($invoice->status !== 'draft')
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 border border-orange-200/50 dark:border-orange-700/50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 bg-orange-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <x-icon name="exclamation-triangle" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-orange-900 dark:text-orange-100 mb-1">Perhatian!</h4>
                                <p class="text-sm text-orange-800 dark:text-orange-200">
                                    Invoice ini memiliki status <strong>{{ $config['text'] }}</strong>. 
                                    Menghapus invoice yang sudah dikirim atau diproses mungkin memerlukan tindakan lanjutan.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($invoice->payments->count() > 0)
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 border border-orange-200/50 dark:border-orange-700/50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 bg-orange-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <x-icon name="exclamation-triangle" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-orange-900 dark:text-orange-100 mb-1">Perhatian!</h4>
                                <p class="text-sm text-orange-800 dark:text-orange-200 mb-3">
                                    Invoice ini sudah memiliki <strong>{{ $invoice->payments->count() }} pembayaran</strong>. 
                                    Menghapus invoice ini akan juga menghapus semua data pembayaran yang terkait.
                                </p>
                                <div class="bg-orange-100 dark:bg-orange-800/30 rounded-lg p-3">
                                    <div class="text-sm text-orange-800 dark:text-orange-200">
                                        <div class="font-medium mb-1">Total pembayaran yang akan dihapus:</div>
                                        <div class="text-lg font-bold">Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Safe to delete --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 border border-yellow-200/50 dark:border-yellow-700/50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 bg-yellow-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <x-icon name="exclamation-triangle" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-1">Konfirmasi Penghapusan</h4>
                                <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                                    Invoice ini akan dihapus secara permanen beserta <strong>{{ $invoice->items->count() }} item</strong> yang terkait.
                                </p>
                                
                                {{-- Items Summary --}}
                                @if($invoice->items->count() > 0)
                                    <div class="bg-yellow-100 dark:bg-yellow-800/30 rounded-lg p-3 space-y-2">
                                        <div class="font-medium text-yellow-800 dark:text-yellow-200 text-sm">Item yang akan dihapus:</div>
                                        @foreach($invoice->items->take(3) as $item)
                                            <div class="flex justify-between text-sm text-yellow-700 dark:text-yellow-300">
                                                <span>{{ $item->service_name }}</span>
                                                <span>Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                        @if($invoice->items->count() > 3)
                                            <div class="text-xs text-yellow-600 dark:text-yellow-400 italic">
                                                ... dan {{ $invoice->items->count() - 3 }} item lainnya
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <x-slot:footer>
            <div class="flex justify-end gap-3">
                <x-button x-on:click="$modalClose('invoice-delete-modal')" color="secondary">
                    Batal
                </x-button>
                
                @if($invoice)
                    @php
                        $buttonColor = 'red';
                        $buttonText = 'Hapus Invoice';
                        
                        if ($invoice->status !== 'draft' && $invoice->payments->count() > 0) {
                            $buttonText = 'Hapus Invoice & Pembayaran';
                        } elseif ($invoice->status !== 'draft') {
                            $buttonText = 'Hapus Invoice';
                        } elseif ($invoice->payments->count() > 0) {
                            $buttonText = 'Hapus Invoice & Pembayaran';
                        }
                    @endphp
                    
                    <x-button wire:click="confirm" color="{{ $buttonColor }}" icon="trash" spinner="confirm">
                        {{ $buttonText }}
                    </x-button>
                @endif
            </div>
        </x-slot:footer>
    </x-modal>
</div>