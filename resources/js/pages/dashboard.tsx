import { usePage } from '@inertiajs/react';
import {
    ArrowDownRight,
    ArrowUpRight,
    Building2,
    FileText,
    TrendingDown,
    TrendingUp,
    Wallet,
} from 'lucide-react';
import * as React from 'react';
import ReactApexChart from 'react-apexcharts';
import { AppLayout } from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { PageHeader } from '@/components/shared/page-header';
import { StatsCard } from '@/components/shared/stats-card';
import { formatCurrency } from '@/lib/utils';
import type { SharedProps } from '@/types';

interface Stats {
    total_balance: number;
    income_this_month: number;
    expenses_this_month: number;
    net_this_month: number;
    pending_invoices_count: number;
    pending_invoices_amount: number;
}

interface ChartPoint {
    label: string;
    income: number;
    expenses: number;
}

interface CategoryExpense {
    name: string;
    value: number;
    color: string;
}

interface BankAccount {
    id: number;
    name: string;
    bank: string;
    account_number: string;
    balance: number;
}

interface PendingInvoice {
    id: number;
    invoice_number: string;
    client: string;
    amount: number;
    due_date: string;
    status: string;
    days_until_due: number;
}

interface RecentTransaction {
    date: string;
    description: string;
    type: 'income' | 'expense';
    amount: number;
    account: string;
}

interface DashboardProps extends SharedProps {
    stats: Stats;
    cashFlowChart: ChartPoint[];
    expensesByCategory: CategoryExpense[];
    bankAccounts: BankAccount[];
    pendingInvoices: PendingInvoice[];
    recentTransactions: RecentTransaction[];
}

function StatusBadge({ status }: { status: string }) {
    const map: Record<string, { label: string; variant: 'yellow' | 'red' | 'blue' }> = {
        sent: { label: 'Terkirim', variant: 'blue' },
        partially_paid: { label: 'Sebagian', variant: 'yellow' },
        overdue: { label: 'Jatuh Tempo', variant: 'red' },
    };
    const cfg = map[status] ?? { label: status, variant: 'blue' };
    return <Badge variant={cfg.variant}>{cfg.label}</Badge>;
}

