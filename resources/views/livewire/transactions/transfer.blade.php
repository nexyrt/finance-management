{{-- resources/views/livewire/transactions/transfer.blade.php --}}

<div>
    <x-modal title="Transfer Antar Rekening" wire size="2xl" center persistent>
        <form wire:submit="save" class="space-y-6">
            {{-- Account Selection --}}
            <div class="bg-zinc-50 dark:bg-dark-700 rounded-xl p-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-4">Pilih Rekening</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select.styled wire:model.live="from_account_id"
                                     :options="$accounts->map(fn($account) => [
                                         'label' => $account->account_name . ' - ' . $account->bank_name,
                                         'value' => $account->id
                                     ])->toArray()"
                                     label="Rekening Asal"
                                     placeholder="Pilih rekening asal..."
                                     searchable />

                    <x-select.styled wire:model.live="to_account_id"
                                     :options="$accounts->where('id', '!=', $from_account_id)->map(fn($account) => [
                                         'label' => $account->account_name . ' - ' . $account->bank_name,
                                         'value' => $account->id
                                     ])->toArray()"
                                     label="Rekening Tujuan"
                                     placeholder="Pilih rekening tujuan..."
                                     searchable />
                </div>

                {{-- Transfer Arrow Indicator --}}
                <div class="flex justify-center my-4">
                    <div class="flex items-center gap-2">
                        <div class="h-px bg-zinc-300 dark:bg-dark-600 w-8"></div>
                        <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <x-icon name="arrow-down" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="h-px bg-zinc-300 dark:bg-dark-600 w-8"></div>
                    </div>
                </div>
            </div>

            {{-- Transfer Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column - Amount & Fee --}}
                <div class="space-y-4">
                    <x-wireui-currency wire:model.live="amount"
                                       label="Jumlah Transfer *"
                                       prefix="Rp "
                                       hint="Nominal yang akan ditransfer" />

                    <x-wireui-currency wire:model.live="admin_fee"
                                       label="Biaya Admin"
                                       prefix="Rp "
                                       hint="Biaya administrasi transfer" />
                </div>

                {{-- Right Column - Date & Description --}}
                <div class="space-y-4">
                    <x-date wire:model.live="transfer_date"
                            label="Tanggal Transfer *"
                            helpers />

                    <x-input wire:model.live="description"
                             label="Deskripsi Transfer *"
                             placeholder="Contoh: Pindah dana operasional..."
                             hint="Jelaskan tujuan transfer" />
                </div>
            </div>

            {{-- File Upload Row --}}
            <div class="space-y-4">
                <x-upload wire:model="attachment"
                          label="Bukti Transfer"
                          hint="Upload screenshot atau dokumen bukti transfer (opsional)"
                          tip="Seret dan letakkan file di sini"
                          accept="image/*,.pdf"
                          delete
                          delete-method="deleteUpload" />
            </div>

            {{-- Transfer Preview --}}
            @if($from_account_id && $to_account_id && $amount && $description)
                @php 
                    $fromAcc = $accounts->find($from_account_id);
                    $toAcc = $accounts->find($to_account_id);
                    $transferAmount = App\Models\BankTransaction::parseAmount($amount);
                    $feeAmount = App\Models\BankTransaction::parseAmount($admin_fee);
                @endphp
                
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                            <x-icon name="eye" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h5 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Preview Transfer</h5>
                            <p class="text-xs text-blue-800 dark:text-blue-200">Konfirmasi detail sebelum memproses</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-dark-800 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Dari:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50">{{ $fromAcc?->account_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Ke:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50">{{ $toAcc?->account_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Jumlah Transfer:</span>
                            <span class="font-bold text-lg text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($transferAmount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Biaya Admin:</span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                Rp {{ number_format($feeAmount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="border-t border-zinc-200 dark:border-dark-600 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-dark-600 dark:text-dark-400">Total Debet:</span>
                                <span class="font-bold text-lg text-red-600 dark:text-red-400">
                                    Rp {{ number_format($transferAmount + $feeAmount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Deskripsi:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50 text-right max-w-xs truncate">
                                {{ $description }}
                            </span>
                        </div>
                        @if($attachment)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-dark-600 dark:text-dark-400">Bukti Transfer:</span>
                                <div class="flex items-center gap-2">
                                    <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                        File siap diupload
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="zinc" outline>
                    Batal
                </x-button>
                
                <x-button wire:click="save" 
                          color="blue" 
                          icon="arrow-path" 
                          loading="save">
                    Transfer Dana
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>