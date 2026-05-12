import { Head, router, useForm, usePage } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowUpDown,
    Ban,
    CheckCircle2,
    ChevronDown,
    Clock,
    Download,
    ExternalLink,
    Eye,
    FileText,
    Pencil,
    Plus,
    RotateCcw,
    Send,
    TrendingUp,
    Trash2,
    Wallet,
    X,
} from 'lucide-react';
import * as React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { PageHeader } from '@/components/shared/page-header';
import { Pagination } from '@/components/shared/pagination';
import { StatsCard } from '@/components/shared/stats-card';
import { AppLayout } from '@/layouts/app-layout';
import { cn, formatCurrency, formatDate } from '@/lib/utils';
import type { SharedProps } from '@/types';

/* ─────────────────────────────────── types ─── */

interface InvoiceRow {
    id: number;
    invoice_number: string | null;
    client_name: string;
    client_type: string;
    issue_date: string;
    due_date: string;
    total_amount: number;
    amount_paid: number;
    amount_remaining: number;
    status: 'draft' | 'sent' | 'partially_paid' | 'paid';
    faktur: string | null;
}

interface PaginatedInvoices {
    data: InvoiceRow[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

interface Stats {
    invoice_count: number;
    total_revenue: number;
    total_cogs: number;
    gross_profit: number;
    paid_this_month: number;
    draft_count: number;
    sent_count: number;
    partially_paid_count: number;
    paid_count: number;
}

interface ClientOption {
    label: string;
    value: number;
}

interface Filters {
    search?: string | null;
    status?: string | null;
    client_id?: number | null;
    month?: string;
    per_page?: number;
    sort?: string;
    direction?: string;
}

interface InvoiceDetail {
    id: number;
    invoice_number: string | null;
    status: string;
    issue_date: string;
    due_date: string;
    subtotal: number;
    discount_amount: number;
    discount_type: string;
    discount_value: number;
    discount_reason: string | null;
    total_amount: number;
    amount_paid: number;
    amount_remaining: number;
    faktur: string | null;
    client: {
        id: number;
        name: string;
        email: string | null;
        NPWP: string | null;
        address: string | null;
    };
    items: Array<{
        id: number;
        service_name: string;
        quantity: number;
        unit: string;
        unit_price: number;
        amount: number;
        cogs_amount: number;
        is_tax_deposit: boolean;
    }>;
    payments: Array<{
        id: number;
        amount: number;
        payment_date: string;
        bank_account: string | null;
        notes: string | null;
    }>;
}

interface Props extends SharedProps {
    invoices: PaginatedInvoices;
    stats: Stats;
    clients: ClientOption[];
    rollbackableIds: number[];
    filters: Filters;
}

/* ─────────────────────────────────── helpers ─── */

const STATUS_CONFIG = {
    draft: {
        label: 'Draft',
        bg: 'bg-zinc-100 dark:bg-zinc-800',
        text: 'text-zinc-700 dark:text-zinc-300',
    },
    sent: {
        label: 'Terkirim',
        bg: 'bg-blue-100 dark:bg-blue-900/30',
        text: 'text-blue-700 dark:text-blue-300',
    },
    partially_paid: {
        label: 'Sebagian',
        bg: 'bg-yellow-100 dark:bg-yellow-900/30',
        text: 'text-yellow-700 dark:text-yellow-300',
    },
    paid: {
        label: 'Lunas',
        bg: 'bg-green-100 dark:bg-green-900/30',
        text: 'text-green-700 dark:text-green-300',
    },
} as const;

function StatusBadge({ status }: { status: string }) {
    const cfg = STATUS_CONFIG[status as keyof typeof STATUS_CONFIG] ?? STATUS_CONFIG.draft;
    return (
        <span
            className={cn(
                'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold',
                cfg.bg,
                cfg.text,
            )}
        >
            {cfg.label}
        </span>
    );
}

/* ─────────────────────────────────── slide-over ─── */

function InvoiceDrawer({
    open,
    onClose,
    invoiceId,
    rollbackableIds,
}: {
    open: boolean;
    onClose: () => void;
    invoiceId: number | null;
    rollbackableIds: number[];
}) {
    const [detail, setDetail] = React.useState<InvoiceDetail | null>(null);
    const [loading, setLoading] = React.useState(false);
    const [sendOpen, setSendOpen] = React.useState(false);
    const [deleteOpen, setDeleteOpen] = React.useState(false);
    const [deleteLoading, setDeleteLoading] = React.useState(false);

    const sendForm = useForm({ invoice_number: '' });

    React.useEffect(() => {
        if (!open || !invoiceId) {
            setDetail(null);
            return;
        }
        setLoading(true);
        fetch(`/invoices/${invoiceId}`, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((r) => r.json())
            .then((data) => {
                setDetail(data);
                if (data.status === 'draft') {
                    const now = new Date(data.issue_date);
                    const roman = [
                        '', 'I', 'II', 'III', 'IV', 'V', 'VI',
                        'VII', 'VIII', 'IX', 'X', 'XI', 'XII',
                    ][now.getMonth() + 1];
                    sendForm.setData('invoice_number', data.invoice_number ?? '');
                }
            })
            .catch(console.error)
            .finally(() => setLoading(false));
    }, [open, invoiceId]);

    const handleSend = () => {
        if (!detail) return;
        sendForm.post(`/invoices/${detail.id}/send`, {
            onSuccess: () => {
                setSendOpen(false);
                onClose();
            },
        });
    };

    const handleRollback = () => {
        if (!detail) return;
        router.post(`/invoices/${detail.id}/rollback`, {}, {
            onSuccess: () => onClose(),
        });
    };

    const handleDelete = () => {
        if (!detail) return;
        setDeleteLoading(true);
        router.delete(`/invoices/${detail.id}`, {
            onSuccess: () => {
                setDeleteOpen(false);
                onClose();
            },
            onFinish: () => setDeleteLoading(false),
        });
    };

    const isRollbackable = detail ? rollbackableIds.includes(detail.id) : false;

    const netRevenue = detail
        ? detail.items.filter((i) => !i.is_tax_deposit).reduce((s, i) => s + i.amount, 0)
        : 0;
    const totalCogs = detail
        ? detail.items.filter((i) => !i.is_tax_deposit).reduce((s, i) => s + i.cogs_amount, 0)
        : 0;
    const totalTaxDeposits = detail
        ? detail.items.filter((i) => i.is_tax_deposit).reduce((s, i) => s + i.amount, 0)
        : 0;
    const grossProfit = detail ? detail.total_amount - totalTaxDeposits - totalCogs : 0;

    return (
        <>
            {/* Backdrop */}
            <div
                className={cn(
                    'fixed inset-0 z-40 bg-black/40 backdrop-blur-sm transition-opacity duration-300',
                    open ? 'opacity-100' : 'opacity-0 pointer-events-none',
                )}
                onClick={onClose}
            />

            {/* Panel */}
            <div
                className={cn(
                    'fixed inset-y-0 right-0 z-50 w-full max-w-3xl',
                    'bg-white dark:bg-dark-700',
                    'shadow-2xl border-l border-secondary-200 dark:border-dark-600',
                    'flex flex-col',
                    'transition-transform duration-300 ease-in-out',
                    open ? 'translate-x-0' : 'translate-x-full',
                )}
            >
                {/* Header */}
                <div className="flex items-center justify-between px-6 py-4 border-b border-secondary-200 dark:border-dark-600 shrink-0">
                    <div className="flex items-center gap-3">
                        <div className="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                            <FileText className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h2 className="text-lg font-bold text-dark-900 dark:text-dark-50">
                                {detail?.invoice_number ?? (loading ? '...' : 'Invoice Draft')}
                            </h2>
                            {detail && (
                                <div className="flex items-center gap-2 mt-0.5">
                                    <StatusBadge status={detail.status} />
                                    <span className="text-xs text-dark-500 dark:text-dark-400">
                                        {detail.client.name}
                                    </span>
                                </div>
                            )}
                        </div>
                    </div>
                    <button
                        onClick={onClose}
                        className="h-8 w-8 rounded-lg flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-dark-600 transition-colors"
                    >
                        <X className="w-4 h-4 text-dark-600 dark:text-dark-400" />
                    </button>
                </div>

                {/* Body */}
                <div className="flex-1 overflow-y-auto px-6 py-5 space-y-6">
                    {loading && (
                        <div className="flex items-center justify-center py-12">
                            <div className="h-8 w-8 rounded-full border-2 border-primary-600 border-t-transparent animate-spin" />
                        </div>
                    )}

                    {!loading && detail && (
                        <>
                            {/* Summary metrics */}
                            <div className="grid grid-cols-2 gap-3">
                                <div className="p-3 rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                                    <p className="text-xs text-dark-500 dark:text-dark-400 mb-1">Total Invoice</p>
                                    <p className="text-lg font-bold text-dark-900 dark:text-dark-50">
                                        {formatCurrency(detail.total_amount)}
                                    </p>
                                </div>
                                <div className="p-3 rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                                    <p className="text-xs text-dark-500 dark:text-dark-400 mb-1">Sudah Dibayar</p>
                                    <p className="text-lg font-bold text-green-600 dark:text-green-400">
                                        {formatCurrency(detail.amount_paid)}
                                    </p>
                                </div>
                                <div className="p-3 rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                                    <p className="text-xs text-dark-500 dark:text-dark-400 mb-1">Sisa Tagihan</p>
                                    <p className={cn(
                                        'text-lg font-bold',
                                        detail.amount_remaining > 0
                                            ? 'text-red-600 dark:text-red-400'
                                            : 'text-dark-900 dark:text-dark-50',
                                    )}>
                                        {formatCurrency(detail.amount_remaining)}
                                    </p>
                                </div>
                                <div className="p-3 rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                                    <p className="text-xs text-dark-500 dark:text-dark-400 mb-1">Laba Kotor</p>
                                    <p className={cn(
                                        'text-lg font-bold',
                                        grossProfit >= 0
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-red-600 dark:text-red-400',
                                    )}>
                                        {formatCurrency(grossProfit)}
                                    </p>
                                </div>
                            </div>

                            {/* Client & Dates */}
                            <div className="space-y-3">
                                <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50 border-b border-secondary-200 dark:border-dark-600 pb-2">
                                    Info Invoice
                                </h3>
                                <div className="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                    <div>
                                        <span className="text-dark-500 dark:text-dark-400">Klien</span>
                                        <p className="font-medium text-dark-900 dark:text-dark-50 mt-0.5">{detail.client.name}</p>
                                    </div>
                                    <div>
                                        <span className="text-dark-500 dark:text-dark-400">NPWP</span>
                                        <p className="font-medium text-dark-900 dark:text-dark-50 mt-0.5">{detail.client.NPWP ?? '—'}</p>
                                    </div>
                                    <div>
                                        <span className="text-dark-500 dark:text-dark-400">Tgl Invoice</span>
                                        <p className="font-medium text-dark-900 dark:text-dark-50 mt-0.5">{formatDate(detail.issue_date)}</p>
                                    </div>
                                    <div>
                                        <span className="text-dark-500 dark:text-dark-400">Jatuh Tempo</span>
                                        <p className="font-medium text-dark-900 dark:text-dark-50 mt-0.5">{formatDate(detail.due_date)}</p>
                                    </div>
                                    {detail.client.email && (
                                        <div className="col-span-2">
                                            <span className="text-dark-500 dark:text-dark-400">Email</span>
                                            <p className="font-medium text-dark-900 dark:text-dark-50 mt-0.5">{detail.client.email}</p>
                                        </div>
                                    )}
                                    {detail.client.address && (
                                        <div className="col-span-2">
                                            <span className="text-dark-500 dark:text-dark-400">Alamat</span>
                                            <p className="font-medium text-dark-900 dark:text-dark-50 mt-0.5">{detail.client.address}</p>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Items */}
                            <div className="space-y-3">
                                <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50 border-b border-secondary-200 dark:border-dark-600 pb-2">
                                    Item Invoice
                                </h3>
                                <div className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                                    <table className="w-full text-sm">
                                        <thead>
                                            <tr className="bg-secondary-50 dark:bg-dark-800 border-b border-secondary-200 dark:border-dark-600">
                                                <th className="text-left px-3 py-2 text-xs font-semibold text-dark-600 dark:text-dark-400">Layanan</th>
                                                <th className="text-right px-3 py-2 text-xs font-semibold text-dark-600 dark:text-dark-400">Qty</th>
                                                <th className="text-right px-3 py-2 text-xs font-semibold text-dark-600 dark:text-dark-400">Harga</th>
                                                <th className="text-right px-3 py-2 text-xs font-semibold text-dark-600 dark:text-dark-400">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {detail.items.map((item, idx) => (
                                                <tr
                                                    key={item.id}
                                                    className={cn(
                                                        'border-b border-secondary-200 dark:border-dark-600 last:border-0',
                                                        item.is_tax_deposit && 'bg-yellow-50/50 dark:bg-yellow-900/10',
                                                    )}
                                                >
                                                    <td className="px-3 py-2.5">
                                                        <div className="font-medium text-dark-900 dark:text-dark-50">{item.service_name}</div>
                                                        {item.is_tax_deposit && (
                                                            <span className="text-xs text-yellow-600 dark:text-yellow-400">PPh / Titipan Pajak</span>
                                                        )}
                                                    </td>
                                                    <td className="px-3 py-2.5 text-right text-dark-600 dark:text-dark-400">
                                                        {item.quantity} {item.unit}
                                                    </td>
                                                    <td className="px-3 py-2.5 text-right text-dark-600 dark:text-dark-400">
                                                        {formatCurrency(item.unit_price)}
                                                    </td>
                                                    <td className="px-3 py-2.5 text-right font-semibold text-dark-900 dark:text-dark-50">
                                                        {formatCurrency(item.amount)}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot className="bg-secondary-50 dark:bg-dark-800">
                                            {detail.discount_amount > 0 && (
                                                <>
                                                    <tr className="border-t border-secondary-200 dark:border-dark-600">
                                                        <td colSpan={3} className="px-3 py-2 text-sm text-dark-600 dark:text-dark-400">Subtotal</td>
                                                        <td className="px-3 py-2 text-right text-sm text-dark-900 dark:text-dark-50">{formatCurrency(detail.subtotal)}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colSpan={3} className="px-3 py-1.5 text-sm text-dark-600 dark:text-dark-400">
                                                            Diskon
                                                            {detail.discount_reason && (
                                                                <span className="text-xs text-dark-400 ml-1">({detail.discount_reason})</span>
                                                            )}
                                                        </td>
                                                        <td className="px-3 py-1.5 text-right text-sm text-red-600 dark:text-red-400">
                                                            -{formatCurrency(detail.discount_amount)}
                                                        </td>
                                                    </tr>
                                                </>
                                            )}
                                            <tr className="border-t border-secondary-200 dark:border-dark-600">
                                                <td colSpan={3} className="px-3 py-2.5 font-bold text-dark-900 dark:text-dark-50">Total</td>
                                                <td className="px-3 py-2.5 text-right font-bold text-dark-900 dark:text-dark-50">{formatCurrency(detail.total_amount)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            {/* Payments */}
                            {detail.payments.length > 0 && (
                                <div className="space-y-3">
                                    <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50 border-b border-secondary-200 dark:border-dark-600 pb-2">
                                        Riwayat Pembayaran
                                    </h3>
                                    <div className="space-y-2">
                                        {detail.payments.map((p) => (
                                            <div
                                                key={p.id}
                                                className="flex items-center justify-between p-3 rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800"
                                            >
                                                <div>
                                                    <p className="text-sm font-medium text-dark-900 dark:text-dark-50">
                                                        {formatDate(p.payment_date)}
                                                    </p>
                                                    {p.bank_account && (
                                                        <p className="text-xs text-dark-500 dark:text-dark-400">{p.bank_account}</p>
                                                    )}
                                                    {p.notes && (
                                                        <p className="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{p.notes}</p>
                                                    )}
                                                </div>
                                                <p className="font-bold text-green-600 dark:text-green-400 text-sm">
                                                    +{formatCurrency(p.amount)}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* HPP breakdown */}
                            {(netRevenue > 0 || totalCogs > 0) && (
                                <div className="space-y-3">
                                    <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50 border-b border-secondary-200 dark:border-dark-600 pb-2">
                                        Analisis Laba
                                    </h3>
                                    <div className="space-y-2 text-sm">
                                        {[
                                            { label: 'Pendapatan Bersih', value: netRevenue, cls: '' },
                                            { label: 'HPP / COGS', value: totalCogs, cls: 'text-red-600 dark:text-red-400' },
                                            { label: 'Titipan Pajak', value: totalTaxDeposits, cls: 'text-yellow-600 dark:text-yellow-400' },
                                            { label: 'Laba Kotor', value: grossProfit, cls: grossProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-red-600 dark:text-red-400 font-bold' },
                                        ].map(({ label, value, cls }) => (
                                            <div key={label} className="flex justify-between">
                                                <span className="text-dark-600 dark:text-dark-400">{label}</span>
                                                <span className={cn('font-medium text-dark-900 dark:text-dark-50', cls)}>
                                                    {formatCurrency(value)}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </>
                    )}
                </div>

                {/* Footer actions */}
                {detail && (
                    <div className="px-6 py-4 border-t border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 shrink-0">
                        <div className="flex flex-wrap items-center gap-2">
                            {detail.status === 'draft' && (
                                <Button
                                    size="sm"
                                    variant="primary"
                                    icon={<Send className="w-3.5 h-3.5" />}
                                    onClick={() => setSendOpen(true)}
                                >
                                    Kirim Invoice
                                </Button>
                            )}
                            {isRollbackable && (
                                <Button
                                    size="sm"
                                    variant="yellow"
                                    icon={<RotateCcw className="w-3.5 h-3.5" />}
                                    onClick={handleRollback}
                                >
                                    Rollback ke Draft
                                </Button>
                            )}
                            <Button
                                size="sm"
                                variant="outline"
                                icon={<Pencil className="w-3.5 h-3.5" />}
                                onClick={() => router.get(`/invoices/${detail.id}/edit`)}
                            >
                                Edit
                            </Button>
                            {detail.invoice_number && (
                                <Button
                                    size="sm"
                                    variant="outline"
                                    icon={<Download className="w-3.5 h-3.5" />}
                                    onClick={() => window.open(`/invoice/${detail.id}/download`, '_blank')}
                                >
                                    PDF
                                </Button>
                            )}
                            <Button
                                size="sm"
                                variant="ghost"
                                className="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 ml-auto"
                                icon={<Trash2 className="w-3.5 h-3.5" />}
                                onClick={() => setDeleteOpen(true)}
                            >
                                Hapus
                            </Button>
                        </div>
                    </div>
                )}
            </div>

            {/* Send modal */}
            <Dialog open={sendOpen} onOpenChange={setSendOpen}>
                <DialogContent size="md">
                    <DialogHeader>
                        <div className="flex items-center gap-4 py-2">
                            <div className="h-12 w-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                <Send className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">
                                    Kirim Invoice
                                </DialogTitle>
                                <p className="text-sm text-dark-600 dark:text-dark-400">
                                    Konfirmasi nomor invoice sebelum mengirim
                                </p>
                            </div>
                        </div>
                    </DialogHeader>
                    <div className="px-6 py-4 space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-dark-900 dark:text-dark-300 mb-1.5">
                                Nomor Invoice
                            </label>
                            <Input
                                value={sendForm.data.invoice_number}
                                onChange={(e) => sendForm.setData('invoice_number', e.target.value)}
                                placeholder="001/INV/KSN-XXX/I/2026"
                                className={sendForm.errors.invoice_number ? 'border-red-500' : ''}
                            />
                            {sendForm.errors.invoice_number && (
                                <p className="mt-1 text-xs text-red-600">{sendForm.errors.invoice_number}</p>
                            )}
                        </div>
                    </div>
                    <DialogFooter>
                        <Button variant="zinc" onClick={() => setSendOpen(false)} className="w-full sm:w-auto order-2 sm:order-1">
                            Batal
                        </Button>
                        <Button
                            variant="primary"
                            onClick={handleSend}
                            loading={sendForm.processing}
                            className="w-full sm:w-auto order-1 sm:order-2"
                        >
                            Konfirmasi & Kirim
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Delete confirm */}
            <ConfirmDialog
                open={deleteOpen}
                onOpenChange={setDeleteOpen}
                title="Hapus Invoice"
                description={`Invoice ${detail?.invoice_number ?? 'ini'} akan dihapus permanen beserta semua item-nya.`}
                confirmLabel="Hapus Invoice"
                loading={deleteLoading}
                onConfirm={handleDelete}
            />
        </>
    );
}

/* ─────────────────────────────────── main page ─── */

function InvoicesPage({ invoices, stats, clients, rollbackableIds, filters }: Props) {
    const { flash } = usePage<Props>().props;

    const [drawerOpen, setDrawerOpen] = React.useState(false);
    const [selectedId, setSelectedId] = React.useState<number | null>(null);

    const currentFilters = {
        search: filters.search ?? '',
        status: filters.status ?? '',
        client_id: filters.client_id ?? '',
        month: filters.month ?? new Date().toISOString().slice(0, 7),
        sort: filters.sort ?? 'issue_date',
        direction: filters.direction ?? 'desc',
        per_page: filters.per_page ?? 25,
    };

    const [search, setSearch] = React.useState(currentFilters.search);

    const navigate = (params: Record<string, unknown>) => {
        router.get('/invoices', { ...currentFilters, ...params, page: 1 }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const handleSearchSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        navigate({ search });
    };

    const handlePageChange = (page: number) => {
        router.get('/invoices', { ...currentFilters, page }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const openDrawer = (id: number) => {
        setSelectedId(id);
        setDrawerOpen(true);
    };

    const tabs = [
        { key: '', label: 'Semua', count: null },
        { key: 'draft', label: 'Draft', count: stats.draft_count },
        { key: 'sent', label: 'Terkirim', count: stats.sent_count },
        { key: 'partially_paid', label: 'Sebagian', count: stats.partially_paid_count },
        { key: 'paid', label: 'Lunas', count: stats.paid_count },
    ];

    return (
        <>
            <Head title="Invoice" />

            <div className="space-y-6">
                {/* Flash */}
                {(flash?.success || flash?.error) && (
                    <div className={cn(
                        'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium',
                        flash.success
                            ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800'
                            : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800',
                    )}>
                        {flash.success ? <CheckCircle2 className="w-4 h-4 shrink-0" /> : <AlertCircle className="w-4 h-4 shrink-0" />}
                        {flash.success ?? flash.error}
                    </div>
                )}

                {/* Header */}
                <PageHeader
                    title="Invoice"
                    description="Kelola semua invoice dan pembayaran klien"
                    action={
                        <Button
                            variant="primary"
                            size="md"
                            icon={<Plus className="w-4 h-4" />}
                            onClick={() => router.get('/invoices/create')}
                        >
                            Buat Invoice
                        </Button>
                    }
                />

                {/* Stats */}
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <StatsCard
                        label="Total Pendapatan"
                        value={formatCurrency(stats.total_revenue)}
                        icon={<Wallet className="w-6 h-6" />}
                        color="blue"
                    />
                    <StatsCard
                        label="Laba Kotor"
                        value={
                            <span className={stats.gross_profit < 0 ? 'text-red-500' : undefined}>
                                {formatCurrency(stats.gross_profit)}
                            </span>
                        }
                        icon={<TrendingUp className="w-6 h-6" />}
                        color="emerald"
                    />
                    <StatsCard
                        label="Dibayar Bulan Ini"
                        value={formatCurrency(stats.paid_this_month)}
                        icon={<CheckCircle2 className="w-6 h-6" />}
                        color="green"
                    />
                    <StatsCard
                        label="Total Invoice"
                        value={stats.invoice_count}
                        icon={<FileText className="w-6 h-6" />}
                        color="purple"
                    />
                </div>

                {/* Tabs */}
                <div className="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                    {tabs.map((tab) => {
                        const active = currentFilters.status === tab.key;
                        return (
                            <button
                                key={tab.key}
                                onClick={() => navigate({ status: tab.key })}
                                className={cn(
                                    'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
                                    active
                                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600',
                                )}
                            >
                                <span>{tab.label}</span>
                                {tab.count !== null && (
                                    <span className={cn(
                                        'px-1.5 py-0.5 text-xs font-bold rounded-full',
                                        active
                                            ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                            : 'bg-zinc-200 dark:bg-dark-600 text-dark-500 dark:text-dark-400',
                                    )}>
                                        {tab.count}
                                    </span>
                                )}
                            </button>
                        );
                    })}
                </div>

                {/* Filters */}
                <div className="flex flex-col gap-3">
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label className="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Klien</label>
                            <select
                                value={currentFilters.client_id}
                                onChange={(e) => navigate({ client_id: e.target.value || '' })}
                                className="w-full h-9 rounded-lg border border-secondary-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-sm text-dark-900 dark:text-dark-300 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            >
                                <option value="">Semua Klien</option>
                                {clients.map((c) => (
                                    <option key={c.value} value={c.value}>{c.label}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Bulan</label>
                            <input
                                type="month"
                                value={currentFilters.month}
                                onChange={(e) => navigate({ month: e.target.value })}
                                className="w-full h-9 rounded-lg border border-secondary-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-sm text-dark-900 dark:text-dark-300 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            />
                        </div>
                        <form onSubmit={handleSearchSubmit}>
                            <label className="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Cari</label>
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="No. invoice atau nama klien..."
                                className="h-9"
                            />
                        </form>
                    </div>
                </div>

                {/* Table */}
                <div className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden bg-white dark:bg-dark-700">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-secondary-50 dark:bg-dark-800 border-b border-secondary-200 dark:border-dark-600">
                                    {[
                                        { key: 'invoice_number', label: 'No. Invoice' },
                                        { key: 'client_name', label: 'Klien' },
                                        { key: 'issue_date', label: 'Tgl Invoice' },
                                        { key: 'due_date', label: 'Jatuh Tempo' },
                                        { key: 'total_amount', label: 'Jumlah' },
                                        { key: 'status', label: 'Status', sortable: false },
                                        { key: 'actions', label: 'Aksi', sortable: false },
                                    ].map((col) => (
                                        <th
                                            key={col.key}
                                            className={cn(
                                                'px-4 py-3 text-left text-xs font-semibold text-dark-600 dark:text-dark-400 whitespace-nowrap',
                                                col.sortable !== false && 'cursor-pointer select-none hover:text-dark-900 dark:hover:text-dark-50',
                                                col.key === 'actions' && 'text-right',
                                            )}
                                            onClick={() => {
                                                if (col.sortable === false) return;
                                                const dir = currentFilters.sort === col.key && currentFilters.direction === 'asc' ? 'desc' : 'asc';
                                                navigate({ sort: col.key, direction: dir });
                                            }}
                                        >
                                            <span className="inline-flex items-center gap-1">
                                                {col.label}
                                                {col.sortable !== false && (
                                                    <ArrowUpDown className="w-3 h-3 opacity-40" />
                                                )}
                                            </span>
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {invoices.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="px-4 py-12 text-center text-dark-500 dark:text-dark-400">
                                            <FileText className="w-8 h-8 mx-auto mb-2 opacity-40" />
                                            <p>Tidak ada invoice ditemukan</p>
                                        </td>
                                    </tr>
                                ) : (
                                    invoices.data.map((inv) => {
                                        const isOverdue =
                                            inv.status !== 'paid' &&
                                            new Date(inv.due_date) < new Date();
                                        return (
                                            <tr
                                                key={inv.id}
                                                className="border-b border-secondary-200 dark:border-dark-600 last:border-0 hover:bg-secondary-50 dark:hover:bg-dark-800/50 transition-colors"
                                            >
                                                <td className="px-4 py-3">
                                                    <span className="font-mono text-xs font-medium text-dark-900 dark:text-dark-50">
                                                        {inv.invoice_number ?? (
                                                            <span className="text-dark-400 dark:text-dark-500 italic">Draft</span>
                                                        )}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="font-medium text-dark-900 dark:text-dark-50">{inv.client_name}</div>
                                                    <div className="text-xs text-dark-500 dark:text-dark-400 capitalize">{inv.client_type}</div>
                                                </td>
                                                <td className="px-4 py-3 text-dark-600 dark:text-dark-400 whitespace-nowrap">
                                                    {formatDate(inv.issue_date)}
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <span className={cn(
                                                        'text-sm',
                                                        isOverdue
                                                            ? 'text-red-600 dark:text-red-400 font-semibold'
                                                            : 'text-dark-600 dark:text-dark-400',
                                                    )}>
                                                        {formatDate(inv.due_date)}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="font-semibold text-dark-900 dark:text-dark-50 whitespace-nowrap">
                                                        {formatCurrency(inv.total_amount)}
                                                    </div>
                                                    {inv.amount_paid > 0 && inv.status !== 'paid' && (
                                                        <div className="text-xs text-dark-500 dark:text-dark-400">
                                                            Sisa: {formatCurrency(inv.amount_remaining)}
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <StatusBadge status={inv.status} />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex items-center justify-end gap-1">
                                                        <button
                                                            title="Lihat Detail"
                                                            onClick={() => openDrawer(inv.id)}
                                                            className="h-7 w-7 rounded-lg flex items-center justify-center hover:bg-blue-50 dark:hover:bg-blue-900/20 text-dark-500 dark:text-dark-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                        >
                                                            <Eye className="w-4 h-4" />
                                                        </button>
                                                        <button
                                                            title="Edit"
                                                            onClick={() => router.get(`/invoices/${inv.id}/edit`)}
                                                            className="h-7 w-7 rounded-lg flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-dark-600 text-dark-500 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-50 transition-colors"
                                                        >
                                                            <Pencil className="w-4 h-4" />
                                                        </button>
                                                        {inv.invoice_number && (
                                                            <button
                                                                title="Download PDF"
                                                                onClick={() => window.open(`/invoice/${inv.id}/download`, '_blank')}
                                                                className="h-7 w-7 rounded-lg flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-dark-600 text-dark-500 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-50 transition-colors"
                                                            >
                                                                <Download className="w-4 h-4" />
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Pagination */}
                <Pagination
                    meta={{
                        current_page: invoices.current_page,
                        last_page: invoices.last_page,
                        per_page: invoices.per_page,
                        total: invoices.total,
                        from: invoices.from,
                        to: invoices.to,
                    }}
                    onPageChange={handlePageChange}
                />
            </div>

            {/* Slide-over drawer */}
            <InvoiceDrawer
                open={drawerOpen}
                onClose={() => setDrawerOpen(false)}
                invoiceId={selectedId}
                rollbackableIds={rollbackableIds}
            />
        </>
    );
}

InvoicesPage.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;

export default InvoicesPage;
