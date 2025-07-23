{{-- resources/views/livewire/clients/delete.blade.php --}}
<div>
    <x-modal wire="clientDeleteModal" id="client-delete-modal" center>
        <x-slot:header>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-icon name="trash" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Hapus Klien</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Konfirmasi penghapusan klien</p>
                </div>
            </div>
        </x-slot:header>

        @if($client)
            <div class="space-y-6">
                {{-- Client Info --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            @if($client->logo)
                                <img class="h-12 w-12 rounded-xl object-cover shadow-md" src="{{ $client->logo }}" alt="{{ $client->name }}">
                            @else
                                <div class="h-12 w-12 rounded-xl flex items-center justify-center shadow-md
                                    {{ $client->type === 'individual' 
                                        ? 'bg-gradient-to-br from-blue-500 to-blue-600' 
                                        : 'bg-gradient-to-br from-purple-500 to-purple-600' }}">
                                    <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                        class="w-6 h-6 text-white" />
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $client->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <x-badge text="{{ $client->type === 'individual' ? 'Individu' : 'Perusahaan' }}" 
                                         color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                                @if($client->NPWP)
                                    <span class="text-xs text-gray-600 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                        {{ $client->NPWP }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Warning Message --}}
                @if($client && $client->invoices && $client->invoices->count() > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200/50 dark:border-red-700/50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 bg-red-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-900 dark:text-red-100 mb-1">Perhatian!</h4>
                                <p class="text-sm text-red-800 dark:text-red-200 mb-3">
                                    Klien ini memiliki <strong>{{ $client->invoices->count() }} invoice</strong> yang akan ikut terhapus secara permanen.
                                </p>
                                <div class="bg-red-100 dark:bg-red-800/30 rounded-lg p-3">
                                    <div class="text-sm text-red-800 dark:text-red-200">
                                        <div class="font-medium mb-1">Total nilai invoice:</div>
                                        <div class="text-lg font-bold">Rp {{ number_format($client->invoices->sum('total_amount'), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200/50 dark:border-green-700/50">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                            </div>
                            <p class="text-sm text-green-800 dark:text-green-200">
                                Klien ini tidak memiliki invoice terkait.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <x-slot:footer>
            <div class="flex justify-end gap-3">
                <x-button wire:click="$toggle('clientDeleteModal')" color="secondary">
                    Batal
                </x-button>
                <x-button wire:click="confirm" x-on:click="$modalClose('client-delete-modal')" color="red" icon="trash" spinner="confirm">
                    Hapus Klien
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
