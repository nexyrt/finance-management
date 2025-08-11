<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Income vs Expense</h1>
        <button wire:click="refreshData" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Refresh Data
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border dark:border-gray-700 p-6">
        <div x-data="chartJsComponent(@js($chartData))" x-init="createChart()" wire:key="chart-{{ json_encode($chartData) }}">
            <canvas id="income-expense-chart" class="w-full"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function chartJsComponent(data) {
        return {
            chart: null,

            createChart() {
                const ctx = document.getElementById('income-expense-chart');
                if (!ctx) return;

                const isDark = document.documentElement.classList.contains('dark');

                const labels = data.map(item => item.month);
                const incomeData = data.map(item => item.income);
                const expenseData = data.map(item => item.expense);

                if (this.chart) {
                    this.chart.destroy();
                }

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Income',
                            data: incomeData,
                            backgroundColor: '#10B981',
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                        }, {
                            label: 'Expense',
                            data: expenseData,
                            backgroundColor: '#EF4444',
                            borderColor: '#DC2626',
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: isDark ? '#F3F4F6' : '#1F2937'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y + 'K IDR';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: isDark ? '#D1D5DB' : '#374151'
                                },
                                grid: {
                                    color: isDark ? '#374151' : '#E5E7EB'
                                }
                            },
                            y: {
                                ticks: {
                                    color: isDark ? '#D1D5DB' : '#374151',
                                    callback: function(value) {
                                        return value + 'K';
                                    }
                                },
                                grid: {
                                    color: isDark ? '#374151' : '#E5E7EB'
                                },
                                title: {
                                    display: true,
                                    text: 'Amount (IDR)',
                                    color: isDark ? '#D1D5DB' : '#374151'
                                }
                            }
                        },
                        elements: {
                            bar: {
                                borderRadius: 6
                            }
                        }
                    }
                });

                // Set canvas height
                ctx.style.height = '400px';
            }
        }
    }
</script>
