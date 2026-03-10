<div class="space-y-6" x-data="monthlyInvoiceForm()" @click.away="closeAllDropdowns()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.ri_create_invoice_page_title') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">{{ __('pages.ri_create_invoice_page_desc') }}</p>
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

            {{-- Section 1: Invoice Details --}}
            <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-dark-200 dark:border-white/10 overflow-hidden">
                {{-- Section Header --}}
                <div class="px-6 py-4 border-b border-dark-100 dark:border-white/8 flex items-center gap-3">
                    <div class="h-8 w-8 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_invoice_details_section') }}</h2>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_invoice_details_section_desc') }}</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Template Selector (full width) --}}
                    <div class="space-y-1.5" id="monthly-tmpl-wrapper">
                        <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                            {{ __('pages.ri_template_select_label') }} <span class="text-red-500">*</span>
                        </label>
                        <div x-data="{ open: false, search: '' }" class="relative">
                            <div x-show="!invoiceData.template_id" @click="open = !open"
                                class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#1e1e1e] text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition flex items-center justify-between">
                                <span>{{ __('pages.ri_template_select_placeholder') }}</span>
                                <svg class="w-4 h-4 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <div x-show="invoiceData.template_id"
                                class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#1e1e1e] flex items-center justify-between">
                                <div>
                                    <span class="text-dark-900 dark:text-dark-50 font-medium" x-text="invoiceData.template_label"></span>
                                    <span class="ml-2 text-xs text-dark-400 dark:text-dark-500" x-text="'(' + (invoiceData.template_frequency || '') + ')'"></span>
                                </div>
                                <button @click="clearTemplate()" type="button" class="text-dark-400 hover:text-red-500 transition ml-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <template x-teleport="body">
                                <div x-show="open" x-transition id="monthly-tmpl-dd"
                                    class="fixed z-9999 bg-white dark:bg-[#1e1e1e] border border-dark-200 dark:border-white/8 rounded-lg shadow-xl max-h-80 overflow-hidden"
                                    style="display:none;"
                                    x-effect="
                                        if (open) {
                                            const el = document.getElementById('monthly-tmpl-wrapper');
                                            const rect = el.getBoundingClientRect();
                                            const dd = document.getElementById('monthly-tmpl-dd');
                                            dd.style.top = (rect.bottom + window.scrollY + 4) + 'px';
                                            dd.style.left = rect.left + 'px';
                                            dd.style.width = rect.width + 'px';
                                        }
                                    ">
                                    <div class="p-2 border-b border-dark-200 dark:border-white/8">
                                        <input type="text" x-model="search" @click.stop placeholder="{{ __('pages.ri_search_templates_placeholder') }}"
                                            class="w-full px-3 py-2 border border-dark-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                    </div>
                                    <div class="overflow-y-auto max-h-64">
                                        <template x-for="tmpl in filteredTemplates(search)" :key="tmpl.id">
                                            <div @click="selectTemplate(tmpl); open = false; search = ''"
                                                class="px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-white/8 last:border-0">
                                                <div class="font-medium text-dark-900 dark:text-dark-50 text-sm" x-text="tmpl.label"></div>
                                                <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5" x-text="tmpl.frequency"></div>
                                            </div>
                                        </template>
                                        <div x-show="filteredTemplates(search).length === 0" class="px-4 py-8 text-center text-dark-500 dark:text-dark-400 text-sm">
                                            {{ __('pages.ri_no_templates_found') }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        @error('invoiceData.template_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Scheduled Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('pages.ri_scheduled_date_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                    <svg class="w-4 h-4 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="date" wire:model="invoiceData.scheduled_date"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-dark-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                            </div>
                            @error('invoiceData.scheduled_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
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
    function monthlyInvoiceForm() {
        return {
            invoiceData: {
                template_id: null,
                template_label: '',
                template_frequency: '',
                scheduled_date: null,
            },
            items: [],
            discount: {
                type: 'fixed',
                value: 0,
                reason: ''
            },
            templates: @js($this->availableTemplates),
            clients: @js($this->clients),
            services: @js($this->services),
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
                default: '{{ __('pages.ri_create_invoice_btn') }}',
                saving: '{{ __('pages.ri_saving_btn') }}'
            },

            filteredTemplates(search) {
                if (!search) return this.templates;
                const s = search.toLowerCase();
                return this.templates.filter(t => t.label.toLowerCase().includes(s) || t.frequency.toLowerCase().includes(s));
            },

            selectTemplate(tmpl) {
                this.invoiceData.template_id = tmpl.id;
                this.invoiceData.template_label = tmpl.label;
                this.invoiceData.template_frequency = tmpl.frequency;
                this.$wire.set('invoiceData.template_id', tmpl.id);

                // Auto-populate items from template
                if (tmpl.invoice_template && tmpl.invoice_template.items) {
                    this.items = tmpl.invoice_template.items.map(item => ({
                        id: this.nextId++,
                        client_id: item.client_id,
                        client_name: '',
                        service_name: item.service_name,
                        quantity: item.quantity,
                        unit_price: item.unit_price ? item.unit_price.toLocaleString('id-ID') : '',
                        amount: item.amount || 0,
                        cogs_amount: item.cogs_amount ? item.cogs_amount.toLocaleString('id-ID') : '',
                        profit: (item.amount || 0) - (item.cogs_amount || 0),
                        is_tax_deposit: item.is_tax_deposit || false,
                    }));

                    // Init dropdown states
                    this.items.forEach(item => {
                        this.itemSelectOpen[item.id] = false;
                        this.itemSelectSearch[item.id] = '';
                        this.serviceSelectOpen[item.id] = false;
                        this.serviceSelectSearch[item.id] = '';
                        // Resolve client name
                        const client = this.clients.find(c => c.id === item.client_id);
                        if (client) item.client_name = client.name;
                    });

                    // Auto-populate discount
                    if (tmpl.invoice_template.discount_type) {
                        this.discount = {
                            type: tmpl.invoice_template.discount_type,
                            value: tmpl.invoice_template.discount_value || 0,
                            reason: tmpl.invoice_template.discount_reason || '',
                        };
                    }
                }
            },

            clearTemplate() {
                this.invoiceData.template_id = null;
                this.invoiceData.template_label = '';
                this.invoiceData.template_frequency = '';
                this.$wire.set('invoiceData.template_id', null);
                this.items = [];
                this.discount = { type: 'fixed', value: 0, reason: '' };
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
                    client_id: null,
                    client_name: '',
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
                    this.$wire.invoiceData = {
                        template_id: this.invoiceData.template_id,
                        scheduled_date: this.invoiceData.scheduled_date || this.$wire.invoiceData?.scheduled_date,
                    };
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

                    await this.$wire.save();
                } catch (error) {
                    console.error(error);
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
