import { useState, useEffect, useCallback, useRef } from 'react';
import { ArrowUpDown, ArrowUp, ArrowDown, Paperclip, Search, X, RefreshCw, Building2, User } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { Combobox } from '@/components/ui/combobox';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Pagination } from '@/components/shared/pagination';
import { EmptyState } from '@/components/shared/empty-state';
import { formatCurrency, toastError } from '@/lib/utils';

interface Payment {
    id: number;
    amount: number;
    payment_date: string;
    payment_method: string | null;
    reference_number: string | null;
    invoice_number: string | null;
    invoice_status: string | null;
    client_name: string | null;
    client_type: string | null;
    attachment_url: string | null;
    attachment_name: string | null;
}

interface Meta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface PaymentsTableProps {
    accountId: number;
    onAttachmentView: (url: string, filename?: string) => void;
    refreshKey?: number;
}

type SortDir = 'asc' | 'desc';

const STATUS_VARIANTS: Record<string, 'green' | 'yellow' | 'blue' | 'red' | 'zinc'> = {
    paid: 'green',
    partially_paid: 'yellow',
    sent: 'blue',
    overdue: 'red',
    draft: 'zinc',
};

const STATUS_LABELS: Record<string, string> = {
    paid: 'Lunas',
    partially_paid: 'Sebagian',
    sent: 'Terkirim',
    overdue: 'Jatuh Tempo',
    draft: 'Draft',
};

const METHOD_LABELS: Record<string, string> = {
    bank_transfer: 'Transfer Bank',
    cash: 'Tunai',
    check: 'Cek',
    other: 'Lainnya',
};

