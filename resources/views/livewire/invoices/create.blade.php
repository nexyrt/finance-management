<div>
    <x-modal wire="showModal" size="7xl" center persistent>
        <x-slot:title>
            Create New Invoice
        </x-slot:title>

        <div class="space-y-6">
            {{-- Invoice Details --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Invoice Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <x-input wire:model="invoice_number" label="Invoice Number" />

                    <x-select.styled wire:model.live="billed_to_id" :options="$clients" label="Bill To"
                        placeholder="Select client..." searchable required />

                    <x-date wire:model.live="issue_date" label="Issue Date" required />

                    <x-date wire:model="due_date" label="Due Date" required />
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Invoice Items</h3>
                    </div>
                </div>

                <!-- Table Header -->
                <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-12 gap-4 p-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <div class="col-span-1">#</div>
                        <div class="col-span-2">Client</div>
                        <div class="col-span-3">Service</div>
                        <div class="col-span-1">Qty</div>
                        <div class="col-span-2">Price</div>
                        <div class="col-span-2">Total</div>
                        <div class="col-span-1 text-center">Actions</div>
                    </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($items as $index => $item)
                        <div class="grid grid-cols-12 gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            wire:key="item-{{ $index }}">

                            <div class="col-span-1 flex items-center">
                                <x-badge :text="$index + 1" color="primary" size="sm" />
                            </div>

                            <div class="col-span-2">
                                <x-select.styled class="w-full" wire:model.blur="items.{{ $index }}.client_id"
                                    :options="$clients" placeholder="Select client..." searchable />
                            </div>

                            <div class="col-span-3 space-y-2">
                                <x-select.styled wire:model.blur="items.{{ $index }}.service_id"
                                    :options="$services" placeholder="Select service..." searchable />

                                @if (empty($item['service_id']))
                                    <x-input wire:model.blur="items.{{ $index }}.service_name"
                                        placeholder="Custom service name" class="text-sm" />
                                @else
                                    <div
                                        class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded text-xs text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        Template: {{ $item['service_name'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-span-1">
                                <x-input wire:model.blur="items.{{ $index }}.quantity" type="number"
                                    min="1" class="text-center" />
                            </div>

                            <div class="col-span-2">
                                <x-wireui-currency prefix="Rp " wire:model.blur="items.{{ $index }}.price" />
                            </div>

                            <div class="col-span-2">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="col-span-1 flex justify-center">
                                @if (count($items) > 1)
                                    <x-button.circle wire:click="removeItem({{ $index }})" icon="trash"
                                        color="red" size="sm" />
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <p class="font-medium">No items found</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Discount Section -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Discount (Optional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-select.styled wire:model.live="discount_type" :options="[
                        ['label' => 'Fixed Amount', 'value' => 'fixed'],
                        ['label' => 'Percentage', 'value' => 'percentage'],
                    ]" label="Discount Type" />

                    <div>
                        @if ($discount_type === 'percentage')
                            <x-input wire:model.live="discount_value" type="number" min="0" max="100"
                                step="0.1" label="Discount Value (%)" />
                        @else
                            <x-wireui-currency wire:model.live="discount_value" label="Discount Amount" />
                        @endif
                    </div>

                    <x-input wire:model="discount_reason" label="Discount Reason" placeholder="Optional reason" />
                </div>

                @if ($this->discountAmount > 0)
                    <div
                        class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-green-700 dark:text-green-300">Discount Applied:</span>
                            <span class="font-semibold text-green-800 dark:text-green-200">
                                -Rp {{ number_format($this->discountAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <!-- Summary -->
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Total {{ count($items) }} item(s) • Subtotal:
                        <span class="font-medium text-gray-900 dark:text-gray-100">Rp
                            {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($this->discountAmount > 0)
                        <div>Discount:
                            <span class="font-medium text-green-600 dark:text-green-400">-Rp
                                {{ number_format($this->discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div>Grand Total:
                        <span class="font-bold text-lg text-gray-900 dark:text-gray-100">Rp
                            {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <x-button wire:click="addItem" icon="plus" color="primary" size="sm">
                        Add Item
                    </x-button>
                    <x-button wire:click="$set('showModal', false)" color="secondary">
                        Cancel
                    </x-button>
                    <x-button wire:click="save" color="primary" icon="check">
                        Create Invoice
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
