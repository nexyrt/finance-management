<div class="max-w-7xl mx-auto p-6" x-data="{
    // Invoice data
    invoice: {
        client_id: null,
        client_name: '',
        issue_date: '',
        due_date: ''
    },

    // Items repeater
    items: [],
    nextId: 1,

    // Clients data dari server
    clients: @js($this->clients),

    // Select dropdown state
    selectOpen: false,
    selectSearch: '',

    // Loading state
    saving: false,

    // Initialize
    init() {
        this.addItem();
        // Set default dates
        this.invoice.issue_date = new Date().toISOString().split('T')[0];
        this.invoice.due_date = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    },

    // Filtered clients based on search
    get filteredClients() {
        if (!this.selectSearch) return this.clients;
        const search = this.selectSearch.toLowerCase();
        return this.clients.filter(client =>
            client.name.toLowerCase().includes(search) ||
            (client.email && client.email.toLowerCase().includes(search))
        );
    },

    // Select client
    selectClient(client) {
        this.invoice.client_id = client.id;
        this.invoice.client_name = client.name;
        this.selectOpen = false;
        this.selectSearch = '';
    },

    // Clear selection
    clearClient() {
        this.invoice.client_id = null;
        this.invoice.client_name = '';
    },

    // Add item
    addItem() {
        this.items.push({
            id: this.nextId++,
            service_name: '',
            quantity: 1,
            unit_price: '',
            amount: 0,
            cogs_amount: '',
            profit: 0,
            is_tax_deposit: false
        });
    },

    // Remove item
    removeItem(index) {
        this.items.splice(index, 1);
    },

    // Calculate amount & profit untuk satu item
    calculateItem(item) {
        const quantity = parseInt(item.quantity) || 1;
        const unitPrice = this.parseAmount(item.unit_price);
        const cogsAmount = this.parseAmount(item.cogs_amount);

        item.amount = quantity * unitPrice;
        item.profit = item.amount - cogsAmount;
    },

    // Parse currency
    parseAmount(value) {
        if (!value) return 0;
        return parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;
    },

    // Format currency
    formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
    },

    // Calculate subtotal (exclude tax deposits)
    get subtotal() {
        return this.items
            .filter(item => !item.is_tax_deposit)
            .reduce((sum, item) => sum + (item.amount || 0), 0);
    },

    // Calculate total profit (exclude tax deposits)
    get totalProfit() {
        return this.items
            .filter(item => !item.is_tax_deposit)
            .reduce((sum, item) => sum + (item.profit || 0), 0);
    },

    // Calculate tax deposits
    get taxDeposits() {
        return this.items
            .filter(item => item.is_tax_deposit)
            .reduce((sum, item) => sum + (item.amount || 0), 0);
    },

    // Sync to Livewire and save
    async syncAndSave() {
        this.saving = true;
        $wire.set('invoice', this.invoice);
        $wire.set('items', this.items);
        await $wire.save();
        this.saving = false;
    }
}" @click.away="selectOpen = false">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Invoice</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Invoice form dengan Alpine.js & Custom Select Search</p>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div
            class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-green-900 dark:text-green-200">Success!</h3>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Error Message --}}
    @if (session()->has('error'))
        <div
            class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-900 dark:text-red-200">Error!</h3>
                <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <h3 class="text-sm font-semibold text-red-900 dark:text-red-200 mb-2">Please fix the following errors:</h3>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Invoice Information</h2>

                <div class="space-y-4">
                    {{-- Client Select --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Client *
                        </label>
                        <div class="relative">
                            {{-- Selected Client Display --}}
                            <div x-show="!invoice.client_id" @click="selectOpen = !selectOpen"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-400 cursor-pointer hover:border-blue-400 transition">
                                Select a client...
                            </div>

                            <div x-show="invoice.client_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-between">
                                <span class="text-gray-900 dark:text-white" x-text="invoice.client_name"></span>
                                <button @click="clearClient()" type="button"
                                    class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Dropdown --}}
                            <div x-show="selectOpen" x-transition
                                class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-80 overflow-hidden">

                                {{-- Search Input --}}
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                    <input type="text" x-model="selectSearch" @click.stop
                                        placeholder="Search clients..."
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                {{-- Options List --}}
                                <div class="overflow-y-auto max-h-64">
                                    <template x-for="client in filteredClients" :key="client.id">
                                        <div @click="selectClient(client)"
                                            class="px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <div class="flex items-center gap-3">
                                                {{-- Logo/Avatar --}}
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                                    <span x-text="client.name.charAt(0).toUpperCase()"></span>
                                                </div>
                                                {{-- Info --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 dark:text-white truncate"
                                                        x-text="client.name"></div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate"
                                                        x-text="client.email || '-'"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Empty State --}}
                                    <div x-show="filteredClients.length === 0"
                                        class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p>No clients found</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Issue Date *
                            </label>
                            <input type="date" x-model="invoice.issue_date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Due Date *
                            </label>
                            <input type="date" x-model="invoice.due_date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Invoice Items</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add services or products</p>
                    </div>
                    <button @click="addItem()" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </button>
                </div>

                {{-- Items List --}}
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300"
                                        x-text="`Item #${index + 1}`"></span>
                                    {{-- Tax Deposit Badge --}}
                                    <span x-show="item.is_tax_deposit"
                                        class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded">
                                        Tax Deposit
                                    </span>
                                </div>
                                <button x-show="items.length > 1" @click="removeItem(index)" type="button"
                                    class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Service Name --}}
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Service Name *
                                </label>
                                <input type="text" x-model="item.service_name" placeholder="Enter service name"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            {{-- Quantity & Unit Price --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Quantity *
                                    </label>
                                    <input type="number" x-model.number="item.quantity" @input="calculateItem(item)"
                                        min="1"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Unit Price *
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                        <input type="text" x-model="item.unit_price" @input="calculateItem(item)"
                                            x-mask:dynamic="$money($input, '.')" placeholder="0"
                                            class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            {{-- Amount & COGS --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Amount
                                    </label>
                                    <div
                                        class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400"
                                            x-text="formatCurrency(item.amount)"></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        COGS (Cost)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                        <input type="text" x-model="item.cogs_amount" @input="calculateItem(item)"
                                            x-mask:dynamic="$money($input, '.')" placeholder="0"
                                            class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            {{-- Profit & Tax Deposit --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Profit
                                    </label>
                                    <div class="px-3 py-2 rounded-lg"
                                        :class="item.is_tax_deposit ? 'bg-gray-100 dark:bg-gray-700' :
                                            'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800'">
                                        <span class="text-sm font-bold"
                                            :class="item.is_tax_deposit ? 'text-gray-400' : 'text-green-600 dark:text-green-400'"
                                            x-text="item.is_tax_deposit ? '-' : formatCurrency(item.profit)"></span>
                                    </div>
                                </div>

                                <div class="flex items-end">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" x-model="item.is_tax_deposit"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Is Tax Deposit?
                                        </span>
                                    </label>
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
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Summary</h2>

                <div class="space-y-3">
                    {{-- Subtotal --}}
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-semibold text-gray-900 dark:text-white"
                            x-text="formatCurrency(subtotal)"></span>
                    </div>

                    {{-- Tax Deposits --}}
                    <div x-show="taxDeposits > 0"
                        class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Tax Deposits</span>
                        <span class="font-semibold text-yellow-600 dark:text-yellow-400"
                            x-text="formatCurrency(taxDeposits)"></span>
                    </div>

                    {{-- Total Profit --}}
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Profit</span>
                        <span class="font-semibold text-green-600 dark:text-green-400"
                            x-text="formatCurrency(totalProfit)"></span>
                    </div>

                    {{-- Total Items --}}
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Items</span>
                        <span class="font-semibold text-gray-900 dark:text-white" x-text="items.length"></span>
                    </div>
                </div>

                {{-- Actions --}}
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
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="saving ? 'Saving...' : 'Save Invoice'"></span>
                    </button>
                    <button type="button"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-semibold">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Debug Section --}}
    <div
        class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
        <h3 class="text-sm font-semibold text-yellow-900 dark:text-yellow-200 mb-3">🐛 Debug Data</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 overflow-auto">
                <h4 class="text-xs font-semibold mb-2">Invoice:</h4>
                <pre class="text-xs text-gray-800 dark:text-gray-200" x-text="JSON.stringify(invoice, null, 2)"></pre>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 overflow-auto max-h-96">
                <h4 class="text-xs font-semibold mb-2">Items:</h4>
                <pre class="text-xs text-gray-800 dark:text-gray-200" x-text="JSON.stringify(items, null, 2)"></pre>
            </div>
        </div>
    </div>
</div>
