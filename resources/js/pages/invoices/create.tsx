import { Head, router, useForm } from '@inertiajs/react';
import { toast } from 'sonner';
import {
    ArrowLeft,
    ChevronDown,
    FileText,
    Info,
    List,
    Plus,
    Search,
    Trash2,
} from 'lucide-react';
import * as React from 'react';
import { format } from 'date-fns';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/shared/currency-input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { AppLayout } from '@/layouts/app-layout';
import { cn, formatCurrency } from '@/lib/utils';
import type { SharedProps } from '@/types';

/* ─────────────────────────────────── types ─── */

interface ClientOption {
    id: number;
    name: string;
    email: string | null;
}

interface ServiceOption {
    id: number;
    name: string;
    price: number;
    type: string;
}

interface InvoiceItem {
    client_id: number | null;
    service_name: string;
    quantity: string;
    unit: string;
    unit_price: number;
    cogs_amount: number;
    is_tax_deposit: boolean;
}

interface Props extends SharedProps {
    clients: ClientOption[];
    services: ServiceOption[];
    nextSeq: number;
    companyInitials: string;
}

/* ─────────────────────────────────── constants ─── */

const ROMAN_MONTHS = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

const COMMON_UNITS = [
    'jam', 'hari', 'minggu', 'bulan', 'tahun',
    'project', 'paket', 'set', 'lot', 'kali',
    'pcs', 'unit', 'lembar', 'kg', 'ton', 'm²', 'm³',
];

/* ─────────────────────────────────── helpers ─── */

function getRomanMonth(month: number) {
    return ROMAN_MONTHS[month] ?? 'I';
}

function previewInvoiceNumber(seq: number, company: string, clientName: string, date: string) {
    if (!date) return `${String(seq).padStart(3, '0')}/INV/${company}-???/???/????`;
    const d = new Date(date);
    const clientInitials = clientName
        .split(/\s+/)
        .filter((w) => !['pt', 'cv', 'ud', 'tb'].includes(w.toLowerCase()))
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('') || 'XXX';
    return `${String(seq).padStart(3, '0')}/INV/${company}-${clientInitials}/${getRomanMonth(d.getMonth() + 1)}/${d.getFullYear()}`;
}

function emptyItem(defaultClientId: number | null = null): InvoiceItem {
    return {
        client_id: defaultClientId,
        service_name: '',
        quantity: '1',
        unit: '',
        unit_price: 0,
        cogs_amount: 0,
        is_tax_deposit: false,
    };
}

/* ─────────────────────────────────── shared cell style ─── */

const cellCls = 'h-8 text-xs px-2 rounded-md border-transparent hover:border-secondary-300 dark:hover:border-dark-600 bg-transparent dark:bg-transparent focus:bg-white dark:focus:bg-dark-800';

/* ─────────────────────────────────── currency cell ─── */

export function CurrencyCell({ value, onChange }: { value: number; onChange: (v: number) => void }) {
    const [display, setDisplay] = React.useState(() =>
        value > 0 ? value.toLocaleString('id-ID') : '',
    );

    React.useEffect(() => {
        setDisplay(value > 0 ? value.toLocaleString('id-ID') : '');
    }, [value]);

    return (
        <Input
            inputMode="numeric"
            value={display}
            onChange={(e) => {
                const raw = e.target.value.replace(/[^0-9]/g, '');
                const n = parseInt(raw, 10) || 0;
                setDisplay(n > 0 ? n.toLocaleString('id-ID') : '');
                onChange(n);
            }}
            onBlur={() => {
                setDisplay(value > 0 ? value.toLocaleString('id-ID') : '');
            }}
            placeholder="0"
            className={cn(cellCls, 'text-right')}
        />
    );
}

/* ─────────────────────────────────── service lookup ─── */

