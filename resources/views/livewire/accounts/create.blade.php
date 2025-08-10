{{-- resources/views/livewire/accounts/create.blade.php --}}

<x-modal wire="showModal" title="Tambah Rekening Bank" size="lg" center>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="building-library" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Rekening Bank</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">Buat rekening bank baru untuk pengelolaan keuangan</p>
            </div>
        </div>
    </x-slot:title>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Basic Information --}}
        <div class="space-y-4">
            <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Informasi Dasar</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">Data utama rekening bank</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model.live="account_name" 
                         label="Nama Rekening" 
                         placeholder="Contoh: Rekening Operasional"
                         hint="Nama untuk identifikasi rekening" />

                <x-input wire:model.live="bank_name" 
                         label="Nama Bank" 
                         placeholder="Contoh: Bank Mandiri"
                         hint="Nama institusi bank" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model.live="account_number" 
                         label="Nomor Rekening" 
                         placeholder="1234567890"
                         hint="Nomor rekening unik" />

                <x-input wire:model.live="branch" 
                         label="Cabang (Opsional)" 
                         placeholder="Jakarta Pusat"
                         hint="Nama cabang bank" />
            </div>
        </div>

        {{-- Financial Information --}}
        <div class="space-y-4">
            <div class="border-b border-zinc-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Informasi Keuangan</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">Saldo awal rekening</p>
            </div>

            {{-- Use WireUI Currency component --}}
            <x-wireui-currency prefix="Rp " 
                               wire:model.live="initial_balance" 
                               label="Saldo Awal" 
                               placeholder="0" 
                               color="dark:dark"
                               hint="Saldo awal rekening dalam Rupiah" />
        </div>

        {{-- Preview Section --}}
        @if($account_name || $bank_name || $account_number)
        <div class="bg-zinc-50 dark:bg-dark-700 rounded-xl p-4 border border-zinc-200 dark:border-dark-600">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-8 w-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="eye" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Preview Rekening</h5>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Seperti yang akan ditampilkan</p>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-8 w-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                        <x-icon name="building-library" class="w-4 h-4 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-dark-900 dark:text-dark-50 truncate">
                            {{ $account_name ?: 'Nama Rekening' }}
                        </p>
                        <p class="text-sm text-dark-500 dark:text-dark-400">
                            {{ $bank_name ?: 'Nama Bank' }}
                        </p>
                    </div>
                </div>
                @if($account_number)
                <div class="bg-zinc-50 dark:bg-dark-700 rounded-lg px-3 py-2 mt-2">
                    <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">Nomor Rekening</p>
                    <p class="font-mono text-sm font-medium text-dark-900 dark:text-dark-50">
                        {{ $account_number }}
                    </p>
                </div>
                @endif
                @if($initial_balance)
                <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-dark-600">
                    <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">Saldo Awal</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($initial_balance, 0, ',', '.') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </form>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="closeModal" color="zinc" outline class="w-full sm:w-auto order-2 sm:order-1">
                Batal
            </x-button>

            <x-button wire:click="save" color="primary" icon="check" loading="save"
                      class="w-full sm:w-auto order-1 sm:order-2">
                <span wire:loading.remove wire:target="save">Simpan Rekening</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>