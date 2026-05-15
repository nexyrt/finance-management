import { Head, router } from '@inertiajs/react';
import { toast } from 'sonner';
import {
    ArrowDownLeft,
    ArrowUpRight,
    BadgeDollarSign,
    Building2,
    ChevronRight,
    CreditCard,
    Edit2,
    Hash,
    Landmark,
    MapPin,
    Plus,
    RefreshCw,
    Trash2,
    TrendingDown,
    TrendingUp,
    Wallet,
} from 'lucide-react';
import * as React from 'react';
import ReactApexChart from 'react-apexcharts';
import { Button } from '@/components/ui/button';
import { CurrencyInput } from '@/components/shared/currency-input';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { EmptyState } from '@/components/shared/empty-state';
import { PageHeader } from '@/components/shared/page-header';
import { StatsCard } from '@/components/shared/stats-card';
import { AppLayout } from '@/layouts/app-layout';
import { cn, formatCurrency, toastError, toastErrors } from '@/lib/utils';
import type { SharedProps } from '@/types';

/* ─────────────────────────────────── types ─── */

interface BankAccount {
    id: number;
    account_name: string;
    account_number: string;
    bank_name: string;
    branch: string | null;
    initial_balance: number;
    balance: number;
    monthly_income: number;
    monthly_expense: number;
    trend: number;
    transaction_count: number;
    payment_count: number;
}

interface PageStats {
    total_balance: number;
    total_income: number;
    total_expense: number;
    account_count: number;
}

interface Props extends SharedProps {
    accounts: BankAccount[];
    stats: PageStats;
}

interface ChartData {
    months: { label: string; income: number; expense: number }[];
    categories: { label: string; total: number }[];
}

/* ─────────────────────────────────── helpers ─── */

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function getBankInitials(bankName: string): string {
    return bankName
        .split(' ')
        .map((w) => w[0])
        .join('')
        .toUpperCase()
        .slice(0, 3);
}

const BANK_COLORS: Record<string, string> = {
    BCA: 'from-blue-600 to-blue-700',
    BNI: 'from-orange-500 to-orange-600',
    BRI: 'from-blue-800 to-blue-900',
    MANDIRI: 'from-yellow-500 to-yellow-600',
    CIMB: 'from-red-600 to-red-700',
    BSI: 'from-green-600 to-green-700',
    DEFAULT: 'from-primary-600 to-primary-700',
};

function getBankGradient(bankName: string): string {
    const upper = bankName.toUpperCase();
    for (const [key, val] of Object.entries(BANK_COLORS)) {
        if (upper.includes(key)) return val;
    }
    return BANK_COLORS.DEFAULT;
}

/* ─────────────────────────────────── account card ─── */

interface AccountCardProps {
    account: BankAccount;
    selected: boolean;
    onClick: () => void;
}

