import { Head } from '@inertiajs/react';
import { useState, useEffect, useCallback } from 'react';
import { toast } from 'sonner';
import { Plus, Vault, TrendingUp, TrendingDown, ArrowUpRight, ArrowDownRight, Minus, BarChart3, PieChart, Activity } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { EmptyState } from '@/components/shared/empty-state';
import { AppLayout } from '@/layouts/app-layout';
import { formatCurrency, toastError } from '@/lib/utils';
import type { SharedProps } from '@/types';
import WalletStack from './_components/WalletStack';
import LiveActivityTicker from './_components/LiveActivityTicker';
import CategoryBars from './_components/CategoryBars';
import Sparkline30Days from './_components/Sparkline30Days';
import InsightPanel from './_components/InsightPanel';
import IncomeExpenseChart from './_components/IncomeExpenseChart';
import AccountFormModal from './_components/AccountFormModal';

/* ─────────────────────────────────── types ─── */

export interface BankAccount {
    id: number;
    account_name: string;
    account_number: string;
    last4_account_number?: string;
    bank_name: string;
    branch: string | null;
    initial_balance: number;
    balance: number;
    monthly_income: number;
    monthly_expense: number;
    trend: number;
    trend_percentage?: number;
    sparkline_30d?: number[];
    smart_insight?: string;
    transaction_count: number;
    payment_count: number;
}

interface PageStats {
    total_balance: number;
    total_income: number;
    total_expense: number;
    account_count: number;
    trend_30d_total?: number;
    trend_percentage_total?: number;
}

interface MonthlyStats {
    period_label: string;
    total_income: number;
    total_expense: number;
    net_cashflow: number;
    months: { label: string; income: number; expense: number }[];
    categories: { label: string; total: number }[];
}

interface Props extends SharedProps {
    accounts: BankAccount[];
    stats: PageStats;
}

/* ─────────────────────────────────── helpers ─── */

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

/* ─────────────────────────────────── page ─── */

