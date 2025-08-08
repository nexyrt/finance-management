{{-- resources/views/livewire/bank-transactions/types/manual-transaction.blade.php --}}

<div>
    {{-- Modal --}}
    <x-modal wire="showModal" title="Add Manual Transaction" size="2xl" blur>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Bank Account Selection --}}
                <div class="md:col-span-2">
                    <x-select.styled 
                        label="Bank Account"
                        placeholder="Select bank account"
                        :options="$bankAccounts"
                        wire:model="bank_account_id"
                        searchable
                        invalidate
                    />
                    @error('bank_account_id') 
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Transaction Type --}}
                <x-select.styled 
                    label="Transaction Type"
                    placeholder="Select type"
                    :options="$transactionTypes"
                    wire:model="transaction_type"
                    invalidate
                />
                @error('transaction_type') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                {{-- Amount Input --}}
                <div>
                    <x-wireui-currency 
                        label="Amount"
                        placeholder="0"
                        prefix="Rp"
                        thousands="."
                        decimal=","
                        precision="0"
                        wire:model="amount"
                        invalidate
                    />
                    @error('amount') 
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Transaction Date --}}
                <x-date 
                    label="Transaction Date"
                    wire:model="transaction_date"
                    helpers
                    invalidate
                />
                @error('transaction_date') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                {{-- Reference Number --}}
                <div class="flex space-x-3">
                    <div class="flex-1">
                        <x-input 
                            label="Reference Number"
                            placeholder="Auto-generated"
                            wire:model="reference_number"
                            invalidate
                        />
                    </div>
                    <div class="pt-6">
                        <x-button.circle 
                            color="secondary" 
                            outline 
                            icon="arrow-path"
                            wire:click="generateReference"
                        />
                    </div>
                </div>
                @error('reference_number') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <x-input 
                    label="Description" 
                    placeholder="Enter transaction description"
                    wire:model="description"
                    hint="Brief description of the transaction"
                    invalidate
                />
                @error('description') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <x-button 
                    color="secondary dark:dark" 
                    outline
                    wire:click="closeModal"
                >
                    Cancel
                </x-button>
                <x-button 
                    color="primary"
                    icon="check"
                    wire:click="save"
                    loading="save"
                >
                    Save Transaction
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>