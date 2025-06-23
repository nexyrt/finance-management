<section class="w-full p-6">
    <div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Form Bank Account Baru</h2>
        
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div  class="mb-6">
                {{ session('error') }}
            </div>
        @endif
        
        <form wire:submit="save" class="space-y-6">
            {{-- Account Name --}}
            <div>
                <flux:label>Nama Akun <span class="text-red-500">*</span></flux:label>
                <flux:input 
                    wire:model="account_name"
                    placeholder="PT ABC Company"
                    required
                />
                @error('account_name') 
                    <flux:error class="mt-1">{{ $message }}</flux:error> 
                @enderror
            </div>

            {{-- Account Number --}}
            <div>
                <flux:label>Nomor Rekening <span class="text-red-500">*</span></flux:label>
                <flux:input 
                    wire:model="account_number"
                    placeholder="1234567890"
                    required
                />
                @error('account_number') 
                    <flux:error class="mt-1">{{ $message }}</flux:error> 
                @enderror
            </div>

            {{-- Bank Name --}}
            <div>
                <flux:label>Nama Bank <span class="text-red-500">*</span></flux:label>
                <flux:input 
                    wire:model="bank_name"
                    placeholder="Bank Central Asia"
                    required
                />
                @error('bank_name') 
                    <flux:error class="mt-1">{{ $message }}</flux:error> 
                @enderror
            </div>

            {{-- Branch --}}
            <div>
                <flux:label>Cabang</flux:label>
                <flux:input 
                    wire:model="branch"
                    placeholder="Jakarta Pusat"
                />
                @error('branch') 
                    <flux:error class="mt-1">{{ $message }}</flux:error> 
                @enderror
            </div>

            {{-- Initial Balance dengan Currency Input --}}
            <div x-data="currencyInput({
                name: 'initial_balance',
                value: {{ $initial_balance }},
                placeholder: '50.000.000',
                wireModel: 'initial_balance'
            })">
                <flux:label>Saldo Awal <span class="text-red-500">*</span></flux:label>
                <flux:input.group>
                    <flux:input.group.prefix>Rp</flux:input.group.prefix>
                    <flux:input 
                        x-ref="input"
                        placeholder="50.000.000"
                        required
                        x-on:input="handleInput($event)"
                        x-on:keydown="restrictInput($event)"
                        x-on:paste="handlePaste($event)"
                    />
                </flux:input.group>
                <input type="hidden" name="initial_balance" x-ref="hiddenInput" :value="rawValue">
                @error('initial_balance') 
                    <flux:error class="mt-1">{{ $message }}</flux:error> 
                @enderror
                <flux:description>
                    Maksimum: <span x-text="getMaxValueFormatted()"></span>
                </flux:description>
            </div>

            {{-- Current Balance dengan Currency Input --}}
            <div x-data="currencyInput({
                name: 'current_balance',
                value: {{ $current_balance }},
                placeholder: '50.000.000',
                wireModel: 'current_balance'
            })">
                <flux:label>Saldo Saat Ini <span class="text-red-500">*</span></flux:label>
                <flux:input.group>
                    <flux:input.group.prefix>Rp</flux:input.group.prefix>
                    <flux:input 
                        x-ref="input"
                        placeholder="50.000.000"
                        required
                        x-on:input="handleInput($event)"
                        x-on:keydown="restrictInput($event)"
                        x-on:paste="handlePaste($event)"
                    />
                </flux:input.group>
                <input type="hidden" name="current_balance" x-ref="hiddenInput" :value="rawValue">
                @error('current_balance') 
                    <flux:error class="mt-1">{{ $message }}</flux:error> 
                @enderror
                <flux:description>
                    Maksimum: <span x-text="getMaxValueFormatted()"></span>
                </flux:description>
            </div>

            {{-- Debug Info --}}
            <div class="bg-gray-50 p-4 rounded-md">
                <h3 class="font-medium mb-2 text-gray-700">Debug - Raw Values (untuk database):</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <div>Initial Balance: <span class="font-mono">{{ $initial_balance }}</span></div>
                    <div>Current Balance: <span class="font-mono">{{ $current_balance }}</span></div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">
                    Simpan Bank Account
                </flux:button>
                
                <flux:button 
                    type="button" 
                    variant="ghost"
                    wire:click="setTestData"
                >
                    Isi Data Contoh
                </flux:button>
                
                <flux:button 
                    type="button" 
                    variant="ghost"
                    wire:click="resetForm"
                >
                    Reset Form
                </flux:button>
            </div>
        </form>

        {{-- Bank Accounts List --}}
        @if($bankAccounts->count() > 0)
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bank Accounts Terbaru</h3>
            
            <div class="space-y-3">
                @foreach($bankAccounts as $account)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $account->account_name }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ $account->bank_name }} 
                                    @if($account->branch)
                                        - {{ $account->branch }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600">No. Rek: {{ $account->account_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $account->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>