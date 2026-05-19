import { useState, useEffect, useCallback, useRef } from 'react';
import { ArrowUpRight, ArrowDownRight, ArrowUpDown, ArrowUp, ArrowDown, Paperclip, Tag, Trash2, MoreHorizontal, Plus, Search, X, RefreshCw } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import { Skeleton } from '@/components/ui/skeleton';
import { Combobox } from '@/components/ui/combobox';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Pagination } from '@/components/shared/pagination';
import { EmptyState } from '@/components/shared/empty-state';
import { formatCurrency, toastError } from '@/lib/utils';
import { toast } from 'sonner';

interface Category {
    id: number;
    label: string;
    parent: { id: number; label: string } | null;
}

interface Transaction {
    id: number;
    transaction_type: 'credit' | 'debit';
    amount: number;
    transaction_date: string;
    description: string | null;
    reference_number: string | null;
    category_id: number | null;
    category: Category | null;
    attachment_url: string | null;
    attachment_name: string | null;
}

interface Meta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface TransactionsTableProps {
    accountId: number;
    onCreateClick: (type: 'income' | 'expense' | 'transfer') => void;
    onCategorize: (ids: number[], isBulk: boolean) => void;
    onAttachmentView: (url: string, filename?: string) => void;
    refreshKey?: number;
}

