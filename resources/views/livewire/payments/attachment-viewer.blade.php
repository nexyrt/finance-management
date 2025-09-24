<div>
    <x-modal wire title="Lampiran Pembayaran" size="4xl" center>
        @if ($payment && $payment->attachment_path)
            {{-- Header with payment info --}}
            <div
                class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 -m-6 mb-6 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                            <x-icon name="paper-clip" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-50">
                                {{ $payment->attachment_name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Pembayaran {{ $payment->invoice->invoice_number }} •
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <x-button wire:click="downloadAttachment" color="blue" icon="arrow-down-tray" size="sm">
                        Download
                    </x-button>
                </div>
            </div>

            {{-- Attachment Preview --}}
            <div class="space-y-4">
                @if ($payment->isImageAttachment())
                    {{-- Image Preview with Cursor-Based Zoom --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4" x-data="{
                        scale: 1,
                        maxScale: 5,
                        minScale: 0.5,
                        originX: 50,
                        originY: 50,
                        zoom(event, delta) {
                            const rect = event.target.getBoundingClientRect();
                            this.originX = ((event.clientX - rect.left) / rect.width) * 100;
                            this.originY = ((event.clientY - rect.top) / rect.height) * 100;
                    
                            if (delta > 0) this.scale = Math.min(this.scale * 1.2, this.maxScale);
                            else this.scale = Math.max(this.scale / 1.2, this.minScale);
                        },
                        reset() {
                            this.scale = 1;
                            this.originX = 50;
                            this.originY = 50;
                        }
                    }">
                        <div class="mb-2 text-center text-sm text-gray-600 dark:text-gray-400">
                            Tahan Ctrl + scroll untuk zoom ke kursor • Klik untuk reset
                        </div>
                        <div class="flex justify-center overflow-hidden">
                            <img src="{{ Storage::url($payment->attachment_path) }}"
                                alt="{{ $payment->attachment_name }}"
                                class="max-w-full h-auto max-h-96 rounded-lg shadow-md cursor-pointer transition-transform duration-200"
                                :style="`transform: scale(${scale}); transform-origin: ${originX}% ${originY}%`"
                                @wheel.prevent="if ($event.ctrlKey) zoom($event, $event.deltaY > 0 ? -1 : 1)"
                                @click="reset()">
                        </div>
                        <div class="mt-2 text-center text-xs text-gray-500" x-show="scale !== 1">
                            Zoom: <span x-text="Math.round(scale * 100)"></span>%
                        </div>
                    </div>
                @elseif ($payment->isPdfAttachment())
                    {{-- PDF Preview --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden">
                        <div
                            class="bg-red-50 dark:bg-red-900/20 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-icon name="document-text" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                    <h4 class="font-medium text-gray-900 dark:text-gray-50">
                                        {{ $payment->attachment_name }}</h4>
                                </div>
                                <x-button wire:click="downloadAttachment" color="red" icon="arrow-down-tray"
                                    size="sm" outline>
                                    Download PDF
                                </x-button>
                            </div>
                        </div>
                        <div class="relative">
                            <iframe src="{{ $payment->attachment_url }}#view=FitH" class="w-full h-96 border-0"
                                title="{{ $payment->attachment_name }}" loading="lazy">
                            </iframe>
                            {{-- Fallback for unsupported browsers --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-700"
                                id="pdf-fallback-{{ $payment->id }}" style="display: none;">
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mx-auto mb-4">
                                        <x-icon name="document-text" class="w-8 h-8 text-red-600 dark:text-red-400" />
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Browser tidak mendukung preview PDF
                                    </p>
                                    <x-button wire:click="downloadAttachment" color="red" icon="arrow-down-tray"
                                        size="sm">
                                        Download untuk melihat
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Other file types --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6">
                        <div class="text-center">
                            <div
                                class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-icon name="document" class="w-10 h-10 text-gray-500 dark:text-gray-400" />
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-50 mb-2">
                                {{ $payment->attachment_name }}
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                File {{ strtoupper($payment->attachment_type) }} • Preview tidak tersedia
                            </p>
                            <x-button wire:click="downloadAttachment" color="gray" icon="arrow-down-tray">
                                Download untuk melihat
                            </x-button>
                        </div>
                    </div>
                @endif

                {{-- Payment Details --}}
                <div class="border border-gray-200 dark:border-gray-600 rounded-xl p-4">
                    <h4 class="font-medium text-gray-900 dark:text-gray-50 mb-3 flex items-center gap-2">
                        <x-icon name="information-circle" class="w-4 h-4" />
                        Detail Pembayaran
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Nomor Invoice</p>
                            <p class="font-medium text-gray-900 dark:text-gray-50 font-mono">
                                {{ $payment->invoice->invoice_number }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Klien</p>
                            <p class="font-medium text-gray-900 dark:text-gray-50">
                                {{ $payment->invoice->client->name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Jumlah Pembayaran</p>
                            <p class="font-medium text-gray-900 dark:text-gray-50">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Tanggal Pembayaran</p>
                            <p class="font-medium text-gray-900 dark:text-gray-50">
                                {{ $payment->payment_date->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Metode Pembayaran</p>
                            <p class="font-medium text-gray-900 dark:text-gray-50">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Bank</p>
                            <p class="font-medium text-gray-900 dark:text-gray-50">
                                {{ $payment->bankAccount->bank_name }}
                            </p>
                        </div>
                        @if ($payment->reference_number)
                            <div class="sm:col-span-2">
                                <p class="text-gray-600 dark:text-gray-400">Nomor Referensi</p>
                                <p class="font-medium text-gray-900 dark:text-gray-50 font-mono">
                                    {{ $payment->reference_number }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="text-gray-500 mt-2">Memuat lampiran...</p>
            </div>
        @endif

        <x-slot:footer>
            <div class="flex justify-between w-full">
                @if ($payment && $payment->attachment_path)
                    <x-button wire:click="downloadAttachment" color="blue" icon="arrow-down-tray">
                        Download Lampiran
                    </x-button>
                @else
                    <div></div>
                @endif
                <x-button wire:click="$set('modal', false)" color="gray">
                    Tutup
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
