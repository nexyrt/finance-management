<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Edit Invoice {{ $invoice_number }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Modify invoice details and line items</p>
    </div>

    <!-- Status Change Warning -->
    @if ($this->getPreviewStatusProperty() !== $status)
        <div
            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-2">
                <x-icon name="exclamation-triangle" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                <span class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">
                    Status will change from <strong>{{ ucfirst($status) }}</strong> to
                    <strong>{{ ucfirst($this->getPreviewStatusProperty()) }}</strong>
                </span>
            </div>
        </div>
    @endif

    <!-- Invoice Details -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Invoice Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input wire:model="invoice_number" label="Invoice Number" readonly />

            <x-select.styled wire:model="billed_to_id" :options="$clients" label="Bill To" placeholder="Select client..."
                searchable required />

            <x-date wire:model="issue_date" label="Issue Date" required />

            <x-date wire:model="due_date" label="Due Date" required />
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Invoice Items</h3>
        </div>

        <!-- Table Header -->
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <div class="grid grid-cols-12 gap-4 p-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                <div class="col-span-1">#</div>
                <div class="col-span-3">Client</div>
                <div class="col-span-3">Service</div>
                <div class="col-span-1">Qty</div>
                <div class="col-span-2">Price</div>
                <div class="col-span-1">Total</div>
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

                    <div class="col-span-3 flex items-center">
                        <x-select.styled class="w-full" wire:model.blur="items.{{ $index }}.client_id" :options="$clients"
                            placeholder="Select client..." searchable />
                    </div>

                    <div class="col-span-3 space-y-2 ">
                        <x-select.styled wire:model.blur="items.{{ $index }}.service_id" :options="$services"
                            placeholder="Select service..." searchable />

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

                    <div class="col-span-1 flex items-center">
                        <x-input wire:model.blur="items.{{ $index }}.quantity" type="number" min="1"
                            class="text-center" />
                    </div>

                    <div class="col-span-2 flex items-center">
                        <x-wireui-currency prefix="Rp " wire:model.blur="items.{{ $index }}.price" />
                    </div>

                    <div class="col-span-1 flex items-center">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                            Rp {{ number_format($item['total'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="col-span-1 flex justify-center">
                        @if (count($items) > 1)
                            <x-button.circle wire:click="removeItem({{ $index }})" icon="trash" color="red"
                                size="sm" />
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
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Discount</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-select.styled wire:model.live="discount_type" :options="[
                ['label' => 'Fixed Amount', 'value' => 'fixed'],
                ['label' => 'Percentage', 'value' => 'percentage'],
            ]" label="Discount Type" />

            <div>
                @if ($discount_type === 'percentage')
                    <x-input wire:model.live="discount_value" type="number" min="0" max="10000"
                        step="100" label="Discount Value (%)" hint="Example: 1500 = 15%" />
                @else
                    <x-wireui-currency prefix="Rp " wire:model.blur="discount_value" label="Discount Amount" />
                @endif
            </div>

            <x-input wire:model="discount_reason" label="Discount Reason" placeholder="Optional reason" />
        </div>

        <!-- Discount Preview -->
        @if ($this->discountAmount > 0)
            <div
                class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-green-700 dark:text-green-300">
                        Discount Applied:
                        @if ($discount_type === 'percentage')
                            {{ number_format($discount_value / 100, 2) }}%
                        @else
                            Fixed Amount
                        @endif
                    </span>
                    <span class="font-semibold text-green-800 dark:text-green-200">
                        -Rp {{ number_format($this->discountAmount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer Actions -->
    <div class="flex justify-between items-center">
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

        <div class="flex space-x-3">
            <x-button href="{{ route('invoices.index') }}" color="secondary" outline>
                Cancel
            </x-button>
            <x-button wire:click="addItem" icon="plus" color="secondary">
                Add Item
            </x-button>
            <x-button wire:click="save" color="primary" icon="check">
                Update Invoice
            </x-button>
        </div>
    </div>
</div>
