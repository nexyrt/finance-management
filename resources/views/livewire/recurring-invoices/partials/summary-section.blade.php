{{-- Invoice Summary Card --}}
<div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">

    {{-- Rows: label + value --}}
    <div class="divide-y divide-dark-100 dark:divide-dark-700">

        {{-- Subtotal --}}
        <div class="flex items-center justify-between px-4 py-3">
            <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_subtotal_summary') }}</span>
            <span class="text-xs font-semibold text-dark-900 dark:text-dark-50 tabular-nums" x-text="formatCurrency(subtotal)"></span>
        </div>

        {{-- Tax Deposit (conditional) --}}
        <div class="flex items-center justify-between px-4 py-3" x-show="taxDeposits > 0">
            <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_tax_deposits_label') }}</span>
            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 tabular-nums" x-text="formatCurrency(taxDeposits)"></span>
        </div>

        {{-- Discount (conditional) --}}
        <div class="flex items-center justify-between px-4 py-3" x-show="discountAmount > 0">
            <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.ri_discount_label_summary') }}</span>
            <span class="text-xs font-semibold text-red-500 tabular-nums" x-text="'− ' + formatCurrency(discountAmount)"></span>
        </div>

        {{-- Total --}}
        <div class="px-4 py-4 bg-dark-50 dark:bg-dark-900/40">
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide">{{ __('pages.ri_total_amount_summary') }}</span>
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400 tabular-nums" x-text="formatCurrency(totalAmount)"></span>
            </div>
        </div>

        {{-- Profit + Tax pills --}}
        <div class="grid grid-cols-2 divide-x divide-dark-100 dark:divide-dark-700">
            <div class="px-4 py-3">
                <p class="text-[10px] text-dark-400 dark:text-dark-500 mb-0.5">{{ __('pages.ri_net_profit_label') }}</p>
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" x-text="formatCurrency(netProfit)"></p>
            </div>
            <div class="px-4 py-3">
                <p class="text-[10px] text-dark-400 dark:text-dark-500 mb-0.5">{{ __('pages.ri_tax_deposits_label') }}</p>
                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 tabular-nums" x-text="formatCurrency(taxDeposits)"></p>
            </div>
        </div>
    </div>

    {{-- Discount inputs (collapsible section) --}}
    <div x-data="{ open: false }">
        <button @click="open = !open" type="button"
            class="w-full flex items-center justify-between px-4 py-3 border-t border-dark-100 dark:border-dark-700 text-xs text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 hover:bg-dark-50 dark:hover:bg-dark-700/40 transition-colors">
            <span class="font-medium">{{ __('pages.ri_discount_label_summary') }}</span>
            <div class="flex items-center gap-1.5">
                <span x-show="discountAmount > 0" class="text-[10px] font-semibold text-red-500" x-text="'− ' + formatCurrency(discountAmount)"></span>
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
        <div x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="border-t border-dark-100 dark:border-dark-700 px-4 pb-4 pt-3 space-y-2">
            <div class="flex gap-2">
                <select x-model="discount.type"
                    class="flex-1 px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="fixed">{{ __('pages.ri_discount_fixed_option') }}</option>
                    <option value="percentage">%</option>
                </select>
                <input type="text" x-model="discount.value"
                    @input="discount.value = formatInput($event.target.value)"
                    x-show="discount.type === 'fixed'"
                    placeholder="0"
                    class="flex-1 px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                <input type="number" x-model="discount.value"
                    x-show="discount.type === 'percentage'"
                    placeholder="0" min="0" max="100"
                    class="flex-1 px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
            </div>
            <input type="text" x-model="discount.reason"
                placeholder="{{ __('pages.ri_reason_optional_placeholder') }}"
                class="w-full px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
    </div>

    {{-- Save Button --}}
    <div class="p-4 border-t border-dark-100 dark:border-dark-700">
        <button @click="syncAndSave()" type="button"
            :disabled="saving || items.length === 0"
            :class="saving || items.length === 0
                ? 'opacity-50 cursor-not-allowed'
                : 'hover:bg-primary-700 shadow-md shadow-primary-500/20'"
            class="w-full py-3 bg-primary-600 text-white text-sm font-semibold rounded-lg transition-all duration-150 flex items-center justify-center gap-2">
            <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="saving ? buttonText?.saving : buttonText?.default"></span>
        </button>
        <p x-show="items.length === 0" class="text-center text-[10px] text-dark-400 dark:text-dark-500 mt-2">
            {{ __('pages.ri_add_items_to_enable_save') }}
        </p>
    </div>
</div>
