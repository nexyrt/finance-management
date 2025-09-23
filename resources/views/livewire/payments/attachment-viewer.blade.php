<div>
    <x-modal wire title="Bukti Pembayaran" size="3xl" center>
        @if ($payment)
            <div class="space-y-4">
                {{-- Payment Info Header --}}
                <div class="bg-zinc-50 dark:bg-dark-800 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-dark-600 dark:text-dark-400">
                                {{ $payment->payment_date->format('d M Y') }} • {{ $payment->bankAccount->bank_name }}
                            </p>
                            <p class="text-xs text-dark-500 dark:text-dark-400">
                                Invoice: {{ $payment->invoice->invoice_number }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ $payment->attachment_name }}</p>
                            <a href="{{ $payment->attachment_url }}" target="_blank"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                Download Original
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Attachment Viewer --}}
                <div
                    class="bg-white dark:bg-dark-800 rounded-xl border border-zinc-200 dark:border-dark-600 overflow-hidden">
                    @php
                        $extension = strtolower(pathinfo($payment->attachment_name, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = $extension === 'pdf';
                    @endphp

                    @if ($isImage)
                        {{-- Image Preview --}}
                        <div class="flex justify-center p-4">
                            <img src="{{ $payment->attachment_url }}" alt="Bukti Pembayaran"
                                class="max-w-full max-h-96 object-contain rounded-lg shadow-lg"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div style="display:none" class="text-center py-8">
                                <p class="text-red-600">Gagal memuat gambar</p>
                            </div>
                        </div>
                    @elseif($isPdf)
                        {{-- PDF Preview --}}
                        <div class="h-96">
                            <iframe src="{{ $payment->attachment_url }}#toolbar=0" class="w-full h-full rounded-lg"
                                frameborder="0" onload="this.style.display='block'"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                            </iframe>
                            <div style="display:none" class="text-center py-8">
                                <p class="text-amber-600 mb-2">PDF tidak dapat dipratinjau</p>
                                <a href="{{ $payment->attachment_url }}" target="_blank"
                                    class="text-blue-600 hover:underline">Buka di tab baru</a>
                            </div>
                        </div>
                    @else
                        {{-- Generic File --}}
                        <div class="text-center py-12">
                            <div
                                class="bg-zinc-100 dark:bg-dark-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <x-icon name="document" class="w-8 h-8 text-zinc-500 dark:text-dark-400" />
                            </div>
                            <p class="text-dark-900 dark:text-dark-50 font-medium mb-2">{{ $payment->attachment_name }}
                            </p>
                            <p class="text-dark-600 dark:text-dark-400 text-sm mb-4">File tidak dapat dipratinjau</p>
                            <a href="{{ $payment->attachment_url }}" target="_blank"
                                class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline">
                                <x-icon name="arrow-down-tray" class="w-4 h-4" />
                                Download File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <div></div>
                <x-button wire:click="$set('modal', false)" color="zinc">
                    Tutup
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