export function ServiceLookup({
    services,
    onSelect,
}: {
    services: ServiceOption[];
    onSelect: (svc: ServiceOption) => void;
}) {
    const [open, setOpen] = React.useState(false);
    const [search, setSearch] = React.useState('');

    const filtered = search
        ? services.filter((s) => s.name.toLowerCase().includes(search.toLowerCase()))
        : services;

    return (
        <Popover open={open} onOpenChange={(o) => { setOpen(o); if (!o) setSearch(''); }}>
            <PopoverTrigger asChild>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    className="h-7 w-7 shrink-0 text-dark-400 hover:text-primary-600 dark:hover:text-primary-400"
                    title="Pilih dari katalog layanan"
                >
                    <Search className="h-3.5 w-3.5" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-64 p-0 overflow-hidden" align="start">
                <div className="px-2 pt-2 pb-1.5 border-b border-secondary-100 dark:border-dark-600">
                    <Input
                        autoFocus
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari layanan..."
                        icon={<Search className="h-3.5 w-3.5" />}
                        className="h-8 text-xs focus:ring-0"
                    />
                </div>
                <div className="max-h-52 overflow-y-auto p-1.5">
                    {filtered.length === 0 ? (
                        <p className="py-6 text-center text-sm text-dark-400 dark:text-dark-500">
                            Layanan tidak ditemukan
                        </p>
                    ) : (
                        filtered.map((svc) => (
                            <button
                                key={svc.id}
                                type="button"
                                onClick={() => { onSelect(svc); setOpen(false); setSearch(''); }}
                                className="w-full text-left px-2.5 py-2 rounded-lg transition-colors hover:bg-zinc-50 dark:hover:bg-dark-600"
                            >
                                <div className="text-sm font-medium text-dark-700 dark:text-dark-300 truncate">{svc.name}</div>
                                <div className="text-xs text-dark-400 dark:text-dark-500">{formatCurrency(svc.price)}</div>
                            </button>
                        ))
                    )}
                </div>
            </PopoverContent>
        </Popover>
    );
}

/* ─────────────────────────────────── form component ─── */

interface InvoiceFormProps {
    clients: ClientOption[];
    services: ServiceOption[];
    nextSeq: number;
    companyInitials: string;
    existingInvoiceNumber?: string;
    initialData?: {
        client_id: number | null;
        issue_date: string;
        due_date: string;
        discount_type: string;
        discount_value: number;
        discount_reason: string;
        items: InvoiceItem[];
    };
    submitUrl: string;
    method: 'post' | 'put';
    submitLabel: string;
    isEdit?: boolean;
}

