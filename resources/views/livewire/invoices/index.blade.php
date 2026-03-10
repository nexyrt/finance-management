{{-- resources/views/livewire/invoices/index.blade.php --}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.invoice_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.manage_track_invoices') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Workflow Guide Button --}}
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.client_guide_btn') }}
            </button>

            <x-button size="sm" href="{{ route('invoices.create') }}" wire:navigate color="primary">
                <x-slot:left>
                    <x-icon name="plus" class="w-4 h-4" />
                </x-slot:left>
                {{ __('invoice.create_invoice') }}
            </x-button>
        </div>
    </div>

    {{-- Stats + Table (Lazy) --}}
    <livewire:invoices.listing />

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="4xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="map" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.invoice_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.invoice_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        {{-- Tab-based Guide --}}
        <div x-data="{ tab: 'workflow' }" class="space-y-5">

            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <button
                    @click="tab = 'workflow'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'workflow'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrow-path" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.invoice_guide_tab_workflow') }}</span>
                </button>
                <button
                    @click="tab = 'status'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'status'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="tag" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.invoice_guide_tab_status') }}</span>
                </button>
                <button
                    @click="tab = 'features'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'features'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="sparkles" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.invoice_guide_tab_features') }}</span>
                </button>
                <button
                    @click="tab = 'export'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'export'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="printer" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.invoice_guide_tab_export') }}</span>
                </button>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 1: ALUR KERJA --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'workflow'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">
                    {{-- Connecting line --}}
                    <div class="relative">
                        <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 via-yellow-300 to-green-300 dark:from-blue-700 dark:via-purple-700 dark:via-yellow-700 dark:to-green-700 hidden sm:block"></div>

                        <div class="space-y-4">
                            {{-- Step 1: Buat Invoice --}}
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="document-plus" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.invoice_guide_step1_title') }}</h4>
                                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.invoice_guide_step1_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step1_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step1_tip2') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step1_tip3') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step1_tip4') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2: Kirim ke Klien --}}
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="paper-airplane" class="w-5 h-5 text-purple-600 dark:text-purple-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.invoice_guide_step2_title') }}</h4>
                                            <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.invoice_guide_step2_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step2_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step2_tip2') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3: Catat Pembayaran --}}
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-amber-900/40 z-10">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                <div class="flex-1 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="banknotes" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-amber-900 dark:text-amber-200 mb-1">{{ __('pages.invoice_guide_step3_title') }}</h4>
                                            <p class="text-sm text-amber-700 dark:text-amber-300 mb-2">{{ __('pages.invoice_guide_step3_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-amber-600 dark:text-amber-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step3_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-amber-600 dark:text-amber-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.invoice_guide_step3_tip2') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 4: Lunas --}}
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                    <span class="text-white font-bold text-sm">4</span>
                                </div>
                                <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="check-badge" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.invoice_guide_step4_title') }}</h4>
                                            <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.invoice_guide_step4_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Number Format Info --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="hashtag" class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.invoice_guide_number_title') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400 mb-2">{{ __('pages.invoice_guide_number_desc') }}</p>
                                <code class="text-xs bg-gray-200 dark:bg-dark-600 text-dark-700 dark:text-dark-200 px-2 py-1 rounded-lg font-mono">001/INV/SPI-KLN/I/2026</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 2: STATUS & PEMBAYARAN --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'status'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-5">
                    {{-- Status Flow Diagram --}}
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.invoice_guide_status_flow_title') }}</h4>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                                <div class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                                <span class="text-xs font-medium text-dark-700 dark:text-dark-300">Draft</span>
                            </div>
                            <x-icon name="arrow-right" class="w-4 h-4 text-dark-400 dark:text-dark-500" />
                            <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-900/40">
                                <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Sent</span>
                            </div>
                            <x-icon name="arrow-right" class="w-4 h-4 text-dark-400 dark:text-dark-500" />
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 px-3 py-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-900/40">
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                                    <span class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Partially Paid</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-2 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-900/40">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                    <span class="text-xs font-medium text-red-700 dark:text-red-300">Overdue</span>
                                </div>
                            </div>
                            <x-icon name="arrow-right" class="w-4 h-4 text-dark-400 dark:text-dark-500" />
                            <div class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-900/40">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                                <span class="text-xs font-medium text-green-700 dark:text-green-300">Paid</span>
                            </div>
                        </div>
                    </div>

                    {{-- Status Descriptions --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ([
                            ['status' => 'Draft', 'color' => 'gray', 'icon' => 'pencil', 'title' => __('pages.invoice_status_draft_title'), 'desc' => __('pages.invoice_status_draft_desc')],
                            ['status' => 'Sent', 'color' => 'blue', 'icon' => 'paper-airplane', 'title' => __('pages.invoice_status_sent_title'), 'desc' => __('pages.invoice_status_sent_desc')],
                            ['status' => 'Partially Paid', 'color' => 'yellow', 'icon' => 'banknotes', 'title' => __('pages.invoice_status_partial_title'), 'desc' => __('pages.invoice_status_partial_desc')],
                            ['status' => 'Overdue', 'color' => 'red', 'icon' => 'exclamation-triangle', 'title' => __('pages.invoice_status_overdue_title'), 'desc' => __('pages.invoice_status_overdue_desc')],
                            ['status' => 'Paid', 'color' => 'green', 'icon' => 'check-badge', 'title' => __('pages.invoice_status_paid_title'), 'desc' => __('pages.invoice_status_paid_desc')],
                        ] as $s)
                            <div class="flex items-start gap-3 p-3 rounded-xl border
                                @if($s['color'] === 'gray') bg-gray-50 dark:bg-gray-900/10 border-gray-200 dark:border-gray-900/40
                                @elseif($s['color'] === 'blue') bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-900/40
                                @elseif($s['color'] === 'yellow') bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-900/40
                                @elseif($s['color'] === 'red') bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-900/40
                                @elseif($s['color'] === 'green') bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-900/40
                                @endif">
                                <x-badge :text="$s['status']" :color="$s['color']" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ $s['title'] }}</p>
                                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $s['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pembayaran --}}
                    <div class="border-t border-secondary-200 dark:border-dark-600 pt-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.invoice_guide_payment_title') }}</h4>
                        <div class="space-y-2">
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl">
                                <div class="h-7 w-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">1</span>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.invoice_guide_payment_step1') }}</p>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl">
                                <div class="h-7 w-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">2</span>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.invoice_guide_payment_step2') }}</p>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl">
                                <div class="h-7 w-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">3</span>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.invoice_guide_payment_step3') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 3: FITUR KHUSUS --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'features'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">

                    {{-- COGS --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="chart-bar" class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.invoice_guide_cogs_title') }}</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.invoice_guide_cogs_desc') }}</p>
                                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-2.5 font-mono text-xs text-blue-800 dark:text-blue-200">
                                    {{ __('pages.invoice_guide_cogs_formula') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tax Deposit --}}
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="receipt-percent" class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.invoice_guide_taxdeposit_title') }}</h4>
                                <p class="text-xs text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.invoice_guide_taxdeposit_desc') }}</p>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                        <span>{{ __('pages.invoice_guide_taxdeposit_tip1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                        <span>{{ __('pages.invoice_guide_taxdeposit_tip2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Diskon --}}
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="tag" class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.invoice_guide_discount_title') }}</h4>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-200">{{ __('pages.invoice_guide_discount_fixed') }}</p>
                                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">{{ __('pages.invoice_guide_discount_fixed_desc') }}</p>
                                    </div>
                                    <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-200">{{ __('pages.invoice_guide_discount_percent') }}</p>
                                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">{{ __('pages.invoice_guide_discount_percent_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Multi-item + Multi-client --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="list-bullet" class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">{{ __('pages.invoice_guide_multiitem_title') }}</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-300">{{ __('pages.invoice_guide_multiitem_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Outstanding Profit --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="light-bulb" class="w-5 h-5 text-yellow-500 dark:text-yellow-400 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.invoice_guide_profit_title') }}</h4>
                                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                                    <p>{{ __('pages.invoice_guide_profit_desc1') }}</p>
                                    <p>{{ __('pages.invoice_guide_profit_desc2') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 4: CETAK & EKSPOR --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'export'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">

                    {{-- Print Single --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="printer" class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.invoice_guide_print_title') }}</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mb-3">{{ __('pages.invoice_guide_print_desc') }}</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-center">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.invoice_guide_print_full') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.invoice_guide_print_full_desc') }}</p>
                                    </div>
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-center">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.invoice_guide_print_dp') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.invoice_guide_print_dp_desc') }}</p>
                                    </div>
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-center">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.invoice_guide_print_pelunasan') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.invoice_guide_print_pelunasan_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bulk Print --}}
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="document-duplicate" class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.invoice_guide_bulk_print_title') }}</h4>
                                <p class="text-xs text-purple-700 dark:text-purple-300">{{ __('pages.invoice_guide_bulk_print_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Excel Export --}}
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="table-cells" class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.invoice_guide_excel_title') }}</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ __('pages.invoice_guide_excel_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="light-bulb" class="w-5 h-5 text-yellow-500 dark:text-yellow-400 shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.invoice_guide_export_tips_title') }}</h4>
                                <ul class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.invoice_guide_export_tip1') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.invoice_guide_export_tip2') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.invoice_guide_export_tip3') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.client_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Child Components --}}
    <livewire:invoices.show />
    <livewire:invoices.delete />
    <livewire:payments.create />
    <livewire:payments.edit />
</div>
