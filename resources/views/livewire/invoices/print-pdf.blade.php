<div>
    <x-modal wire="showModal" size="3xl" center id="invoice-print-modal" x-on:close="$wire.resetData()">
        <x-slot:header>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon name="printer" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Print Invoice</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Generate PDF dan print invoice</p>
                </div>
            </div>
        </x-slot:header>

        @if($invoice)
            {{-- Invoice Info Header --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200/50 dark:border-green-700/50 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 {{ $invoice->client->type === 'individual' 
                            ? 'bg-gradient-to-br from-blue-400 to-blue-600' 
                            : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                            rounded-xl flex items-center justify-center shadow-lg">
                            <x-icon name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}" 
                                    class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white font-mono">{{ $invoice->invoice_number }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client->name }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->issue_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Print Options --}}
            <x-tab selected="options">
                
                {{-- Tab 1: Print Options --}}
                <x-tab.items tab="options">
                    <x-slot:left>
                        <x-icon name="cog-6-tooth" class="w-4 h-4" />
                    </x-slot:left>
                    Pengaturan

                    <div class="space-y-6">
                        {{-- Format & Orientation --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <x-icon name="document" class="w-4 h-4" />
                                Format Dokumen
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-select.styled 
                                        wire:model.live="format" 
                                        label="Ukuran Kertas"
                                        :options="[
                                            ['label' => '📄 A4 (21 x 29.7 cm)', 'value' => 'A4'],
                                            ['label' => '📄 Letter (21.6 x 27.9 cm)', 'value' => 'Letter'],
                                            ['label' => '📄 Legal (21.6 x 35.6 cm)', 'value' => 'Legal'],
                                            ['label' => '📄 A5 (14.8 x 21 cm)', 'value' => 'A5'],
                                        ]"
                                    />
                                </div>
                                
                                <div>
                                    <x-select.styled 
                                        wire:model.live="orientation" 
                                        label="Orientasi"
                                        :options="[
                                            ['label' => '📱 Portrait (Vertikal)', 'value' => 'portrait'],
                                            ['label' => '📱 Landscape (Horizontal)', 'value' => 'landscape'],
                                        ]"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Content Options --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <x-icon name="list-bullet" class="w-4 h-4" />
                                Konten Invoice
                            </h5>
                            
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model.live="showClientDetails" id="show-client" 
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="show-client" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Tampilkan Detail Klien Lengkap
                                    </label>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model.live="showPayments" id="show-payments" 
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="show-payments" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Tampilkan Riwayat Pembayaran
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Notes --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <x-icon name="pencil-square" class="w-4 h-4" />
                                Catatan Tambahan
                            </h5>
                            
                            <x-textarea 
                                wire:model="notes" 
                                placeholder="Catatan khusus untuk invoice ini (opsional)"
                                rows="3"
                            />
                        </div>
                    </div>
                </x-tab.items>

                {{-- Tab 2: Preview --}}
                <x-tab.items tab="preview">
                    <x-slot:left>
                        <x-icon name="eye" class="w-4 h-4" />
                    </x-slot:left>
                    Preview

                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <x-icon name="document-text" class="w-12 h-12 text-gray-400" />
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Preview PDF</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">Lihat preview sebelum print atau download</p>
                        
                        <div class="space-y-3">
                            <x-button wire:click="previewPdf" color="blue" icon="eye" size="lg">
                                Generate Preview
                            </x-button>
                            
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Preview akan dibuka di tab baru
                            </p>
                        </div>
                    </div>
                </x-tab.items>

            </x-tab>
        @endif

        {{-- Footer Actions --}}
        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                {{-- Info --}}
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <x-icon name="information-circle" class="w-4 h-4" />
                    <span>Format: {{ strtoupper($format) }} {{ ucfirst($orientation) }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Preview --}}
                    <x-button wire:click="previewPdf" color="secondary" icon="eye" outline size="sm">
                        Preview
                    </x-button>
                    
                    {{-- Print --}}
                    <x-button wire:click="print" color="blue" icon="printer" outline>
                        Print
                    </x-button>
                    
                    {{-- Email --}}
                    @if($invoice && $invoice->client->email)
                        <x-button wire:click="sendEmail" color="green" icon="envelope" outline>
                            Email
                        </x-button>
                    @endif
                    
                    {{-- Download --}}
                    <x-button wire:click="downloadPdf" color="green" icon="arrow-down-tray" spinner="downloadPdf">
                        Download PDF
                    </x-button>
                    
                    {{-- Close --}}
                    <x-button x-on:click="$modalClose('invoice-print-modal')" color="secondary">
                        Tutup
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- JavaScript for PDF operations --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Handle PDF preview
            Livewire.on('open-pdf-preview', (data) => {
                window.open(data.url, '_blank');
            });
            
            // Handle PDF print
            Livewire.on('print-pdf', (data) => {
                const printWindow = window.open(data.url, '_blank');
                printWindow.onload = function() {
                    printWindow.print();
                };
            });
        });
    </script>
</div>