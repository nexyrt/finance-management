<div class="max-w-full mx-auto p-6" x-data="monthlyInvoiceEditForm()" @click.away="closeAllDropdowns()">
    {{-- Header --}}
    <div class="mb-6 space-y-1">
        <h1
            class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
            {{ __('pages.ri_edit_invoice_page_title') }}
        </h1>
        <p class="text-dark-600 dark:text-dark-400 text-lg">{{ __('pages.ri_edit_invoice_page_desc', ['period' => \Carbon\Carbon::parse($invoice->scheduled_date)->format('F Y')]) }}</p>
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
            <h3 class="text-sm font-semibold text-red-900 dark:text-red-200 mb-2">{{ __('pages.ri_please_fix') }}</h3>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Invoice Info --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
            <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">{{ __('pages.ri_invoice_info_section') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Template Info (Readonly) --}}
                <div>
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.ri_from_template_label') }}</label>
                    <input type="text" value="{{ $invoice->template->template_name }}" readonly
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-100 dark:bg-dark-700 text-dark-900 dark:text-dark-50 cursor-not-allowed">
                </div>

                {{-- Client (Readonly) --}}
                <div>
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.ri_billed_to_readonly_label') }}</label>
                    <input type="text" value="{{ $invoice->client->name }}" readonly
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-100 dark:bg-dark-700 text-dark-900 dark:text-dark-50 cursor-not-allowed">
                </div>

                {{-- Scheduled Date --}}
                <div>
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.ri_scheduled_date_required_label') }}</label>
                    <input type="date" x-model="invoiceData.scheduled_date"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Items Section - Reuse partial --}}
        @include('livewire.recurring-invoices.partials.items-repeater')

        {{-- Summary Section - Reuse partial --}}
        @include('livewire.recurring-invoices.partials.summary-section')
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
