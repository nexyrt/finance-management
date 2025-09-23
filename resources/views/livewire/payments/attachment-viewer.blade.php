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
                    {{-- Image Preview with Zoom --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4" x-data="{ zoomed: false }">
                        <div class="flex justify-center">
                            <div class="relative group">
                                <img src="{{ $payment->attachment_url }}" alt="{{ $payment->attachment_name }}"
                                    class="max-w-full h-auto max-h-96 rounded-lg shadow-md cursor-pointer transition-transform duration-300 group-hover:scale-105"
                                    @click="zoomed = true">
                                <div
                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-lg transition-colors duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="bg-black/70 text-white px-3 py-2 rounded-lg text-sm font-medium">
                                        <x-icon name="magnifying-glass-plus" class="w-4 h-4 inline mr-1" />
                                        Klik untuk zoom
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Zoom Modal --}}
                        <div x-show="zoomed" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                            @click="zoomed = false" @keydown.escape.window="zoomed = false" style="display: none;">
                            <div class="relative max-w-screen-lg max-h-screen" @click.stop>
                                <img src="{{ $payment->attachment_url }}" alt="{{ $payment->attachment_name }}"
                                    class="max-w-full max-h-full rounded-lg shadow-2xl">
                                <button @click="zoomed = false"
                                    class="absolute -top-12 right-0 bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition-colors">
                                    <x-icon name="x-mark" class="w-6 h-6" />
                                </button>
                                <div class="absolute -bottom-12 left-0 text-white text-sm">
                                    {{ $payment->attachment_name }}
                                </div>
                            </div>
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