export default function Dashboard() {
    const { stats, cashFlowChart, expensesByCategory, bankAccounts, pendingInvoices, recentTransactions } =
        usePage<DashboardProps>().props;

    const isDark =
        typeof window !== 'undefined' && document.documentElement.classList.contains('dark');

    const chartTextColor = isDark ? '#a1a1aa' : '#6b7280';
    const chartGridColor = isDark ? '#3f3f46' : '#f3f4f6';
    const chartBg = isDark ? '#1e1e1e' : '#ffffff';

    const cashFlowOptions: ApexCharts.ApexOptions = {
        chart: { type: 'bar', toolbar: { show: false }, background: 'transparent' },
        plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: cashFlowChart.map((d) => d.label),
            labels: { style: { colors: chartTextColor, fontSize: '12px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: chartTextColor, fontSize: '11px' },
                formatter: (v) => {
                    if (v >= 1_000_000) return `${(v / 1_000_000).toFixed(1)}jt`;
                    if (v >= 1_000) return `${(v / 1_000).toFixed(0)}rb`;
                    return String(v);
                },
            },
        },
        grid: { borderColor: chartGridColor, strokeDashArray: 4 },
        colors: ['#2563eb', '#ef4444'],
        legend: { labels: { colors: chartTextColor }, fontSize: '12px' },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: (v) => formatCurrency(v) },
        },
    };

    const cashFlowSeries = [
        { name: 'Pemasukan', data: cashFlowChart.map((d) => d.income) },
        { name: 'Pengeluaran', data: cashFlowChart.map((d) => d.expenses) },
    ];

    const donutOptions: ApexCharts.ApexOptions = {
        chart: { type: 'donut', background: 'transparent' },
        labels: expensesByCategory.map((d) => d.name),
        colors: expensesByCategory.map((d) => d.color),
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            labels: { colors: chartTextColor },
            fontSize: '12px',
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: (v) => formatCurrency(v) },
        },
        plotOptions: {
            pie: { donut: { size: '65%', labels: { show: false } } },
        },
    };

    const donutSeries = expensesByCategory.map((d) => d.value);

    const net = stats.net_this_month;
    const netPositive = net >= 0;

    return (
        <div className="space-y-6">
            <PageHeader title="Dashboard" description="Ringkasan keuangan bulan ini" />

            {/* Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <StatsCard
                    label="Total Saldo"
                    value={formatCurrency(stats.total_balance)}
                    icon={<Wallet />}
                    color="blue"
                />
                <StatsCard
                    label="Pemasukan Bulan Ini"
                    value={formatCurrency(stats.income_this_month)}
                    icon={<TrendingUp />}
                    color="green"
                />
                <StatsCard
                    label="Pengeluaran Bulan Ini"
                    value={formatCurrency(stats.expenses_this_month)}
                    icon={<TrendingDown />}
                    color="red"
                />
                <StatsCard
                    label="Invoice Tertunda"
                    value={`${stats.pending_invoices_count} (${formatCurrency(stats.pending_invoices_amount)})`}
                    icon={<FileText />}
                    color="orange"
                />
            </div>

            {/* Net row */}
            <div className="flex items-center gap-2 px-1">
                <div
                    className={`flex items-center gap-1.5 text-sm font-semibold ${
                        netPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                    }`}
                >
                    {netPositive ? (
                        <ArrowUpRight className="w-4 h-4" />
                    ) : (
                        <ArrowDownRight className="w-4 h-4" />
                    )}
                    Net bulan ini: {formatCurrency(Math.abs(net))}
                </div>
            </div>

            {/* Charts */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <Card className="xl:col-span-2">
                    <CardHeader>
                        <CardTitle className="text-base font-semibold">Arus Kas — 6 Bulan Terakhir</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {cashFlowChart.length > 0 ? (
                            <ReactApexChart
                                type="bar"
                                options={cashFlowOptions}
                                series={cashFlowSeries}
                                height={240}
                            />
                        ) : (
                            <div className="h-60 flex items-center justify-center text-sm text-dark-400 dark:text-dark-500">
                                Belum ada data transaksi
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold">Pengeluaran per Kategori</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {donutSeries.length > 0 ? (
                            <ReactApexChart
                                type="donut"
                                options={donutOptions}
                                series={donutSeries}
                                height={240}
                            />
                        ) : (
                            <div className="h-60 flex items-center justify-center text-sm text-dark-400 dark:text-dark-500">
                                Belum ada data pengeluaran
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Bottom grid */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">
                {/* Bank Accounts */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold">Rekening Bank</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {bankAccounts.length === 0 && (
                            <p className="text-sm text-dark-400 dark:text-dark-500 text-center py-4">
                                Belum ada rekening
                            </p>
                        )}
                        {bankAccounts.map((acc) => (
                            <div
                                key={acc.id}
                                className="flex items-center gap-3 p-3 rounded-xl bg-secondary-50 dark:bg-dark-600/40"
                            >
                                <div className="h-9 w-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                                    <Building2 className="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-medium text-dark-900 dark:text-dark-50 truncate">
                                        {acc.name}
                                    </p>
                                    <p className="text-xs text-dark-500 dark:text-dark-400">{acc.bank}</p>
                                </div>
                                <p className="text-sm font-semibold text-dark-900 dark:text-dark-50 shrink-0">
                                    {formatCurrency(acc.balance)}
                                </p>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                {/* Pending Invoices */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold">Invoice Tertunda</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {pendingInvoices.length === 0 && (
                            <p className="text-sm text-dark-400 dark:text-dark-500 text-center py-4">
                                Tidak ada invoice tertunda
                            </p>
                        )}
                        {pendingInvoices.map((inv) => (
                            <div key={inv.id} className="space-y-1">
                                <div className="flex items-center justify-between gap-2">
                                    <p className="text-xs font-medium text-dark-900 dark:text-dark-50 truncate">
                                        {inv.invoice_number}
                                    </p>
                                    <StatusBadge status={inv.status} />
                                </div>
                                <div className="flex items-center justify-between gap-2">
                                    <p className="text-xs text-dark-500 dark:text-dark-400 truncate">
                                        {inv.client}
                                    </p>
                                    <p className="text-xs font-semibold text-dark-700 dark:text-dark-300 shrink-0">
                                        {formatCurrency(inv.amount)}
                                    </p>
                                </div>
                                {inv.days_until_due < 0 && (
                                    <p className="text-[10px] text-red-500 dark:text-red-400">
                                        Terlambat {Math.abs(inv.days_until_due)} hari
                                    </p>
                                )}
                                {inv.days_until_due >= 0 && inv.days_until_due <= 7 && (
                                    <p className="text-[10px] text-yellow-600 dark:text-yellow-400">
                                        Jatuh tempo dalam {inv.days_until_due} hari
                                    </p>
                                )}
                            </div>
                        ))}
                    </CardContent>
                </Card>

                {/* Recent Transactions */}
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold">Transaksi Terbaru</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {recentTransactions.length === 0 && (
                            <p className="text-sm text-dark-400 dark:text-dark-500 text-center py-4">
                                Belum ada transaksi
                            </p>
                        )}
                        {recentTransactions.map((tx, i) => (
                            <div key={i} className="flex items-center gap-3 py-1">
                                <div
                                    className={`h-7 w-7 rounded-lg flex items-center justify-center shrink-0 ${
                                        tx.type === 'income'
                                            ? 'bg-green-50 dark:bg-green-900/20'
                                            : 'bg-red-50 dark:bg-red-900/20'
                                    }`}
                                >
                                    {tx.type === 'income' ? (
                                        <ArrowUpRight className="w-3.5 h-3.5 text-green-600 dark:text-green-400" />
                                    ) : (
                                        <ArrowDownRight className="w-3.5 h-3.5 text-red-600 dark:text-red-400" />
                                    )}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs font-medium text-dark-800 dark:text-dark-200 truncate">
                                        {tx.description}
                                    </p>
                                    <p className="text-[10px] text-dark-400 dark:text-dark-500">{tx.date}</p>
                                </div>
                                <p
                                    className={`text-xs font-semibold shrink-0 ${
                                        tx.type === 'income'
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400'
                                    }`}
                                >
                                    {tx.type === 'income' ? '+' : '-'}
                                    {formatCurrency(tx.amount)}
                                </p>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}

Dashboard.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
