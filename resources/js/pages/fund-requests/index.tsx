import { Head, router } from '@inertiajs/react';
import {
    CheckCircle,
    Clock,
    Download,
    Edit,
    FileText,
    Filter,
    Plus,
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { EmptyState } from '@/components/shared/empty-state';
import { PageHeader } from '@/components/shared/page-header';
import { Pagination } from '@/components/shared/pagination';
import { AppLayout } from '@/layouts/app-layout';
import { cn, formatCurrency, formatDate } from '@/lib/utils';
import * as fundRequestRoutes from '@/routes/fund-requests';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type {
    FilterOption,
    FundRequestFilters,
    FundRequestRow,
    FundRequestStats,
    PaginationMeta,
} from './types';

interface Props {
    rows: FundRequestRow[];
    pagination: PaginationMeta;
    stats: FundRequestStats;
    filters: FundRequestFilters;
    bankAccountOptions: FilterOption[];
    userOptions: FilterOption[];
    canApprove: boolean;
    canDisburse: boolean;
}

const STATUS_OPTIONS: FilterOption[] = [
    { label: 'Draft', value: 'draft' },
    { label: 'Pending Review', value: 'pending' },
    { label: 'Approved', value: 'approved' },
    { label: 'Rejected', value: 'rejected' },
    { label: 'Disbursed', value: 'disbursed' },
];

const PRIORITY_OPTIONS: FilterOption[] = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
    { label: 'Urgent', value: 'urgent' },
];

