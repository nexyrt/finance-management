<div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Invoice Items</h2>
        <div class="flex items-center gap-2">
            <input type="number" x-model="bulkCount" min="1" max="50"
                class="w-16 px-2 py-2 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            <button @click="bulkAddItems()" type="button"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Item
            </button>
        </div>
    </div>

    {{-- Table Header --}}
    <div class="hidden lg:grid lg:grid-cols-24 gap-3 px-4 py-3 bg-dark-100 dark:bg-dark-900 rounded-lg mb-2">
        <div class="col-span-1 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">#
        </div>
        <div class="col-span-4 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">Client</div>
        <div class="col-span-5 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">Service</div>
        <div class="col-span-2 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
            Qty</div>
        <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">Unit Price
        </div>
        <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">Amount</div>
        <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">COGS</div>
        <div class="col-span-2 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
            Tax</div>
        <div class="col-span-1 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
        </div>
    </div>

    <div class="space-y-2">
        <template x-for="(item, index) in items" :key="item.id">
            <div>
                {{-- Desktop: Grid Row --}}
                <div
                    class="hidden lg:grid lg:grid-cols-24 gap-3 p-4 bg-dark-50 dark:bg-dark-900/50 rounded-lg border border-dark-200 dark:border-dark-700 items-center">
                    {{-- No (col-span-1) --}}
                    <div class="col-span-1 text-center">
                        <span class="text-sm font-semibold text-dark-700 dark:text-dark-300" x-text="index + 1"></span>
                    </div>

                    {{-- Client (col-span-4) --}}
                    <div class="col-span-4">
                        <div class="relative">
                            <div x-show="!item.client_id" @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                                class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition truncate">
                                Select...
                            </div>
                            <div x-show="item.client_id"
                                class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between gap-1">
                                <span class="text-dark-900 dark:text-dark-50 truncate text-xs"
                                    x-text="item.client_name"></span>
                                <button @click="clearItemClient(item)" type="button"
                                    class="text-dark-400 hover:text-red-500 transition flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="itemSelectOpen[item.id]" x-transition
                                class="absolute z-50 mt-1 w-64 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                    <input type="text" x-model="itemSelectSearch[item.id]" @click.stop
                                        placeholder="Search..."
                                        class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <div class="overflow-y-auto max-h-64">
                                    <template x-for="client in filteredItemClients(item.id)" :key="client.id">
                                        <div @click="selectItemClient(item, client)"
                                            class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                                    <span x-text="client.name.charAt(0).toUpperCase()"></span>
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
                                    <div x-show="filteredItemClients(item.id).length === 0"
                                        class="px-3 py-6 text-center text-dark-500 dark:text-dark-400">
                                        <p class="text-xs">No clients found</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Service (col-span-5) --}}
                    <div class="col-span-5">
                        <div class="relative flex gap-1" @click.away="serviceSelectOpen[item.id] = false">
                            <input type="text" x-model="item.service_name"
                                class="flex-1 px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Type service name...">
                            <button @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]" type="button"
                                class="px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-600 dark:text-dark-400 hover:bg-dark-50 dark:hover:bg-dark-700 transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="serviceSelectOpen[item.id]" x-transition
                                class="absolute z-50 mt-1 w-96 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                    <input type="text" x-model="serviceSelectSearch[item.id]" @click.stop
                                        placeholder="Search services..."
                                        class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <div class="overflow-y-auto max-h-64">
                                    <template x-for="service in filteredItemServices(item.id)" :key="service.id">
                                        <div @click="selectService(item, service)"
                                            class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                            <div class="font-medium text-sm text-dark-900 dark:text-dark-50"
                                                x-text="service.name"></div>
                                            <div class="flex items-center justify-between mt-0.5">
                                                <span class="text-xs text-primary-600 dark:text-primary-400"
                                                    x-text="service.type"></span>
                                                <span class="text-xs font-semibold text-dark-700 dark:text-dark-300"
                                                    x-text="service.formatted_price"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="filteredItemServices(item.id).length === 0"
                                        class="px-3 py-6 text-center text-dark-500 dark:text-dark-400">
                                        <p class="text-xs">No services found</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Qty (col-span-2) --}}
                    <div class="col-span-2">
                        <input type="number" x-model="item.quantity" @input="calculateItem(item)" min="1"
                            class="w-full px-2 py-1.5 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    {{-- Unit Price (col-span-3) --}}
                    <div class="col-span-3">
                        <input type="text" x-model="item.unit_price"
                            @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                            class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="0">
                    </div>

                    {{-- Amount (col-span-3) --}}
                    <div class="col-span-3">
                        <div class="px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-100 dark:bg-dark-900 text-dark-900 dark:text-dark-50 font-semibold text-right"
                            x-text="formatCurrency(item.amount)"></div>
                    </div>

                    {{-- COGS (col-span-3) --}}
                    <div class="col-span-3">
                        <input type="text" x-model="item.cogs_amount"
                            @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                            class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="0">
                    </div>

                    {{-- Tax Checkbox (col-span-2) --}}
                    <div class="col-span-2 flex justify-center">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" x-model="item.is_tax_deposit"
                                class="rounded border-dark-300 dark:border-dark-600 text-primary-600 focus:ring-2 focus:ring-primary-500">
                            <span class="text-xs text-dark-600 dark:text-dark-400">Tax</span>
                        </label>
                    </div>

                    {{-- Remove Button (col-span-1) --}}
                    <div class="col-span-1 flex justify-center">
                        <button @click="removeItem(index)" type="button"
                            class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Mobile: Card Layout --}}
                <div
                    class="block lg:hidden p-4 bg-dark-50 dark:bg-dark-900/50 rounded-lg border border-dark-200 dark:border-dark-700 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-dark-900 dark:text-dark-50"
                                x-text="'#' + (index + 1)"></span>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" x-model="item.is_tax_deposit"
                                    class="rounded border-dark-300 dark:border-dark-600 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                <span class="text-xs text-dark-600 dark:text-dark-400">Tax Deposit</span>
                            </label>
                        </div>
                        <button @click="removeItem(index)" type="button"
                            class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-1 rounded transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2">
                        {{-- Client --}}
                        <div>
                            <label
                                class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Client</label>
                            <div class="relative">
                                <div x-show="!item.client_id"
                                    @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition">
                                    Select client...
                                </div>
                                <div x-show="item.client_id"
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between">
                                    <span class="text-dark-900 dark:text-dark-50" x-text="item.client_name"></span>
                                    <button @click="clearItemClient(item)" type="button"
                                        class="text-dark-400 hover:text-red-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="itemSelectOpen[item.id]" x-transition
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                    <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                        <input type="text" x-model="itemSelectSearch[item.id]" @click.stop
                                            placeholder="Search clients..."
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div class="overflow-y-auto max-h-64">
                                        <template x-for="client in filteredItemClients(item.id)"
                                            :key="client.id">
                                            <div @click="selectItemClient(item, client)"
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
                                        <div x-show="filteredItemClients(item.id).length === 0"
                                            class="px-4 py-8 text-center text-dark-500 dark:text-dark-400">
                                            <p class="text-sm">No clients found</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Service --}}
                        <div>
                            <label class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Service
                                Name</label>
                            <div class="relative flex gap-2" @click.away="serviceSelectOpen[item.id] = false">
                                <input type="text" x-model="item.service_name"
                                    class="flex-1 px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="Type service name...">
                                <button @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]"
                                    type="button"
                                    class="px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-600 dark:text-dark-400 hover:bg-dark-50 dark:hover:bg-dark-700 transition flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="serviceSelectOpen[item.id]" x-transition
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                    <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                        <input type="text" x-model="serviceSelectSearch[item.id]" @click.stop
                                            placeholder="Search services..."
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div class="overflow-y-auto max-h-64">
                                        <template x-for="service in filteredItemServices(item.id)"
                                            :key="service.id">
                                            <div @click="selectService(item, service)"
                                                class="px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                <div class="font-medium text-dark-900 dark:text-dark-50"
                                                    x-text="service.name"></div>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="text-xs text-primary-600 dark:text-primary-400"
                                                        x-text="service.type"></span>
                                                    <span
                                                        class="text-sm font-semibold text-dark-700 dark:text-dark-300"
                                                        x-text="service.formatted_price"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="filteredItemServices(item.id).length === 0"
                                            class="px-4 py-8 text-center text-dark-500 dark:text-dark-400">
                                            <p class="text-sm">No services found</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Qty & Unit Price --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label
                                    class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Quantity</label>
                                <input type="number" x-model="item.quantity" @input="calculateItem(item)"
                                    min="1"
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Unit
                                    Price</label>
                                <input type="text" x-model="item.unit_price"
                                    @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="0">
                            </div>
                        </div>

                        {{-- Amount & COGS --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label
                                    class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Amount</label>
                                <div class="px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-100 dark:bg-dark-900 text-dark-900 dark:text-dark-50 font-semibold"
                                    x-text="formatCurrency(item.amount)"></div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">COGS</label>
                                <input type="text" x-model="item.cogs_amount"
                                    @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
        </template>

        <div x-show="items.length === 0"
            class="p-12 text-center border-2 border-dashed border-dark-300 dark:border-dark-600 rounded-lg">
            <svg class="w-16 h-16 mx-auto text-dark-400 dark:text-dark-500 mb-3" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-dark-600 dark:text-dark-400 text-sm">No items yet. Click "Add Item" to get started.
            </p>
        </div>
    </div>
</div>