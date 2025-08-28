<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-secondary-900 dark:text-dark-50">
            Edit Invoice {{ $invoice_number }}
        </h2>
        <p class="text-sm text-secondary-600 dark:text-dark-400 mt-1">Modify invoice details and line items</p>
    </div>

    <!-- Status Change Warning -->
    @if ($this->getPreviewStatusProperty() !== $status)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
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
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-50 mb-4">Invoice Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-input wire:model="invoice_number" label="Invoice Number" readonly />

            <x-select.styled wire:model="billed_to_id" :options="$clients" label="Bill To" placeholder="Select client..."
                searchable required />

            <x-date wire:model="issue_date" label="Issue Date" required />

            <x-date wire:model="due_date" label="Due Date" required />
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-lg mb-6">
        <div class="p-4 border-b border-secondary-200 dark:border-dark-600">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-50">Invoice Items</h3>
                <x-button wire:click="addItem" icon="plus" color="primary" size="sm">
                    Add Item
                </x-button>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden xl:block">
            <!-- Table Header -->
            <div class="bg-secondary-50 dark:bg-dark-900 border-b border-secondary-200 dark:border-dark-600">
                <div class="grid grid-cols-13 gap-4 p-4 text-sm font-semibold text-secondary-700 dark:text-dark-200">
                    <div class="col-span-1">#</div>
                    <div class="col-span-3">Client</div>
                    <div class="col-span-3">Service</div>
                    <div class="col-span-1">Qty</div>
                    <div class="col-span-2">Price</div>
                    <div class="col-span-2">Total</div>
                    <div class="col-span-1 text-center">Actions</div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="divide-y divide-secondary-100 dark:divide-dark-700">
                @forelse($items as $index => $item)
                    <div class="grid grid-cols-13 gap-4 p-4 hover:bg-secondary-50 dark:hover:bg-dark-700 transition-colors"
                        wire:key="item-{{ $index }}">

                        <div class="col-span-1 flex items-center">
                            <x-badge :text="$index + 1" color="primary" size="sm" />
                        </div>

                        <div class="col-span-3 flex items-center">
                            <div class="w-full">
                                <x-select.styled wire:model.blur="items.{{ $index }}.client_id" :options="$clients"
                                    placeholder="Select client..." searchable />
                            </div>
                        </div>

                        <div class="col-span-3 space-y-2">
                            <x-select.styled wire:model.blur="items.{{ $index }}.service_id" :options="$services"
                                placeholder="Select service..." searchable />

                            @if (empty($item['service_id']))
                                <x-input wire:model.blur="items.{{ $index }}.service_name"
                                    placeholder="Custom service name" class="text-sm" />
                            @else
                                <div class="px-3 py-1 bg-primary-50 dark:bg-primary-900/20 rounded text-xs text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800">
                                    Template: {{ $item['service_name'] }}
                                </div>
                            @endif
                        </div>

                        <div class="col-span-1 flex items-center">
                            <div class="w-full">
                                <x-input wire:model.blur="items.{{ $index }}.quantity" type="number" min="1"
                                    class="text-center" />
                            </div>
                        </div>

                        <div class="col-span-2 flex items-center">
                            <div class="w-full">
                                <x-wireui-currency prefix="Rp " wire:model.blur="items.{{ $index }}.price" />
                            </div>
                        </div>

                        <div class="col-span-2 flex items-center">
                            <div class="font-semibold text-secondary-900 dark:text-dark-100 text-sm">
                                Rp {{ number_format($item['total'], 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="col-span-1 flex justify-center items-center">
                            @if (count($items) > 1)
                                <x-button.circle wire:click="removeItem({{ $index }})" icon="trash" color="red"
                                    size="sm" />
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-secondary-500 dark:text-dark-400">
                        <p class="font-medium">No items found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="xl:hidden divide-y divide-secondary-100 dark:divide-dark-700">
            @forelse($items as $index => $item)
                <div class="p-4 space-y-4" wire:key="item-mobile-{{ $index }}">
                    <div class="flex justify-between items-center">
                        <x-badge :text="'Item ' . ($index + 1)" color="primary" />
                        @if (count($items) > 1)
                            <x-button.circle wire:click="removeItem({{ $index }})" icon="trash"
                                color="red" size="sm" />
                        @endif
                    </div>

                    <div class="space-y-3">
                        <x-select.styled wire:model.blur="items.{{ $index }}.client_id"
                            :options="$clients" placeholder="Select client..." searchable label="Client" />

                        <x-select.styled wire:model.blur="items.{{ $index }}.service_id"
                            :options="$services" placeholder="Select service..." searchable label="Service" />

                        @if (empty($item['service_id']))
                            <x-input wire:model.blur="items.{{ $index }}.service_name"
                                placeholder="Custom service name" label="Service Name" />
                        @else
                            <div class="px-3 py-1 bg-primary-50 dark:bg-primary-900/20 rounded text-xs text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800">
                                Template: {{ $item['service_name'] }}
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            <x-input wire:model.blur="items.{{ $index }}.quantity" type="number"
                                min="1" label="Quantity" />
                            <x-wireui-currency prefix="Rp " wire:model.blur="items.{{ $index }}.price" 
                                label="Price" />
                        </div>

                        <div class="bg-secondary-50 dark:bg-dark-700 p-3 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-secondary-600 dark:text-dark-300">Total:</span>
                                <span class="font-semibold text-secondary-900 dark:text-dark-100">
                                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-secondary-500 dark:text-dark-400">
                    <p class="font-medium">No items found</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Discount Section -->
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-lg p-4 sm:p-6 mb-6">
        <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-50 mb-4">Discount</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
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
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div class="text-sm text-secondary-600 dark:text-dark-400 space-y-1">
            <div>Total {{ count($items) }} item(s) • Subtotal:
                <span class="font-medium text-secondary-900 dark:text-dark-100">Rp
                    {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($this->discountAmount > 0)
                <div>Discount:
                    <span class="font-medium text-green-600 dark:text-green-400">-Rp
                        {{ number_format($this->discountAmount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div>Grand Total:
                <span class="font-bold text-lg text-secondary-900 dark:text-dark-100">Rp
                    {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <x-button href="{{ route('invoices.index') }}" wire:navigate color="secondary dark:dark hover:secondary" outline>
                Cancel
            </x-button>
            <x-button wire:click="addItem" icon="plus" color="primary" size="sm" class="lg:hidden">
                Add Item
            </x-button>
            <x-button wire:click="save" color="primary" icon="check">
                Update Invoice
            </x-button>
        </div>
    </div>
</div>