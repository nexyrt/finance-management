<div>
    <x-modal :title="$categoryId ? 'Edit Category' : 'Edit Category'" wire size="xl" center>
        @if ($categoryId)
            <form id="category-update" wire:submit="save" class="space-y-4">

                {{-- Warning jika tidak bisa ubah type --}}
                @if (!$this->canChangeType)
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <x-icon name="exclamation-triangle"
                                class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" />
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <p class="font-medium">Type cannot be changed</p>
                                <p class="mt-1">This category has {{ $transactionsCount }} transactions</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Type Selector --}}
                <div>
                    <x-select.styled label="Type *" wire:model.live="type" :disabled="!$this->canChangeType"
                        :options="[
                            ['label' => '📈 Income', 'value' => 'income'],
                            ['label' => '📉 Expense', 'value' => 'expense'],
                            ['label' => '⚖️ Adjustment', 'value' => 'adjustment'],
                            ['label' => '🔄 Transfer', 'value' => 'transfer'],
                        ]" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Code Input --}}
                    <div>
                        <x-input label="Code *" wire:model="code"
                            hint="Uppercase, no spaces (e.g., INC_PAYMENT)" required />
                    </div>

                    {{-- Label Input --}}
                    <div>
                        <x-input label="Label *" wire:model="label" required />
                    </div>
                </div>

                {{-- Parent Selector (conditional) --}}
                @if ($type && count($this->parentOptions) > 0)
                    <div>
                        <x-select.styled label="Parent Category (Optional)" wire:model="parent_code"
                            :options="$this->parentOptions"
                            placeholder="Select parent or leave empty for top-level" />
                        <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                            <x-icon name="information-circle" class="w-4 h-4 inline" />
                            Leave empty to keep as parent category
                        </p>
                    </div>
                @elseif($type)
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <x-icon name="information-circle"
                                class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                <p class="font-medium">This is a parent category</p>
                                <p class="mt-1">No other parent categories available for
                                    {{ ucfirst($type) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Usage Stats --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <p class="text-sm font-medium text-dark-900 dark:text-dark-50 mb-2">Usage Statistics</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-dark-500 dark:text-dark-400">Transactions:</span>
                            <span class="font-bold text-dark-900 dark:text-dark-50 ml-2">
                                {{ $transactionsCount }}
                            </span>
                        </div>
                        <div>
                            <span class="text-dark-500 dark:text-dark-400">Children:</span>
                            <span class="font-bold text-dark-900 dark:text-dark-50 ml-2">
                                {{ $childrenCount }}
                            </span>
                        </div>
                    </div>
                </div>

            </form>

            <x-slot:footer>
                <div class="flex justify-between w-full">
                    <x-button color="gray" wire:click="$set('modal', false)">
                        Cancel
                    </x-button>
                    <x-button type="submit" form="category-update" color="green" loading="save" icon="check">
                        Update Category
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>