export default function PaymentsTable({ accountId, onAttachmentView, refreshKey }: PaymentsTableProps) {
    const [payments, setPayments] = useState<Payment[]>([]);
    const [meta, setMeta] = useState<Meta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 });
    const [loading, setLoading] = useState(false);

    const [filterMethod, setFilterMethod] = useState('');
    const [filterStatus, setFilterStatus] = useState('');
    const [filterMonth, setFilterMonth] = useState('');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [sortDir, setSortDir] = useState<SortDir>('desc');
    const [page, setPage] = useState(1);

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => setSearch(searchInput), 300);
        return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    }, [searchInput]);

    useEffect(() => {
        setPage(1);
    }, [accountId, filterMethod, filterStatus, filterMonth, search, sortDir]);

    useEffect(() => {
        setFilterMethod('');
        setFilterStatus('');
        setFilterMonth('');
        setSearchInput('');
        setSearch('');
        setSortDir('desc');
        setPage(1);
    }, [accountId]);

    const fetchPayments = useCallback(() => {
        setLoading(true);
        const params = new URLSearchParams();
        if (filterMethod) params.set('payment_method', filterMethod);
        if (filterStatus) params.set('invoice_status', filterStatus);
        if (filterMonth) params.set('month', filterMonth);
        if (search) params.set('search', search);
        params.set('sort_by', 'payment_date');
        params.set('sort_direction', sortDir);
        params.set('page', String(page));
        params.set('per_page', '15');

        fetch(`/bank-accounts/${accountId}/payments?${params}`, {
            headers: { Accept: 'application/json' },
        })
            .then(r => { if (!r.ok) throw new Error('fetch failed'); return r.json(); })
            .then(d => { setPayments(d.data); setMeta(d.meta); })
            .catch(() => toastError('Gagal memuat pembayaran.'))
            .finally(() => setLoading(false));
    }, [accountId, filterMethod, filterStatus, filterMonth, search, sortDir, page]);

    useEffect(() => {
        fetchPayments();
    }, [fetchPayments, refreshKey]);

    const monthOptions = Array.from({ length: 12 }, (_, i) => {
        const d = new Date();
        d.setMonth(d.getMonth() - i);
        const val = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
        const label = d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        return { label, value: val };
    });

    const activeFilters = [filterMethod, filterStatus, filterMonth, search].filter(Boolean).length;

    return (
        <div className="flex flex-col gap-3">
            {/* ── Filter bar ── */}
            <div className="flex flex-wrap items-center gap-2">
                <Combobox
                    options={[
                        { label: 'Semua Metode', value: '' },
                        { label: 'Transfer Bank', value: 'bank_transfer' },
                        { label: 'Tunai', value: 'cash' },
                    ]}
                    value={filterMethod}
                    onChange={v => setFilterMethod(v ?? '')}
                    placeholder="Semua Metode"
                    className="w-40 h-8 text-xs"
                />
                <Combobox
                    options={[
                        { label: 'Semua Status', value: '' },
                        { label: 'Lunas', value: 'paid' },
                        { label: 'Sebagian', value: 'partially_paid' },
                        { label: 'Terkirim', value: 'sent' },
                        { label: 'Jatuh Tempo', value: 'overdue' },
                    ]}
                    value={filterStatus}
                    onChange={v => setFilterStatus(v ?? '')}
                    placeholder="Semua Status"
                    className="w-40 h-8 text-xs"
                />
                <Combobox
                    options={[{ label: 'Semua Bulan', value: '' }, ...monthOptions]}
                    value={filterMonth}
                    onChange={v => setFilterMonth(v ?? '')}
                    placeholder="Semua Bulan"
                    className="w-44 h-8 text-xs"
                />
                <div className="relative flex-1 min-w-36">
                    <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-dark-400" />
                    <input
                        value={searchInput}
                        onChange={e => setSearchInput(e.target.value)}
                        placeholder="Cari invoice / klien / ref..."
                        className="w-full h-8 pl-8 pr-8 text-xs rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-300 placeholder:text-dark-400 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    />
                    {searchInput && (
                        <button onClick={() => setSearchInput('')} className="absolute right-2 top-1/2 -translate-y-1/2">
                            <X className="w-3.5 h-3.5 text-dark-400 hover:text-dark-600" />
                        </button>
                    )}
                </div>
                {activeFilters > 0 && (
                    <Badge variant="blue" className="text-[10px] px-2 py-0.5">{activeFilters} filter aktif</Badge>
                )}
                <span className="text-xs text-dark-500 dark:text-dark-400 tabular-nums whitespace-nowrap">
                    {meta.total} pembayaran
                </span>
                <Button
                    size="sm"
                    variant="ghost"
                    onClick={fetchPayments}
                    className="h-8 w-8 p-0"
                    title="Refresh"
                >
                    <RefreshCw className="w-3.5 h-3.5" />
                </Button>
            </div>

            {/* ── Table ── */}
            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-xs">
                        <thead>
                            <tr className="border-b border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                                <th
                                    className="text-left px-4 py-2.5 font-semibold text-dark-500 dark:text-dark-400 cursor-pointer hover:text-dark-900 select-none whitespace-nowrap"
                                    onClick={() => setSortDir(d => d === 'asc' ? 'desc' : 'asc')}
                                >
                                    <div className="flex items-center gap-1">
                                        Tanggal
                                        {sortDir === 'asc'
                                            ? <ArrowUp className="w-3 h-3 text-primary-500" />
                                            : <ArrowDown className="w-3 h-3 text-primary-500" />
                                        }
                                    </div>
                                </th>
                                <th className="text-left px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400">Invoice</th>
                                <th className="text-left px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400 hidden md:table-cell">Klien</th>
                                <th className="text-right px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400">Jumlah</th>
                                <th className="text-left px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400 hidden sm:table-cell">Metode</th>
                                <th className="w-8 px-2 py-2.5" />
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-secondary-100 dark:divide-dark-700">
                            {loading ? (
                                Array.from({ length: 5 }).map((_, i) => (
                                    <tr key={i}>
                                        {Array.from({ length: 6 }).map((__, j) => (
                                            <td key={j} className="px-3 py-3"><Skeleton className="h-4 w-full" /></td>
                                        ))}
                                    </tr>
                                ))
                            ) : payments.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="py-12">
                                        <EmptyState
                                            icon="credit-card"
                                            title="Belum ada pembayaran"
                                            description="Pembayaran invoice akan muncul di sini."
                                        />
                                    </td>
                                </tr>
                            ) : (
                                payments.map(p => (
                                    <tr key={p.id} className="group hover:bg-secondary-50 dark:hover:bg-dark-700/50 transition-colors">
                                        <td className="px-4 py-2.5 text-dark-600 dark:text-dark-400 whitespace-nowrap">
                                            {new Date(p.payment_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' })}
                                        </td>
                                        <td className="px-3 py-2.5">
                                            <p className="font-semibold text-dark-900 dark:text-dark-200">{p.invoice_number ?? '—'}</p>
                                            {p.invoice_status && (
                                                <Badge
                                                    variant={STATUS_VARIANTS[p.invoice_status] ?? 'zinc'}
                                                    className="text-[10px] px-1.5 py-0 mt-0.5"
                                                >
                                                    {STATUS_LABELS[p.invoice_status] ?? p.invoice_status}
                                                </Badge>
                                            )}
                                        </td>
                                        <td className="px-3 py-2.5 hidden md:table-cell">
                                            {p.client_name ? (
                                                <div className="flex items-center gap-1.5">
                                                    {p.client_type === 'company'
                                                        ? <Building2 className="w-3 h-3 text-dark-400 shrink-0" />
                                                        : <User className="w-3 h-3 text-dark-400 shrink-0" />
                                                    }
                                                    <span className="text-dark-700 dark:text-dark-300 truncate max-w-32">{p.client_name}</span>
                                                </div>
                                            ) : <span className="text-dark-400">—</span>}
                                        </td>
                                        <td className="px-3 py-2.5 text-right font-bold tabular-nums text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                            +{formatCurrency(p.amount)}
                                        </td>
                                        <td className="px-3 py-2.5 hidden sm:table-cell">
                                            {p.payment_method && (
                                                <Badge variant="zinc" className="text-[10px] px-1.5 py-0">
                                                    {METHOD_LABELS[p.payment_method] ?? p.payment_method}
                                                </Badge>
                                            )}
                                        </td>
                                        <td className="px-2 py-2.5">
                                            {p.attachment_url && (
                                                <button
                                                    onClick={() => onAttachmentView(p.attachment_url!, p.attachment_name ?? undefined)}
                                                    className="opacity-0 group-hover:opacity-100 transition-opacity text-dark-400 hover:text-primary-500"
                                                    title="Lihat lampiran"
                                                >
                                                    <Paperclip className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {meta.last_page > 1 && (
                <Pagination
                    currentPage={meta.current_page}
                    lastPage={meta.last_page}
                    perPage={meta.per_page}
                    total={meta.total}
                    onPageChange={setPage}
                />
            )}
        </div>
    );
}
