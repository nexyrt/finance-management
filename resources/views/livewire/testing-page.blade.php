<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-dark-900 dark:text-dark-50">Testing Page</h1>
        <p class="text-gray-600 dark:text-gray-400 text-lg">Eksperimen komponen</p>
    </div>

    <x-button wire:click="loadAccounts" color="primary">Load Bank Accounts</x-button>

    {{-- Error --}}
    @if ($error)
        <p class="text-sm text-red-500">Error: {{ $error }}</p>
    @endif

    {{-- Content --}}
    @if (empty($accounts))
        <div class="flex items-center gap-3 text-sm text-dark-500 dark:text-dark-400">
            <x-icon name="building-library" class="w-5 h-5" />
            <span>Belum ada data. Klik tombol untuk load.</span>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($accounts as $account)
                <x-card class="hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-icon name="building-library" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm text-dark-600 dark:text-dark-400">{{ $account['label'] }}</p>
                            <p class="text-xs text-dark-400 dark:text-dark-500">ID: {{ $account['value'] }}</p>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif


</div>