export function InvoiceForm({
    clients,
    services,
    nextSeq,
    companyInitials,
    existingInvoiceNumber,
    initialData,
    submitUrl,
    method,
    submitLabel,
    isEdit = false,
}: InvoiceFormProps) {
    const { data, setData, post, put, processing, errors } = useForm({
        client_id: initialData?.client_id ?? null as number | null,
        issue_date: initialData?.issue_date ?? new Date().toISOString().slice(0, 10),
        due_date: initialData?.due_date ?? '',
        items: (initialData?.items ?? [emptyItem()]) as InvoiceItem[],
        discount_type: initialData?.discount_type ?? 'fixed',
        discount_value: initialData?.discount_value ?? 0,
        discount_reason: initialData?.discount_reason ?? '',
    });

    const selectedClient = clients.find((c) => c.id === data.client_id);
    const invoicePreview = previewInvoiceNumber(
        nextSeq,
        companyInitials,
        selectedClient?.name ?? '',
        data.issue_date,
    );

    /* ── calculations ── */
    const itemTotals = data.items.map((item) => {
        const qty = parseFloat(item.quantity) || 0;
        return Math.round(item.unit_price * qty);
    });
    const subtotal = itemTotals.reduce((a, b) => a + b, 0);
    const discountAmount =
        data.discount_type === 'percentage'
            ? Math.round((subtotal * (data.discount_value || 0)) / 100)
            : data.discount_value || 0;
    const totalAmount = Math.max(0, subtotal - discountAmount);
    const totalCogs = data.items
        .filter((i) => !i.is_tax_deposit)
        .reduce((s, i) => s + (i.cogs_amount || 0), 0);
    const totalTaxDeposits = data.items.reduce((s, item, idx) =>
        s + (item.is_tax_deposit ? itemTotals[idx] : 0), 0);
    const grossProfit = totalAmount - totalTaxDeposits - totalCogs;
    const pph05 = Math.round((totalAmount - totalTaxDeposits) * 0.005);

    /* ── item handlers ── */
    const addItem = () =>
        setData('items', [...data.items, emptyItem(data.client_id)]);

    const removeItem = (idx: number) =>
        setData('items', data.items.filter((_, i) => i !== idx));

    const updateItem = (idx: number, field: keyof InvoiceItem, value: unknown) =>
        setData('items', data.items.map((item, i) => (i === idx ? { ...item, [field]: value } : item)));

    const updateItemFields = (idx: number, updates: Partial<InvoiceItem>) =>
        setData('items', data.items.map((item, i) => (i === idx ? { ...item, ...updates } : item)));

    const handleServiceNameChange = (idx: number, name: string) => {
        const matched = services.find((s) => s.name === name);
        updateItemFields(idx, {
            service_name: name,
            ...(matched ? { unit_price: matched.price } : {}),
        });
    };

    /* ── submit ── */
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const options = {
            onSuccess: () => toast.success(isEdit ? 'Invoice berhasil diperbarui.' : 'Invoice berhasil dibuat.'),
            onError: () => toast.error('Gagal menyimpan invoice. Periksa kembali form Anda.'),
        };
        if (method === 'put') put(submitUrl, options);
        else post(submitUrl, options);
    };

    /* ── client options ── */
    const clientOptions = clients.map((c) => ({ value: c.id, label: c.name }));

    const [discountOpen, setDiscountOpen] = React.useState(false);

    return (
        <form onSubmit={handleSubmit}>
            <div className="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">

                {/* ── LEFT COLUMN (4/5) ── */}
                <div className="xl:col-span-4 space-y-6">

                    {/* Card 1: Detail Invoice */}
                    <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                        <div className="px-6 py-4 border-b border-secondary-200 dark:border-dark-600 flex items-center gap-3">
                            <div className="w-8 h-8 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center shrink-0">
                                <FileText className="w-4 h-4 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h2 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Detail Invoice</h2>
                                <p className="text-xs text-dark-500 dark:text-dark-400">Informasi dasar invoice</p>
                            </div>
                        </div>
                        <div className="p-6 space-y-5">
                            {/* Invoice number preview */}
                            <div className="flex items-center gap-3 p-4 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20">
                                <FileText className="w-5 h-5 text-primary-600 dark:text-primary-400 shrink-0" />
                                <div>
                                    <p className="text-xs text-primary-600 dark:text-primary-400 font-medium">
                                        {isEdit ? 'Nomor Invoice' : 'Preview Nomor Invoice (otomatis saat dikirim)'}
                                    </p>
                                    <p className="font-mono text-sm font-semibold text-primary-700 dark:text-primary-300">
                                        {isEdit ? (existingInvoiceNumber ?? '—') : invoicePreview}
                                    </p>
                                </div>
                            </div>

                            {/* Client + Dates */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <Combobox
                                    options={clientOptions}
                                    value={data.client_id}
                                    onChange={(v) => setData('client_id', v ? Number(v) : null)}
                                    placeholder="Pilih klien..."
                                    label="Klien *"
                                    hint={selectedClient?.email ?? undefined}
                                    error={errors.client_id}
                                />
                                <div className="grid grid-cols-2 gap-3">
                                    <DatePicker
                                        label="Tgl Invoice *"
                                        value={data.issue_date ? new Date(data.issue_date + 'T00:00:00') : null}
                                        onChange={(d) => setData('issue_date', d ? format(d, 'yyyy-MM-dd') : '')}
                                        error={errors.issue_date}
                                    />
                                    <DatePicker
                                        label="Jatuh Tempo *"
                                        value={data.due_date ? new Date(data.due_date + 'T00:00:00') : null}
                                        onChange={(d) => setData('due_date', d ? format(d, 'yyyy-MM-dd') : '')}
                                        minDate={data.issue_date ? new Date(data.issue_date + 'T00:00:00') : undefined}
                                        error={errors.due_date}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Card 2: Invoice Items */}
                    <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                        <div className="px-6 py-4 border-b border-secondary-200 dark:border-dark-600 flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <div className="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                                    <List className="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <h2 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Item Invoice</h2>
                                    <p className="text-xs text-dark-500 dark:text-dark-400 flex items-center gap-1">
                                        <Info className="w-3 h-3 shrink-0" />
                                        Tiap item bisa ditujukan ke klien berbeda
                                    </p>
                                </div>
                            </div>
                            <Button type="button" variant="outline" size="sm" onClick={addItem}>
                                <Plus className="w-3.5 h-3.5 mr-1" /> Tambah Item
                            </Button>
                        </div>

                        {errors.items && (
                            <p className="px-6 pt-3 text-xs text-red-600 dark:text-red-400">{errors.items}</p>
                        )}

                        <div className="overflow-x-auto">
                        {/* min-w ensures single-row layout; scrolls on narrow viewports */}
                        <div style={{ minWidth: '820px' }}>

                            {/* Header */}
                            <div className="grid bg-secondary-50 dark:bg-dark-800 border-b border-secondary-200 dark:border-dark-600"
                                style={{ gridTemplateColumns: '32px 2fr 3fr 1fr 1.5fr 2fr 2fr 40px 2fr 36px' }}>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-400 dark:text-dark-500 text-center">#</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400">Klien</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400">Nama Layanan</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400 text-right">Qty</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400">Satuan</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400 text-right">Harga Sat.</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400 text-right">HPP</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400 text-center" title="Titipan Pajak (PPh)">PPh</div>
                                <div className="px-2 py-2.5 text-xs font-semibold text-dark-600 dark:text-dark-400 text-right">Subtotal</div>
                                <div />
                            </div>

                            {/* Rows */}
                            {data.items.map((item, idx) => (
                                <div
                                    key={idx}
                                    className={cn(
                                        'grid border-b border-secondary-200 dark:border-dark-600 last:border-0',
                                        'hover:bg-secondary-50/50 dark:hover:bg-dark-800/30 transition-colors group',
                                        item.is_tax_deposit && 'bg-amber-50/40 dark:bg-amber-900/5',
                                    )}
                                    style={{ gridTemplateColumns: '32px 2fr 3fr 1fr 1.5fr 2fr 2fr 40px 2fr 36px' }}
                                >
                                    {/* Row number */}
                                    <div className="flex items-center justify-center px-1 py-1.5">
                                        <span className="text-xs font-mono text-dark-400 dark:text-dark-500">{idx + 1}</span>
                                    </div>

                                    {/* Client (per-item) */}
                                    <div className="flex items-center px-1 py-1.5">
                                        <Combobox
                                            options={[
                                                { value: -1, label: '— default —' },
                                                ...clientOptions,
                                            ]}
                                            value={item.client_id ?? -1}
                                            onChange={(v) => {
                                                const val = v ? Number(v) : null;
                                                updateItem(idx, 'client_id', val === -1 ? null : val);
                                            }}
                                            placeholder="Default"
                                            className="w-full [&_button]:h-8 [&_button]:text-xs [&_button]:px-2 [&_button]:rounded-md [&_button]:ring-0 [&_button]:shadow-none"
                                        />
                                    </div>

                                    {/* Service name + lookup */}
                                    <div className="flex items-center gap-0.5 px-1 py-1.5">
                                        <div className="flex-1 min-w-0">
                                            <Input
                                                value={item.service_name}
                                                onChange={(e) => handleServiceNameChange(idx, e.target.value)}
                                                placeholder="Nama layanan..."
                                                error={errors[`items.${idx}.service_name` as keyof typeof errors]}
                                                className={cellCls}
                                            />
                                        </div>
                                        <ServiceLookup
                                            services={services}
                                            onSelect={(svc) => updateItemFields(idx, { service_name: svc.name, unit_price: svc.price })}
                                        />
                                    </div>

                                    {/* Qty */}
                                    <div className="flex items-center px-1 py-1.5">
                                        <Input
                                            type="number"
                                            value={item.quantity}
                                            onChange={(e) => updateItem(idx, 'quantity', e.target.value)}
                                            min="0.001"
                                            step="any"
                                            placeholder="1"
                                            className={cn(cellCls, 'text-right')}
                                        />
                                    </div>

                                    {/* Unit */}
                                    <div className="flex items-center px-1 py-1.5">
                                        <Input
                                            list={`unit-list-${idx}`}
                                            value={item.unit}
                                            onChange={(e) => updateItem(idx, 'unit', e.target.value)}
                                            placeholder="satuan"
                                            className={cellCls}
                                        />
                                        <datalist id={`unit-list-${idx}`}>
                                            {COMMON_UNITS.map((u) => (
                                                <option key={u} value={u} />
                                            ))}
                                        </datalist>
                                    </div>

                                    {/* Unit price */}
                                    <div className="flex items-center px-1 py-1.5">
                                        <CurrencyCell value={item.unit_price} onChange={(v) => updateItem(idx, 'unit_price', v)} />
                                    </div>

                                    {/* COGS / HPP */}
                                    <div className="flex items-center px-1 py-1.5">
                                        <CurrencyCell value={item.cogs_amount} onChange={(v) => updateItem(idx, 'cogs_amount', v)} />
                                    </div>

                                    {/* PPh toggle */}
                                    <div className="flex items-center justify-center px-1 py-1.5">
                                        <button
                                            type="button"
                                            onClick={() => updateItem(idx, 'is_tax_deposit', !item.is_tax_deposit)}
                                            title={item.is_tax_deposit ? 'Titipan pajak: aktif' : 'Titipan pajak: nonaktif'}
                                            className={cn(
                                                'h-5 w-5 rounded flex items-center justify-center border text-xs font-bold transition-colors',
                                                item.is_tax_deposit
                                                    ? 'bg-amber-500 border-amber-500 text-white'
                                                    : 'border-secondary-300 dark:border-dark-600 text-transparent hover:border-amber-400',
                                            )}
                                        >
                                            ✓
                                        </button>
                                    </div>

                                    {/* Line total */}
                                    <div className="flex items-center justify-end px-2 py-1.5">
                                        <span className="text-xs font-semibold text-dark-900 dark:text-dark-50 tabular-nums whitespace-nowrap">
                                            {formatCurrency(itemTotals[idx])}
                                        </span>
                                    </div>

                                    {/* Delete */}
                                    <div className="flex items-center justify-center px-1 py-1.5">
                                        {data.items.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeItem(idx)}
                                                className="h-6 w-6 rounded flex items-center justify-center text-dark-300 dark:text-dark-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 opacity-0 group-hover:opacity-100 transition-all"
                                            >
                                                <Trash2 className="w-3.5 h-3.5" />
                                            </button>
                                        )}
                                    </div>
                                </div>
                            ))}

                            {/* Footer: subtotal row */}
                            <div className="grid bg-secondary-50/60 dark:bg-dark-800/60 border-t border-secondary-200 dark:border-dark-600"
                                style={{ gridTemplateColumns: '32px 2fr 3fr 1fr 1.5fr 2fr 2fr 40px 2fr 36px' }}>
                                <div className="col-span-8 px-3 py-2 text-xs text-dark-500 dark:text-dark-400 flex items-center gap-1">
                                    <span className="font-medium text-dark-700 dark:text-dark-300">{data.items.length} item</span>
                                    <span>·</span>
                                    <span>HPP total:</span>
                                    <span className="font-medium text-dark-700 dark:text-dark-300">{formatCurrency(totalCogs)}</span>
                                </div>
                                <div className="px-2 py-2 text-xs font-bold text-dark-900 dark:text-dark-50 text-right tabular-nums">
                                    {formatCurrency(subtotal)}
                                </div>
                                <div />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                {/* ── RIGHT COLUMN — sticky summary (1/5) ── */}
                <div className="xl:col-span-1 xl:self-start xl:sticky xl:top-6">
                    <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">

                        {/* Summary rows */}
                        <div className="divide-y divide-secondary-100 dark:divide-dark-600">
                            <div className="flex items-center justify-between px-4 py-3">
                                <span className="text-xs text-dark-500 dark:text-dark-400">Subtotal</span>
                                <span className="text-xs font-semibold text-dark-900 dark:text-dark-50 tabular-nums">{formatCurrency(subtotal)}</span>
                            </div>

                            {totalTaxDeposits > 0 && (
                                <div className="flex items-center justify-between px-4 py-3">
                                    <span className="text-xs text-dark-500 dark:text-dark-400">Titipan Pajak</span>
                                    <span className="text-xs font-semibold text-amber-600 dark:text-amber-400 tabular-nums">{formatCurrency(totalTaxDeposits)}</span>
                                </div>
                            )}

                            {discountAmount > 0 && (
                                <div className="flex items-center justify-between px-4 py-3">
                                    <span className="text-xs text-dark-500 dark:text-dark-400">
                                        Diskon{data.discount_type === 'percentage' ? ` (${data.discount_value}%)` : ''}
                                    </span>
                                    <span className="text-xs font-semibold text-red-500 tabular-nums">− {formatCurrency(discountAmount)}</span>
                                </div>
                            )}

                            <div className="px-4 py-4 bg-secondary-50 dark:bg-dark-900/40">
                                <div className="flex items-baseline justify-between gap-2">
                                    <span className="text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide">Total</span>
                                    <span className="text-2xl font-bold text-primary-600 dark:text-primary-400 tabular-nums">{formatCurrency(totalAmount)}</span>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 divide-x divide-secondary-100 dark:divide-dark-600">
                                <div className="px-4 py-3">
                                    <p className="text-[10px] text-dark-400 dark:text-dark-500 mb-0.5">Laba Kotor</p>
                                    <p className={cn('text-xs font-bold tabular-nums', grossProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400')}>
                                        {formatCurrency(grossProfit)}
                                    </p>
                                </div>
                                <div className="px-4 py-3">
                                    <p className="text-[10px] text-dark-400 dark:text-dark-500 mb-0.5">Est. PPh 0.5%</p>
                                    <p className="text-xs font-bold text-orange-600 dark:text-orange-400 tabular-nums">{formatCurrency(pph05)}</p>
                                </div>
                            </div>
                        </div>

                        {/* Discount accordion */}
                        <div>
                            <button
                                type="button"
                                onClick={() => setDiscountOpen(!discountOpen)}
                                className="w-full flex items-center justify-between px-4 py-3 border-t border-secondary-200 dark:border-dark-600 text-xs text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 hover:bg-secondary-50 dark:hover:bg-dark-700/40 transition-colors"
                            >
                                <span className="font-medium">Diskon</span>
                                <div className="flex items-center gap-1.5">
                                    {discountAmount > 0 && (
                                        <span className="text-[10px] font-semibold text-red-500">− {formatCurrency(discountAmount)}</span>
                                    )}
                                    <ChevronDown className={cn('w-3.5 h-3.5 transition-transform duration-200', discountOpen && 'rotate-180')} />
                                </div>
                            </button>
                            {discountOpen && (
                                <div className="border-t border-secondary-100 dark:border-dark-700 px-4 pb-4 pt-3 space-y-2">
                                    <div className="flex gap-2">
                                        {(['fixed', 'percentage'] as const).map((type) => (
                                            <button
                                                key={type}
                                                type="button"
                                                onClick={() => setData('discount_type', type)}
                                                className={cn(
                                                    'flex-1 py-1.5 rounded-lg text-xs font-medium border transition-colors',
                                                    data.discount_type === type
                                                        ? 'bg-primary-600 text-white border-primary-600'
                                                        : 'bg-white dark:bg-dark-800 text-dark-600 dark:text-dark-400 border-secondary-200 dark:border-dark-600 hover:bg-secondary-50 dark:hover:bg-dark-700',
                                                )}
                                            >
                                                {type === 'fixed' ? 'Nominal' : '%'}
                                            </button>
                                        ))}
                                    </div>
                                    {data.discount_type === 'fixed' ? (
                                        <CurrencyInput
                                            value={data.discount_value}
                                            onChange={(v) => setData('discount_value', v)}
                                            placeholder="0"
                                        />
                                    ) : (
                                        <div className="relative">
                                            <Input
                                                type="number"
                                                value={data.discount_value}
                                                onChange={(e) => setData('discount_value', parseFloat(e.target.value) || 0)}
                                                min={0}
                                                max={100}
                                                step={0.01}
                                                className="pr-8"
                                            />
                                            <span className="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-dark-500 dark:text-dark-400">%</span>
                                        </div>
                                    )}
                                    <Input
                                        value={data.discount_reason}
                                        onChange={(e) => setData('discount_reason', e.target.value)}
                                        placeholder="Alasan diskon (opsional)..."
                                    />
                                </div>
                            )}
                        </div>

                        {/* Action buttons */}
                        <div className="p-4 border-t border-secondary-200 dark:border-dark-600 space-y-2">
                            <Button
                                type="submit"
                                variant="primary"
                                loading={processing}
                                disabled={data.items.length === 0}
                                className="w-full"
                            >
                                {submitLabel}
                            </Button>
                            <Button
                                type="button"
                                variant="zinc"
                                onClick={() => router.get('/invoices')}
                                className="w-full"
                            >
                                Batal
                            </Button>
                            {data.items.length === 0 && (
                                <p className="text-center text-[10px] text-dark-400 dark:text-dark-500">
                                    Tambah item untuk mengaktifkan simpan
                                </p>
                            )}
                        </div>
                    </div>
                </div>

            </div>{/* end xl:grid-cols-3 */}
        </form>
    );
}

/* ─────────────────────────────────── page ─── */

function CreateInvoicePage({ clients, services, nextSeq, companyInitials }: Props) {
    return (
        <>
            <Head title="Buat Invoice" />
            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div className="flex items-center gap-3">
                        <button
                            onClick={() => router.get('/invoices')}
                            className="h-9 w-9 rounded-xl flex items-center justify-center border border-secondary-200 dark:border-dark-600 hover:bg-zinc-100 dark:hover:bg-dark-600 transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4 text-dark-600 dark:text-dark-400" />
                        </button>
                        <div>
                            <h1 className="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                                Buat Invoice
                            </h1>
                            <p className="text-gray-600 dark:text-zinc-400 text-lg">
                                Invoice baru akan disimpan sebagai draft
                            </p>
                        </div>
                    </div>
                </div>

                <InvoiceForm
                    clients={clients}
                    services={services}
                    nextSeq={nextSeq}
                    companyInitials={companyInitials}
                    submitUrl="/invoices"
                    method="post"
                    submitLabel="Simpan sebagai Draft"
                />
            </div>
        </>
    );
}

CreateInvoicePage.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;

export default CreateInvoicePage;
