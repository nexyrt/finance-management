{{-- resources/views/livewire/clients/delete.blade.php --}}
<div>
    <x-modal wire="showDeleteModal" center>
        <x-slot:header>
            <div class="flex items-center gap-4">
                <x-tooltip text="Peringatan: Tindakan penghapusan permanen" position="bottom" color="red">
                    <div class="h-12 w-12 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <x-icon name="exclamation-triangle" class="w-6 h-6 text-white" />
                    </div>
                </x-tooltip>
                <div>
                    <h3 class="text-xl font-bold text-secondary-900 dark:text-white">Hapus Klien</h3>
                    <p class="text-sm text-secondary-600 dark:text-secondary-400">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
        </x-slot:header>

        @if($client)
            <div class="space-y-6">
                {{-- Warning Message --}}
                <div class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-2xl p-6 border border-red-200/50 dark:border-red-700/50">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 bg-red-500/10 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                            <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-red-900 dark:text-red-100">Peringatan Penting!</h4>
                            <p class="text-sm text-red-800 dark:text-red-200 leading-relaxed">
                                Anda akan menghapus klien <strong class="font-bold">{{ $client->name }}</strong>. 
                                Tindakan ini akan menghapus <strong>semua data terkait</strong> termasuk invoice, 
                                pembayaran, dan relasi bisnis secara permanen.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Client Summary --}}
                <div class="bg-secondary-50 dark:bg-secondary-800/50 rounded-2xl p-6 border border-secondary-200/50 dark:border-secondary-700/50">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative">
                            @if($client->logo)
                                <img class="h-14 w-14 rounded-2xl object-cover shadow-md" src="{{ $client->logo }}" alt="{{ $client->name }}">
                            @else
                                <div class="h-14 w-14 rounded-2xl flex items-center justify-center shadow-md
                                    {{ $client->type === 'individual' 
                                        ? 'bg-gradient-to-br from-blue-500 to-blue-600' 
                                        : 'bg-gradient-to-br from-purple-500 to-purple-600' }}">
                                    <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                        class="w-7 h-7 text-white" />
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-secondary-900 dark:text-white">{{ $client->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <x-badge text="{{ $client->type === 'individual' ? 'Individu' : 'Perusahaan' }}" 
                                         color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                                @if($client->NPWP)
                                    <span class="text-xs text-secondary-600 dark:text-secondary-400 font-mono bg-secondary-100 dark:bg-secondary-700 px-2 py-1 rounded">
                                        {{ $client->NPWP }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Invoice Impact --}}
                @if($client && $client->invoices && $client->invoices->count() > 0)
                    <x-card class="bg-white/60 dark:bg-secondary-900/60 backdrop-blur-sm border-secondary-200/50 dark:border-secondary-700/50">
                        <x-slot:header>
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 bg-orange-500/10 dark:bg-orange-400/10 rounded-lg flex items-center justify-center">
                                    <x-icon name="document-text" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                </div>
                                <h4 class="font-semibold text-secondary-900 dark:text-white">
                                    Invoice yang Akan Dihapus ({{ $client->invoices->count() }})
                                </h4>
                            </div>
                        </x-slot:header>
                        
                        <div class="max-h-64 overflow-y-auto space-y-3">
                            @foreach($client->invoices as $invoice)
                                <div class="flex items-center justify-between p-4 bg-secondary-50/50 dark:bg-secondary-800/50 rounded-xl border border-secondary-200/50 dark:border-secondary-700/50">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                                            <x-icon name="document" class="w-4 h-4 text-white" />
                                        </div>
                                        <div>
                                            <div class="font-semibold text-secondary-900 dark:text-white">{{ $invoice->invoice_number }}</div>
                                            <div class="text-sm text-secondary-600 dark:text-secondary-400">
                                                {{ $invoice->issue_date->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right space-y-1">
                                        <div class="font-bold text-secondary-900 dark:text-white">
                                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                        </div>
                                        <x-badge text="{{ $invoice->status === 'paid' ? 'Lunas' : ($invoice->status === 'overdue' ? 'Terlambat' : ucfirst($invoice->status)) }}" 
                                                 color="{{ $invoice->status === 'paid' ? 'green' : ($invoice->status === 'overdue' ? 'red' : 'yellow') }}" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <x-slot:footer>
                            <div class="flex items-center justify-between pt-4 border-t border-secondary-200/50 dark:border-secondary-700/50">
                                <span class="font-semibold text-secondary-900 dark:text-white">Total Nilai Invoice:</span>
                                <span class="text-lg font-bold text-red-600 dark:text-red-400">
                                    Rp {{ number_format($client->invoices->sum('total_amount'), 0, ',', '.') }}
                                </span>
                            </div>
                        </x-slot:footer>
                    </x-card>
                @else
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-6 border border-green-200/50 dark:border-green-700/50">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 bg-green-500/10 rounded-lg flex items-center justify-center">
                                <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                            </div>
                            <p class="text-sm text-green-800 dark:text-green-200">
                                Klien ini tidak memiliki invoice, sehingga aman untuk dihapus.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Confirmation Input --}}
                <div class="bg-secondary-50 dark:bg-secondary-800/50 rounded-2xl p-6 border border-secondary-200/50 dark:border-secondary-700/50">
                    <x-tooltip text="Ketik nama klien persis seperti yang tertera untuk mengkonfirmasi penghapusan" position="top" color="amber">
                        <label class="block text-sm font-semibold text-secondary-900 dark:text-white mb-3">
                            Untuk konfirmasi, ketik nama klien: <span class="text-red-600 dark:text-red-400 font-mono">{{ $client->name }}</span>
                        </label>
                    </x-tooltip>
                    <x-input 
                        wire:model.live="confirmationName"
                        placeholder="Ketik nama klien untuk konfirmasi..."
                        class="font-mono"
                    />
                </div>
            </div>
        @endif

        <x-slot:footer>
            <div class="flex justify-between items-center">
                <div class="text-sm text-secondary-600 dark:text-secondary-400">
                    @if($client && $client->invoices->count() > 0)
                        <span class="text-red-600 dark:text-red-400">⚠️ {{ $client->invoices->count() }} invoice akan dihapus</span>
                    @elseif($client)
                        <span class="text-green-600 dark:text-green-400">✅ Aman untuk dihapus</span>
                    @endif
                </div>
                <div class="flex gap-3">
                    <x-tooltip text="Batalkan proses penghapusan" position="top" color="secondary">
                        <x-button wire:click="$toggle('showDeleteModal')" color="secondary" icon="x-mark">
                            Batal
                        </x-button>
                    </x-tooltip>
                    <x-tooltip text="{{ !isset($confirmationName) || !$client || $confirmationName !== $client->name ? 'Ketik nama klien terlebih dahulu untuk mengaktifkan tombol hapus' : 'Hapus klien dan semua data terkait secara permanen' }}" 
                               position="top" 
                               color="{{ !isset($confirmationName) || !$client || $confirmationName !== $client->name ? 'amber' : 'red' }}">
                        <x-button 
                            wire:click="confirm" 
                            color="red" 
                            icon="trash"
                            spinner="confirm"
                            :disabled="!isset($confirmationName) || !$client || $confirmationName !== $client->name"
                        >
                            Hapus Klien
                        </x-button>
                    </x-tooltip>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
