<div>
    <x-modal wire="modal" size="lg" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Review Piutang</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Setujui atau tolak pengajuan piutang</p>
                </div>
            </div>
        </x-slot:title>

        @if ($receivable)
            <div class="space-y-6">
                {{-- Receivable Info --}}
                <div class="bg-secondary-50 dark:bg-dark-700 rounded-lg p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Peminjam</div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $receivable->debtor?->name }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Jenis</div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50">
                                {{ $receivable->type === 'employee_loan' ? 'Karyawan' : 'Perusahaan' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Jumlah Pokok</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($receivable->principal_amount, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Bunga</div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50">
                                {{ $receivable->interest_rate }}%
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Akun Tujuan Pencairan</div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50 flex items-center gap-2">
                                @if (strtoupper($receivable->disbursement_account) === 'CASH')
                                    <x-icon name="banknotes" class="w-4 h-4 text-green-600" />
                                    <span class="text-green-600">CASH</span>
                                @else
                                    <x-icon name="building-library" class="w-4 h-4 text-blue-600" />
                                    <span>{{ $receivable->disbursement_account }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Tujuan</div>
                            <div class="font-medium text-dark-900 dark:text-dark-50">{{ $receivable->purpose }}</div>
                        </div>
                    </div>
                </div>

                {{-- Action Selection --}}
                <div class="flex gap-4">
                    <label class="flex-1 relative">
                        <input type="radio" wire:model.live="action" value="approve" class="peer sr-only" />
                        <div
                            class="p-4 border-2 rounded-lg cursor-pointer transition-all
                                    border-dark-200 dark:border-dark-600
                                    peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                            <div class="flex items-center gap-3">
                                <x-icon name="check-circle" class="w-6 h-6 text-green-600" />
                                <div>
                                    <div class="font-semibold text-dark-900 dark:text-dark-50">Setujui</div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">Cairkan piutang</div>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="flex-1 relative">
                        <input type="radio" wire:model.live="action" value="reject" class="peer sr-only" />
                        <div
                            class="p-4 border-2 rounded-lg cursor-pointer transition-all
                                    border-dark-200 dark:border-dark-600
                                    peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                            <div class="flex items-center gap-3">
                                <x-icon name="x-circle" class="w-6 h-6 text-red-600" />
                                <div>
                                    <div class="font-semibold text-dark-900 dark:text-dark-50">Tolak</div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">Kembalikan untuk revisi</div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Notes --}}
                <x-textarea wire:model="notes"
                    label="{{ $action === 'reject' ? 'Alasan Penolakan *' : 'Catatan (opsional)' }}"
                    placeholder="Tulis catatan..." rows="3" />
            </div>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>

                @if ($action === 'approve')
                    <x-button wire:click="approve" color="green" icon="check" loading="approve"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        Setujui Piutang
                    </x-button>
                @else
                    <x-button wire:click="reject" color="red" icon="x-mark" loading="reject"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        Tolak Piutang
                    </x-button>
                @endif
            </div>
        </x-slot:footer>
    </x-modal>
</div>
