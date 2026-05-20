import { Head, router } from '@inertiajs/react';
import {
    ArrowLeftRight,
    ArrowRight,
    Building2,
    Download,
    Filter,
    Paperclip,
    Search,
    Trash2,
    X,
} from 'lucide-react';
import * as React from 'react';
import { toast } from 'sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { EmptyState } from '@/components/shared/empty-state';
import { PageHeader } from '@/components/shared/page-header';
import { Pagination } from '@/components/shared/pagination';
import { AppLayout } from '@/layouts/app-layout';
import { cn, formatCurrency, formatDate } from '@/lib/utils';
import * as cashFlowRoutes from '@/routes/cash-flow';
import { CashFlowStatsBar } from './components/cash-flow-stats';
import type {
    CashFlowStats,
    FilterOption,
    PaginationMeta,
    TransferFilters,
    TransferRow,
} from './types';

interface Props {
    rows: TransferRow[];
    pagination: PaginationMeta;
    stats: CashFlowStats;
    filters: TransferFilters;
    bankAccountOptions: FilterOption[];
}

function isoOrNull(d: Date | null): string | null {
    return d ? d.toISOString().slice(0, 10) : null;
}
function parseIso(s: string | null): Date | null {
    return s ? new Date(s) : null;
}

export default function CashFlowTransfers({ rows, pagination, stats, filters, bankAccountOptions }: Props) {
    const [selected, setSelected] = React.useState<number[]>([]);
    const [bulkDeleteOpen, setBulkDeleteOpen] = React.useState(false);
    const [deleteProcessing, setDeleteProcessing] = React.useState(false);
    const [search, setSearch] = React.useState(filters.search ?? '');

    React.useEffect(() => {
        const t = setTimeout(() => {
            if (search !== (filters.search ?? '')) apply({ search, page: 1 });
        }, 350);
        return () => clearTimeout(t);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const apply = (patch: Partial<TransferFilters>) => {
        const next = { ...filters, ...patch };
        router.get(
            cashFlowRoutes.transfers.url(),
            {
                search: next.search || undefined,
                date_from: next.date_from || undefined,
                date_to: next.date_to || undefined,
                bank_accounts: next.bank_accounts.length > 0 ? next.bank_accounts.join(',') : undefined,
                per_page: next.per_page,
                page: next.page,
            },
            { preserveScroll: true, preserveState: true, only: ['rows', 'pagination', 'stats', 'filters'], replace: true },
        );
    };

    const reset = () => {
        setSearch('');
        router.get(cashFlowRoutes.transfers.url(), {}, { preserveScroll: true });
    };

    const toggleAll = () => {
        if (selected.length === rows.length) setSelected([]);
        else setSelected(rows.map((r) => r.id));
    };
    const toggleOne = (id: number) =>
        setSelected((p) => (p.includes(id) ? p.filter((x) => x !== id) : [...p, id]));

    const handleBulkDelete = () => {
        setDeleteProcessing(true);
        router.post(
            cashFlowRoutes.bulkDestroy.url(),
            { uids: selected.map((id) => `transaction-${id}`) },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(`${selected.length} transfer berhasil dihapus`);
                    setSelected([]);
                    setBulkDeleteOpen(false);
                },
                onError: () => toast.error('Gagal menghapus'),
                onFinish: () => setDeleteProcessing(false),
            },
        );
    };

    const exportPdf = () => {
        const params = new URLSearchParams();
        params.set('section', 'transfers');
        if (filters.date_from) params.set('date_from', filters.date_from);
        if (filters.date_to) params.set('date_to', filters.date_to);
        window.open(`/cash-flow/export/pdf?${params.toString()}`, '_blank');
    };

    const activeFilterCount =
        (filters.search ? 1 : 0) +
        (filters.date_from && filters.date_to ? 1 : 0) +
        filters.bank_accounts.length;

    const allSelected = rows.length > 0 && selected.length === rows.length;
    const periodLabel =
        filters.date_from && filters.date_to
            ? `${formatDate(filters.date_from)} – ${formatDate(filters.date_to)}`
            : 'Semua waktu';

    return (
        <AppLayout>
            <Head title="Transfer & Penyesuaian" />

            <div className="space-y-6">
                <PageHeader
                    title="Transfer & Penyesuaian"
                    description="Riwayat transfer antar rekening internal Anda."
                    action={
                        <Button variant="outline" size="md" onClick={exportPdf}>
                            <Download className="w-4 h-4" />
                            Export PDF
                        </Button>
                    }
                />

                <CashFlowStatsBar
                    stats={stats}
                    period={periodLabel}
                    primaryLabel="Total Transfer"
                    primaryTone="purple"
                />

                <div className="space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <DatePicker
                            mode="range"
                            label="Rentang Tanggal"
                            value={{ from: parseIso(filters.date_from), to: parseIso(filters.date_to) }}
                            onChange={(r) => apply({ date_from: isoOrNull(r.from), date_to: isoOrNull(r.to), page: 1 })}
                            placeholder="Pilih rentang"
                            clearable
                        />
                        <Combobox
                            label="Rekening Terkait"
                            options={bankAccountOptions}
                            value={filters.bank_accounts[0] ?? null}
                            onChange={(v) => apply({ bank_accounts: v ? [Number(v)] : [], page: 1 })}
                            placeholder="Semua rekening"
                            clearable
                        />
                        <Input
                            label="Cari"
                            icon={<Search className="w-4 h-4" />}
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Deskripsi, referensi..."
                        />
                    </div>

                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            {activeFilterCount > 0 && (
                                <Badge variant="default" className="gap-1">
                                    <Filter className="w-3 h-3" />
                                    {activeFilterCount} filter aktif
                                </Badge>
                            )}
                            <span className="text-sm text-dark-500 dark:text-dark-400">
                                Menampilkan <span className="tabular-nums">{rows.length}</span> dari{' '}
                                <span className="tabular-nums">{pagination.total}</span> hasil
                            </span>
                        </div>
                        {activeFilterCount > 0 && (
                            <Button variant="ghost" size="sm" onClick={reset}>
                                <X className="w-3.5 h-3.5" />
                                Reset
                            </Button>
                        )}
                    </div>
                </div>

                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                    {rows.length === 0 ? (
                        <EmptyState
                            icon={<ArrowLeftRight className="w-7 h-7" />}
                            title="Belum ada transfer"
                            description="Buat transfer pertama Anda dari halaman Rekening Bank."
                        />
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="bg-secondary-50/60 dark:bg-dark-800/60 border-b border-secondary-200 dark:border-dark-600">
                                    <tr>
                                        <th className="w-10 px-4 py-3">
                                            <Checkbox checked={allSelected} onCheckedChange={toggleAll} />
                                        </th>
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Tanggal
                                        </th>
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Alur Transfer
                                        </th>
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400 hidden lg:table-cell">
                                            Deskripsi
                                        </th>
                                        <th className="px-3 py-3 text-right text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Jumlah
                                        </th>
                                        <th className="px-3 py-3 text-right text-xs font-semibold text-dark-500 dark:text-dark-400 hidden md:table-cell">
                                            Total Debit
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-secondary-100 dark:divide-dark-600">
                                    {rows.map((row) => (
                                        <tr
                                            key={row.id}
                                            className={cn(
                                                'transition-colors',
                                                selected.includes(row.id)
                                                    ? 'bg-primary-50/50 dark:bg-primary-900/10'
                                                    : 'hover:bg-secondary-50/80 dark:hover:bg-dark-800/50',
                                            )}
                                        >
                                            <td className="px-4 py-3 align-middle">
                                                <Checkbox
                                                    checked={selected.includes(row.id)}
                                                    onCheckedChange={() => toggleOne(row.id)}
                                                />
                                            </td>
                                            <td className="px-3 py-3 align-middle whitespace-nowrap text-sm text-dark-700 dark:text-dark-300">
                                                <div>{formatDate(row.transaction_date)}</div>
                                                {row.reference_number && (
                                                    <div className="text-[11px] text-dark-400 dark:text-dark-500 font-mono mt-0.5">
                                                        {row.reference_number}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-3 py-3 align-middle">
                                                <div className="flex items-center gap-2 min-w-0">
                                                    <AccountChip account={row.from_account} tone="red" />
                                                    <ArrowRight className="w-3.5 h-3.5 text-purple-400 shrink-0" />
                                                    <AccountChip account={row.to_account} tone="green" />
                                                </div>
                                            </td>
                                            <td className="px-3 py-3 align-middle hidden lg:table-cell">
                                                <div className="text-sm text-dark-700 dark:text-dark-300 truncate max-w-72">
                                                    {row.description || '—'}
                                                </div>
                                                {row.attachment_url && (
                                                    <a
                                                        href={row.attachment_url}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="inline-flex items-center gap-1 mt-1 text-xs text-primary-600 dark:text-primary-400 hover:underline"
                                                    >
                                                        <Paperclip className="w-3 h-3" />
                                                        Lampiran
                                                    </a>
                                                )}
                                            </td>
                                            <td className="px-3 py-3 align-middle text-right">
                                                <span className="font-semibold text-purple-600 dark:text-purple-400 tabular-nums">
                                                    {formatCurrency(row.amount)}
                                                </span>
                                            </td>
                                            <td className="px-3 py-3 align-middle text-right hidden md:table-cell">
                                                <div className="text-sm font-semibold text-dark-900 dark:text-dark-50 tabular-nums">
                                                    {formatCurrency(row.total_debit)}
                                                </div>
                                                {row.admin_fee > 0 && (
                                                    <div className="text-[11px] text-dark-500 dark:text-dark-400">
                                                        + admin {formatCurrency(row.admin_fee)}
                                                    </div>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}

                    {pagination.last_page > 1 && (
                        <div className="px-4 py-3 border-t border-secondary-200 dark:border-dark-600">
                            <Pagination meta={pagination} onPageChange={(page) => apply({ page })} />
                        </div>
                    )}
                </div>
            </div>

            {selected.length > 0 && (
                <div className="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 animate-in slide-in-from-bottom-4 duration-200">
                    <div className="bg-white dark:bg-dark-700 rounded-xl shadow-xl border border-secondary-200 dark:border-dark-600 px-4 py-3 flex items-center gap-4 min-w-80">
                        <div className="flex items-center gap-2">
                            <Badge variant="default">{selected.length}</Badge>
                            <span className="text-sm font-medium text-dark-900 dark:text-dark-50">
                                transfer dipilih
                            </span>
                        </div>
                        <div className="flex items-center gap-2">
                            <Button variant="zinc" size="sm" onClick={() => setSelected([])}>
                                Batal
                            </Button>
                            <Button variant="red" size="sm" onClick={() => setBulkDeleteOpen(true)}>
                                <Trash2 className="w-3.5 h-3.5" />
                                Hapus
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            <ConfirmDialog
                open={bulkDeleteOpen}
                onOpenChange={setBulkDeleteOpen}
                title={`Hapus ${selected.length} transfer?`}
                description="Pasangan transfer (debit + credit) akan ikut terhapus. Tindakan ini tidak dapat dibatalkan."
                confirmLabel={`Hapus ${selected.length}`}
                loading={deleteProcessing}
                onConfirm={handleBulkDelete}
            />
        </AppLayout>
    );
}

/* ─── Account chip ──────────────────────────────────────── */

interface AccountChipProps {
    account: { account_name: string; bank_name: string } | null;
    tone: 'red' | 'green';
}

function AccountChip({ account, tone }: AccountChipProps) {
    const toneMap = {
        red: 'border-red-200 dark:border-red-900/40 bg-red-50/60 dark:bg-red-900/10 text-red-700 dark:text-red-300',
        green: 'border-green-200 dark:border-green-900/40 bg-green-50/60 dark:bg-green-900/10 text-green-700 dark:text-green-300',
    };
    if (!account) {
        return <span className="text-xs text-dark-400 dark:text-dark-500 italic">tidak diketahui</span>;
    }
    return (
        <div className={cn('inline-flex items-center gap-1.5 px-2 py-1 rounded-md border text-xs min-w-0 max-w-44', toneMap[tone])}>
            <Building2 className="w-3 h-3 shrink-0" />
            <span className="truncate font-medium">{account.account_name}</span>
        </div>
    );
}
