<div>
    <div class="mx-auto p-6" x-data="invoiceForm()" @click.away="closeAllDropdowns()">
        {{-- Header --}}
        <div class="mb-6 space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
                Create Invoice
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">Multi-client invoice with dynamic items</p>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div
                class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div
                class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <h3 class="text-sm font-semibold text-red-900 dark:text-red-200 mb-2">Please fix:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Invoice Info --}}
                <div
                    class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
                    <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Invoice Information</h2>
                    <div class="space-y-4">
                        {{-- Client Select --}}
                        <div>
                            <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Billed To
                                (Owner) *</label>
                            <div class="relative">
                                <div x-show="!invoice.client_id" @click="selectOpen = !selectOpen"
                                    class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition">
                                    Select owner/client...
                                </div>
                                <div x-show="invoice.client_id"
                                    class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between">
                                    <span class="text-dark-900 dark:text-dark-50" x-text="invoice.client_name"></span>
                                    <button @click="clearClient()" type="button"
                                        class="text-dark-400 hover:text-red-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                {{-- Dropdown --}}
                                <div x-show="selectOpen" x-transition
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                    <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                        <input type="text" x-model="selectSearch" @click.stop
                                            placeholder="Search clients..."
                                            class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div class="overflow-y-auto max-h-64">
                                        <template x-for="client in filteredClients" :key="client.id">
                                            <div @click="selectClient(client)"
                                                class="px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                                        <span x-text="client.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-dark-900 dark:text-dark-50 truncate"
                                                            x-text="client.name"></div>
                                                        <div class="text-sm text-dark-500 dark:text-dark-400 truncate"
                                                            x-text="client.email || '-'"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="filteredClients.length === 0"
                                            class="px-4 py-8 text-center text-dark-500 dark:text-dark-400">
                                            <p class="text-sm">No clients found</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">💡 All items will auto-fill with
                                this client</p>
                        </div>

                        {{-- Dates --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Issue Date
                                    *</label>
                                <input type="date" x-model="invoice.issue_date"
                                    class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Due Date
                                    *</label>
                                <input type="date" x-model="invoice.due_date"
                                    class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items Section --}}
                <div
                    class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Invoice Items</h2>
                            <p class="text-sm text-dark-500 dark:text-dark-400">Services per company</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="showBulkAdd = !showBulkAdd" type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Bulk Add
                            </button>
                            <button @click="addItem()" type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Item
                            </button>
                        </div>
                    </div>

                    {{-- Bulk Add Panel --}}
                    <div x-show="showBulkAdd" x-transition
                        class="mb-4 p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">How many
                                    items?</label>
                                <input type="number" x-model.number="bulkAddCount" min="1" max="100"
                                    class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            <button @click="bulkAddItems()" type="button"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                                Add <span x-text="bulkAddCount"></span> Items
                            </button>
                            <button @click="showBulkAdd = false" type="button"
                                class="px-4 py-2 border border-dark-300 dark:border-dark-600 text-dark-700 dark:text-dark-300 rounded-lg hover:bg-dark-50 dark:hover:bg-dark-700">
                                Cancel
                            </button>
                        </div>
                    </div>

                    {{-- Items List --}}
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div
                                class="p-4 bg-dark-50 dark:bg-dark-900 rounded-lg border border-dark-200 dark:border-dark-700">
                                {{-- Header --}}
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-dark-900 dark:text-dark-50"
                                            x-text="`Item #${index + 1}`"></span>
                                        <span x-show="item.is_tax_deposit"
                                            class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded">Tax
                                            Deposit</span>
                                    </div>
                                    <button x-show="items.length > 1" @click="removeItem(index)" type="button"
                                        class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- ROW 1: Client | Service Template | Service Name | Unit Price --}}
                                <div class="grid grid-cols-12 gap-3 mb-3">
                                    {{-- Client Select (3 cols) --}}
                                    <div class="col-span-12 md:col-span-3">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Client
                                            *</label>
                                        <div class="relative" @click.away="itemSelectOpen[item.id] = false">
                                            <div x-show="!item.client_id"
                                                @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                                                class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition text-sm">
                                                Select...
                                            </div>
                                            <div x-show="item.client_id"
                                                class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between">
                                                <span class="text-dark-900 dark:text-dark-50 text-sm truncate"
                                                    x-text="item.client_name"></span>
                                                <button @click="clearItemClient(item)" type="button"
                                                    class="text-dark-400 hover:text-red-500 ml-2 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            {{-- Client Dropdown --}}
                                            <div x-show="itemSelectOpen[item.id]" x-transition
                                                class="absolute z-40 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-64 overflow-hidden">
                                                <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                                    <input type="text" x-model="itemSelectSearch[item.id]"
                                                        @click.stop placeholder="Search..."
                                                        class="w-full px-3 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                </div>
                                                <div class="overflow-y-auto max-h-48">
                                                    <template x-for="client in filteredItemClients(item.id)"
                                                        :key="client.id">
                                                        <div @click="selectItemClient(item, client)"
                                                            class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition">
                                                            <div class="flex items-center gap-2">
                                                                <div
                                                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                                                    <span
                                                                        x-text="client.name.charAt(0).toUpperCase()"></span>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate"
                                                                        x-text="client.name"></div>
                                                                    <div class="text-xs text-dark-500 dark:text-dark-400 truncate"
                                                                        x-text="client.email || '-'"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Service Template (2 cols) --}}
                                    <div class="col-span-12 md:col-span-2">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Template</label>
                                        <div class="relative" @click.away="serviceSelectOpen[item.id] = false">
                                            <button type="button"
                                                @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]"
                                                class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-700 dark:text-dark-300 hover:border-primary-400 transition flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                                </svg>
                                            </button>
                                            {{-- Service Dropdown --}}
                                            <div x-show="serviceSelectOpen[item.id]" x-transition
                                                class="absolute z-40 mt-1 w-80 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-64 overflow-hidden">
                                                <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                                    <input type="text" x-model="serviceSelectSearch[item.id]"
                                                        @click.stop placeholder="Search services..."
                                                        class="w-full px-3 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                </div>
                                                <div class="overflow-y-auto max-h-48">
                                                    <template x-for="service in filteredItemServices(item.id)"
                                                        :key="service.id">
                                                        <div @click="selectService(item, service)"
                                                            class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate"
                                                                        x-text="service.name"></div>
                                                                    <div class="text-xs text-dark-500 dark:text-dark-400"
                                                                        x-text="service.type || 'Service'"></div>
                                                                </div>
                                                                <span
                                                                    class="ml-3 text-sm font-semibold text-primary-600 dark:text-primary-400 flex-shrink-0"
                                                                    x-text="service.formatted_price"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredItemServices(item.id).length === 0"
                                                        class="px-4 py-6 text-center text-dark-500 dark:text-dark-400">
                                                        <p class="text-sm">No services found</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Service Name (4 cols) --}}
                                    <div class="col-span-12 md:col-span-4">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Service
                                            Name *</label>
                                        <input type="text" x-model="item.service_name"
                                            placeholder="Enter service name"
                                            class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>

                                    {{-- Unit Price (3 cols) --}}
                                    <div class="col-span-12 md:col-span-3">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Unit
                                            Price *</label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-dark-500 dark:text-dark-400 text-sm">Rp</span>
                                            <input type="text" x-model="item.unit_price"
                                                @input="calculateItem(item)" x-mask:dynamic="$money($input, '.')"
                                                placeholder="0"
                                                class="w-full pl-10 pr-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                    </div>
                                </div>

                                {{-- ROW 2: Quantity | Amount | COGS | Profit | Tax Deposit --}}
                                <div class="grid grid-cols-12 gap-3">
                                    {{-- Quantity (2 cols - smallest) --}}
                                    <div class="col-span-6 md:col-span-2">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Qty
                                            *</label>
                                        <input type="number" x-model.number="item.quantity"
                                            @input="calculateItem(item)" min="1"
                                            class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>

                                    {{-- Amount (3 cols - read only) --}}
                                    <div class="col-span-6 md:col-span-3">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Amount</label>
                                        <div
                                            class="px-3 py-2 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg flex items-center h-[42px]">
                                            <span
                                                class="text-sm font-bold text-primary-600 dark:text-primary-400 truncate"
                                                x-text="formatCurrency(item.amount)"></span>
                                        </div>
                                    </div>

                                    {{-- COGS (3 cols) --}}
                                    <div class="col-span-6 md:col-span-3">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">COGS</label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 -translate-y-1/2 text-dark-500 dark:text-dark-400 text-sm">Rp</span>
                                            <input type="text" x-model="item.cogs_amount"
                                                @input="calculateItem(item)" x-mask:dynamic="$money($input, '.')"
                                                placeholder="0"
                                                class="w-full pl-10 pr-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                    </div>

                                    {{-- Profit (2 cols - read only) --}}
                                    <div class="col-span-6 md:col-span-2">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Profit</label>
                                        <div class="px-3 py-2 rounded-lg flex items-center h-[42px]"
                                            :class="item.is_tax_deposit ? 'bg-dark-100 dark:bg-dark-700' :
                                                'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800'">
                                            <span class="text-sm font-bold truncate"
                                                :class="item.is_tax_deposit ? 'text-dark-400' :
                                                    'text-green-600 dark:text-green-400'"
                                                x-text="item.is_tax_deposit ? '-' : formatCurrency(item.profit)"></span>
                                        </div>
                                    </div>

                                    {{-- Tax Deposit Checkbox (2 cols) --}}
                                    <div class="col-span-12 md:col-span-2">
                                        <label
                                            class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">&nbsp;</label>
                                        <div class="flex items-center h-[42px]">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" x-model="item.is_tax_deposit"
                                                    class="w-4 h-4 text-primary-600 border-dark-300 rounded focus:ring-primary-500">
                                                <span
                                                    class="text-sm font-medium text-dark-900 dark:text-dark-50 whitespace-nowrap">Tax
                                                    Deposit</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">Summary</h2>
                    <div class="space-y-3">
                        <div
                            class="flex justify-between items-center pb-3 border-b border-dark-200 dark:border-dark-700">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Subtotal</span>
                            <span class="font-semibold text-dark-900 dark:text-dark-50"
                                x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div x-show="taxDeposits > 0"
                            class="flex justify-between items-center pb-3 border-b border-dark-200 dark:border-dark-700">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Tax Deposits</span>
                            <span class="font-semibold text-yellow-600 dark:text-yellow-400"
                                x-text="formatCurrency(taxDeposits)"></span>
                        </div>
                        <div
                            class="flex justify-between items-center pb-3 border-b border-dark-200 dark:border-dark-700">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Total Profit</span>
                            <span class="font-semibold text-green-600 dark:text-green-400"
                                x-text="formatCurrency(totalProfit)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">Total Items</span>
                            <span class="font-semibold text-dark-900 dark:text-dark-50" x-text="items.length"></span>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button @click="syncAndSave()" type="button" :disabled="saving"
                            :class="saving ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition inline-flex items-center justify-center gap-2 font-semibold disabled:hover:bg-blue-600">
                            <svg x-show="!saving" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-text="saving ? 'Saving...' : 'Save Invoice'"></span>
                        </button>
                        <button type="button"
                            class="w-full px-4 py-3 border border-dark-300 dark:border-dark-600 text-dark-700 dark:text-dark-300 rounded-lg hover:bg-dark-50 dark:hover:bg-dark-700 transition font-semibold">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                invoice: {
                    client_id: null,
                    client_name: '',
                    issue_date: '',
                    due_date: ''
                },
                items: [],
                nextId: 1,
                bulkAddCount: 1,
                showBulkAdd: false,
                saving: false,
                clients: @js($this->clients),
                services: @js($this->services),
                selectOpen: false,
                selectSearch: '',
                itemSelectOpen: {},
                itemSelectSearch: {},
                serviceSelectOpen: {},
                serviceSelectSearch: {},

                init() {
                    this.addItem();
                    const t = new Date();
                    this.invoice.issue_date = t.toISOString().split('T')[0];
                    this.invoice.due_date = new Date(t.getTime() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                },

                get filteredClients() {
                    return this.filter(this.clients, this.selectSearch, ['name', 'email']);
                },
                filteredItemClients(id) {
                    return this.filter(this.clients, this.itemSelectSearch[id], ['name', 'email']);
                },
                filteredItemServices(id) {
                    return this.filter(this.services, this.serviceSelectSearch[id], ['name', 'type']);
                },
                get subtotal() {
                    return this.items.filter(i => !i.is_tax_deposit).reduce((s, i) => s + (i.amount || 0), 0);
                },
                get totalProfit() {
                    return this.items.filter(i => !i.is_tax_deposit).reduce((s, i) => s + (i.profit || 0), 0);
                },
                get taxDeposits() {
                    return this.items.filter(i => i.is_tax_deposit).reduce((s, i) => s + (i.amount || 0), 0);
                },

                filter(arr, search, keys) {
                    if (!search) return arr;
                    const s = search.toLowerCase();
                    return arr.filter(item => keys.some(k => item[k]?.toLowerCase().includes(s)));
                },

                selectClient(c) {
                    this.invoice.client_id = c.id;
                    this.invoice.client_name = c.name;
                    this.selectOpen = false;
                    this.selectSearch = '';
                    this.items.forEach(i => {
                        if (!i.client_id) {
                            i.client_id = c.id;
                            i.client_name = c.name;
                        }
                    });
                },
                clearClient() {
                    this.invoice.client_id = null;
                    this.invoice.client_name = '';
                },

                selectItemClient(item, c) {
                    item.client_id = c.id;
                    item.client_name = c.name;
                    this.itemSelectOpen[item.id] = false;
                    this.itemSelectSearch[item.id] = '';
                },
                clearItemClient(item) {
                    item.client_id = null;
                    item.client_name = '';
                },

                selectService(item, s) {
                    item.service_name = s.name;
                    item.unit_price = s.formatted_price.replace('Rp ', '');
                    this.serviceSelectOpen[item.id] = false;
                    this.serviceSelectSearch[item.id] = '';
                    this.calculateItem(item);
                },

                addItem() {
                    const item = {
                        id: this.nextId++,
                        client_id: this.invoice.client_id || null,
                        client_name: this.invoice.client_name || '',
                        service_name: '',
                        quantity: 1,
                        unit_price: '',
                        amount: 0,
                        cogs_amount: '',
                        profit: 0,
                        is_tax_deposit: false
                    };
                    this.items.push(item);
                    this.itemSelectOpen[item.id] = false;
                    this.itemSelectSearch[item.id] = '';
                    this.serviceSelectOpen[item.id] = false;
                    this.serviceSelectSearch[item.id] = '';
                },

                bulkAddItems() {
                    for (let i = 0; i < parseInt(this.bulkAddCount || 1); i++) this.addItem();
                    this.showBulkAdd = false;
                    this.bulkAddCount = 1;
                },

                removeItem(index) {
                    const id = this.items[index].id;
                    ['itemSelectOpen', 'itemSelectSearch', 'serviceSelectOpen', 'serviceSelectSearch'].forEach(o =>
                        delete this[o][id]);
                    this.items.splice(index, 1);
                },

                calculateItem(item) {
                    const qty = parseInt(item.quantity) || 1,
                        price = this.parse(item.unit_price),
                        cogs = this.parse(item.cogs_amount);
                    item.amount = qty * price;
                    item.profit = item.amount - cogs;
                },

                parse(val) {
                    return parseInt((val || '').toString().replace(/[^0-9]/g, '')) || 0;
                },
                formatCurrency(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
                },

                closeAllDropdowns() {
                    this.selectOpen = false;
                    Object.keys(this.itemSelectOpen).forEach(k => this.itemSelectOpen[k] = false);
                    Object.keys(this.serviceSelectOpen).forEach(k => this.serviceSelectOpen[k] = false);
                },

                // ✅ FIX: Gunakan @this dari Livewire
                async syncAndSave() {
                    this.saving = true;
                    try {
                        // Set data ke Livewire menggunakan @this
                        this.$wire.invoice = this.invoice;
                        this.$wire.items = this.items;

                        // Call save method
                        await this.$wire.save();
                    } catch (error) {
                        console.error('Save failed:', error);
                    } finally {
                        this.saving = false;
                    }
                }
            }
        }
    </script>
</div>
