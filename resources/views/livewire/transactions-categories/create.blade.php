<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="blue" icon="plus" class="w-full sm:w-auto">
        Add Category
    </x-button>

    {{-- Modal --}}
    <x-modal title="Create Transaction Category" center wire size="xl">
        <form id="category-create" wire:submit="save" class="space-y-4">
            
            {{-- Type Selector --}}
            <div>
                <x-select.styled label="Type *" wire:model.live="type" :options="[
                    ['label' => '📈 Income', 'value' => 'income'],
                    ['label' => '📉 Expense', 'value' => 'expense'],
                    ['label' => '⚖️ Adjustment', 'value' => 'adjustment'],
                    ['label' => '🔄 Transfer', 'value' => 'transfer'],
                ]" placeholder="Pilih type category..." required />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Code Input --}}
                <div>
                    <x-input label="Code *" wire:model="code" hint="Uppercase, no spaces (e.g., INC_PAYMENT)"
                        required />
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
                        :options="$this->parentOptions" placeholder="Select parent or leave empty for top-level" />
                    <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                        <x-icon name="information-circle" class="w-4 h-4 inline" />
                        Leave empty to create a parent category
                    </p>
                </div>
            @elseif($type)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex gap-3">
                        <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p class="font-medium">No parent categories available</p>
                            <p class="mt-1">This will be created as a parent category for {{ ucfirst($type) }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Helper Text --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    <strong>Tips:</strong>
                </p>
                <ul class="mt-2 text-sm text-dark-500 dark:text-dark-400 space-y-1 list-disc list-inside">
                    <li>Use descriptive codes: <code class="text-xs bg-gray-200 dark:bg-gray-800 px-1 py-0.5 rounded">INC_CLIENT_PAYMENT</code></li>
                    <li>Parent categories group related child categories</li>
                    <li>Child categories inherit their parent's type</li>
                </ul>
            </div>

        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button color="gray" wire:click="$set('modal', false)">
                    Cancel
                </x-button>
                <x-button type="submit" form="category-create" color="blue" loading="save" icon="check">
                    Save Category
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>