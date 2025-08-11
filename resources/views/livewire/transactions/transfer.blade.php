{{-- resources/views/livewire/transactions/transfer.blade.php --}}

<x-modal wire="showModal" title="Transfer Antar Rekening" size="xl" center>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="arrow-path" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Transfer Antar Rekening</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">Pindahkan dana antar rekening bank</p>
            </div>
        </div>
    </x-slot:title>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Account Selection --}}
        <div class="bg-zinc-50 dark:bg-dark-700 rounded-xl p-4">
            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-4">Pilih Rekening</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- From Account --}}
                <div>
                    <x-select.styled wire:model.live="from_account_id"
                                     :options="$accounts->map(fn($account) => [
                                         'label' => $account->account_name . ' - ' . $account->bank_name,
                                         'value' => $account->id
                                     ])->toArray()"
                                     label="Rekening Asal"
                                     placeholder="Pilih rekening asal..."
                                     searchable />
                </div>

                {{-- Transfer Arrow --}}
                <div class="hidden md:flex md:items-center md:justify-center md:col-span-2 md:order-3">
                    <div class="flex items-center gap-2">
                        <div class="h-px bg-zinc-300 dark:bg-dark-600 w-8"></div>
                        <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <x-icon name="arrow-right" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="h-px bg-zinc-300 dark:bg-dark-600 w-8"></div>
                    </div>
                </div>

                {{-- To Account --}}
                <div>
                    <x-select.styled wire:model.live="to_account_id"
                                     :options="$accounts->where('id', '!=', $from_account_id)->map(fn($account) => [
                                         'label' => $account->account_name . ' - ' . $account->bank_name,
                                         'value' => $account->id
                                     ])->toArray()"
                                     label="Rekening Tujuan"
                                     placeholder="Pilih rekening tujuan..."
                                     searchable />
                </div>
            </div>
        </div>

        {{-- Transfer Details --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Column --}}
            <div class="space-y-4">
                <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Transfer</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Informasi transfer dana</p>
                </div>

                <x-wireui-currency prefix="Rp "
                                   wire:model.live="amount"
                                   label="Jumlah Transfer"
                                   placeholder="0"
                                   color="dark:dark"
                                   hint="Jumlah yang akan ditransfer" />

                <x-wireui-currency prefix="Rp "
                                   wire:model.live="admin_fee"
                                   label="Biaya Admin"
                                   placeholder="2500"
                                   color="dark:dark"
                                   hint="Biaya administrasi transfer (default: Rp 2.500)" />

                <x-date wire:model.live="transfer_date"
                        label="Tanggal Transfer"
                        helpers />
            </div>

            {{-- Right Column --}}
            <div class="space-y-4">
                <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Keterangan</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Deskripsi transfer</p>
                </div>

                <x-input wire:model.live="description"
                         label="Deskripsi"
                         placeholder="Contoh: Pindah dana operasional..."
                         hint="Jelaskan tujuan transfer" />
            </div>
        </div>

        {{-- Transfer Preview --}}
        @if($from_account_id && $to_account_id && $amount && $description)
        @php 
            $fromAcc = $accounts->find($from_account_id);
            $toAcc = $accounts->find($to_account_id);
        @endphp
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-8 w-8 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                    <x-icon name="eye" class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-green-900 dark:text-green-100">Preview Transfer</h5>
                    <p class="text-xs text-green-800 dark:text-green-200">Konfirmasi detail transfer</p>
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
                    <span class="text-sm text-dark-600 dark:text-dark-400">Jumlah:</span>
                    <span class="font-bold text-lg text-blue-600 dark:text-blue-400">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-600 dark:text-dark-400">Biaya Admin:</span>
                    <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($admin_fee, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-zinc-200 dark:border-dark-600 pt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-dark-600 dark:text-dark-400">Total Debet:</span>
                        <span class="font-bold text-lg text-red-600 dark:text-red-400">Rp {{ number_format($amount + $admin_fee, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-600 dark:text-dark-400">Deskripsi:</span>
                    <span class="font-medium text-dark-900 dark:text-dark-50">{{ $description }}</span>
                </div>
            </div>
        </div>
        @endif
    </form>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="closeModal" color="zinc" outline class="w-full sm:w-auto order-2 sm:order-1">
                Batal
            </x-button>

            <x-button wire:click="save" color="blue" icon="arrow-path" loading="save"
                      class="w-full sm:w-auto order-1 sm:order-2">
                <span wire:loading.remove wire:target="save">Transfer Dana</span>
                <span wire:loading wire:target="save">Memproses...</span>
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>