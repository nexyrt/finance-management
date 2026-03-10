<div class="space-y-6" x-data="recurringTemplateForm()" @click.away="closeAllDropdowns()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.ri_create_template_page_title') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">{{ __('pages.ri_create_template_page_desc') }}</p>
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

            {{-- Section 1: Template Details --}}
            <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">
                {{-- Section Header --}}
                <div class="px-6 py-4 border-b border-dark-100 dark:border-dark-700 flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_template_info_section') }}</h2>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_template_info_section_desc') }}</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Row 1: Template Name --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                            {{ __('pages.ri_template_name_label') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="template.template_name"
                            class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                            placeholder="{{ __('pages.ri_template_name_placeholder') }}">
                    </div>

                    {{-- Row 2: Frequency + Client --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Frequency --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">{{ __('pages.ri_frequency_label') }}</label>
                            <select x-model="template.frequency"
                                class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-size-[1.25rem] bg-position-[right_0.5rem_center] bg-no-repeat pr-10">
                                <template x-for="freq in frequencyOptions" :key="freq.value">
                                    <option :value="freq.value" x-text="freq.label"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Client --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('pages.ri_billed_to_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative" @click.away="selectOpen = false">
                                <div x-show="!template.client_id"
                                    x-ref="clientTrigger"
                                    @click="
                                        const rect = $refs.clientTrigger.getBoundingClientRect();
                                        const el = document.getElementById('tmpl-client-dd');
                                        if (el) { el.style.left = rect.left + 'px'; el.style.width = rect.width + 'px'; el.style.top = (rect.bottom + 4) + 'px'; setTimeout(() => { const ddH = el.offsetHeight; const spaceBelow = window.innerHeight - rect.bottom; const spaceAbove = rect.top; el.style.top = (spaceBelow >= ddH || spaceBelow >= spaceAbove ? rect.bottom + 4 : rect.top - ddH - 4) + 'px'; }, 0); }
                                        selectOpen = !selectOpen;"
                                    class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-400 dark:text-dark-500 cursor-pointer hover:border-primary-400 dark:hover:border-primary-500 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ __('pages.ri_select_client_placeholder') }}
                                </div>
                                <div x-show="template.client_id"
                                    class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shrink-0"
                                        x-text="template.client_name ? template.client_name.charAt(0).toUpperCase() : ''"></div>
                                    <span class="flex-1 text-dark-900 dark:text-dark-50 font-medium truncate" x-text="template.client_name"></span>
                                    <button @click="clearClient()" type="button"
                                        class="shrink-0 text-dark-300 hover:text-red-500 dark:text-dark-500 dark:hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <template x-teleport="body">
                                    <div x-show="selectOpen"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        id="tmpl-client-dd"
                                        @click.away="selectOpen = false"
                                        class="fixed z-9999 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-xl overflow-hidden">
                                        <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                            <input type="text" x-model="selectSearch" @click.stop
                                                placeholder="{{ __('pages.ri_search_clients_placeholder') }}"
                                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                        <div class="overflow-y-auto max-h-56">
                                            <template x-for="client in filteredClients" :key="client.id">
                                                <div @click="selectClient(client)"
                                                    class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer flex items-center gap-3 border-b border-dark-50 dark:border-dark-700 last:border-0">
                                                    <div class="w-8 h-8 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shrink-0"
                                                        x-text="client.name.charAt(0).toUpperCase()"></div>
                                                    <div class="min-w-0">
                                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate" x-text="client.name"></div>
                                                        <div class="text-xs text-dark-400 dark:text-dark-500 truncate" x-text="client.email || '-'"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredClients.length === 0" class="px-4 py-8 text-center">
                                                <p class="text-sm text-dark-400 dark:text-dark-500">{{ __('pages.ri_no_clients_found') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Start Date + End Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Start Date --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('pages.ri_start_date_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-400 dark:text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" x-model="template.start_date"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                            </div>
                        </div>

                        {{-- End Date --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('pages.ri_end_date_label') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-400 dark:text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" x-model="template.end_date"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
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
    function recurringTemplateForm() {
        return {
            template: {
                template_name: '',
                client_id: null,
                client_name: '',
                start_date: null,
                end_date: null,
                frequency: 'monthly',
            },
            items: [],
            discount: {
                type: 'fixed',
                value: 0,
                reason: ''
            },
            clients: @js($this->clients),
            services: @js($this->services),
            frequencyOptions: @js($this->frequencyOptions),
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
                default: '{{ __('pages.ri_save_template_btn') }}',
                saving: '{{ __('pages.ri_saving_btn') }}'
            },

            init() {
                const t = new Date();
                this.template.start_date = t.toISOString().split('T')[0];

                // Set end date 1 year from now
                const endDate = new Date(t);
                endDate.setFullYear(endDate.getFullYear() + 1);
                this.template.end_date = endDate.toISOString().split('T')[0];
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
                this.template.client_id = c.id;
                this.template.client_name = c.name;
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
                this.template.client_id = null;
                this.template.client_name = '';
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
                    client_id: this.template.client_id || null,
                    client_name: this.template.client_name || '',
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
                    this.$wire.template = this.template;
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
