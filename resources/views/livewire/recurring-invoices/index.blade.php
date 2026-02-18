<div class="space-y-6" x-data="{ showGuide: localStorage.getItem('ri_guide_dismissed') !== '1' }">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.recurring_invoices') }}
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                {{ __('pages.automate_billing_process') }}
            </p>
        </div>

        <!-- Key Metrics + Guide Toggle -->
        <div class="flex items-center gap-3">
            <button @click="showGuide = !showGuide; localStorage.setItem('ri_guide_dismissed', showGuide ? '0' : '1')"
                    class="h-9 w-9 flex items-center justify-center rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-700 transition-colors"
                    :title="showGuide ? 'Sembunyikan panduan' : 'Tampilkan panduan'">
                <x-icon name="question-mark-circle" class="w-5 h-5" />
            </button>
            <div
                class="flex gap-6 bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-zinc-200 dark:border-dark-600 p-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ number_format($this->activeTemplatesCount, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.active_templates') }}</div>
                </div>
                <div class="w-px bg-zinc-200 dark:bg-dark-600"></div>
                <div class="text-center">
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->totalProjectedRevenue, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.projected') }} {{ now()->year }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Getting Started Banner -->
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
                    <h3 class="font-semibold text-blue-900 dark:text-blue-100">Panduan Recurring Invoices</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400">Ikuti 3 langkah berikut — dan pahami konsep periode di bawah</p>
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
                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Buat Template</h4>
                </div>
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    Template adalah "resep" invoice yang akan diulang secara berkala. Tentukan client, item tagihan, harga, dan frekuensi.
                </p>
                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span><span class="font-medium">Start Date</span> — awal periode pertama (tagihan mulai 1 siklus setelahnya)</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span><span class="font-medium">End Date</span> — batas akhir; siklus yang melewati tanggal ini tidak akan ditagih</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span><span class="font-medium">Frequency</span> — monthly (tiap bulan), quarterly (tiap 3 bulan), semi-annual (tiap 6 bulan), annual (tiap tahun)</span>
                    </div>
                </div>
                <div class="pt-1">
                    <span class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-lg">
                        <x-icon name="arrow-right" class="w-3 h-3" /> Tab: Templates
                    </span>
                </div>
            </div>

            <!-- Step 2: Generate Invoice -->
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">2</div>
                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Generate Invoice</h4>
                </div>
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    Buka tab <strong>Monthly</strong>, pilih bulan dan tahun, lalu klik <strong>Generate Invoices</strong>. Sistem akan membuat draft invoice untuk semua template aktif yang sesuai bulan tersebut.
                </p>
                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span><span class="font-medium">Issue Date</span> — tanggal yang tercetak di invoice</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span><span class="font-medium">Due Date</span> — tanggal jatuh tempo pembayaran</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="information-circle" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" />
                        <span>Hanya bulan yang masuk dalam siklus valid template yang bisa di-generate (lihat konsep periode di bawah)</span>
                    </div>
                </div>
                <div class="pt-1">
                    <span class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-lg">
                        <x-icon name="arrow-right" class="w-3 h-3" /> Tab: Monthly
                    </span>
                </div>
            </div>

            <!-- Step 3: Publish -->
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">3</div>
                    <h4 class="font-semibold text-dark-900 dark:text-dark-50">Publish Invoice</h4>
                </div>
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    Draft invoice hasil generate perlu di-publish untuk menjadi invoice resmi. Setelah publish, invoice muncul di modul Invoices dan siap untuk pencatatan pembayaran.
                </p>
                <div class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>Bisa publish satu per satu atau <span class="font-medium">Bulk Publish</span> beberapa sekaligus</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span>Tanggal bisa di-override per invoice saat publish</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-icon name="information-circle" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" />
                        <span>Invoice yang sudah publish tidak bisa diedit atau dihapus</span>
                    </div>
                </div>
                <div class="pt-1">
                    <span class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-lg">
                        <x-icon name="arrow-right" class="w-3 h-3" /> Tab: Monthly
                    </span>
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
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">Memahami Konsep Periode Tagihan</h4>
                    <p class="text-xs text-dark-600 dark:text-dark-400 mb-3">
                        Tagihan <strong>tidak dimulai di bulan Start Date</strong>, melainkan di bulan pertama setelah satu siklus penuh berjalan dari Start Date.
                        Setiap siklus menghasilkan satu invoice yang dijadwalkan di bulan berakhirnya siklus tersebut.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        {{-- Contoh Monthly --}}
                        <div class="bg-white dark:bg-dark-800 border border-amber-200 dark:border-amber-900/50 rounded-lg p-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide">Contoh: Monthly</span>
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
                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide">Contoh: Quarterly</span>
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
                                <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wide">Aturan Umum</span>
                            </div>
                            <div class="text-xs text-dark-600 dark:text-dark-400 space-y-1.5">
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                                    <span>Invoice pertama selalu di bulan <strong>setelah</strong> Start Date (bukan di bulan Start Date itu sendiri)</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                                    <span>Siklus valid jika tanggal akhir siklus ≤ End Date</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" />
                                    <span>Bulan yang bukan bagian dari siklus frequency tidak akan muncul saat generate</span>
                                </div>
                                <div class="flex items-start gap-1.5">
                                    <x-icon name="information-circle" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" />
                                    <span>Jumlah total invoice = jumlah siklus yang berakhir sebelum atau tepat di End Date</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <x-tab :selected="__('pages.templates')">
        <x-tab.items :tab="__('pages.templates')">
            <x-slot:right>
                <x-icon name="document-text" class="w-5 h-5" />
            </x-slot:right>
            <livewire:recurring-invoices.templates-tab />
        </x-tab.items>
        <x-tab.items :tab="__('pages.monthly')">
            <x-slot:right>
                <x-icon name="calendar" class="w-5 h-5" />
            </x-slot:right>
            <livewire:recurring-invoices.monthly-tab />
        </x-tab.items>
        <x-tab.items :tab="__('pages.analytics')">
            <x-slot:right>
                <x-icon name="chart-bar" class="w-5 h-5" />
            </x-slot:right>
            <livewire:recurring-invoices.analytics-tab />
        </x-tab.items>
    </x-tab>

    <!-- Modal Components -->
    <livewire:recurring-invoices.view-template />
</div>
