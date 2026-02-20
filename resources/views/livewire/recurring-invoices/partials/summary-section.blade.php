{{-- Invoice Summary Grid - Half Width Right --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Empty left column on desktop --}}
    <div class="hidden lg:block"></div>

    {{-- Summary right column --}}
    <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
        <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-6">{{ __('pages.ri_invoice_summary_title') }}</h2>

        <div class="space-y-4">
            {{-- Subtotal --}}
            <div class="flex justify-between items-center">
                <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ri_subtotal_summary') }}</span>
                <span class="text-lg font-semibold text-dark-900 dark:text-dark-50" x-text="formatCurrency(subtotal)"></span>
            </div>

            {{-- Discount Section --}}
            <div class="pt-4 pb-4 border-t border-b border-dark-200 dark:border-dark-700 space-y-3">
                <div class="flex flex-col gap-2">
                    <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.ri_discount_label_summary') }}</span>
                    <select x-model="discount.type"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                        <option value="fixed">{{ __('pages.ri_discount_fixed_option') }}</option>
                        <option value="percentage">{{ __('pages.ri_discount_percentage_option') }}</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <input type="text" x-model="discount.value"
                        @input="discount.value = formatInput($event.target.value)"
                        x-show="discount.type === 'fixed'" placeholder="{{ __('pages.ri_enter_amount_placeholder') }}"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <input type="number" x-model="discount.value" x-show="discount.type === 'percentage'"
                        placeholder="{{ __('pages.ri_enter_percentage_placeholder') }}" min="0" max="100"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <input type="text" x-model="discount.reason" placeholder="{{ __('pages.ri_reason_optional_placeholder') }}"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <div class="flex justify-between items-center" x-show="discountAmount > 0">
                    <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ri_discount_applied_label') }}</span>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400" x-text="'- ' + formatCurrency(discountAmount)"></span>
                </div>
            </div>

            {{-- Total --}}
            <div class="py-4 px-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-base font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_total_amount_summary') }}</span>
                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400" x-text="formatCurrency(totalAmount)"></span>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="grid grid-cols-2 gap-3 pt-3">
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <span class="text-xs text-green-700 dark:text-green-400 block mb-1">{{ __('pages.ri_net_profit_label') }}</span>
                    <span class="text-base font-semibold text-green-600 dark:text-green-400" x-text="formatCurrency(netProfit)"></span>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <span class="text-xs text-blue-700 dark:text-blue-400 block mb-1">{{ __('pages.ri_tax_deposits_label') }}</span>
                    <span class="text-base font-semibold text-blue-600 dark:text-blue-400" x-text="formatCurrency(taxDeposits)"></span>
                </div>
            </div>

            {{-- Save/Update Button --}}
            <button @click="syncAndSave()" type="button" :disabled="saving || items.length === 0"
                :class="saving || items.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700'"
                class="w-full mt-4 px-6 py-3 bg-primary-600 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                <svg x-show="saving" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="saving ? buttonText?.saving : buttonText?.default"></span>
            </button>
        </div>
    </div>
</div>