export default function FundRequestsIndex({
    rows,
    pagination,
    stats,
    filters,
    bankAccountOptions,
    userOptions,
    canApprove,
    canDisburse,
}: Props) {
    const [selected, setSelected] = React.useState<number[]>([]);
    const [search, setSearch] = React.useState(filters.search ?? '');

    // Detail dialog
    const [detailRow, setDetailRow] = React.useState<FundRequestRow | null>(null);

    // Review dialog
    const [reviewRow, setReviewRow] = React.useState<FundRequestRow | null>(null);
    const [reviewAction, setReviewAction] = React.useState<'approve' | 'reject'>('approve');
    const [reviewNotes, setReviewNotes] = React.useState('');
    const [reviewProcessing, setReviewProcessing] = React.useState(false);

    // Disburse dialog
    const [disburseRow, setDisburseRow] = React.useState<FundRequestRow | null>(null);
    const [disburseBankAccountId, setDisburseBankAccountId] = React.useState<number | null>(null);
    const [disburseDate, setDisburseDate] = React.useState(new Date().toISOString().slice(0, 10));
    const [disburseNotes, setDisburseNotes] = React.useState('');
    const [disburseProcessing, setDisburseProcessing] = React.useState(false);

    // Delete confirm
    const [deleteRow, setDeleteRow] = React.useState<FundRequestRow | null>(null);
    const [deleteProcessing, setDeleteProcessing] = React.useState(false);

    // Submit confirm
    const [submitRow, setSubmitRow] = React.useState<FundRequestRow | null>(null);
    const [submitProcessing, setSubmitProcessing] = React.useState(false);

    React.useEffect(() => {
        const t = setTimeout(() => {
            if (search !== (filters.search ?? '')) apply({ search, page: 1 });
        }, 350);
        return () => clearTimeout(t);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const apply = (patch: Partial<FundRequestFilters>) => {
        const next = { ...filters, ...patch };
        router.get(
            fundRequestRoutes.index.url(),
            {
                tab: next.tab,
                search: next.search || undefined,
                status: next.status || undefined,
                priority: next.priority || undefined,
                user_id: next.user_id || undefined,
                month: next.month || undefined,
                per_page: next.per_page,
                page: next.page,
            },
            { preserveScroll: true, preserveState: true, only: ['rows', 'pagination', 'stats', 'filters'], replace: true },
        );
    };

    const reset = () => {
        setSearch('');
        router.get(fundRequestRoutes.index.url(), { tab: filters.tab }, { preserveScroll: true });
    };

    const toggleAll = () => {
        setSelected(selected.length === rows.length ? [] : rows.map((r) => r.id));
    };
    const toggleOne = (id: number) =>
        setSelected((p) => (p.includes(id) ? p.filter((x) => x !== id) : [...p, id]));

    const handleDelete = (row: FundRequestRow) => {
        setDeleteProcessing(true);
        router.delete(fundRequestRoutes.destroy.url({ fundRequest: row.id }), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Permintaan dana berhasil dihapus');
                setDeleteRow(null);
                setDetailRow(null);
            },
            onError: () => toast.error('Gagal menghapus'),
            onFinish: () => setDeleteProcessing(false),
        });
    };

    const handleSubmit = (row: FundRequestRow) => {
        setSubmitProcessing(true);
        router.post(
            fundRequestRoutes.submit.url({ fundRequest: row.id }),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Permintaan dana berhasil diajukan');
                    setSubmitRow(null);
                    setDetailRow(null);
                },
                onError: () => toast.error('Gagal mengajukan'),
                onFinish: () => setSubmitProcessing(false),
            },
        );
    };

    const handleReview = () => {
        if (!reviewRow) return;
        setReviewProcessing(true);
        router.post(
            fundRequestRoutes.review.url({ fundRequest: reviewRow.id }),
            { action: reviewAction, review_notes: reviewNotes || undefined },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(reviewAction === 'approve' ? 'Permintaan dana disetujui' : 'Permintaan dana ditolak');
                    setReviewRow(null);
                    setDetailRow(null);
                    setReviewNotes('');
                },
                onError: () => toast.error('Gagal memproses'),
                onFinish: () => setReviewProcessing(false),
            },
        );
    };

    const handleDisburse = () => {
        if (!disburseRow) return;
        setDisburseProcessing(true);
        router.post(
            fundRequestRoutes.disburse.url({ fundRequest: disburseRow.id }),
            {
                bank_account_id: disburseBankAccountId,
                disbursement_date: disburseDate,
                disbursement_notes: disburseNotes || undefined,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Dana berhasil dicairkan');
                    setDisburseRow(null);
                    setDetailRow(null);
                    setDisburseBankAccountId(null);
                    setDisburseNotes('');
                },
                onError: () => toast.error('Gagal mencairkan dana'),
                onFinish: () => setDisburseProcessing(false),
            },
        );
    };

    const activeFilterCount =
        (filters.search ? 1 : 0) +
        (filters.status ? 1 : 0) +
        (filters.priority ? 1 : 0) +
        (filters.user_id ? 1 : 0) +
        (filters.month ? 1 : 0);

    const allSelected = rows.length > 0 && selected.length === rows.length;

    const tabItems = [
        ...(canApprove ? [{ value: 'all', label: 'Semua Pengajuan' }] : []),
        { value: 'my', label: 'Pengajuan Saya' },
    ];

    return (
        <AppLayout>
            <Head title="Permintaan Dana" />

            <div className="space-y-6">
                <PageHeader
                    title="Permintaan Dana"
                    description="Kelola pengajuan pencairan dana operasional."
                    action={
                        <Button variant="primary" size="md" onClick={() => router.visit(fundRequestRoutes.create.url())}>
                            <Plus className="w-4 h-4" />
                            Buat Permintaan
                        </Button>
                    }
                />

                {/* Stats bar */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <StatCard label="Total" value={stats.total} sub="permintaan" color="blue" />
                    <StatCard label="Pending" value={stats.pending_count} sub="menunggu review" color="yellow" />
                    <StatCard label="Approved" value={stats.approved_count} sub="siap dicairkan" color="green" />
                    <StatCard label="Dicairkan" value={stats.disbursed_count} sub="selesai" color="purple" />
                </div>

                {/* Tabs */}
                {canApprove && (
                    <Tabs
                        items={tabItems}
                        value={filters.tab}
                        onChange={(v) => apply({ tab: v, page: 1 })}
                    />
                )}

                {/* Filters */}
                <div className="space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <Combobox
                            label="Status"
                            options={STATUS_OPTIONS}
                            value={filters.status ?? null}
                            onChange={(v) => apply({ status: v ? String(v) : null, page: 1 })}
                            placeholder="Semua status"
                            clearable
                        />
                        <Combobox
                            label="Prioritas"
                            options={PRIORITY_OPTIONS}
                            value={filters.priority ?? null}
                            onChange={(v) => apply({ priority: v ? String(v) : null, page: 1 })}
                            placeholder="Semua prioritas"
                            clearable
                        />
                        {filters.tab === 'all' && (
                            <Combobox
                                label="Pemohon"
                                options={userOptions}
                                value={filters.user_id ?? null}
                                onChange={(v) => apply({ user_id: v ? String(v) : null, page: 1 })}
                                placeholder="Semua pengguna"
                                clearable
                            />
                        )}
                        <Input
                            label="Cari"
                            icon={<Search className="w-4 h-4" />}
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Judul, tujuan..."
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

                {/* Table */}
                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                    {rows.length === 0 ? (
                        <EmptyState
                            icon={<FileText className="w-7 h-7" />}
                            title="Belum ada permintaan dana"
                            description="Buat permintaan dana baru untuk memulai."
                            action={
                                <Button variant="primary" size="sm" onClick={() => router.visit(fundRequestRoutes.create.url())}>
                                    <Plus className="w-4 h-4" />
                                    Buat Permintaan
                                </Button>
                            }
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
                                            No. Permintaan
                                        </th>
                                        {filters.tab === 'all' && (
                                            <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400 hidden md:table-cell">
                                                Pemohon
                                            </th>
                                        )}
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Judul
                                        </th>
                                        <th className="px-3 py-3 text-right text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Total
                                        </th>
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400 hidden lg:table-cell">
                                            Prioritas
                                        </th>
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400 hidden lg:table-cell">
                                            Dibutuhkan
                                        </th>
                                        <th className="px-3 py-3 text-left text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Status
                                        </th>
                                        <th className="px-3 py-3 text-right text-xs font-semibold text-dark-500 dark:text-dark-400">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-secondary-100 dark:divide-dark-600">
                                    {rows.map((row) => (
                                        <tr
                                            key={row.id}
                                            onClick={() => setDetailRow(row)}
                                            className={cn(
                                                'transition-colors cursor-pointer',
                                                selected.includes(row.id)
                                                    ? 'bg-primary-50/50 dark:bg-primary-900/10'
                                                    : 'hover:bg-secondary-50/80 dark:hover:bg-dark-800/50',
                                            )}
                                        >
                                            <td className="px-4 py-3 align-middle" onClick={(e) => e.stopPropagation()}>
                                                <Checkbox
                                                    checked={selected.includes(row.id)}
                                                    onCheckedChange={() => toggleOne(row.id)}
                                                />
                                            </td>
                                            <td className="px-3 py-3 align-middle">
                                                <span className="text-xs font-mono text-dark-500 dark:text-dark-400">
                                                    {row.request_number}
                                                </span>
                                            </td>
                                            {filters.tab === 'all' && (
                                                <td className="px-3 py-3 align-middle hidden md:table-cell">
                                                    <span className="text-sm text-dark-700 dark:text-dark-300">{row.user_name}</span>
                                                </td>
                                            )}
                                            <td className="px-3 py-3 align-middle">
                                                <div className="font-medium text-dark-900 dark:text-dark-50 truncate max-w-56">
                                                    {row.title}
                                                </div>
                                                <div className="text-xs text-dark-400 dark:text-dark-500 mt-0.5">
                                                    {row.items_count} item
                                                </div>
                                            </td>
                                            <td className="px-3 py-3 align-middle text-right">
                                                <span className="font-semibold text-dark-900 dark:text-dark-50 tabular-nums">
                                                    {formatCurrency(row.total_amount)}
                                                </span>
                                            </td>
                                            <td className="px-3 py-3 align-middle hidden lg:table-cell">
                                                <PriorityBadge priority={row.priority} />
                                            </td>
                                            <td className="px-3 py-3 align-middle hidden lg:table-cell text-sm text-dark-600 dark:text-dark-400">
                                                {formatDate(row.needed_by_date)}
                                            </td>
                                            <td className="px-3 py-3 align-middle">
                                                <StatusBadge status={row.status} />
                                            </td>
                                            <td className="px-3 py-3 align-middle text-right" onClick={(e) => e.stopPropagation()}>
                                                <div className="flex items-center justify-end gap-1.5">
                                                    {row.can_submit && (
                                                        <Button variant="outline" size="sm" onClick={() => setSubmitRow(row)}>
                                                            <CheckCircle className="w-3.5 h-3.5" />
                                                            Ajukan
                                                        </Button>
                                                    )}
                                                    {row.can_review && canApprove && (
                                                        <Button variant="outline" size="sm" onClick={() => { setReviewRow(row); setReviewAction('approve'); setReviewNotes(''); }}>
                                                            <Clock className="w-3.5 h-3.5" />
                                                            Review
                                                        </Button>
                                                    )}
                                                    {row.can_disburse && canDisburse && (
                                                        <Button variant="green" size="sm" onClick={() => { setDisburseRow(row); setDisburseBankAccountId(null); setDisburseDate(new Date().toISOString().slice(0, 10)); setDisburseNotes(''); }}>
                                                            Cairkan
                                                        </Button>
                                                    )}
                                                    {row.can_edit && (
                                                        <Button
                                                            variant="outline"
                                                            size="icon"
                                                            onClick={() => router.visit(fundRequestRoutes.edit.url({ fundRequest: row.id }))}
                                                        >
                                                            <Edit className="w-3.5 h-3.5" />
                                                        </Button>
                                                    )}
                                                    {row.can_delete && (
                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            onClick={() => setDeleteRow(row)}
                                                            className="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10"
                                                        >
                                                            <Trash2 className="w-3.5 h-3.5" />
                                                        </Button>
                                                    )}
                                                </div>
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

            {/* Detail dialog */}
            <Dialog open={!!detailRow} onOpenChange={(open) => { if (!open) setDetailRow(null); }}>
                <DialogContent size="lg">
                    <DialogHeader>
                        <DialogTitle>{detailRow?.title}</DialogTitle>
                    </DialogHeader>
                    {detailRow && (
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4 text-sm">
                                <Field label="No. Permintaan" value={<span className="font-mono">{detailRow.request_number}</span>} />
                                <Field label="Pemohon" value={detailRow.user_name ?? '—'} />
                                <Field label="Prioritas" value={<PriorityBadge priority={detailRow.priority} />} />
                                <Field label="Dibutuhkan" value={formatDate(detailRow.needed_by_date)} />
                                <Field label="Total" value={formatCurrency(detailRow.total_amount)} />
                                <Field label="Status" value={<StatusBadge status={detailRow.status} />} />
                                {detailRow.items_count > 0 && (
                                    <Field label="Jumlah Item" value={`${detailRow.items_count} item`} />
                                )}
                                {detailRow.reviewed_by_name && (
                                    <Field label="Direview oleh" value={detailRow.reviewed_by_name} />
                                )}
                                {detailRow.disbursed_by_name && (
                                    <Field label="Dicairkan oleh" value={`${detailRow.disbursed_by_name} — ${formatDate(detailRow.disbursement_date ?? '')}`} />
                                )}
                                {detailRow.review_notes && (
                                    <div className="col-span-2">
                                        <Field label="Catatan Review" value={detailRow.review_notes} />
                                    </div>
                                )}
                            </div>
                            {detailRow.purpose && (
                                <div>
                                    <p className="text-xs text-dark-500 dark:text-dark-400 mb-1">Tujuan</p>
                                    <p className="text-sm text-dark-700 dark:text-dark-300">{detailRow.purpose}</p>
                                </div>
                            )}
                            {detailRow.attachment_url && (
                                <a
                                    href={detailRow.attachment_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:underline"
                                >
                                    <Download className="w-4 h-4" />
                                    {detailRow.attachment_name ?? 'Lihat Lampiran'}
                                </a>
                            )}
                        </div>
                    )}
                    <DialogFooter>
                        <div className="flex flex-wrap gap-2">
                            {detailRow?.can_submit && (
                                <Button variant="primary" size="sm" onClick={() => { setDetailRow(null); setSubmitRow(detailRow); }}>
                                    <CheckCircle className="w-3.5 h-3.5" />
                                    Ajukan
                                </Button>
                            )}
                            {detailRow?.can_review && canApprove && (
                                <>
                                    <Button variant="green" size="sm" onClick={() => { setDetailRow(null); setReviewRow(detailRow); setReviewAction('approve'); setReviewNotes(''); }}>
                                        Setujui
                                    </Button>
                                    <Button variant="red" size="sm" onClick={() => { setDetailRow(null); setReviewRow(detailRow); setReviewAction('reject'); setReviewNotes(''); }}>
                                        Tolak
                                    </Button>
                                </>
                            )}
                            {detailRow?.can_disburse && canDisburse && (
                                <Button variant="green" size="sm" onClick={() => { setDetailRow(null); setDisburseRow(detailRow); setDisburseBankAccountId(null); setDisburseDate(new Date().toISOString().slice(0, 10)); setDisburseNotes(''); }}>
                                    Cairkan Dana
                                </Button>
                            )}
                            {detailRow?.can_edit && (
                                <Button variant="outline" size="sm" onClick={() => router.visit(fundRequestRoutes.edit.url({ fundRequest: detailRow.id }))}>
                                    <Edit className="w-3.5 h-3.5" />
                                    Edit
                                </Button>
                            )}
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Review dialog */}
            <Dialog open={!!reviewRow} onOpenChange={(open) => { if (!open) setReviewRow(null); }}>
                <DialogContent size="md">
                    <DialogHeader>
                        <DialogTitle>
                            {reviewAction === 'approve' ? 'Setujui Permintaan Dana' : 'Tolak Permintaan Dana'}
                        </DialogTitle>
                    </DialogHeader>
                    <div className="space-y-4">
                        {reviewRow && (
                            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 p-3 bg-secondary-50/50 dark:bg-dark-800/50">
                                <p className="font-medium text-dark-900 dark:text-dark-50">{reviewRow.title}</p>
                                <p className="text-sm text-dark-500 dark:text-dark-400 mt-0.5">
                                    {reviewRow.user_name} · {formatCurrency(reviewRow.total_amount)}
                                </p>
                            </div>
                        )}
                        <Textarea
                            label="Catatan Review"
                            value={reviewNotes}
                            onChange={(e) => setReviewNotes(e.target.value)}
                            placeholder="Catatan untuk pemohon (opsional)..."
                            rows={3}
                        />
                    </div>
                    <DialogFooter>
                        <Button variant="zinc" size="sm" onClick={() => setReviewRow(null)}>Batal</Button>
                        <Button
                            variant={reviewAction === 'approve' ? 'green' : 'red'}
                            size="sm"
                            onClick={handleReview}
                            disabled={reviewProcessing}
                        >
                            {reviewAction === 'approve' ? 'Setujui' : 'Tolak'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Disburse dialog */}
            <Dialog open={!!disburseRow} onOpenChange={(open) => { if (!open) setDisburseRow(null); }}>
                <DialogContent size="md">
                    <DialogHeader>
                        <DialogTitle>Cairkan Dana</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-4">
                        {disburseRow && (
                            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 p-3 bg-secondary-50/50 dark:bg-dark-800/50">
                                <p className="font-medium text-dark-900 dark:text-dark-50">{disburseRow.title}</p>
                                <p className="text-sm text-dark-500 dark:text-dark-400 mt-0.5">
                                    Total: <span className="font-semibold text-dark-900 dark:text-dark-50">{formatCurrency(disburseRow.total_amount)}</span>
                                </p>
                            </div>
                        )}
                        <Combobox
                            label="Rekening Bank *"
                            options={bankAccountOptions}
                            value={disburseBankAccountId}
                            onChange={(v) => setDisburseBankAccountId(v ? Number(v) : null)}
                            placeholder="Pilih rekening"
                        />
                        <div>
                            <Label>Tanggal Pencairan *</Label>
                            <Input
                                type="date"
                                value={disburseDate}
                                onChange={(e) => setDisburseDate(e.target.value)}
                                max={new Date().toISOString().slice(0, 10)}
                            />
                        </div>
                        <Textarea
                            label="Catatan Pencairan"
                            value={disburseNotes}
                            onChange={(e) => setDisburseNotes(e.target.value)}
                            placeholder="Catatan atau nomor referensi (opsional)..."
                            rows={2}
                        />
                    </div>
                    <DialogFooter>
                        <Button variant="zinc" size="sm" onClick={() => setDisburseRow(null)}>Batal</Button>
                        <Button
                            variant="green"
                            size="sm"
                            onClick={handleDisburse}
                            disabled={disburseProcessing || !disburseBankAccountId}
                        >
                            Cairkan Dana
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Submit confirm */}
            <ConfirmDialog
                open={!!submitRow}
                onOpenChange={(open) => { if (!open) setSubmitRow(null); }}
                title="Ajukan permintaan dana?"
                description="Setelah diajukan, permintaan akan masuk ke antrian review dan tidak dapat diedit."
                confirmLabel="Ajukan"
                loading={submitProcessing}
                onConfirm={() => submitRow && handleSubmit(submitRow)}
            />

            {/* Delete confirm */}
            <ConfirmDialog
                open={!!deleteRow}
                onOpenChange={(open) => { if (!open) setDeleteRow(null); }}
                title="Hapus permintaan dana ini?"
                description="Tindakan ini tidak dapat dibatalkan."
                confirmLabel="Hapus"
                loading={deleteProcessing}
                onConfirm={() => deleteRow && handleDelete(deleteRow)}
            />
        </AppLayout>
    );
}

/* ─── helpers ──────────────────────────────────────────── */

function Field({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div>
            <p className="text-xs text-dark-500 dark:text-dark-400 mb-0.5">{label}</p>
            <p className="text-sm font-medium text-dark-900 dark:text-dark-50">{value}</p>
        </div>
    );
}

function StatCard({ label, value, sub, color }: { label: string; value: string | number; sub?: string; color: string }) {
    const colorMap: Record<string, string> = {
        blue: 'text-blue-600 dark:text-blue-400',
        yellow: 'text-yellow-600 dark:text-yellow-400',
        green: 'text-green-600 dark:text-green-400',
        purple: 'text-purple-600 dark:text-purple-400',
    };
    return (
        <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 px-4 py-3">
            <p className="text-xs text-dark-500 dark:text-dark-400">{label}</p>
            <p className={cn('text-xl font-bold tabular-nums mt-0.5', colorMap[color] ?? '')}>{value}</p>
            {sub && <p className="text-xs text-dark-400 dark:text-dark-500">{sub}</p>}
        </div>
    );
}

const STATUS_BADGE_MAP: Record<string, { label: string; variant: string }> = {
    draft: { label: 'Draft', variant: 'zinc' },
    pending: { label: 'Pending Review', variant: 'yellow' },
    approved: { label: 'Approved', variant: 'blue' },
    rejected: { label: 'Rejected', variant: 'red' },
    disbursed: { label: 'Disbursed', variant: 'green' },
};

const PRIORITY_BADGE_MAP: Record<string, { label: string; variant: string }> = {
    low: { label: 'Low', variant: 'zinc' },
    medium: { label: 'Medium', variant: 'blue' },
    high: { label: 'High', variant: 'orange' },
    urgent: { label: 'Urgent', variant: 'red' },
};

function StatusBadge({ status }: { status: string }) {
    const { label, variant } = STATUS_BADGE_MAP[status] ?? { label: status, variant: 'zinc' };
    return <Badge variant={variant as never}>{label}</Badge>;
}

function PriorityBadge({ priority }: { priority: string }) {
    const { label, variant } = PRIORITY_BADGE_MAP[priority] ?? { label: priority, variant: 'zinc' };
    return <Badge variant={variant as never}>{label}</Badge>;
}