type SortColumn = 'transaction_date' | 'amount' | 'description';
type SortDir = 'asc' | 'desc';

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export default function TransactionsTable({
    accountId,
    onCreateClick,
    onCategorize,
    onAttachmentView,
    refreshKey,
}: TransactionsTableProps) {
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [meta, setMeta] = useState<Meta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 });
    const [loading, setLoading] = useState(false);

    const [filterType, setFilterType] = useState('');
    const [filterCategory, setFilterCategory] = useState('');
    const [filterMonth, setFilterMonth] = useState('');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [sortBy, setSortBy] = useState<SortColumn>('transaction_date');
    const [sortDir, setSortDir] = useState<SortDir>('desc');
    const [page, setPage] = useState(1);

    const [selected, setSelected] = useState<number[]>([]);
    const [deleting, setDeleting] = useState(false);

    const [categories, setCategories] = useState<{ label: string; value: number }[]>([]);

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    // Load category options
    useEffect(() => {
        fetch('/api/transaction-categories', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setCategories(
                data.filter((d: { disabled?: boolean }) => !d.disabled)
                    .map((d: { label: string; value: number }) => ({ label: d.label, value: d.value }))
            ))
            .catch(() => {});
    }, []);

    // Debounce search
    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => setSearch(searchInput), 300);
        return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    }, [searchInput]);

    // Reset page on filter/sort/account change
    useEffect(() => {
        setPage(1);
        setSelected([]);
    }, [accountId, filterType, filterCategory, filterMonth, search, sortBy, sortDir]);

    // Fetch transactions
    const fetchTransactions = useCallback(() => {
        setLoading(true);
        const params = new URLSearchParams();
        if (filterType) params.set('transaction_type', filterType);
        if (filterCategory) params.set('category_id', filterCategory);
        if (filterMonth) params.set('month', filterMonth);
        if (search) params.set('search', search);
        params.set('sort_by', sortBy);
        params.set('sort_direction', sortDir);
        params.set('page', String(page));
        params.set('per_page', '15');

        fetch(`/bank-accounts/${accountId}/transactions?${params}`, {
            headers: { Accept: 'application/json' },
        })
            .then(r => { if (!r.ok) throw new Error('fetch failed'); return r.json(); })
            .then(d => { setTransactions(d.data); setMeta(d.meta); })
            .catch(() => toastError('Gagal memuat transaksi.'))
            .finally(() => setLoading(false));
    }, [accountId, filterType, filterCategory, filterMonth, search, sortBy, sortDir, page]);

    useEffect(() => {
        fetchTransactions();
    }, [fetchTransactions, refreshKey]);

    // Reset selection when account changes
    useEffect(() => {
        setSelected([]);
        setFilterType('');
        setFilterCategory('');
        setFilterMonth('');
        setSearchInput('');
        setSearch('');
        setSortBy('transaction_date');
        setSortDir('desc');
        setPage(1);
    }, [accountId]);

    function toggleSort(col: SortColumn) {
        if (sortBy === col) {
            setSortDir(d => d === 'asc' ? 'desc' : 'asc');
        } else {
            setSortBy(col);
            setSortDir('desc');
        }
    }

    function toggleSelect(id: number) {
        setSelected(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]);
    }

    function toggleSelectAll() {
        if (selected.length === transactions.length) {
            setSelected([]);
        } else {
            setSelected(transactions.map(t => t.id));
        }
    }

    async function handleDelete(id: number) {
        setDeleting(true);
        try {
            const res = await fetch(`/bank-transactions/${id}`, {
                method: 'DELETE',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
            });
            const data = await res.json();
            if (!res.ok) { toastError(data.message ?? 'Gagal menghapus.'); return; }
            toast.success(data.message);
            fetchTransactions();
        } catch { toastError('Gagal terhubung ke server.'); }
        finally { setDeleting(false); }
    }

    async function handleBulkDelete() {
        if (!selected.length) return;
        setDeleting(true);
        try {
            const res = await fetch('/bank-transactions/bulk', {
                method: 'DELETE',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ transaction_ids: selected }),
            });
            const data = await res.json();
            if (!res.ok) { toastError(data.message ?? 'Gagal menghapus.'); return; }
            toast.success(data.message);
            setSelected([]);
            fetchTransactions();
        } catch { toastError('Gagal terhubung ke server.'); }
        finally { setDeleting(false); }
    }

    const activeFilters = [filterType, filterCategory, filterMonth, search].filter(Boolean).length;

    function SortIcon({ col }: { col: SortColumn }) {
        if (sortBy !== col) return <ArrowUpDown className="w-3 h-3 opacity-40" />;
        return sortDir === 'asc'
            ? <ArrowUp className="w-3 h-3 text-primary-500" />
            : <ArrowDown className="w-3 h-3 text-primary-500" />;
    }

    // Month options — current + last 11 months
    const monthOptions = Array.from({ length: 12 }, (_, i) => {
        const d = new Date();
        d.setMonth(d.getMonth() - i);
        const val = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
        const label = d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        return { label, value: val };
    });

    return (
        <div className="flex flex-col gap-3">
            {/* ── Filter bar ── */}
            <div className="flex flex-col gap-2">
                <div className="flex flex-wrap items-center gap-2">
                    {/* Type filter */}
                    <Combobox
                        options={[
                            { label: 'Semua Tipe', value: '' },
                            { label: 'Pemasukan', value: 'credit' },
                            { label: 'Pengeluaran', value: 'debit' },
                        ]}
                        value={filterType}
                        onChange={v => setFilterType(v ?? '')}
                        placeholder="Semua Tipe"
                        className="w-36 h-8 text-xs"
                    />
                    {/* Category filter */}
                    <Combobox
                        options={[{ label: 'Semua Kategori', value: '' }, ...categories]}
                        value={filterCategory}
                        onChange={v => setFilterCategory(v ?? '')}
                        placeholder="Semua Kategori"
                        searchPlaceholder="Cari kategori..."
                        className="w-44 h-8 text-xs"
                    />
                    {/* Month filter */}
                    <Combobox
                        options={[{ label: 'Semua Bulan', value: '' }, ...monthOptions]}
                        value={filterMonth}
                        onChange={v => setFilterMonth(v ?? '')}
                        placeholder="Semua Bulan"
                        className="w-44 h-8 text-xs"
                    />
                    {/* Search */}
                    <div className="relative flex-1 min-w-36">
                        <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-dark-400" />
                        <input
                            value={searchInput}
                            onChange={e => setSearchInput(e.target.value)}
                            placeholder="Cari deskripsi / ref..."
                            className="w-full h-8 pl-8 pr-8 text-xs rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-300 placeholder:text-dark-400 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        />
                        {searchInput && (
                            <button onClick={() => setSearchInput('')} className="absolute right-2 top-1/2 -translate-y-1/2">
                                <X className="w-3.5 h-3.5 text-dark-400 hover:text-dark-600" />
                            </button>
                        )}
                    </div>
                    {/* Active filters badge */}
                    {activeFilters > 0 && (
                        <Badge variant="blue" className="text-[10px] px-2 py-0.5">{activeFilters} filter aktif</Badge>
                    )}
                    <span className="text-xs text-dark-500 dark:text-dark-400 ml-auto tabular-nums whitespace-nowrap">
                        {meta.total} transaksi
                    </span>
                </div>

                {/* Action buttons */}
                <div className="flex items-center gap-2">
                    <Button
                        size="sm"
                        onClick={() => onCreateClick('income')}
                        className="h-8 gap-1.5 text-xs bg-emerald-600 hover:bg-emerald-700 text-white"
                    >
                        <Plus className="w-3.5 h-3.5" />
                        Pemasukan
                    </Button>
                    <Button
                        size="sm"
                        onClick={() => onCreateClick('expense')}
                        className="h-8 gap-1.5 text-xs bg-rose-600 hover:bg-rose-700 text-white"
                    >
                        <Plus className="w-3.5 h-3.5" />
                        Pengeluaran
                    </Button>
                    <Button
                        size="sm"
                        variant="outline"
                        onClick={() => onCreateClick('transfer')}
                        className="h-8 gap-1.5 text-xs"
                    >
                        <ArrowUpDown className="w-3.5 h-3.5" />
                        Transfer
                    </Button>
                    <Button
                        size="sm"
                        variant="ghost"
                        onClick={fetchTransactions}
                        className="h-8 w-8 p-0 ml-auto"
                        title="Refresh"
                    >
                        <RefreshCw className="w-3.5 h-3.5" />
                    </Button>
                </div>
            </div>

            {/* ── Table ── */}
            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-xs">
                        <thead>
                            <tr className="border-b border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                                <th className="w-10 px-3 py-2.5">
                                    <Checkbox
                                        checked={transactions.length > 0 && selected.length === transactions.length}
                                        onCheckedChange={toggleSelectAll}
                                    />
                                </th>
                                <th
                                    className="text-left px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400 cursor-pointer hover:text-dark-900 dark:hover:text-dark-200 select-none"
                                    onClick={() => toggleSort('description')}
                                >
                                    <div className="flex items-center gap-1">
                                        Transaksi <SortIcon col="description" />
                                    </div>
                                </th>
                                <th className="text-left px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400 hidden sm:table-cell">
                                    Kategori
                                </th>
                                <th
                                    className="text-left px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400 cursor-pointer hover:text-dark-900 dark:hover:text-dark-200 select-none whitespace-nowrap"
                                    onClick={() => toggleSort('transaction_date')}
                                >
                                    <div className="flex items-center gap-1">
                                        Tanggal <SortIcon col="transaction_date" />
                                    </div>
                                </th>
                                <th
                                    className="text-right px-3 py-2.5 font-semibold text-dark-500 dark:text-dark-400 cursor-pointer hover:text-dark-900 dark:hover:text-dark-200 select-none"
                                    onClick={() => toggleSort('amount')}
                                >
                                    <div className="flex items-center justify-end gap-1">
                                        Jumlah <SortIcon col="amount" />
                                    </div>
                                </th>
                                <th className="w-10 px-2 py-2.5" />
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-secondary-100 dark:divide-dark-700">
                            {loading ? (
                                Array.from({ length: 5 }).map((_, i) => (
                                    <tr key={i}>
                                        <td className="px-3 py-3"><Skeleton className="h-4 w-4" /></td>
                                        <td className="px-3 py-3"><Skeleton className="h-4 w-40" /></td>
                                        <td className="px-3 py-3 hidden sm:table-cell"><Skeleton className="h-4 w-24" /></td>
                                        <td className="px-3 py-3"><Skeleton className="h-4 w-16" /></td>
                                        <td className="px-3 py-3 text-right"><Skeleton className="h-4 w-24 ml-auto" /></td>
                                        <td className="px-2 py-3"><Skeleton className="h-4 w-4" /></td>
                                    </tr>
                                ))
                            ) : transactions.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="py-12">
                                        <EmptyState
                                            icon="list"
                                            title="Belum ada transaksi"
                                            description="Tambah transaksi menggunakan tombol di atas."
                                        />
                                    </td>
                                </tr>
                            ) : (
                                transactions.map(t => {
                                    const isCredit = t.transaction_type === 'credit';
                                    const isTrf = t.reference_number?.startsWith('TRF');
                                    return (
                                        <tr
                                            key={t.id}
                                            className={[
                                                'group transition-colors duration-100',
                                                selected.includes(t.id)
                                                    ? 'bg-primary-50 dark:bg-primary-900/10'
                                                    : 'hover:bg-secondary-50 dark:hover:bg-dark-700/50',
                                            ].join(' ')}
                                        >
                                            <td className="px-3 py-2.5">
                                                <Checkbox
                                                    checked={selected.includes(t.id)}
                                                    onCheckedChange={() => toggleSelect(t.id)}
                                                />
                                            </td>
                                            <td className="px-3 py-2.5 max-w-48">
                                                <div className="flex items-center gap-2">
                                                    <div className={`shrink-0 w-6 h-6 rounded-md flex items-center justify-center ${
                                                        isCredit
                                                            ? 'bg-emerald-100 dark:bg-emerald-900/30'
                                                            : 'bg-rose-100 dark:bg-rose-900/30'
                                                    }`}>
                                                        {isCredit
                                                            ? <ArrowUpRight className="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                                                            : <ArrowDownRight className="w-3 h-3 text-rose-600 dark:text-rose-400" />
                                                        }
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="font-medium text-dark-900 dark:text-dark-200 truncate">
                                                            {t.description ?? (isCredit ? 'Pemasukan' : 'Pengeluaran')}
                                                        </p>
                                                        {t.reference_number && (
                                                            <p className="text-[10px] text-dark-400 dark:text-dark-500 font-mono truncate">
                                                                {t.reference_number}
                                                                {isTrf && <span className="ml-1 text-blue-400 dark:text-blue-500">[TRF]</span>}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-3 py-2.5 hidden sm:table-cell">
                                                {t.category ? (
                                                    <div>
                                                        {t.category.parent && (
                                                            <p className="text-[10px] text-dark-400 dark:text-dark-500">{t.category.parent.label}</p>
                                                        )}
                                                        <Badge variant="zinc" className="text-[10px] px-1.5 py-0">
                                                            {t.category.label}
                                                        </Badge>
                                                    </div>
                                                ) : (
                                                    <span className="text-[10px] text-dark-400 dark:text-dark-500 italic">—</span>
                                                )}
                                            </td>
                                            <td className="px-3 py-2.5 whitespace-nowrap text-dark-600 dark:text-dark-400">
                                                {new Date(t.transaction_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' })}
                                            </td>
                                            <td className="px-3 py-2.5 text-right whitespace-nowrap font-bold tabular-nums">
                                                <span className={isCredit ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'}>
                                                    {isCredit ? '+' : '-'}{formatCurrency(t.amount)}
                                                </span>
                                            </td>
                                            <td className="px-2 py-2.5">
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger asChild>
                                                        <Button variant="ghost" size="icon" className="h-7 w-7 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <MoreHorizontal className="w-3.5 h-3.5" />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end" className="w-44">
                                                        {t.attachment_url && (
                                                            <>
                                                                <DropdownMenuItem onClick={() => onAttachmentView(t.attachment_url!, t.attachment_name ?? undefined)}>
                                                                    <Paperclip className="w-3.5 h-3.5 mr-2" />
                                                                    Lihat Lampiran
                                                                </DropdownMenuItem>
                                                                <DropdownMenuSeparator />
                                                            </>
                                                        )}
                                                        <DropdownMenuItem onClick={() => onCategorize([t.id], false)}>
                                                            <Tag className="w-3.5 h-3.5 mr-2" />
                                                            Kategorikan
                                                        </DropdownMenuItem>
                                                        <DropdownMenuSeparator />
                                                        <DropdownMenuItem
                                                            onClick={() => handleDelete(t.id)}
                                                            className="text-rose-600 dark:text-rose-400 focus:text-rose-600 dark:focus:text-rose-400"
                                                        >
                                                            <Trash2 className="w-3.5 h-3.5 mr-2" />
                                                            Hapus
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </td>
                                        </tr>
                                    );
                                })
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* ── Pagination ── */}
            {meta.last_page > 1 && (
                <Pagination
                    currentPage={meta.current_page}
                    lastPage={meta.last_page}
                    perPage={meta.per_page}
                    total={meta.total}
                    onPageChange={setPage}
                />
            )}

            {/* ── Bulk action bar ── */}
            {selected.length > 0 && (
                <div className="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 animate-in slide-in-from-bottom-4 duration-200">
                    <div className="flex items-center gap-3 px-5 py-3 rounded-2xl bg-dark-800 dark:bg-dark-700 border border-dark-600 shadow-[0_8px_32px_rgba(0,0,0,0.5)]">
                        <span className="text-xs font-semibold text-white/80">
                            {selected.length} dipilih
                        </span>
                        <div className="w-px h-4 bg-white/10" />
                        <Button
                            size="sm"
                            variant="ghost"
                            onClick={() => onCategorize(selected, true)}
                            className="h-7 gap-1.5 text-xs text-white/70 hover:text-white hover:bg-white/10"
                        >
                            <Tag className="w-3.5 h-3.5" />
                            Kategorikan
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            onClick={handleBulkDelete}
                            disabled={deleting}
                            className="h-7 gap-1.5 text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-500/10"
                        >
                            <Trash2 className="w-3.5 h-3.5" />
                            Hapus
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            onClick={() => setSelected([])}
                            className="h-7 w-7 p-0 text-white/40 hover:text-white hover:bg-white/10"
                        >
                            <X className="w-3.5 h-3.5" />
                        </Button>
                    </div>
                </div>
            )}
        </div>
    );
}
