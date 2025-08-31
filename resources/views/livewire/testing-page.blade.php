<div class="max-w-3xl mx-auto p-6 space-y-6">
    <h2 class="text-xl font-bold">Testing Page - Edit Invoice</h2>

    <!-- Invoice Selection -->
    <div class="grid grid-cols-1 gap-4">
        <x-select.styled wire:model.live="selectedInvoiceId" :options="$invoiceOptions" label="Select Invoice to Edit"
            placeholder="Choose an invoice..." searchable />
    </div>

    <!-- Current Invoice Info -->
    @if ($invoice)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="font-semibold text-green-800">Current Invoice: #{{ $invoice->invoice_number }}</h3>
            <p class="text-green-700">Client: {{ $invoice->client->name }}</p>
            <p class="text-green-700">Items: {{ $invoice->items->count() }}</p>
        </div>
    @endif

    <!-- Alpine.js Repeater -->
    <div x-data="{
        services: [
            { name: '', price: 0, quantity: 1 }
        ],
    
        addService() {
            this.services.push({ name: '', price: 0, quantity: 1 });
        },
    
        removeService(index) {
            this.services.splice(index, 1);
            if (this.services.length === 0) {
                this.services = [{ name: '', price: 0, quantity: 1 }];
            }
        },
    
        getTotal() {
            return this.services.reduce((sum, service) => {
                return sum + ((parseInt(service.price) || 0) * (parseInt(service.quantity) || 1));
            }, 0);
        },
    
        submitToLivewire() {
            const validServices = this.services.filter(s => s.name.trim() && s.price > 0);
            $wire.saveServices(validServices);
        }
    }"
        x-on:populate-repeater.window="
        console.log('Event received:', $event.detail);
        const data = Array.isArray($event.detail[0]) ? $event.detail[0] : $event.detail;
        services = data.length > 0 ? data : [{ name: '', price: 0, quantity: 1 }];
        console.log('Services after populate:', services);
    "
        x-on:reset-repeater.window="
        services = [{ name: '', price: 0, quantity: 1 }];
    ">

        <!-- Controls -->
        <div class="flex justify-between items-center">
            <h3 class="font-semibold">Invoice Items</h3>
            <x-button x-on:click="addService()" icon="plus" color="green" size="sm">
                Add Item
            </x-button>
        </div>

        <!-- Services List -->
        <div class="space-y-3">
            <template x-for="(service, index) in services" :key="index">
                <div class="p-4 border rounded-lg bg-gray-50"
                    x-transition:enter="transform transition ease-out duration-200"
                    x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">

                    <div class="grid grid-cols-12 gap-3 items-center">
                        <!-- Service Name -->
                        <div class="col-span-5">
                            <x-input x-model="service.name" label="Service Name" placeholder="Enter service name" />
                        </div>

                        <!-- Quantity -->
                        <div class="col-span-2">
                            <x-input x-model="service.quantity" label="Qty" type="number" min="1"
                                placeholder="1" />
                        </div>

                        <!-- Unit Price -->
                        <div class="col-span-3">
                            <x-input x-model="service.price" label="Unit Price" type="number" min="0"
                                placeholder="0" />
                        </div>

                        <!-- Remove Button -->
                        <div class="col-span-2 pt-6">
                            <x-button.circle x-on:click="removeService(index)" x-show="services.length > 1"
                                icon="trash" color="red" size="sm" />
                        </div>
                    </div>

                    <!-- Item Total -->
                    <div x-show="service.name || service.price > 0"
                        class="mt-2 text-sm text-gray-600 flex justify-between">
                        <span x-text="service.name || 'Unnamed service'"></span>
                        <span class="font-medium">
                            <span x-text="service.quantity || 1"></span> × Rp <span
                                x-text="parseInt(service.price || 0).toLocaleString('id-ID')"></span> =
                            <strong>Rp <span
                                    x-text="((service.quantity || 1) * (parseInt(service.price) || 0)).toLocaleString('id-ID')"></span></strong>
                        </span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Grand Total -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="font-semibold">Grand Total:</span>
                <span class="text-lg font-bold text-blue-600">
                    Rp <span x-text="getTotal().toLocaleString('id-ID')"></span>
                </span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-3">
            <x-button x-on:click="submitToLivewire()" color="primary" icon="check">
                Update Invoice
            </x-button>

            <!-- Debug -->
            <details class="flex-1">
                <summary class="cursor-pointer text-sm text-gray-500">Debug Data</summary>
                <pre class="text-xs bg-gray-100 p-2 rounded mt-1" x-text="JSON.stringify(services, null, 2)"></pre>
            </details>
        </div>
    </div>
</div>
