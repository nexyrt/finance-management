{{-- resources/views/livewire/transactions/transfer.blade.php --}}

<div>
    <x-modal title="Transfer Antar Rekening" wire size="3xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-path" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-50">Transfer Antar Rekening</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pindahkan dana antar rekening bank</p>
                </div>
            </div>
        </x-slot:title>

        <form wire:submit="save" class="space-y-6">
            {{-- Account Selection Card --}}
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 rounded-xl p-6 border border-blue-100 dark:border-blue-900/30">
                <div class="flex items-center gap-2 mb-4">
                    <x-icon name="building-library" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Pilih Rekening</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select.styled wire:model.live="from_account_id" :options="$this->fromAccountOptions"
                        placeholder="Pilih rekening asal..." searchable>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Rekening Asal</span>
                                <x-tooltip text="Rekening yang akan didebet" position="top" color="zinc" />
                            </div>
                        </x-slot:label>
                    </x-select.styled>

                    <x-select.styled wire:model.live="to_account_id" :options="$this->toAccountOptions"
                        placeholder="Pilih rekening tujuan..." searchable>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Rekening Tujuan</span>
                                <x-tooltip text="Rekening yang akan dikredit" position="top" color="zinc" />
                            </div>
                        </x-slot:label>
                    </x-select.styled>
                </div>

                {{-- Transfer Flow Indicator --}}
                <div class="flex justify-center my-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Debit</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <div
                                class="h-px bg-gradient-to-r from-red-300 to-green-300 dark:from-red-700 dark:to-green-700 w-16">
                            </div>
                            <div
                                class="h-10 w-10 bg-blue-500 dark:bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                <x-icon name="arrow-right" class="w-5 h-5 text-white" />
                            </div>
                            <div
                                class="h-px bg-gradient-to-r from-green-300 to-green-500 dark:from-green-700 dark:to-green-500 w-16">
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Kredit</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category Selection --}}
            <div>
                <x-select.styled wire:model.live="category_id" :options="$this->transferCategories"
                    placeholder="Pilih kategori transfer..." searchable>
                    <x-slot:label>
                        <div class="flex items-center gap-2">
                            <span>Kategori Transfer *</span>
                            <x-tooltip text="Kategori untuk mengklasifikasikan transfer" position="top"
                                color="zinc" />
                        </div>
                    </x-slot:label>
                </x-select.styled>
            </div>

            {{-- Transfer Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="banknotes" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Detail Nominal</h4>
                        </div>
                    </div>

                    <x-wireui-currency wire:model.live="amount" prefix="Rp " placeholder="0">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Jumlah Transfer *</span>
                                <x-tooltip text="Nominal yang akan ditransfer" position="top" color="zinc" />
                            </div>
                        </x-slot:label>
                    </x-wireui-currency>

                    <x-wireui-currency wire:model.live="admin_fee" prefix="Rp " placeholder="0">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Biaya Admin</span>
                                <x-tooltip text="Biaya administrasi transfer" position="top" color="zinc" />
                            </div>
                        </x-slot:label>
                    </x-wireui-currency>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="document-text" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">Informasi Transfer</h4>
                        </div>
                    </div>

                    <x-date wire:model.live="transfer_date" helpers>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Tanggal Transfer *</span>
                                <x-tooltip text="Tanggal eksekusi transfer" position="top" color="zinc" />
                            </div>
                        </x-slot:label>
                    </x-date>

                    <x-input wire:model.live="description" placeholder="Contoh: Pindah dana operasional...">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Deskripsi Transfer *</span>
                                <x-tooltip text="Jelaskan tujuan transfer" position="top" color="zinc" />
                            </div>
                        </x-slot:label>
                    </x-input>
                </div>
            </div>

            {{-- File Upload --}}
            <div>
                <x-upload wire:model="attachment" tip="Seret dan letakkan file di sini" accept="image/*,.pdf" delete
                    delete-method="deleteUpload">
                    <x-slot:label>
                        <div class="flex items-center gap-2">
                            <span>Bukti Transfer</span>
                            <x-tooltip text="Upload screenshot atau dokumen bukti transfer (opsional)" position="top"
                                color="zinc" />
                        </div>
                    </x-slot:label>
                </x-upload>
            </div>

            {{-- Enhanced Preview --}}
            @if ($from_account_id && $to_account_id && $amount && $description)
                @php
                    $fromAcc = $this->accounts->find($from_account_id);
                    $toAcc = $this->accounts->find($to_account_id);
                    $transferAmount = App\Models\BankTransaction::parseAmount($amount);
                    $feeAmount = App\Models\BankTransaction::parseAmount($admin_fee);
                @endphp

                <div
                    class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="h-10 w-10 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center">
                            <x-icon name="eye" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h5 class="text-base font-semibold text-blue-900 dark:text-blue-100">Preview Transfer</h5>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Konfirmasi detail sebelum memproses</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 space-y-4 shadow-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Dari
                                    Rekening</span>
                                <p class="font-semibold text-gray-900 dark:text-gray-50">{{ $fromAcc?->account_name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $fromAcc?->bank_name }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ke
                                    Rekening</span>
                                <p class="font-semibold text-gray-900 dark:text-gray-50">{{ $toAcc?->account_name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $toAcc?->bank_name }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Jumlah Transfer:</span>
                                <span class="font-bold text-lg text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format($transferAmount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Biaya Admin:</span>
                                <span class="font-medium text-red-600 dark:text-red-400">
                                    Rp {{ number_format($feeAmount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-900 dark:text-gray-50">Total Debet:</span>
                                    <span class="font-bold text-xl text-red-600 dark:text-red-400">
                                        Rp {{ number_format($transferAmount + $feeAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="space-y-2">
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Deskripsi</span>
                                <p class="text-sm text-gray-900 dark:text-gray-50">{{ $description }}</p>
                            </div>
                        </div>

                        @if ($attachment)
                            <div
                                class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-2">
                                    <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    <span class="text-sm font-medium text-green-700 dark:text-green-300">
                                        Bukti transfer siap diupload
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-between w-full gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>

                <x-button wire:click="save" color="blue" icon="arrow-path" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    <span wire:loading.remove wire:target="save">Transfer Dana</span>
                    <span wire:loading wire:target="save">Memproses Transfer...</span>
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
