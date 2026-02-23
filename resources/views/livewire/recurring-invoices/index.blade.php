<div
    class="space-y-6"
    x-data="{
        activeTab: $persist('templates').as('ri_active_tab'),
        showGuide: localStorage.getItem('ri_guide_dismissed') !== '1',
    }"
>

    {{-- ══════════════════════════════════════════
        HEADER
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.recurring_invoices') }}
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                {{ __('pages.automate_billing_process') }}
            </p>
        </div>

        {{-- Stats + Guide button --}}
        <div class="flex items-center gap-3 flex-shrink-0">
            {{-- Guide toggle --}}
            <button
                @click="showGuide = !showGuide; localStorage.setItem('ri_guide_dismissed', showGuide ? '0' : '1')"
                class="h-10 w-10 flex items-center justify-center rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-700 transition-all"
                :title="showGuide ? '{{ __('pages.ri_guide_hide') }}' : '{{ __('pages.ri_guide_show') }}'"
            >
                <x-icon name="question-mark-circle" class="w-5 h-5" />
            </button>

            {{-- Key metrics --}}
            <div class="flex gap-5 bg-white dark:bg-dark-800 rounded-xl border border-zinc-200 dark:border-dark-600 px-5 py-4 shadow-sm">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 leading-tight">
                        {{ number_format($this->activeTemplatesCount, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.active_templates') }}</div>
                </div>
                <div class="w-px bg-zinc-100 dark:bg-dark-600"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400 leading-tight">
                        Rp {{ number_format($this->totalProjectedRevenue / 1000000, 1, ',', '.') }}jt
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.projected') }} {{ now()->year }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
        GUIDE BANNER
    ══════════════════════════════════════════ --}}
    <div x-show="showGuide"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="bg-white dark:bg-dark-800 border border-blue-200 dark:border-blue-900/50 rounded-xl overflow-hidden">

        <!-- Banner Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-900/50">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-100">{{ __('pages.ri_guide_title') }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('pages.ri_guide_subtitle') }}</p>
                </div>
            </div>
            <button @click="showGuide = false; localStorage.setItem('ri_guide_dismissed', '1')"
                    class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-200 transition-colors">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <!-- 3 Steps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-blue-100 dark:divide-blue-900/30">

            <!-- Step 1: Buat Template -->
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">1</div>
                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_step1_title') }}</h4>
                </div>
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    {{ __('pages.ri_step1_desc') }}
                </p>
                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{!! __('pages.ri_step1_start_date_hint', ['label' => '<span class="font-medium">Start Date</span>']) !!}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{!! __('pages.ri_step1_end_date_hint', ['label' => '<span class="font-medium">End Date</span>']) !!}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{!! __('pages.ri_step1_frequency_hint', ['label' => '<span class="font-medium">Frequency</span>']) !!}</span>
                    </div>
                </div>
                <div class="pt-1">
                    <button @click="activeTab = 'templates'" class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <x-icon name="arrow-right" class="w-3 h-3" /> {{ __('pages.ri_step1_tab_hint') }}
                    </button>
                </div>
            </div>

            <!-- Step 2: Generate Invoice -->
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">2</div>
                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_step2_title') }}</h4>
                </div>
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    {{ __('pages.ri_step2_desc') }}
                </p>
                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{!! __('pages.ri_step2_issue_date_hint', ['label' => '<span class="font-medium">Issue Date</span>']) !!}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{!! __('pages.ri_step2_due_date_hint', ['label' => '<span class="font-medium">Due Date</span>']) !!}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="information-circle" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" />
                        <span>{{ __('pages.ri_step2_warning') }}</span>
                    </div>
                </div>
                <div class="pt-1">
                    <button @click="activeTab = 'monthly'" class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <x-icon name="arrow-right" class="w-3 h-3" /> {{ __('pages.ri_step2_tab_hint') }}
                    </button>
                </div>
            </div>

            <!-- Step 3: Publish -->
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">3</div>
                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.ri_step3_title') }}</h4>
                </div>
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    {{ __('pages.ri_step3_desc') }}
                </p>
                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{{ __('pages.ri_step3_hint1') }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>{{ __('pages.ri_step3_hint2') }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="information-circle" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" />
                        <span>{{ __('pages.ri_step3_warning') }}</span>
                    </div>
                </div>
                <div class="pt-1">
                    <button @click="activeTab = 'monthly'" class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <x-icon name="arrow-right" class="w-3 h-3" /> {{ __('pages.ri_step3_tab_hint') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Konsep Periode -->
        <div class="border-t border-blue-100 dark:border-blue-900/30 px-6 py-5 bg-amber-50/50 dark:bg-amber-900/10">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 bg-amber-100 dark:bg-amber-900/40 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <x-icon name="light-bulb" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.ri_period_concept_title') }}</h4>
                    <p class="text-xs text-dark-600 dark:text-dark-400 mb-3">
                        {{ __('pages.ri_period_concept_desc') }}
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        {{-- Contoh Monthly --}}
                        <div class="bg-white dark:bg-dark-800 border border-amber-200 dark:border-amber-900/50 rounded-lg p-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide">{{ __('pages.ri_example_monthly_label') }}</span>
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400 space-y-1">
                                <div><span class="font-medium text-dark-700 dark:text-dark-300">Start:</span> 19 Februari</div>
                                <div><span class="font-medium text-dark-700 dark:text-dark-300">End:</span> 10 Desember</div>
                            </div>
                            <div class="text-xs text-dark-600 dark:text-dark-400 space-y-0.5 pt-1 border-t border-amber-100 dark:border-amber-900/30">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                                    <span>Siklus 1: 19 Feb → <span class="font-medium">19 Mar</span> ✓ (≤ 10 Des)</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                                    <span>Siklus 2–9: Apr s.d. <span class="font-medium">Nov</span> ✓</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                    <span>Siklus 10: <span class="font-medium">19 Des</span> ✗ (> 10 Des)</span>
                                </div>
                                <div class="pt-1 font-medium text-dark-700 dark:text-dark-200">→ 9 invoice (Mar–Nov)</div>
                            </div>
                        </div>

                        {{-- Contoh Quarterly --}}
                        <div class="bg-white dark:bg-dark-800 border border-amber-200 dark:border-amber-900/50 rounded-lg p-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide">{{ __('pages.ri_example_quarterly_label') }}</span>
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400 space-y-1">
                                <div><span class="font-medium text-dark-700 dark:text-dark-300">Start:</span> 1 Januari 2026</div>
                                <div><span class="font-medium text-dark-700 dark:text-dark-300">End:</span> 31 Desember 2026</div>
                            </div>
                            <div class="text-xs text-dark-600 dark:text-dark-400 space-y-0.5 pt-1 border-t border-amber-100 dark:border-amber-900/30">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                                    <span>Siklus 1: Jan → <span class="font-medium">Apr</span> ✓</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                                    <span>Siklus 2: Apr → <span class="font-medium">Jul</span> ✓</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                                    <span>Siklus 3: Jul → <span class="font-medium">Okt</span> ✓</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                    <span>Siklus 4: Okt → <span class="font-medium">Jan 2027</span> ✗</span>
                                </div>
                                <div class="pt-1 font-medium text-dark-700 dark:text-dark-200">→ 3 invoice (Apr, Jul, Okt)</div>
                            </div>
                        </div>

                        {{-- Aturan Umum --}}
                        <div class="bg-white dark:bg-dark-800 border border-blue-200 dark:border-blue-900/50 rounded-lg p-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wide">{{ __('pages.ri_general_rules_label') }}</span>
                            </div>
                            <div class="text-xs text-dark-600 dark:text-dark-400 space-y-1.5">
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                                    <span>{{ __('pages.ri_rule1') }}</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                                    <span>{{ __('pages.ri_rule2') }}</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                                    <span>{{ __('pages.ri_rule3') }}</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="information-circle" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" />
                                    <span>{{ __('pages.ri_rule4') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
        CUSTOM TAB NAVIGATION — segmented control premium
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

        {{-- Segmented tab bar --}}
        <div class="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600 self-start">

            {{-- Templates --}}
            <button
                @click="activeTab = 'templates'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                :class="activeTab === 'templates'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
            >
                <x-icon name="document-text" class="w-4 h-4 flex-shrink-0" />
                <span>{{ __('pages.templates') }}</span>
                <span
                    class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold transition-colors"
                    :class="activeTab === 'templates'
                        ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300'
                        : 'bg-zinc-200 dark:bg-dark-600 text-dark-500 dark:text-dark-400'"
                >{{ $this->activeTemplatesCount }}</span>
            </button>

            {{-- Monthly --}}
            <button
                @click="activeTab = 'monthly'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                :class="activeTab === 'monthly'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
            >
                <x-icon name="calendar" class="w-4 h-4 flex-shrink-0" />
                <span>{{ __('pages.monthly') }}</span>
            </button>

            {{-- Analytics --}}
            <button
                @click="activeTab = 'analytics'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                :class="activeTab === 'analytics'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
            >
                <x-icon name="chart-bar" class="w-4 h-4 flex-shrink-0" />
                <span>{{ __('pages.analytics') }}</span>
            </button>
        </div>

        {{-- Divider line + context subtitle --}}
        <div class="hidden sm:flex items-center gap-3 flex-1 min-w-0">
            <div class="h-px flex-1 bg-gradient-to-r from-zinc-200 dark:from-dark-600 to-transparent"></div>
            <p x-show="activeTab === 'templates'" x-transition.opacity class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">
                {{ __('pages.ri_guide_subtitle') }}
            </p>
            <p x-show="activeTab === 'monthly'" x-transition.opacity class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">
                {{ __('pages.ri_step2_tab_hint') }}
            </p>
            <p x-show="activeTab === 'analytics'" x-transition.opacity class="text-xs text-dark-400 dark:text-dark-500 flex-shrink-0">
                {{ __('pages.ri_last_updated', ['datetime' => now()->format('d M Y')]) }}
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
        TAB PANELS
    ══════════════════════════════════════════ --}}

    {{-- Templates Panel --}}
    <div
        x-show="activeTab === 'templates'"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
    >
        <livewire:recurring-invoices.templates-tab />
    </div>

    {{-- Monthly Panel --}}
    <div
        x-show="activeTab === 'monthly'"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
    >
        <livewire:recurring-invoices.monthly-tab />
    </div>

    {{-- Analytics Panel --}}
    <div
        x-show="activeTab === 'analytics'"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
    >
        <livewire:recurring-invoices.analytics-tab />
    </div>

    {{-- Modal Components --}}
    <livewire:recurring-invoices.view-template />
</div>