function AccountCard({ account, selected, onClick }: AccountCardProps) {
    const gradient = getBankGradient(account.bank_name);
    const isPositiveTrend = account.trend >= 0;

    return (
        <button
            onClick={onClick}
            className={cn(
                'w-full text-left rounded-xl border transition-all duration-200 overflow-hidden group',
                selected
                    ? 'border-primary-500 shadow-md shadow-primary-100 dark:shadow-primary-900/20'
                    : 'border-secondary-200 dark:border-dark-600 hover:border-primary-300 dark:hover:border-primary-700',
            )}
        >
            {/* Top accent bar */}
            <div className={cn('h-1 w-full bg-gradient-to-r', gradient)} />

            <div className="p-4 bg-white dark:bg-dark-700">
                <div className="flex items-start justify-between gap-2 mb-3">
                    <div className="flex items-center gap-2.5">
                        {/* Bank initials badge */}
                        <div className={cn('w-9 h-9 rounded-lg bg-gradient-to-br flex items-center justify-center shrink-0 shadow-sm', gradient)}>
                            <span className="text-[10px] font-black text-white tracking-wider">
                                {getBankInitials(account.bank_name)}
                            </span>
                        </div>
                        <div className="min-w-0">
                            <p className="text-sm font-semibold text-dark-900 dark:text-dark-50 truncate leading-tight">
                                {account.account_name}
                            </p>
                            <p className="text-[11px] text-dark-500 dark:text-dark-400 truncate">
                                {account.bank_name}{account.branch ? ` · ${account.branch}` : ''}
                            </p>
                        </div>
                    </div>
                    <ChevronRight className={cn(
                        'w-4 h-4 shrink-0 mt-0.5 transition-transform duration-200',
                        selected ? 'text-primary-500 rotate-90' : 'text-dark-300 dark:text-dark-600 group-hover:translate-x-0.5',
                    )} />
                </div>

                {/* Account number */}
                <p className="font-mono text-xs text-dark-400 dark:text-dark-500 mb-3 tracking-wider">
                    {account.account_number}
                </p>

                {/* Balance */}
                <div className="flex items-end justify-between gap-2">
                    <div>
                        <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-wide mb-0.5">Saldo</p>
                        <p className="text-base font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                            {formatCurrency(account.balance)}
                        </p>
                    </div>
                    {/* Trend badge */}
                    <div className={cn(
                        'flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-semibold',
                        isPositiveTrend
                            ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400'
                            : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                    )}>
                        {isPositiveTrend ? <TrendingUp className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
                        <span>30h</span>
                    </div>
                </div>
            </div>
        </button>
    );
}

/* ─────────────────────────────────── account detail ─── */

interface AccountDetailProps {
    account: BankAccount;
    onEdit: (account: BankAccount) => void;
    onDelete: (account: BankAccount) => void;
}

function AccountDetail({ account, onEdit, onDelete }: AccountDetailProps) {
    const [chartData, setChartData] = React.useState<ChartData | null>(null);
    const [chartLoading, setChartLoading] = React.useState(false);
    const gradient = getBankGradient(account.bank_name);
    const isDark = document.documentElement.classList.contains('dark');

    React.useEffect(() => {
        setChartData(null);
        setChartLoading(true);
        fetch(`/bank-accounts/${account.id}/chart-data`, {
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
        })
            .then((r) => r.json())
            .then((d) => setChartData(d))
            .catch(() => toastError('Gagal memuat data grafik.'))
            .finally(() => setChartLoading(false));
    }, [account.id]);

    const netCashflow = account.monthly_income - account.monthly_expense;

    return (
        <div className="space-y-5">
            {/* Hero card */}
            <div className={cn('rounded-xl bg-gradient-to-br text-white overflow-hidden relative', gradient)}>
                {/* decorative circles */}
                <div className="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/5" />
                <div className="absolute -bottom-6 -right-2 w-24 h-24 rounded-full bg-white/5" />

                <div className="relative p-5 sm:p-6">
                    <div className="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p className="text-white/70 text-xs uppercase tracking-widest mb-0.5">Rekening</p>
                            <h2 className="text-lg sm:text-xl font-bold leading-tight">{account.account_name}</h2>
                            <p className="text-white/70 text-sm mt-0.5">
                                {account.bank_name}{account.branch ? ` · ${account.branch}` : ''}
                            </p>
                        </div>
                        <div className="flex items-center gap-1.5 shrink-0">
                            <button
                                onClick={() => onEdit(account)}
                                className="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors"
                                title="Edit"
                            >
                                <Edit2 className="w-3.5 h-3.5" />
                            </button>
                            <button
                                onClick={() => onDelete(account)}
                                className="p-2 rounded-lg bg-white/10 hover:bg-red-500/40 transition-colors"
                                title="Hapus"
                            >
                                <Trash2 className="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <div className="font-mono text-white/60 text-sm tracking-widest mb-4">
                        {account.account_number}
                    </div>

                    <div className="flex items-end justify-between gap-3">
                        <div>
                            <p className="text-white/60 text-xs uppercase tracking-wide mb-0.5">Saldo Saat Ini</p>
                            <p className="text-2xl sm:text-3xl font-black tabular-nums">
                                {formatCurrency(account.balance)}
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-white/60 text-[10px] uppercase tracking-wide mb-0.5">Saldo Awal</p>
                            <p className="text-white/80 text-sm font-semibold tabular-nums">
                                {formatCurrency(account.initial_balance)}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Monthly stats */}
            <div className="grid grid-cols-3 gap-3">
                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-3.5">
                    <div className="flex items-center gap-1.5 mb-2">
                        <ArrowDownLeft className="w-3.5 h-3.5 text-emerald-500" />
                        <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-wide font-medium">Masuk</p>
                    </div>
                    <p className="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                        {formatCurrency(account.monthly_income)}
                    </p>
                    <p className="text-[10px] text-dark-400 dark:text-dark-500 mt-0.5">bulan ini</p>
                </div>
                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-3.5">
                    <div className="flex items-center gap-1.5 mb-2">
                        <ArrowUpRight className="w-3.5 h-3.5 text-red-500" />
                        <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-wide font-medium">Keluar</p>
                    </div>
                    <p className="text-sm font-bold text-red-600 dark:text-red-400 tabular-nums">
                        {formatCurrency(account.monthly_expense)}
                    </p>
                    <p className="text-[10px] text-dark-400 dark:text-dark-500 mt-0.5">bulan ini</p>
                </div>
                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-3.5">
                    <div className="flex items-center gap-1.5 mb-2">
                        <RefreshCw className="w-3.5 h-3.5 text-blue-500" />
                        <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-wide font-medium">Net</p>
                    </div>
                    <p className={cn('text-sm font-bold tabular-nums', netCashflow >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400')}>
                        {netCashflow >= 0 ? '+' : ''}{formatCurrency(netCashflow)}
                    </p>
                    <p className="text-[10px] text-dark-400 dark:text-dark-500 mt-0.5">bulan ini</p>
                </div>
            </div>

            {/* Metadata */}
            <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                <h3 className="text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide mb-3">Info Rekening</h3>
                <div className="space-y-2.5">
                    <div className="flex items-center gap-2.5">
                        <Landmark className="w-3.5 h-3.5 text-dark-400 dark:text-dark-500 shrink-0" />
                        <span className="text-xs text-dark-500 dark:text-dark-400 w-16 shrink-0">Bank</span>
                        <span className="text-xs font-medium text-dark-900 dark:text-dark-50">{account.bank_name}</span>
                    </div>
                    {account.branch && (
                        <div className="flex items-center gap-2.5">
                            <MapPin className="w-3.5 h-3.5 text-dark-400 dark:text-dark-500 shrink-0" />
                            <span className="text-xs text-dark-500 dark:text-dark-400 w-16 shrink-0">Cabang</span>
                            <span className="text-xs font-medium text-dark-900 dark:text-dark-50">{account.branch}</span>
                        </div>
                    )}
                    <div className="flex items-center gap-2.5">
                        <Hash className="w-3.5 h-3.5 text-dark-400 dark:text-dark-500 shrink-0" />
                        <span className="text-xs text-dark-500 dark:text-dark-400 w-16 shrink-0">No. Rek</span>
                        <span className="text-xs font-mono font-medium text-dark-900 dark:text-dark-50 tracking-wider">{account.account_number}</span>
                    </div>
                    <div className="flex items-center gap-2.5">
                        <CreditCard className="w-3.5 h-3.5 text-dark-400 dark:text-dark-500 shrink-0" />
                        <span className="text-xs text-dark-500 dark:text-dark-400 w-16 shrink-0">Transaksi</span>
                        <span className="text-xs font-medium text-dark-900 dark:text-dark-50">
                            {account.transaction_count} transaksi · {account.payment_count} pembayaran
                        </span>
                    </div>
                </div>
            </div>

            {/* Charts */}
            {chartLoading ? (
                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-6 flex items-center justify-center h-48">
                    <RefreshCw className="w-5 h-5 text-dark-400 dark:text-dark-500 animate-spin" />
                </div>
            ) : chartData ? (
                <div className="space-y-4">
                    {/* Monthly bar chart */}
                    <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                        <h3 className="text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide mb-3">
                            Cashflow 12 Bulan
                        </h3>
                        <ReactApexChart
                            type="bar"
                            height={200}
                            series={[
                                { name: 'Masuk', data: chartData.months.map((m) => m.income) },
                                { name: 'Keluar', data: chartData.months.map((m) => m.expense) },
                            ]}
                            options={{
                                chart: { toolbar: { show: false }, background: 'transparent', stacked: false },
                                xaxis: {
                                    categories: chartData.months.map((m) => m.label),
                                    labels: { style: { colors: isDark ? '#a1a1aa' : '#71717a', fontSize: '10px' } },
                                    axisBorder: { show: false },
                                    axisTicks: { show: false },
                                },
                                yaxis: {
                                    labels: {
                                        formatter: (v) => v >= 1_000_000 ? `${(v / 1_000_000).toFixed(0)}M` : `${(v / 1000).toFixed(0)}K`,
                                        style: { colors: isDark ? '#a1a1aa' : '#71717a', fontSize: '10px' },
                                    },
                                },
                                colors: ['#10b981', '#f43f5e'],
                                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                                dataLabels: { enabled: false },
                                grid: { borderColor: isDark ? '#27272a' : '#f1f5f9', strokeDashArray: 3 },
                                legend: {
                                    show: true,
                                    position: 'top',
                                    horizontalAlign: 'right',
                                    fontSize: '11px',
                                    labels: { colors: isDark ? '#a1a1aa' : '#71717a' },
                                    markers: { size: 6 },
                                },
                                tooltip: {
                                    y: { formatter: (v) => formatCurrency(v) },
                                    theme: isDark ? 'dark' : 'light',
                                },
                                theme: { mode: isDark ? 'dark' : 'light' },
                            }}
                        />
                    </div>

                    {/* Category breakdown donut */}
                    {chartData.categories.length > 0 && (
                        <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                            <h3 className="text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide mb-3">
                                Top Kategori Pengeluaran
                            </h3>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                <ReactApexChart
                                    type="donut"
                                    height={160}
                                    series={chartData.categories.map((c) => c.total)}
                                    options={{
                                        chart: { background: 'transparent' },
                                        labels: chartData.categories.map((c) => c.label),
                                        colors: ['#2563eb', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#06b6d4'],
                                        legend: { show: false },
                                        dataLabels: { enabled: false },
                                        plotOptions: { pie: { donut: { size: '65%' } } },
                                        tooltip: { y: { formatter: (v) => formatCurrency(v) }, theme: isDark ? 'dark' : 'light' },
                                        stroke: { width: 0 },
                                        theme: { mode: isDark ? 'dark' : 'light' },
                                    }}
                                />
                                <div className="space-y-2">
                                    {chartData.categories.map((cat, i) => {
                                        const colors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-violet-500', 'bg-cyan-500'];
                                        const total = chartData.categories.reduce((s, c) => s + c.total, 0);
                                        const pct = total > 0 ? Math.round((cat.total / total) * 100) : 0;
                                        return (
                                            <div key={i} className="flex items-center gap-2">
                                                <span className={cn('w-2 h-2 rounded-full shrink-0', colors[i % colors.length])} />
                                                <span className="text-[11px] text-dark-600 dark:text-dark-400 truncate flex-1">{cat.label}</span>
                                                <span className="text-[11px] font-semibold text-dark-700 dark:text-dark-300 tabular-nums">{pct}%</span>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            ) : null}
        </div>
    );
}

/* ─────────────────────────────────── account form modal ─── */

interface AccountFormModalProps {
    open: boolean;
    onClose: () => void;
    onSuccess: (account: BankAccount, isEdit: boolean) => void;
    editTarget?: BankAccount | null;
}

function AccountFormModal({ open, onClose, onSuccess, editTarget }: AccountFormModalProps) {
    const isEdit = !!editTarget;
    const [form, setForm] = React.useState({
        account_name: '',
        account_number: '',
        bank_name: '',
        branch: '',
        initial_balance: 0,
    });
    const [errors, setErrors] = React.useState<Record<string, string>>({});
    const [loading, setLoading] = React.useState(false);

    React.useEffect(() => {
        if (open) {
            if (editTarget) {
                setForm({
                    account_name: editTarget.account_name,
                    account_number: editTarget.account_number,
                    bank_name: editTarget.bank_name,
                    branch: editTarget.branch ?? '',
                    initial_balance: editTarget.initial_balance,
                });
            } else {
                setForm({ account_name: '', account_number: '', bank_name: '', branch: '', initial_balance: 0 });
            }
            setErrors({});
        }
    }, [open, editTarget]);

    const handleSubmit = async () => {
        setLoading(true);
        setErrors({});
        const url = isEdit ? `/bank-accounts/${editTarget!.id}` : '/bank-accounts';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), Accept: 'application/json' },
                body: JSON.stringify(form),
            });
            const json = await res.json();
            if (!res.ok) {
                if (res.status === 422 && json.errors) {
                    setErrors(json.errors);
                    toastErrors(json.errors, 'BankAccountForm');
                } else {
                    toastError(json.message ?? 'Terjadi kesalahan.');
                }
                return;
            }
            toast.success(json.message);
            onSuccess(json.account, isEdit);
            onClose();
        } catch {
            toastError('Terjadi kesalahan jaringan.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={(v) => !v && onClose()}>
            <DialogContent size="md">
                <DialogHeader>
                    <div className="flex items-center gap-4 my-3">
                        <div className={cn(
                            'h-12 w-12 rounded-xl flex items-center justify-center',
                            isEdit ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-green-50 dark:bg-green-900/20',
                        )}>
                            <Landmark className={cn('w-6 h-6', isEdit ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400')} />
                        </div>
                        <div>
                            <DialogTitle>{isEdit ? 'Edit Rekening' : 'Tambah Rekening'}</DialogTitle>
                            <p className="text-sm text-dark-500 dark:text-dark-400 mt-0.5">
                                {isEdit ? 'Perbarui data rekening bank' : 'Daftarkan rekening bank baru'}
                            </p>
                        </div>
                    </div>
                </DialogHeader>

                <div className="px-6 pb-2 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input
                            label="Nama Rekening *"
                            value={form.account_name}
                            onChange={(e) => setForm({ ...form, account_name: e.target.value })}
                            placeholder="Rekening Operasional"
                            error={errors.account_name}
                        />
                        <Input
                            label="Nama Bank *"
                            value={form.bank_name}
                            onChange={(e) => setForm({ ...form, bank_name: e.target.value })}
                            placeholder="BCA, BNI, Mandiri..."
                            error={errors.bank_name}
                        />
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input
                            label="Nomor Rekening *"
                            value={form.account_number}
                            onChange={(e) => setForm({ ...form, account_number: e.target.value })}
                            placeholder="1234567890"
                            error={errors.account_number}
                        />
                        <Input
                            label="Cabang"
                            value={form.branch}
                            onChange={(e) => setForm({ ...form, branch: e.target.value })}
                            placeholder="KCP Jakarta Selatan"
                            error={errors.branch}
                        />
                    </div>
                    <CurrencyInput
                        label="Saldo Awal *"
                        value={form.initial_balance}
                        onChange={(v) => setForm({ ...form, initial_balance: v })}
                        hint={isEdit ? 'Mengubah saldo awal akan mempengaruhi saldo saat ini.' : 'Saldo pada saat rekening pertama kali didaftarkan.'}
                        error={errors.initial_balance}
                    />
                </div>

                <DialogFooter>
                    <Button variant="zinc" onClick={onClose} disabled={loading} className="w-full sm:w-auto order-2 sm:order-1">
                        Batal
                    </Button>
                    <Button
                        variant={isEdit ? 'blue' : 'primary'}
                        onClick={handleSubmit}
                        loading={loading}
                        className="w-full sm:w-auto order-1 sm:order-2"
                    >
                        {isEdit ? 'Simpan Perubahan' : 'Tambah Rekening'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

/* ─────────────────────────────────── main page ─── */

export default function BankAccountsIndex({ accounts: initAccounts, stats: initStats }: Props) {
    const [accounts, setAccounts] = React.useState<BankAccount[]>(initAccounts);
    const [stats, setStats] = React.useState<PageStats>(initStats);
    const [selectedId, setSelectedId] = React.useState<number | null>(
        initAccounts.length > 0 ? initAccounts[0].id : null,
    );
    const [formOpen, setFormOpen] = React.useState(false);
    const [editTarget, setEditTarget] = React.useState<BankAccount | null>(null);
    const [deleteTarget, setDeleteTarget] = React.useState<BankAccount | null>(null);
    const [deleteLoading, setDeleteLoading] = React.useState(false);

    const selectedAccount = accounts.find((a) => a.id === selectedId) ?? null;

    const recalcStats = (list: BankAccount[]) => ({
        total_balance: list.reduce((s, a) => s + a.balance, 0),
        total_income: list.reduce((s, a) => s + a.monthly_income, 0),
        total_expense: list.reduce((s, a) => s + a.monthly_expense, 0),
        account_count: list.length,
    });

    const handleFormSuccess = (account: BankAccount, isEdit: boolean) => {
        setAccounts((prev) => {
            const next = isEdit
                ? prev.map((a) => (a.id === account.id ? account : a))
                : [...prev, account];
            setStats(recalcStats(next));
            return next;
        });
        if (!isEdit) setSelectedId(account.id);
    };

    const handleDelete = async () => {
        if (!deleteTarget) return;
        setDeleteLoading(true);
        try {
            const res = await fetch(`/bank-accounts/${deleteTarget.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), Accept: 'application/json' },
            });
            const json = await res.json();
            if (!res.ok) { toastError(json.message ?? 'Gagal menghapus.'); return; }
            toast.success(json.message);
            setAccounts((prev) => {
                const next = prev.filter((a) => a.id !== deleteTarget.id);
                setStats(recalcStats(next));
                if (selectedId === deleteTarget.id) setSelectedId(next[0]?.id ?? null);
                return next;
            });
        } catch {
            toastError('Terjadi kesalahan jaringan.');
        } finally {
            setDeleteLoading(false);
            setDeleteTarget(null);
        }
    };

    const openEdit = (account: BankAccount) => {
        setEditTarget(account);
        setFormOpen(true);
    };
    const openDelete = (account: BankAccount) => setDeleteTarget(account);

    return (
        <>
            <Head title="Bank Accounts" />
            <div className="space-y-6">
                <PageHeader
                    title="Rekening Bank"
                    description="Kelola rekening dan pantau arus kas"
                    action={
                        <Button
                            variant="primary"
                            size="sm"
                            onClick={() => { setEditTarget(null); setFormOpen(true); }}
                        >
                            <Plus className="w-4 h-4 mr-1.5" /> Tambah Rekening
                        </Button>
                    }
                />

                {/* Stats */}
                <div className="grid grid-cols-2 xl:grid-cols-4 gap-4">
                    <StatsCard
                        label="Total Saldo"
                        value={formatCurrency(stats.total_balance)}
                        icon={<Wallet />}
                        color="blue"
                    />
                    <StatsCard
                        label="Pemasukan Bulan Ini"
                        value={formatCurrency(stats.total_income)}
                        icon={<ArrowDownLeft />}
                        color="green"
                    />
                    <StatsCard
                        label="Pengeluaran Bulan Ini"
                        value={formatCurrency(stats.total_expense)}
                        icon={<ArrowUpRight />}
                        color="red"
                    />
                    <StatsCard
                        label="Jumlah Rekening"
                        value={stats.account_count}
                        icon={<BadgeDollarSign />}
                        color="purple"
                    />
                </div>

                {accounts.length === 0 ? (
                    <EmptyState
                        icon={<Building2 className="w-12 h-12" />}
                        title="Belum ada rekening"
                        description="Tambahkan rekening bank pertama untuk mulai mencatat arus kas"
                        action={
                            <Button variant="primary" onClick={() => { setEditTarget(null); setFormOpen(true); }}>
                                <Plus className="w-4 h-4 mr-1.5" /> Tambah Rekening
                            </Button>
                        }
                    />
                ) : (
                    <div className="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                        {/* Left — account list */}
                        <div className="xl:col-span-1 space-y-2.5">
                            <p className="text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide px-0.5">
                                {accounts.length} Rekening
                            </p>
                            {accounts.map((account) => (
                                <AccountCard
                                    key={account.id}
                                    account={account}
                                    selected={selectedId === account.id}
                                    onClick={() => setSelectedId(account.id)}
                                />
                            ))}
                        </div>

                        {/* Right — detail panel */}
                        <div className="xl:col-span-2 xl:sticky xl:top-6">
                            {selectedAccount ? (
                                <AccountDetail
                                    account={selectedAccount}
                                    onEdit={openEdit}
                                    onDelete={openDelete}
                                />
                            ) : (
                                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 flex items-center justify-center h-64">
                                    <p className="text-sm text-dark-400 dark:text-dark-500">Pilih rekening untuk melihat detail</p>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>

            {/* Modals */}
            <AccountFormModal
                open={formOpen}
                onClose={() => { setFormOpen(false); setEditTarget(null); }}
                onSuccess={handleFormSuccess}
                editTarget={editTarget}
            />

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(v) => !v && setDeleteTarget(null)}
                title="Hapus Rekening?"
                description={
                    deleteTarget && (deleteTarget.transaction_count + deleteTarget.payment_count) > 0
                        ? `Rekening "${deleteTarget.account_name}" memiliki ${deleteTarget.transaction_count} transaksi dan ${deleteTarget.payment_count} pembayaran yang akan ikut terhapus permanen.`
                        : `Rekening "${deleteTarget?.account_name}" akan dihapus permanen.`
                }
                onConfirm={handleDelete}
                variant="danger"
            />
        </>
    );
}

BankAccountsIndex.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
