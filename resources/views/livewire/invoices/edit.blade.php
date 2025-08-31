<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-secondary-900 dark:text-dark-50">
            Edit Invoice {{ $invoice_number }}
        </h2>
        <p class="text-sm text-secondary-600 dark:text-dark-400 mt-1">Modify invoice details and line items</p>
    </div>

    <!-- Alpine.js Container -->
    <div x-data="{
        items: [
            { client_id: '', service_id: '', service_name: '', quantity: 1, price: 0, cogs_amount: 0 }
        ],
        clients: @js($clients),
        services: @js($services),
    
        addItem() {
            this.items.push({
                client_id: '',
                service_id: '',
                service_name: '',
                quantity: 1,
                price: 0,
                cogs_amount: 0
            });
        },
    
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
    
        setServiceDetails(index, serviceId) {
            const service = this.services.find(s => s.value == serviceId);
            if (service) {
                this.items[index].service_name = service.label;
                this.items[index].price = service.price;
            }
        },
    
        getItemTotal(item) {
            return (item.quantity || 0) * (item.price || 0);
        },
    
        getSubtotal() {
            return this.items.reduce((sum, item) => sum + this.getItemTotal(item), 0);
        },
    
        getTotalCogs() {
            return this.items.reduce((sum, item) => sum + (item.cogs_amount || 0), 0);
        },
    
        getDiscountAmount() {
            const subtotal = this.getSubtotal();
            if ($wire.discount_type === 'percentage') {
                return Math.floor((subtotal * $wire.discount_value) / 10000);
            } else {
                return parseInt($wire.discount_value) || 0;
            }
        },
    
        getGrandTotal() {
            return Math.max(0, this.getSubtotal() - this.getDiscountAmount());
        },
    
        getGrossProfit() {
            return this.getGrandTotal() - this.getTotalCogs();
        },
    
        submitInvoice() {
            const validItems = this.items.filter(item =>
                item.client_id &&
                item.service_name.trim() &&
                item.quantity > 0 &&
                item.price >= 0
            );
    
            if (validItems.length === 0) {
                alert('Please add at least one valid item');
                return;
            }
    
            $wire.saveInvoice(validItems, this.getSubtotal(), this.getGrandTotal());
        }
    }"
        x-on:populate-invoice-items.window="
        const data = Array.isArray($event.detail[0]) ? $event.detail[0] : $event.detail;
        items = data.length > 0 ? data : [{ client_id: '', service_id: '', service_name: '', quantity: 1, price: 0, cogs_amount: 0 }];
        console.log('Alpine populated with:', items);
    "
        x-init="// Trigger population after Alpine.js is ready
        $nextTick(() => {
            if (@js($invoice && $invoice->items->isNotEmpty())) {
                $wire.$dispatch('populate-invoice-items', @js(
    $invoice
        ? $invoice->items
            ->map(function ($item) {
                return [
                    'client_id' => $item->client_id,
                    'service_id' => '',
                    'service_name' => $item->service_name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'cogs_amount' => $item->cogs_amount ?? 0,
                ];
            })
            ->toArray()
        : [],
));
            }
        });">>

        <!-- Invoice Details -->
        <div
            class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-lg p-4 sm:p-6 mb-6">
            <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-50 mb-4">Invoice Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-input wire:model="invoice_number" label="Invoice Number" readonly />

                <x-select.styled wire:model="billed_to_id" :options="$clients" label="Bill To"
                    placeholder="Select client..." searchable required />

                <x-date wire:model="issue_date" label="Issue Date" required />
                <x-date wire:model="due_date" label="Due Date" required />
            </div>
        </div>

        <!-- Invoice Items (Alpine.js Managed) -->
        <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-lg mb-6">
            <div class="p-4 border-b border-secondary-200 dark:border-dark-600">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-dark-50">Invoice Items</h3>
                    <x-button x-on:click="addItem()" icon="plus" color="primary" size="sm">
                        Add Item
                    </x-button>
                </div>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-secondary-100 dark:divide-dark-700">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 space-y-4" x-transition:enter="transform transition ease-out duration-200"
                        x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">

                        <!-- Item Header -->
                        <div class="flex justify-between items-center">
                            <x-badge x-text="`Item ${index + 1}`" color="primary" />
                            <x-button.circle x-on:click="removeItem(index)" x-show="items.length > 1" icon="trash"
                                color="red" size="sm" />
                        </div>

                        <!-- Item Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                            <!-- Client -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                <select x-model="item.client_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select client...</option>
                                    <template x-for="client in clients">
                                        <option :value="client.value" x-text="client.label"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Service -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                <select x-model="item.service_id"
                                    x-on:change="setServiceDetails(index, item.service_id)"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select service...</option>
                                    <template x-for="service in services">
                                        <option :value="service.value" x-text="service.label"></option>
                                    </template>
                                </select>

                                <!-- Custom service name if no service selected -->
                                <input x-show="!item.service_id" x-model="item.service_name" type="text"
                                    placeholder="Custom service name"
                                    class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                                <input x-model="item.quantity" type="number" min="1"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                <input x-model="item.price" type="number" min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </div>

                        <!-- Additional Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- COGS -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">COGS Amount</label>
                                <input x-model="item.cogs_amount" type="number" min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>

                            <!-- Item Total (Read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Total</label>
                                <div class="px-3 py-2 bg-gray-100 rounded-md font-medium">
                                    Rp <span x-text="getItemTotal(item).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <!-- Item Profit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Profit</label>
                                <div class="px-3 py-2 rounded-md font-medium"
                                    x-bind:class="(getItemTotal(item) - (item.cogs_amount || 0)) >= 0 ?
                                        'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    Rp <span
                                        x-text="(getItemTotal(item) - (item.cogs_amount || 0)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Discount Section -->
        <div
            class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-lg p-4 sm:p-6 mb-6">
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
                        <x-input wire:model.live="discount_value" type="number" min="0"
                            label="Discount Amount" />
                    @endif
                </div>

                <x-input wire:model="discount_reason" label="Discount Reason" placeholder="Optional reason" />
            </div>

            <!-- Discount Preview (Alpine.js reactive) -->
            <div x-show="getDiscountAmount() > 0" x-transition
                class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-green-700 dark:text-green-300">Discount Applied:</span>
                    <span class="font-semibold text-green-800 dark:text-green-200">
                        -Rp <span x-text="getDiscountAmount().toLocaleString('id-ID')"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Summary (Alpine.js Reactive) -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-3">Invoice Summary</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-blue-600 dark:text-blue-300">Subtotal:</span>
                    <div class="font-semibold">Rp <span x-text="getSubtotal().toLocaleString('id-ID')"></span></div>
                </div>

                <div>
                    <span class="text-red-600 dark:text-red-400">Total COGS:</span>
                    <div class="font-semibold text-red-600">Rp <span
                            x-text="getTotalCogs().toLocaleString('id-ID')"></span></div>
                </div>

                <div x-show="getDiscountAmount() > 0">
                    <span class="text-green-600 dark:text-green-400">Discount:</span>
                    <div class="font-semibold text-green-600">-Rp <span
                            x-text="getDiscountAmount().toLocaleString('id-ID')"></span></div>
                </div>

                <div>
                    <span class="text-blue-600 dark:text-blue-300">Grand Total:</span>
                    <div class="font-bold text-lg text-blue-800 dark:text-blue-200">Rp <span
                            x-text="getGrandTotal().toLocaleString('id-ID')"></span></div>
                </div>
            </div>

            <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-700">
                <div class="flex justify-between items-center">
                    <span class="text-blue-600 dark:text-blue-300">Gross Profit:</span>
                    <span class="font-bold text-green-600 dark:text-green-400">
                        Rp <span x-text="getGrossProfit().toLocaleString('id-ID')"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="text-sm text-secondary-600 dark:text-dark-400">
                <span x-text="items.length"></span> item(s) •
                Real-time calculations powered by Alpine.js
            </div>

            <div class="flex flex-wrap gap-3">
                <x-button href="{{ route('invoices.index') }}" wire:navigate color="secondary" outline>
                    Cancel
                </x-button>

                <x-button x-on:click="submitInvoice()" color="primary" icon="check">
                    Update Invoice
                </x-button>
            </div>
        </div>
    </div>
</div>
