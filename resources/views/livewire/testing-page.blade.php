<div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ $invoiceId ? 'Edit Invoice' : 'Create Invoice' }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage invoice details and line items</p>
    </div>
    
    <!-- Invoice Details -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Invoice Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input 
                wire:model="invoice_number"
                label="Invoice Number"
                readonly />
            
            <x-select.styled 
                wire:model="billed_to_id"
                :options="$clients"
                label="Bill To"
                placeholder="Select client..."
                searchable 
                required />
            
            <x-date 
                wire:model="issue_date"
                label="Issue Date"
                required />
            
            <x-date 
                wire:model="due_date"
                label="Due Date"
                required />
        </div>
    </div>
    
    <!-- Invoice Items -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-6">
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
                <div class="grid grid-cols-12 gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" wire:key="item-{{ $index }}">
                    
                    <!-- Row Number -->
                    <div class="col-span-1 flex items-center">
                        <x-badge :text="$index + 1" color="primary" size="sm" />
                    </div>
                    
                    <!-- Client Select -->
                    <div class="col-span-3">
                        <x-select.styled 
                            wire:model.blur="items.{{ $index }}.client_id"
                            :options="$clients"
                            placeholder="Select client..."
                            searchable />
                    </div>
                    
                    <!-- Service Select -->
                    <div class="col-span-3 space-y-2">
                        <x-select.styled 
                            wire:model.blur="items.{{ $index }}.service_id"
                            :options="$services"
                            placeholder="Select service..."
                            searchable />
                        
                        @if(empty($item['service_id']))
                            <x-input 
                                wire:model.blur="items.{{ $index }}.service_name"
                                placeholder="Custom service name"
                                class="text-sm" />
                        @else
                            <div class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded text-xs text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                Template: {{ $item['service_name'] }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Quantity -->
                    <div class="col-span-1">
                        <x-input 
                            wire:model.blur="items.{{ $index }}.quantity"
                            type="number"
                            min="1"
                            class="text-center" />
                    </div>
                    
                    <!-- Price -->
                    <div class="col-span-2">
                        <x-wireui-currency
                            wire:model.blur="items.{{ $index }}.price" />
                    </div>
                    
                    <!-- Total -->
                    <div class="col-span-1 flex items-center">
                        <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">
                            Rp {{ number_format($item['total'], 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="col-span-1 flex justify-center">
                        @if(count($items) > 1)
                            <x-button.circle 
                                wire:click="removeItem({{ $index }})"
                                icon="trash"
                                color="red"
                                size="sm" />
                        @endif
                    </div>
                </div>
                
            @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <p class="font-medium">No items added yet</p>
                    <p class="text-sm">Click "Add Item" to start building your invoice</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Footer Actions -->
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Total {{ count($items) }} item(s) • Grand Total: 
            <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
        </div>
        
        <div class="flex space-x-3">
            <x-button wire:click="resetAll" color="secondary" outline>
                Reset All
            </x-button>
            <x-button wire:click="addItem" icon="plus" color="secondary">
                Add Item
            </x-button>
            <x-button wire:click="save" color="primary" icon="check">
                {{ $invoiceId ? 'Update Invoice' : 'Create Invoice' }}
            </x-button>
        </div>
    </div>
</div>