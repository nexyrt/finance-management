<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-dark-900 dark:text-dark-50">Testing Page</h1>
        <p class="text-gray-600 dark:text-gray-400 text-lg">Eksperimen komponen</p>
    </div>

    {{-- Test 1: Ambil Data dari Model --}}
    <x-card>
        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Test 1: Ambil Data dari Model</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                    Klik tombol → load bank accounts (sama logika dengan <code>/api/bank-accounts</code>)
                </p>
            </div>

            <x-button wire:click="loadBankAccounts" loading="loadBankAccounts" color="blue" icon="arrow-down-tray">
                Load Bank Accounts
            </x-button>

            @if (count($bankAccounts) > 0)
                <div class="space-y-1">
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ count($bankAccounts) }} accounts loaded:</p>
                    @foreach ($bankAccounts as $account)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-dark-700 rounded-lg text-sm">
                            <span class="font-mono text-xs text-dark-400 w-6">{{ $account['value'] }}</span>
                            <span class="text-dark-900 dark:text-dark-50">{{ $account['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-card>

    {{-- Test 2: Google Translate --}}
    <x-card>
        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Test 2: Google Translate</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                    Test <code>stichoza/google-translate-php</code>. Parameter <code>:name</code>, <code>:count</code>, dll terjaga.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <x-textarea wire:model="inputText" label="Input (Bahasa Indonesia)" rows="4"
                        placeholder="Contoh: Selamat datang :name, Anda memiliki :count notifikasi" />

                    <x-select.styled wire:model.live="targetLang" label="Target Language"
                        :options="[
                            ['value' => 'zh', 'label' => 'Chinese (zh)'],
                            ['value' => 'en', 'label' => 'English (en)'],
                            ['value' => 'ja', 'label' => 'Japanese (ja)'],
                            ['value' => 'ko', 'label' => 'Korean (ko)'],
                        ]" />

                    <x-button wire:click="translateText" loading="translateText" color="green" icon="language">
                        Translate
                    </x-button>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-dark-900 dark:text-dark-50">Hasil Terjemahan</label>
                    <div class="min-h-32 p-3 bg-gray-50 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-xl text-sm text-dark-900 dark:text-dark-50 whitespace-pre-wrap">
                        {{ $translatedText ?: '(belum ada hasil)' }}
                    </div>
                </div>
            </div>
        </div>
    </x-card>
</div>
