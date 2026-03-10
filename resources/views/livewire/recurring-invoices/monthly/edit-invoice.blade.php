<div class="space-y-6" x-data="monthlyInvoiceEditForm()" @click.away="closeAllDropdowns()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.ri_edit_invoice_page_title') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">{{ __('pages.ri_edit_invoice_page_desc', ['period' => \Carbon\Carbon::parse($invoice->scheduled_date)->format('F Y')]) }}</p>
        </div>
        <x-button href="{{ route('recurring-invoices.index') }}" wire:navigate color="zinc" size="sm">
            <x-slot:left>
                <x-icon name="arrow-left" class="w-4 h-4" />
            </x-slot:left>
            {{ __('common.back') }}
        </x-button>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <h3 class="text-sm font-semibold text-red-900 dark:text-red-200 mb-2">{{ __('pages.ri_please_fix') }}</h3>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Left Column (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Section 1: Invoice Info --}}
            <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">
                {{-- Section Header --}}
                <div class="px-6 py-4 border-b border-dark-100 dark:border-dark-700 flex items-center gap-3">
                    <div class="h-8 w-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_invoice_info_section') }}</h2>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_invoice_info_section_desc') }}</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Template + Client (Readonly) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Template Info (Readonly) --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">{{ __('pages.ri_from_template_label') }}</label>
                            <input type="text" value="{{ $invoice->template->template_name }}" readonly
                                class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-50 dark:bg-dark-700 text-dark-500 dark:text-dark-400 cursor-not-allowed">
                        </div>

                        {{-- Client (Readonly) --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">{{ __('pages.ri_billed_to_readonly_label') }}</label>
                            <input type="text" value="{{ $invoice->client->name }}" readonly
                                class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-50 dark:bg-dark-700 text-dark-500 dark:text-dark-400 cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Scheduled Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">{{ __('pages.ri_scheduled_date_required_label') }}</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                    <svg class="w-4 h-4 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="date" x-model="invoiceData.scheduled_date"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Items --}}
            @include('livewire.recurring-invoices.partials.items-repeater')

        </div>

        {{-- Right Column (1/3) Sticky --}}
        <div class="lg:col-span-1 lg:sticky lg:top-6">
            @include('livewire.recurring-invoices.partials.summary-section')
        </div>
    </div>
</div>

<script>
    function monthlyInvoiceEditForm() {
        return {
            invoiceData: {
                scheduled_date: null,
            },
            items: [],
            discount: {
                type: 'fixed',
                value: 0,
                reason: ''
            },
            clients: @js($this->clients),
            services: @js($this->services),
            existingInvoice: @js($this->existingInvoiceData),
            existingItems: @js($this->existingItems),
            existingDiscount: @js($this->existingDiscount),
            nextId: 1,
            selectOpen: false,
            selectSearch: '',
            itemSelectOpen: {},
            itemSelectSearch: {},
            serviceSelectOpen: {},
            serviceSelectSearch: {},
            saving: false,
            bulkCount: 1,
            buttonText: {
                default: '{{ __('pages.ri_update_invoice_btn') }}',
                saving: '{{ __('pages.ri_updating_invoice_btn') }}'
            },

            init() {
                // Load existing data
                this.invoiceData = {
                    ...this.existingInvoice
                };

                // Load items with ID tracking
                this.items = this.existingItems.map(item => ({
                    ...item,
                    id: this.nextId++
                }));

                // Initialize dropdown states
                this.items.forEach(item => {
                    this.itemSelectOpen[item.id] = false;
                    this.itemSelectSearch[item.id] = '';
                    this.serviceSelectOpen[item.id] = false;
                    this.serviceSelectSearch[item.id] = '';
                });

                // Load discount
                this.discount = {
                    ...this.existingDiscount
                };
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
            get netProfit() {
                return Math.max(0, this.totalProfit - this.discountAmount);
            },
            get taxDeposits() {
                return this.items.filter(i => i.is_tax_deposit).reduce((s, i) => s + (i.amount || 0), 0);
            },
            get discountAmount() {
                if (this.discount.type === 'fixed') return this.parse(this.discount.value);
                return Math.round((this.subtotal * (this.discount.value / 100)));
            },
            get totalAmount() {
                return Math.max(0, this.subtotal - this.discountAmount);
            },

            filter(arr, search, keys) {
                if (!search) return arr;
                const s = search.toLowerCase();
                return arr.filter(item => keys.some(k => item[k]?.toLowerCase().includes(s)));
            },

            selectClient(c) {
                // Client locked for monthly invoice
            },
            clearClient() {
                // Client locked for monthly invoice
            },

            selectItemClient(item, c) {
                item.client_id = c.id;
                item.client_name = c.name;
                this.itemSelectOpen[item.id] = false;
            },
            clearItemClient(item) {
                item.client_id = null;
                item.client_name = '';
            },

            selectService(item, s) {
                item.service_name = s.name;
                item.unit_price = s.formatted_price.replace('Rp ', '');
                this.serviceSelectOpen[item.id] = false;
                this.calculateItem(item);
            },

            addItem() {
                const item = {
                    id: this.nextId++,
                    client_id: @js($invoice->client_id),
                    client_name: @js($invoice->client->name),
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
                const count = parseInt(this.bulkCount) || 1;
                for (let i = 0; i < count; i++) {
                    this.addItem();
                }
                this.bulkCount = 1;
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
            formatInput(val) {
                const num = (val || '').toString().replace(/[^0-9]/g, '');
                if (!num) return '';
                return parseInt(num).toLocaleString('id-ID');
            },
            formatCurrency(val) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
            },
            closeAllDropdowns() {
                this.selectOpen = false;
                Object.keys(this.itemSelectOpen).forEach(k => this.itemSelectOpen[k] = false);
                Object.keys(this.serviceSelectOpen).forEach(k => this.serviceSelectOpen[k] = false);
            },

            async syncAndSave() {
                this.saving = true;
                try {
                    this.$wire.invoiceData = this.invoiceData;
                    this.$wire.items = this.items;

                    let discountValue = 0;
                    if (this.discount.type === 'percentage') {
                        discountValue = parseFloat(this.discount.value) || 0;
                    } else {
                        discountValue = this.parse(this.discount.value);
                    }

                    this.$wire.discount = {
                        type: this.discount.type,
                        value: discountValue,
                        reason: this.discount.reason || ''
                    };

                    await this.$wire.update();
                } catch (error) {
                    console.error(error);
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
