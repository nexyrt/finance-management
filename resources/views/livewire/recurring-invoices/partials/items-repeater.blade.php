{{-- Section: Invoice Items --}}
<div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">

    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-dark-100 dark:border-dark-700 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_invoice_items_section_title') }}</h2>
                <p class="text-xs text-dark-500 dark:text-dark-400">
                    <span x-text="items.length"></span> {{ __('pages.ri_items_added') }}
                </p>
            </div>
        </div>

        {{-- Add Item Controls --}}
        <div class="flex items-center gap-2">
            <div class="flex items-center border border-dark-200 dark:border-dark-600 rounded-lg overflow-hidden">
                <button @click="bulkCount = Math.max(1, bulkCount - 1)" type="button"
                    class="px-2.5 py-2 text-dark-500 hover:text-dark-900 dark:hover:text-dark-50 hover:bg-dark-50 dark:hover:bg-dark-700 transition-colors text-sm font-medium">−</button>
                <input type="number" x-model="bulkCount" min="1" max="50"
                    class="w-12 py-2 text-sm text-center bg-white dark:bg-dark-800 border-x border-dark-200 dark:border-dark-600 text-dark-900 dark:text-dark-50 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                <button @click="bulkCount = Math.min(50, bulkCount + 1)" type="button"
                    class="px-2.5 py-2 text-dark-500 hover:text-dark-900 dark:hover:text-dark-50 hover:bg-dark-50 dark:hover:bg-dark-700 transition-colors text-sm font-medium">+</button>
            </div>
            <button @click="bulkAddItems()" type="button"
                class="flex items-center gap-1.5 px-3.5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('pages.ri_add_item_btn') }}
            </button>
        </div>
    </div>

    {{-- Empty State --}}
    <div x-show="items.length === 0" class="px-6 py-16 text-center">
        <div class="w-14 h-14 bg-dark-50 dark:bg-dark-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-dark-300 dark:text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-dark-500 dark:text-dark-400">{{ __('pages.ri_no_items_empty') }}</p>
        <p class="text-xs text-dark-400 dark:text-dark-500 mt-1">{{ __('pages.ri_click_add_item') }}</p>
    </div>

    {{-- Desktop Table --}}
    <div x-show="items.length > 0" class="hidden md:block overflow-x-auto">
        <div class="min-w-[900px]">
            {{-- Table Header --}}
            <div class="grid grid-cols-24 gap-2 px-6 py-2.5 bg-dark-50 dark:bg-dark-900/60 border-b border-dark-100 dark:border-dark-700">
                <div class="col-span-1 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-center">#</div>
                <div class="col-span-4 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider">{{ __('pages.ri_col_client_header') }}</div>
                <div class="col-span-5 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider">{{ __('pages.ri_col_service_header') }}</div>
                <div class="col-span-2 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-center">{{ __('pages.ri_col_qty_header') }}</div>
                <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-right">{{ __('pages.ri_col_unit_price_header') }}</div>
                <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-right">{{ __('pages.ri_col_amount_header') }}</div>
                <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-right">{{ __('pages.ri_col_cogs_header') }}</div>
                <div class="col-span-2 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-center">{{ __('pages.ri_col_tax_header') }}</div>
                <div class="col-span-1"></div>
            </div>

            {{-- Rows --}}
            <div class="divide-y divide-dark-100 dark:divide-dark-700">
                <template x-for="(item, index) in items" :key="'d-' + item.id">
                    <div class="grid grid-cols-24 gap-2 px-6 py-3 items-center hover:bg-dark-50/50 dark:hover:bg-dark-900/30 transition-colors group">

                        {{-- # --}}
                        <div class="col-span-1 text-center">
                            <span class="text-xs font-bold text-dark-400 dark:text-dark-500" x-text="index + 1"></span>
                        </div>

                        {{-- Client --}}
                        <div class="col-span-4" @click.away="itemSelectOpen[item.id] = false">
                            <div x-show="!item.client_id"
                                :id="`clientInput-${item.id}`"
                                @click="
                                    const trigger = $event.target;
                                    const rect = trigger.getBoundingClientRect();
                                    const el = document.getElementById('client-dd-' + item.id);
                                    if (el) { el.style.left = rect.left + 'px'; el.style.top = (rect.bottom + 4) + 'px'; requestAnimationFrame(() => requestAnimationFrame(() => { const ddH = el.offsetHeight; const spaceBelow = window.innerHeight - rect.bottom; const spaceAbove = rect.top; el.style.top = (spaceBelow >= ddH || spaceBelow >= spaceAbove ? rect.bottom + 4 : rect.top - ddH - 4) + 'px'; })); }
                                    itemSelectOpen[item.id] = true;
                                    const sh = (e) => {
                                        const dd = document.getElementById('client-dd-' + item.id);
                                        if (dd && dd.contains(e.target)) return;
                                        itemSelectOpen[item.id] = false;
                                        window.removeEventListener('scroll', sh, true);
                                    };
                                    window.addEventListener('scroll', sh, true);"
                                class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-400 dark:text-dark-500 cursor-pointer hover:border-primary-400 transition-colors truncate">
                                {{ __('common.select') }}
                            </div>
                            <div x-show="item.client_id"
                                class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center gap-1">
                                <span class="flex-1 text-dark-900 dark:text-dark-50 font-medium truncate" x-text="item.client_name"></span>
                                <button @click="clearItemClient(item)" type="button" class="shrink-0 text-dark-300 hover:text-red-500 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <template x-teleport="body">
                                <div x-show="itemSelectOpen[item.id]"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    :id="`client-dd-${item.id}`"
                                    @click.away="itemSelectOpen[item.id] = false"
                                    class="fixed z-9999 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-xl overflow-hidden"
                                    style="width: 240px;">
                                    <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                        <input type="text" x-model="itemSelectSearch[item.id]" @click.stop
                                            placeholder="{{ __('common.search') }}..."
                                            class="w-full px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div class="overflow-y-auto max-h-48">
                                        <template x-for="c in filteredItemClients(item.id)" :key="c.id">
                                            <div @click="selectItemClient(item, c)"
                                                class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer flex items-center gap-2 border-b border-dark-50 dark:border-dark-700 last:border-0">
                                                <div class="w-6 h-6 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0" x-text="c.name.charAt(0).toUpperCase()"></div>
                                                <div class="min-w-0">
                                                    <div class="text-xs font-medium text-dark-900 dark:text-dark-50 truncate" x-text="c.name"></div>
                                                    <div class="text-[10px] text-dark-400 truncate" x-text="c.email || '-'"></div>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="filteredItemClients(item.id).length === 0" class="py-6 text-center text-xs text-dark-400">{{ __('pages.ri_no_clients_found') }}</div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Service --}}
                        <div class="col-span-5" @click.away="serviceSelectOpen[item.id] = false">
                            <input type="text" x-model="item.service_name"
                                :id="`serviceInput-${item.id}`"
                                class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :placeholder="'{{ __('pages.ri_search_services_placeholder') }}...'"
                                @click="
                                    const rect = $event.target.getBoundingClientRect();
                                    const el = document.getElementById('service-dd-' + item.id);
                                    if (el) { el.style.left = rect.left + 'px'; el.style.top = (rect.bottom + 4) + 'px'; requestAnimationFrame(() => requestAnimationFrame(() => { const ddH = el.offsetHeight; const spaceBelow = window.innerHeight - rect.bottom; const spaceAbove = rect.top; el.style.top = (spaceBelow >= ddH || spaceBelow >= spaceAbove ? rect.bottom + 4 : rect.top - ddH - 4) + 'px'; })); }
                                    serviceSelectOpen[item.id] = true;
                                    const sh = (e) => {
                                        const dd = document.getElementById('service-dd-' + item.id);
                                        if (dd && dd.contains(e.target)) return;
                                        serviceSelectOpen[item.id] = false;
                                        window.removeEventListener('scroll', sh, true);
                                    };
                                    window.addEventListener('scroll', sh, true);">
                            <template x-teleport="body">
                                <div x-show="serviceSelectOpen[item.id]"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    :id="`service-dd-${item.id}`"
                                    @click.away="serviceSelectOpen[item.id] = false"
                                    class="fixed z-9999 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-xl overflow-hidden"
                                    style="width: 280px;">
                                    <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                        <input type="text" x-model="serviceSelectSearch[item.id]" @click.stop
                                            placeholder="{{ __('common.search') }} {{ strtolower(__('common.services')) }}..."
                                            class="w-full px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div class="overflow-y-auto max-h-48">
                                        <template x-for="svc in filteredItemServices(item.id)" :key="svc.id">
                                            <div @click="selectService(item, svc)"
                                                class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer border-b border-dark-50 dark:border-dark-700 last:border-0">
                                                <div class="text-xs font-semibold text-dark-900 dark:text-dark-50" x-text="svc.name"></div>
                                                <div class="flex items-center justify-between mt-0.5">
                                                    <span class="text-[10px] text-primary-600 dark:text-primary-400" x-text="svc.type"></span>
                                                    <span class="text-[10px] font-semibold text-dark-600 dark:text-dark-300" x-text="svc.formatted_price"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="filteredItemServices(item.id).length === 0" class="py-6 text-center text-xs text-dark-400">{{ __('pages.ri_no_services_found') }}</div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Qty --}}
                        <div class="col-span-2">
                            <input type="text" x-model="item.quantity" @input="calculateItem(item)"
                                class="w-full px-2 py-1.5 text-xs text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="1">
                        </div>

                        {{-- Unit Price --}}
                        <div class="col-span-3">
                            <input type="text" x-model="item.unit_price"
                                @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                                class="w-full px-2 py-1.5 text-xs text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0">
                        </div>

                        {{-- Amount --}}
                        <div class="col-span-3">
                            <div class="px-2 py-1.5 text-xs text-right rounded-lg bg-dark-50 dark:bg-dark-900/60 text-dark-700 dark:text-dark-200 font-semibold"
                                x-text="formatCurrency(item.amount)"></div>
                        </div>

                        {{-- COGS --}}
                        <div class="col-span-3">
                            <input type="text" x-model="item.cogs_amount"
                                @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                                class="w-full px-2 py-1.5 text-xs text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0">
                        </div>

                        {{-- Tax --}}
                        <div class="col-span-2 flex justify-center">
                            <label class="cursor-pointer">
                                <input type="checkbox" x-model="item.is_tax_deposit"
                                    class="rounded border-dark-300 dark:border-dark-600 text-primary-600 focus:ring-2 focus:ring-primary-500">
                            </label>
                        </div>

                        {{-- Delete --}}
                        <div class="col-span-1 flex justify-center">
                            <button @click="removeItem(index)" type="button"
                                class="p-1.5 text-dark-300 dark:text-dark-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Mobile Card Layout --}}
    <div x-show="items.length > 0" class="block md:hidden divide-y divide-dark-100 dark:divide-dark-700">
        <template x-for="(item, index) in items" :key="'m-' + item.id">
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-dark-100 dark:bg-dark-700 rounded-lg flex items-center justify-center text-xs font-bold text-dark-600 dark:text-dark-300" x-text="index + 1"></span>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" x-model="item.is_tax_deposit"
                                class="rounded border-dark-300 text-primary-600 focus:ring-2 focus:ring-primary-500">
                            <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_tax_deposit_full_label') }}</span>
                        </label>
                    </div>
                    <button @click="removeItem(index)" type="button"
                        class="p-1.5 text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Client --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.ri_col_client_header') }}</label>
                    <div class="relative">
                        <div x-show="!item.client_id" @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-400 cursor-pointer hover:border-primary-400 transition-colors">
                            {{ __('common.select') }}
                        </div>
                        <div x-show="item.client_id"
                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center gap-2">
                            <span class="flex-1 text-dark-900 dark:text-dark-50 font-medium truncate" x-text="item.client_name"></span>
                            <button @click="clearItemClient(item)" type="button" class="text-dark-300 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="itemSelectOpen[item.id]" x-transition
                            class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                <input type="text" x-model="itemSelectSearch[item.id]" @click.stop placeholder="{{ __('common.search') }}..."
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div class="overflow-y-auto max-h-48">
                                <template x-for="c in filteredItemClients(item.id)" :key="c.id">
                                    <div @click="selectItemClient(item, c)"
                                        class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer flex items-center gap-3 border-b border-dark-50 last:border-0">
                                        <div class="w-7 h-7 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shrink-0" x-text="c.name.charAt(0).toUpperCase()"></div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate" x-text="c.name"></div>
                                            <div class="text-xs text-dark-400 truncate" x-text="c.email || '-'"></div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="filteredItemClients(item.id).length === 0" class="py-6 text-center text-sm text-dark-400">{{ __('pages.ri_no_clients_found') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Service --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.ri_col_service_header') }}</label>
                    <div class="relative flex gap-2" @click.away="serviceSelectOpen[item.id] = false">
                        <input type="text" x-model="item.service_name"
                            class="flex-1 px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            :placeholder="'{{ __('pages.ri_search_services_placeholder') }}...'">
                        <button @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]" type="button"
                            class="px-2.5 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 hover:bg-dark-50 transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="serviceSelectOpen[item.id]" x-transition
                            class="absolute z-50 top-full mt-1 left-0 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-lg overflow-hidden">
                            <div class="overflow-y-auto max-h-48">
                                <template x-for="svc in filteredItemServices(item.id)" :key="svc.id">
                                    <div @click="selectService(item, svc)" class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer border-b border-dark-50 last:border-0">
                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50" x-text="svc.name"></div>
                                        <div class="flex items-center justify-between mt-0.5">
                                            <span class="text-xs text-primary-600 dark:text-primary-400" x-text="svc.type"></span>
                                            <span class="text-xs font-semibold text-dark-600 dark:text-dark-300" x-text="svc.formatted_price"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="filteredItemServices(item.id).length === 0" class="py-6 text-center text-sm text-dark-400">{{ __('pages.ri_no_services_found') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Qty & Unit Price --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.ri_col_qty_header') }}</label>
                        <input type="text" x-model="item.quantity" @input="calculateItem(item)"
                            class="w-full px-2 py-2 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="1">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.ri_col_unit_price_header') }}</label>
                        <input type="text" x-model="item.unit_price"
                            @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                            class="w-full px-2 py-2 text-sm text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="0">
                    </div>
                </div>

                {{-- Amount & COGS --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.ri_col_amount_header') }}</label>
                        <div class="px-3 py-2 text-sm text-right rounded-lg bg-dark-50 dark:bg-dark-900/60 text-dark-700 dark:text-dark-200 font-semibold"
                            x-text="formatCurrency(item.amount)"></div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.ri_col_cogs_header') }}</label>
                        <input type="text" x-model="item.cogs_amount"
                            @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                            class="w-full px-3 py-2 text-sm text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="0">
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
