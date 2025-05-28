<!-- filepath: e:\Application\finance-management\resources\views\livewire\dashboard.blade.php -->
<section class="w-full p-6 bg-white dark:bg-zinc-800">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard Keuangan</h1>
        <p class="text-gray-500 dark:text-zinc-400">Selamat datang kembali, berikut ringkasan keuangan Anda</p>
    </div>

    <!-- Stats Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-md dark:shadow-zinc-950/25 p-6 border-l-4 border-blue-500 transition-all duration-500 hover:shadow-lg dark:hover:shadow-zinc-950/50 transform hover:-translate-y-1"
            x-data="{ isVisible: false }" x-init="setTimeout(() => { isVisible = true }, 100)"
            :class="{ 'opacity-0': !isVisible, 'opacity-100': isVisible }">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalRevenue }}</p>
                    <p
                        class="text-xs {{ $revenueGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-2 flex items-center">
                        @if ($revenueGrowth >= 0)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                    clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                        {{ abs($revenueGrowth) }}% dari bulan lalu
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-md dark:shadow-zinc-950/25 p-6 border-l-4 border-amber-500 transition-all duration-500 hover:shadow-lg dark:hover:shadow-zinc-950/50 transform hover:-translate-y-1"
            x-data="{ isVisible: false }" x-init="setTimeout(() => { isVisible = true }, 200)"
            :class="{ 'opacity-0': !isVisible, 'opacity-100': isVisible }">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Tagihan Tertunda</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $outstanding }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                        Perlu ditindaklanjuti
                    </p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 dark:text-amber-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-md dark:shadow-zinc-950/25 p-6 border-l-4 border-indigo-500 transition-all duration-500 hover:shadow-lg dark:hover:shadow-zinc-950/50 transform hover:-translate-y-1"
            x-data="{ isVisible: false }" x-init="setTimeout(() => { isVisible = true }, 300)"
            :class="{ 'opacity-0': !isVisible, 'opacity-100': isVisible }">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Klien</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalClients }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $newClientsThisMonth }} klien baru bulan ini
                    </p>
                </div>
                <div class="bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 dark:text-indigo-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Bank Balance -->
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-md dark:shadow-zinc-950/25 p-6 border-l-4 border-emerald-500 transition-all duration-500 hover:shadow-lg dark:hover:shadow-zinc-950/50 transform hover:-translate-y-1"
            x-data="{ isVisible: false }" x-init="setTimeout(() => { isVisible = true }, 400)"
            :class="{ 'opacity-0': !isVisible, 'opacity-100': isVisible }">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Saldo Bank</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalBankBalance }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                        Total semua akun bank
                    </p>
                </div>
                <div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500 dark:text-emerald-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg shadow-md dark:shadow-zinc-950/25 w-full"
            x-data="{
                isVisible: false,
                monthlyRevenue: @js($monthlyRevenue), // Menggunakan data yang sudah ada
                init() {
                    setTimeout(() => {
                        this.isVisible = true;
                        this.initChart();
                    }, 500);
                },
                initChart() {
                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    const isDark = document.documentElement.classList.contains('dark');
            
                    // Extract labels dan data dari monthlyRevenue
                    const labels = this.monthlyRevenue.map(item => item.month);
                    const data = this.monthlyRevenue.map(item => parseFloat(item.revenue));
            
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels, // Menggunakan data real: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May']
                            datasets: [{
                                label: 'Pendapatan',
                                data: data, // Menggunakan data real: [0, 85474089.08, 127341312.06, ...]
                                backgroundColor: isDark ? 'rgba(59, 130, 246, 0.1)' : 'rgba(59, 130, 246, 0.1)',
                                borderColor: isDark ? 'rgba(96, 165, 250, 0.8)' : 'rgba(59, 130, 246, 0.8)',
                                borderWidth: 2,
                                pointBackgroundColor: isDark ? '#18181b' : '#ffffff',
                                pointBorderColor: isDark ? 'rgba(96, 165, 250, 0.8)' : 'rgba(59, 130, 246, 0.8)',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart'
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        drawBorder: false,
                                        color: isDark ? 'rgba(161, 161, 170, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                                    },
                                    ticks: {
                                        color: isDark ? '#a1a1aa' : '#6b7280',
                                        callback: function(value) {
                                            // Format untuk angka besar sesuai data Anda
                                            if (value >= 1000000000) {
                                                return 'Rp ' + (value / 1000000000).toFixed(1) + ' Miliar';
                                            } else if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(0) + ' Juta';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + ' Ribu';
                                            }
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: isDark ? '#a1a1aa' : '#6b7280'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: isDark ? 'rgba(39, 39, 42, 0.9)' : 'rgba(0, 0, 0, 0.8)',
                                    titleColor: isDark ? '#fafafa' : '#ffffff',
                                    bodyColor: isDark ? '#fafafa' : '#ffffff',
                                    borderColor: isDark ? 'rgba(96, 165, 250, 0.3)' : 'rgba(59, 130, 246, 0.3)',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.y;
                                            return 'Pendapatan: Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }
            }" x-init="init()"
            :class="{
                'opacity-0': !isVisible,
                'opacity-100 transform translate-y-0': isVisible,
                'transform translate-y-4': !isVisible
            }"
            class="transition-all duration-1000">

            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Ringkasan Pendapatan</h2>
            <canvas id="revenueChart"></canvas>
        </div>

        <!-- Most Used Services Chart -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg shadow-md dark:shadow-zinc-950/25" x-data="{
            isVisible: false,
            init() {
                setTimeout(() => {
                    this.isVisible = true;
                    this.initChart();
                }, 300);
            },
            initChart() {
                const ctx = document.getElementById('servicesChart').getContext('2d');
                const isDark = document.documentElement.classList.contains('dark');
        
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Konsultasi Pajak', 'Pembukuan', 'Audit Keuangan', 'Izin Usaha', 'Lainnya'],
                        datasets: [{
                            data: [35, 25, 20, 15, 5],
                            backgroundColor: [
                                isDark ? 'rgba(34, 197, 94, 0.8)' : 'rgba(16, 185, 129, 0.8)',
                                isDark ? 'rgba(59, 130, 246, 0.8)' : 'rgba(59, 130, 246, 0.8)',
                                isDark ? 'rgba(168, 85, 247, 0.8)' : 'rgba(147, 51, 234, 0.8)',
                                isDark ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)',
                                isDark ? 'rgba(239, 68, 68, 0.8)' : 'rgba(239, 68, 68, 0.8)'
                            ],
                            borderColor: [
                                isDark ? 'rgba(34, 197, 94, 1)' : 'rgba(16, 185, 129, 1)',
                                isDark ? 'rgba(59, 130, 246, 1)' : 'rgba(59, 130, 246, 1)',
                                isDark ? 'rgba(168, 85, 247, 1)' : 'rgba(147, 51, 234, 1)',
                                isDark ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)',
                                isDark ? 'rgba(239, 68, 68, 1)' : 'rgba(239, 68, 68, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 2000,
                            easing: 'easeOutQuart'
                        },
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2.0,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: isDark ? '#ffffff' : '#374151',
                                    usePointStyle: true,
                                    padding: 15,
                                    boxWidth: 12,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        cutout: '55%',
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10
                            }
                        }
                    }
                });
            }
        }"
            x-init="init()"
            :class="{
                'opacity-0': !isVisible,
                'opacity-100 transform translate-y-0': isVisible,
                'transform translate-y-4': !
                    isVisible
            }"
            class="transition-all duration-1000">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Layanan Paling Diminati</h2>
            <canvas id="servicesChart"></canvas>
        </div>
    </div>

    <!-- Business Insights Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Earning Clients -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-lg dark:shadow-zinc-950/50 border border-gray-100 dark:border-zinc-800"
            x-data="{ isVisible: false }" x-init="setTimeout(() => { isVisible = true }, 700)"
            :class="{ 'opacity-0': !isVisible, 'opacity-100 transform translate-y-0': isVisible, 'transform translate-y-4': !
                    isVisible }"
            class="transition-all duration-1000">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-zinc-800">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                        <div class="w-2 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full mr-3"></div>
                        Klien Pendapatan Tertinggi
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Berdasarkan total transaksi</p>
                </div>
                <a href="#"
                    class="bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200 flex items-center">
                    <span>Lihat Semua</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="space-y-3">
                @foreach ($topEarningClients as $client)
                    <div
                        class="group relative overflow-hidden bg-gradient-to-r from-gray-50 to-gray-100/50 dark:from-zinc-800 dark:to-zinc-800/50 rounded-xl p-4 hover:shadow-md dark:hover:shadow-zinc-950/25 transition-all duration-300 hover:scale-[1.02] border border-transparent hover:border-blue-200 dark:hover:border-blue-800/30">
                        <!-- Ranking Badge -->
                        <div
                            class="absolute top-2 right-2 opacity-10 group-hover:opacity-20 transition-opacity duration-300">
                            <span
                                class="text-6xl font-black text-gray-300 dark:text-zinc-600">#{{ $client['rank'] }}</span>
                        </div>

                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center space-x-4">
                                <!-- Rank Circle with gradient -->
                                <div class="relative">
                                    @php
                                        $gradientColors = [
                                            1 => 'from-yellow-400 to-yellow-600',
                                            2 => 'from-gray-400 to-gray-600',
                                            3 => 'from-orange-400 to-orange-600',
                                            4 => 'from-blue-400 to-blue-600',
                                        ];
                                        $textColors = [
                                            1 => 'text-yellow-800',
                                            2 => 'text-gray-800',
                                            3 => 'text-orange-800',
                                            4 => 'text-blue-800',
                                        ];
                                    @endphp
                                    <div
                                        class="bg-gradient-to-br {{ $gradientColors[$client['rank']] ?? 'from-indigo-400 to-indigo-600' }} rounded-full w-14 h-14 flex items-center justify-center shadow-lg">
                                        <span class="text-lg font-bold text-white">{{ $client['rank'] }}</span>
                                    </div>
                                    @if ($client['rank'] == 1)
                                        <div class="absolute -top-1 -right-1">
                                            <div
                                                class="w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3 w-3 text-yellow-800" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Client Info -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">
                                            {{ $client['name'] }}</h3>
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full">
                                            {{ ucfirst($client['type']) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-zinc-400">
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            Klien Premium
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Revenue Info -->
                            <div class="text-right">
                                <div class="flex items-baseline space-x-1 mb-2">
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($client['total_revenue'] / 1000000, 1) }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">Juta</span>
                                </div>

                                <!-- Growth Indicator -->
                                <div class="flex items-center justify-end space-x-1">
                                    <div
                                        class="flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $client['growth'] >= 0 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                        @if ($client['growth'] >= 0)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                        @endif
                                        {{ $client['growth'] >= 0 ? '+' : '' }}{{ $client['growth'] }}%
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">bulan ini</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between text-xs text-gray-500 dark:text-zinc-400 mb-2">
                                <span>Progress Target</span>
                                <span>{{ rand(70, 95) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                <div class="bg-gradient-to-r {{ $gradientColors[$client['rank']] ?? 'from-indigo-400 to-indigo-600' }} h-2 rounded-full transition-all duration-700 ease-out"
                                    style="width: {{ rand(70, 95) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Footer -->
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-zinc-800">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500 dark:text-zinc-400">Total dari {{ count($topEarningClients) }} klien
                        teratas</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        Rp {{ number_format(collect($topEarningClients)->sum('total_revenue') / 1000000, 1) }}M
                    </span>
                </div>
            </div>
        </div>

        <!-- Revenue by Service Type -->
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-lg shadow-md h-fit dark:shadow-zinc-950/25"
            x-data="{
                isVisible: false,
                init() {
                    setTimeout(() => {
                        this.isVisible = true;
                        this.initChart();
                    }, 800);
                },
                initChart() {
                    const ctx = document.getElementById('revenueByServiceChart').getContext('2d');
                    const isDark = document.documentElement.classList.contains('dark');
            
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Konsultasi Pajak', 'Pembukuan', 'Audit Keuangan', 'Izin Usaha'],
                            datasets: [{
                                label: 'Pendapatan (Juta)',
                                data: [89.2, 63.5, 51.8, 38.7],
                                backgroundColor: [
                                    isDark ? 'rgba(34, 197, 94, 0.7)' : 'rgba(16, 185, 129, 0.7)',
                                    isDark ? 'rgba(59, 130, 246, 0.7)' : 'rgba(59, 130, 246, 0.7)',
                                    isDark ? 'rgba(168, 85, 247, 0.7)' : 'rgba(147, 51, 234, 0.7)',
                                    isDark ? 'rgba(251, 191, 36, 0.7)' : 'rgba(245, 158, 11, 0.7)'
                                ],
                                borderColor: [
                                    isDark ? 'rgba(34, 197, 94, 1)' : 'rgba(16, 185, 129, 1)',
                                    isDark ? 'rgba(59, 130, 246, 1)' : 'rgba(59, 130, 246, 1)',
                                    isDark ? 'rgba(168, 85, 247, 1)' : 'rgba(147, 51, 234, 1)',
                                    isDark ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)'
                                ],
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart'
                            },
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: isDark ? 'rgba(161, 161, 170, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                                    },
                                    ticks: {
                                        color: isDark ? '#a1a1aa' : '#6b7280',
                                        callback: function(value) {
                                            return 'Rp ' + value + 'M';
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: isDark ? '#a1a1aa' : '#6b7280'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            }" x-init="init()"
            :class="{
                'opacity-0': !isVisible,
                'opacity-100 transform translate-y-0': isVisible,
                'transform translate-y-4': !
                    isVisible
            }"
            class="transition-all duration-1000">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Pendapatan Berdasarkan Jenis Layanan
                </h2>
                <span class="text-sm text-gray-500 dark:text-zinc-400">Tahun Ini</span>
            </div>
            <canvas id="revenueByServiceChart" class="w-full h-64"></canvas>
        </div>
    </div>
</section>

<!-- Include Chart.js for the charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
