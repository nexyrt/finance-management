<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-xl font-bold mb-4">Simple Repeater</h2>
    
    <x-button wire:click="addItem" icon="plus" class="mb-4">
        Add Item
    </x-button>
    
    <div class="space-y-3">
        @foreach($items as $index => $item)
            <div class="grid grid-cols-5 gap-3 items-end p-3 border rounded" wire:key="item-{{ $index }}">
                
                <x-input 
                    wire:model="items.{{ $index }}.name"
                    placeholder="Item name"
                    label="Name" />
                
                <x-input 
                    wire:model.blur="items.{{ $index }}.quantity"
                    type="number"
                    label="Qty" />
                
                <x-wireui-currency
                    wire:model.blur="items.{{ $index }}.price"
                    label="Price" />
                
                <div>
                    <label class="block text-sm mb-1">Total</label>
                    <div class="px-3 py-2 bg-gray-100 rounded">
                        Rp {{ number_format($item['total'], 0, ',', '.') }}
                    </div>
                </div>
                
                @if(count($items) > 1)
                    <x-button 
                        wire:click="removeItem({{ $index }})"
                        icon="trash"
                        color="red"
                        size="sm" />
                @endif
            </div>
        @endforeach
    </div>
    
    <x-button wire:click="save" color="primary" class="mt-4">
        Save Items
    </x-button>
</div>  