export default function BankAccountsPage({ accounts: initialAccounts, stats: initialStats }: Props) {
    const [accounts, setAccounts] = useState<BankAccount[]>(initialAccounts);
    const [stats, setStats] = useState<PageStats>(initialStats);
    const [selectedIdx, setSelectedIdx] = useState(0);

    const [modalOpen, setModalOpen] = useState(false);
    const [editAccount, setEditAccount] = useState<BankAccount | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<BankAccount | null>(null);
    const [deleting, setDeleting] = useState(false);

    const selected = accounts[Math.min(selectedIdx, accounts.length - 1)] ?? null;

    // Monthly stats (income/expense/net + 12-month chart + categories)
    const [monthlyStats, setMonthlyStats] = useState<MonthlyStats | null>(null);
    const [monthlyStatsLoading, setMonthlyStatsLoading] = useState(false);
    const [monthlyStatsLoaded, setMonthlyStatsLoaded] = useState<number | null>(null);

    const loadMonthlyStats = useCallback((accountId: number) => {
        if (monthlyStatsLoaded === accountId) return;
        setMonthlyStatsLoading(true);
        fetch(`/bank-accounts/${accountId}/monthly-stats`, {
            headers: { Accept: 'application/json' },
        })
            .then((r) => {
                if (!r.ok) throw new Error('monthly-stats fetch failed');
                return r.json();
            })
            .then((d) => {
                setMonthlyStats(d);
                setMonthlyStatsLoaded(accountId);
            })
            .catch(() => setMonthlyStats(null))
            .finally(() => setMonthlyStatsLoading(false));
    }, [monthlyStatsLoaded]);

    // Reset monthly stats when account changes
    useEffect(() => {
        if (selected && monthlyStatsLoaded !== selected.id) {
            setMonthlyStats(null);
            setMonthlyStatsLoaded(null);
            loadMonthlyStats(selected.id);
        }
    }, [selected?.id]);

    function recalcStats(list: BankAccount[]): PageStats {
        const trend30d = list.reduce((s, a) => s + (a.trend ?? 0), 0);
        const totalInitial = list.reduce((s, a) => s + a.initial_balance, 0);
        return {
            total_balance: list.reduce((s, a) => s + a.balance, 0),
            total_income: list.reduce((s, a) => s + a.monthly_income, 0),
            total_expense: list.reduce((s, a) => s + a.monthly_expense, 0),
            account_count: list.length,
            trend_30d_total: trend30d,
            trend_percentage_total: totalInitial > 0 ? parseFloat(((trend30d / totalInitial) * 100).toFixed(1)) : 0,
        };
    }

    function handleCreated(account: BankAccount) {
        const updated = [...accounts, account];
        setAccounts(updated);
        setStats(recalcStats(updated));
        setSelectedIdx(updated.length - 1);
    }

    function handleUpdated(account: BankAccount) {
        const updated = accounts.map((a) => (a.id === account.id ? account : a));
        setAccounts(updated);
        setStats(recalcStats(updated));
        // Invalidate monthly stats so it reloads
        if (account.id === selected?.id) {
            setMonthlyStats(null);
            setMonthlyStatsLoaded(null);
        }
    }

    async function handleDelete() {
        if (!deleteTarget) return;
        setDeleting(true);
        try {
            const res = await fetch(`/bank-accounts/${deleteTarget.id}`, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });
            const data = await res.json();
            if (!res.ok) {
                toastError(data.message ?? 'Gagal menghapus rekening.');
                return;
            }
            toast.success(data.message ?? 'Rekening dihapus.');
            const updated = accounts.filter((a) => a.id !== deleteTarget.id);
            setAccounts(updated);
            setStats(recalcStats(updated));
            setSelectedIdx((prev) => Math.min(prev, Math.max(0, updated.length - 1)));
            setMonthlyStats(null);
            setMonthlyStatsLoaded(null);
        } catch {
            toastError('Gagal terhubung ke server.');
        } finally {
            setDeleting(false);
            setDeleteTarget(null);
        }
    }

    const trendUp = (stats.trend_30d_total ?? 0) >= 0;
    const trendPct = Math.abs(stats.trend_percentage_total ?? 0);

    const net = monthlyStats?.net_cashflow ?? 0;
    const netPositive = net >= 0;

    return (
        <AppLayout>
            <Head title="Bank Accounts" />

            <div className="min-h-screen space-y-5">
                {/* ══════════════════════ HERO — Premium Banking Dark ══════════════════════ */}
                <div className="relative overflow-hidden rounded-2xl bg-linear-to-b from-zinc-900 to-zinc-950 border border-white/5">
                    {/* Ambient glow */}
                    <div className="absolute -top-32 left-1/2 -translate-x-1/2 w-150 h-75 rounded-full bg-blue-500/10 blur-[80px] pointer-events-none" />
                    <div className="absolute bottom-0 left-0 right-0 h-px bg-linear-to-r from-transparent via-white/10 to-transparent" />

                    <div className="relative px-6 pt-6 pb-8">
                        {/* Top bar */}
                        <div className="flex items-center justify-between mb-8">
                            <div className="flex items-center gap-2 text-white/40 text-xs">
                                <Vault className="w-3.5 h-3.5" />
                                <span>Treasury</span>
                                <span>/</span>
                                <span className="text-white/70">Bank Accounts</span>
                            </div>
                            <Button
                                size="sm"
                                onClick={() => { setEditAccount(null); setModalOpen(true); }}
                                className="gap-1.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 backdrop-blur-sm"
                            >
                                <Plus className="w-3.5 h-3.5" />
                                Tambah Rekening
                            </Button>
                        </div>

                        {accounts.length === 0 ? (
                            <EmptyState
                                icon="credit-card"
                                title="Belum ada rekening"
                                description="Tambahkan rekening bank pertama Anda untuk mulai melacak arus kas."
                                action={{
                                    label: 'Tambah Rekening',
                                    onClick: () => { setEditAccount(null); setModalOpen(true); },
                                }}
                            />
                        ) : (
                            <div className="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-8 items-center">
                                {/* Left — Treasury summary */}
                                <div>
                                    <p className="text-white/40 text-[10px] uppercase tracking-[0.25em] mb-2">
                                        TREASURY · {accounts.length} rekening aktif
                                    </p>
                                    <p className="text-white font-black text-4xl sm:text-5xl tabular-nums tracking-tight leading-none">
                                        {formatCurrency(stats.total_balance)}
                                    </p>
                                    <div className="flex items-center gap-2 mt-3">
                                        {trendUp ? (
                                            <TrendingUp className="w-3.5 h-3.5 text-emerald-400" />
                                        ) : (
                                            <TrendingDown className="w-3.5 h-3.5 text-rose-400" />
                                        )}
                                        <span className={`text-sm font-semibold tabular-nums ${trendUp ? 'text-emerald-400' : 'text-rose-400'}`}>
                                            {trendUp ? '+' : '-'}{trendPct.toFixed(1)}%
                                        </span>
                                        <span className="text-white/30 text-xs">·</span>
                                        <span className="text-white/50 text-xs">
                                            Net {trendUp ? '+' : ''}{formatCurrency(stats.trend_30d_total ?? 0)} (30 hari)
                                        </span>
                                    </div>

                                    {/* Mini aggregate stats */}
                                    <div className="flex items-center gap-4 mt-5">
                                        <div className="flex items-center gap-1.5">
                                            <div className="w-1.5 h-1.5 rounded-full bg-emerald-400" />
                                            <span className="text-[11px] text-white/50">Masuk</span>
                                            <span className="text-[11px] font-bold text-emerald-400 tabular-nums">
                                                +{formatCurrency(stats.total_income)}
                                            </span>
                                        </div>
                                        <div className="w-px h-3 bg-white/15" />
                                        <div className="flex items-center gap-1.5">
                                            <div className="w-1.5 h-1.5 rounded-full bg-rose-400" />
                                            <span className="text-[11px] text-white/50">Keluar</span>
                                            <span className="text-[11px] font-bold text-rose-400 tabular-nums">
                                                -{formatCurrency(stats.total_expense)}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Right — Vertical wallet list */}
                                <div className="w-full lg:w-96">
                                    <WalletStack
                                        accounts={accounts}
                                        selectedIdx={Math.min(selectedIdx, accounts.length - 1)}
                                        onSelect={setSelectedIdx}
                                        onAddAccount={() => { setEditAccount(null); setModalOpen(true); }}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {/* ══════════════════════ DETAIL PANEL ══════════════════════ */}
                {selected && (
                    <div className="space-y-4">

                        {/* ── ROW 1: Stat Cards (left 3 cols) + Live Activity (right) ── */}
                        <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4">

                            {/* Left: 3 mini stat cards + Sparkline */}
                            <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                                {/* Period label */}
                                <div className="flex items-center justify-between mb-3">
                                    <h3 className="text-[10px] font-semibold text-dark-400 dark:text-dark-500 uppercase tracking-widest">
                                        Ringkasan Periode
                                    </h3>
                                    {monthlyStats && (
                                        <span className="text-[10px] text-dark-400 dark:text-dark-500 bg-secondary-100 dark:bg-dark-800 px-2 py-0.5 rounded-md">
                                            {monthlyStats.period_label}
                                        </span>
                                    )}
                                </div>

                                {monthlyStatsLoading ? (
                                    <div className="grid grid-cols-3 gap-3 animate-pulse">
                                        {[1, 2, 3].map((i) => (
                                            <div key={i} className="h-16 bg-secondary-100 dark:bg-dark-800 rounded-xl" />
                                        ))}
                                    </div>
                                ) : (
                                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        {/* Income */}
                                        <div className="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-900/30">
                                            <div className="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center shrink-0">
                                                <ArrowUpRight className="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                                            </div>
                                            <div className="min-w-0">
                                                <p className="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Pemasukan</p>
                                                <p className="text-sm font-bold text-emerald-700 dark:text-emerald-300 tabular-nums truncate">
                                                    {formatCurrency(monthlyStats?.total_income ?? selected.monthly_income)}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Expense */}
                                        <div className="flex items-center gap-3 p-3 bg-rose-50 dark:bg-rose-900/20 rounded-xl border border-rose-100 dark:border-rose-900/30">
                                            <div className="h-9 w-9 bg-rose-100 dark:bg-rose-900/40 rounded-lg flex items-center justify-center shrink-0">
                                                <ArrowDownRight className="w-4.5 h-4.5 text-rose-600 dark:text-rose-400" />
                                            </div>
                                            <div className="min-w-0">
                                                <p className="text-[11px] text-rose-600 dark:text-rose-400 font-medium">Pengeluaran</p>
                                                <p className="text-sm font-bold text-rose-700 dark:text-rose-300 tabular-nums truncate">
                                                    {formatCurrency(monthlyStats?.total_expense ?? selected.monthly_expense)}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Net Cashflow */}
                                        <div className={`flex items-center gap-3 p-3 rounded-xl border ${
                                            netPositive
                                                ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-900/30'
                                                : 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-900/30'
                                        }`}>
                                            <div className={`h-9 w-9 rounded-lg flex items-center justify-center shrink-0 ${
                                                netPositive
                                                    ? 'bg-blue-100 dark:bg-blue-900/40'
                                                    : 'bg-amber-100 dark:bg-amber-900/40'
                                            }`}>
                                                <Minus className={`w-4.5 h-4.5 ${netPositive ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400'}`} />
                                            </div>
                                            <div className="min-w-0">
                                                <p className={`text-[11px] font-medium ${netPositive ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400'}`}>
                                                    Net Cashflow
                                                </p>
                                                <p className={`text-sm font-bold tabular-nums truncate ${netPositive ? 'text-blue-700 dark:text-blue-300' : 'text-amber-700 dark:text-amber-300'}`}>
                                                    {net >= 0 ? '+' : ''}{formatCurrency(monthlyStats?.net_cashflow ?? 0)}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Sparkline 30 hari */}
                                <div className="mt-4 pt-3 border-t border-secondary-100 dark:border-dark-600">
                                    <div className="flex items-center justify-between mb-2">
                                        <h3 className="text-[10px] font-semibold text-dark-400 dark:text-dark-500 uppercase tracking-widest">
                                            Arus 30 Hari
                                        </h3>
                                        <div className="flex items-center gap-3 text-[10px] tabular-nums">
                                            <span className="text-emerald-600 dark:text-emerald-400">
                                                +{formatCurrency(selected.monthly_income)}
                                            </span>
                                            <span className="text-rose-600 dark:text-rose-400">
                                                -{formatCurrency(selected.monthly_expense)}
                                            </span>
                                        </div>
                                    </div>
                                    <Sparkline30Days
                                        data={selected.sparkline_30d ?? []}
                                        width={280}
                                        height={52}
                                        className="w-full"
                                    />
                                </div>
                            </div>

                            {/* Right: Live Activity Ticker */}
                            <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                                <div className="flex items-center justify-between mb-3">
                                    <div className="flex items-center gap-2">
                                        <Activity className="w-3.5 h-3.5 text-dark-400 dark:text-dark-500" />
                                        <h3 className="text-[10px] font-semibold text-dark-400 dark:text-dark-500 uppercase tracking-widest">
                                            Live Activity
                                        </h3>
                                    </div>
                                    <span className="text-[10px] text-dark-400 dark:text-dark-500 bg-secondary-100 dark:bg-dark-800 px-2 py-0.5 rounded-md">
                                        {selected.transaction_count + selected.payment_count} total
                                    </span>
                                </div>
                                <LiveActivityTicker accountId={selected.id} />
                            </div>
                        </div>

                        {/* ── ROW 2: Bar Chart 12 Bulan + Category Bars + Insight Panel ── */}
                        <div className="grid grid-cols-1 lg:grid-cols-[1.8fr_1fr_0.8fr] gap-4">

                            {/* Col 1: Income vs Expense Bar Chart */}
                            <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                                <div className="flex items-center gap-2.5 mb-4">
                                    <div className="h-8 w-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                                        <BarChart3 className="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-200">
                                            Pemasukan vs Pengeluaran
                                        </h3>
                                        <p className="text-[11px] text-dark-500 dark:text-dark-400">12 bulan terakhir</p>
                                    </div>
                                </div>
                                {monthlyStatsLoading ? (
                                    <div className="h-35 animate-pulse bg-secondary-100 dark:bg-dark-800 rounded-lg" />
                                ) : (
                                    <IncomeExpenseChart months={monthlyStats?.months ?? []} height={140} />
                                )}
                            </div>

                            {/* Col 2: Top Pengeluaran per Kategori */}
                            <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                                <div className="flex items-center gap-2.5 mb-4">
                                    <div className="h-8 w-8 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center shrink-0">
                                        <PieChart className="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <div>
                                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-200">
                                            Top Pengeluaran
                                        </h3>
                                        <p className="text-[11px] text-dark-500 dark:text-dark-400">
                                            {monthlyStats?.period_label ?? 'Bulan ini'}
                                        </p>
                                    </div>
                                </div>
                                {monthlyStatsLoading ? (
                                    <div className="space-y-2.5 animate-pulse">
                                        {[1, 2, 3, 4].map((i) => (
                                            <div key={i} className="h-8 bg-secondary-100 dark:bg-dark-800 rounded" />
                                        ))}
                                    </div>
                                ) : (
                                    <CategoryBars categories={monthlyStats?.categories ?? []} />
                                )}
                            </div>

                            {/* Col 3: Insight + Actions */}
                            <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-4">
                                <h3 className="text-[10px] font-semibold text-dark-400 dark:text-dark-500 uppercase tracking-widest mb-3">
                                    Insight
                                </h3>
                                <InsightPanel
                                    account={selected}
                                    onEdit={() => { setEditAccount(selected); setModalOpen(true); }}
                                    onDelete={() => setDeleteTarget(selected)}
                                />
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* ══════════════════════ MODALS ══════════════════════ */}
            <AccountFormModal
                open={modalOpen}
                onOpenChange={setModalOpen}
                editAccount={editAccount}
                onCreated={handleCreated}
                onUpdated={handleUpdated}
            />

            <ConfirmDialog
                open={deleteTarget !== null}
                onOpenChange={(open) => !open && setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus Rekening"
                description={
                    deleteTarget
                        ? `Hapus rekening "${deleteTarget.account_name}"? Semua transaksi terkait akan ikut dihapus dan tidak bisa dikembalikan.`
                        : ''
                }
                variant="danger"
            />
        </AppLayout>
    );
}
