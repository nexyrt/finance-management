<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Testing Page
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">Eksperimen komponen</p>
        </div>
        <x-button wire:click="loadAccounts" color="primary" size="sm">
            <x-slot:left>
                <x-icon name="arrow-path" class="w-4 h-4" />
            </x-slot:left>
            Refresh
        </x-button>
    </div>

    {{-- Error --}}
    @if ($error)
        <div class="flex items-center gap-3 p-4 border border-red-200 dark:border-red-900/30 bg-red-50 dark:bg-red-900/10 rounded-xl">
            <x-icon name="exclamation-circle" class="w-5 h-5 text-red-500 flex-shrink-0" />
            <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
        </div>
    @endif

    {{-- Content --}}
    @if (empty($accounts))
        <div class="flex items-center gap-3 text-sm text-dark-500 dark:text-dark-400">
            <x-icon name="building-library" class="w-5 h-5" />
            <span>Belum ada data.</span>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($accounts as $account)
                <x-card class="hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-icon name="building-library" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ translate_text($account['label']) }}</p>
                            <p class="text-xs text-dark-400 dark:text-dark-500">ID: {{ $account['value'] }}</p>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
