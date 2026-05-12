import { Head, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    CheckCircle2,
    FileText,
    Plus,
    Trash2,
} from 'lucide-react';
import * as React from 'react';
import { CurrencyInput } from '@/components/shared/currency-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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

const ROMAN_MONTHS = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

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

function emptyItem(): InvoiceItem {
    return {
        service_name: '',
        quantity: '1',
        unit: 'pcs',
        unit_price: 0,
        cogs_amount: 0,
        is_tax_deposit: false,
    };
}

/* ─────────────────────────────────── form component ─── */

interface InvoiceFormProps {
    clients: ClientOption[];
    services: ServiceOption[];
    nextSeq: number;
    companyInitials: string;
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
    const totalTaxDepositsCalc = data.items.reduce((s, item, idx) => {
        return s + (item.is_tax_deposit ? itemTotals[idx] : 0);
    }, 0);
    const grossProfit = totalAmount - totalTaxDepositsCalc - totalCogs;
    const pph05 = Math.round((totalAmount - totalTaxDepositsCalc) * 0.005);

    /* ── item handlers ── */
    const addItem = () => setData('items', [...data.items, emptyItem()]);

    const removeItem = (idx: number) =>
        setData('items', data.items.filter((_, i) => i !== idx));

    const updateItem = (idx: number, field: keyof InvoiceItem, value: unknown) => {
        setData(
            'items',
            data.items.map((item, i) => (i === idx ? { ...item, [field]: value } : item)),
        );
    };

    const fillFromService = (idx: number, serviceId: number) => {
        const svc = services.find((s) => s.id === serviceId);
        if (!svc) return;
        updateItem(idx, 'service_name', svc.name);
        updateItem(idx, 'unit_price', svc.price);
    };

    /* ── submit ── */
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (method === 'put') {
            put(submitUrl);
        } else {
            post(submitUrl);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            {/* Invoice number preview */}
            <div className="flex items-center gap-3 p-4 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20">
                <FileText className="w-5 h-5 text-primary-600 dark:text-primary-400 shrink-0" />
                <div>
                    <p className="text-xs text-primary-600 dark:text-primary-400 font-medium">
                        Preview Nomor Invoice (otomatis saat dikirim)
                    </p>
                    <p className="font-mono text-sm font-semibold text-primary-700 dark:text-primary-300">
                        {invoicePreview}
                    </p>
                </div>
            </div>

            {/* Two-column: client info + dates */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Client */}
                <div className="space-y-4">
                    <div className="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Informasi Klien</h3>
                        <p className="text-xs text-dark-500 dark:text-dark-400 mt-0.5">Pilih klien yang akan ditagihkan</p>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-dark-900 dark:text-dark-300 mb-1.5">
                            Klien <span className="text-red-500">*</span>
                        </label>
                        <select
                            value={data.client_id ?? ''}
                            onChange={(e) => setData('client_id', e.target.value ? Number(e.target.value) : null)}
                            className={cn(
                                'w-full h-9 rounded-lg border text-sm px-3',
                                'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-300',
                                'focus:outline-none focus:ring-2 focus:ring-primary-500',
                                errors.client_id
                                    ? 'border-red-500 dark:border-red-500'
                                    : 'border-secondary-300 dark:border-dark-600',
                            )}
                        >
                            <option value="">Pilih klien...</option>
                            {clients.map((c) => (
                                <option key={c.id} value={c.id}>{c.name}</option>
                            ))}
                        </select>
                        {errors.client_id && (
                            <p className="mt-1 text-xs text-red-600 dark:text-red-400">{errors.client_id}</p>
                        )}
                        {selectedClient?.email && (
                            <p className="mt-1 text-xs text-dark-400 dark:text-dark-500">{selectedClient.email}</p>
                        )}
                    </div>
                </div>

                {/* Dates */}
                <div className="space-y-4">
                    <div className="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Tanggal Invoice</h3>
                        <p className="text-xs text-dark-500 dark:text-dark-400 mt-0.5">Tanggal terbit dan jatuh tempo</p>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-dark-900 dark:text-dark-300 mb-1.5">
                                Tgl Invoice <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                value={data.issue_date}
                                onChange={(e) => setData('issue_date', e.target.value)}
                                className={cn(
                                    'w-full h-9 rounded-lg border text-sm px-3',
                                    'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-300',
                                    'focus:outline-none focus:ring-2 focus:ring-primary-500',
                                    errors.issue_date
                                        ? 'border-red-500 dark:border-red-500'
                                        : 'border-secondary-300 dark:border-dark-600',
                                )}
                            />
                            {errors.issue_date && (
                                <p className="mt-1 text-xs text-red-600">{errors.issue_date}</p>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-dark-900 dark:text-dark-300 mb-1.5">
                                Jatuh Tempo <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                value={data.due_date}
                                onChange={(e) => setData('due_date', e.target.value)}
                                min={data.issue_date}
                                className={cn(
                                    'w-full h-9 rounded-lg border text-sm px-3',
                                    'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-300',
                                    'focus:outline-none focus:ring-2 focus:ring-primary-500',
                                    errors.due_date
                                        ? 'border-red-500 dark:border-red-500'
                                        : 'border-secondary-300 dark:border-dark-600',
                                )}
                            />
                            {errors.due_date && (
                                <p className="mt-1 text-xs text-red-600">{errors.due_date}</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Invoice Items */}
            <div className="space-y-4">
                <div className="flex items-center justify-between border-b border-secondary-200 dark:border-dark-600 pb-3">
                    <div>
                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Item Invoice</h3>
                        <p className="text-xs text-dark-500 dark:text-dark-400 mt-0.5">
                            Tambahkan layanan dan harga yang ditagihkan
                        </p>
                    </div>
                    <Button type="button" variant="outline" size="sm" icon={<Plus className="w-3.5 h-3.5" />} onClick={addItem}>
                        Tambah Item
                    </Button>
                </div>

                {errors.items && (
                    <p className="text-xs text-red-600 dark:text-red-400">{errors.items}</p>
                )}

                <div className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                    {/* Header row */}
                    <div className="grid grid-cols-12 gap-2 px-4 py-2.5 bg-secondary-50 dark:bg-dark-800 border-b border-secondary-200 dark:border-dark-600">
                        <div className="col-span-4 text-xs font-semibold text-dark-600 dark:text-dark-400">Layanan</div>
                        <div className="col-span-1 text-xs font-semibold text-dark-600 dark:text-dark-400">Qty</div>
                        <div className="col-span-1 text-xs font-semibold text-dark-600 dark:text-dark-400">Sat.</div>
                        <div className="col-span-2 text-xs font-semibold text-dark-600 dark:text-dark-400">Harga Sat.</div>
                        <div className="col-span-2 text-xs font-semibold text-dark-600 dark:text-dark-400">HPP</div>
                        <div className="col-span-1 text-xs font-semibold text-dark-600 dark:text-dark-400 text-center">PPh</div>
                        <div className="col-span-1 text-xs font-semibold text-dark-600 dark:text-dark-400 text-right">Total</div>
                    </div>

                    {data.items.map((item, idx) => (
                        <div
                            key={idx}
                            className={cn(
                                'border-b border-secondary-200 dark:border-dark-600 last:border-0 px-4 py-3',
                                item.is_tax_deposit && 'bg-yellow-50/50 dark:bg-yellow-900/5',
                            )}
                        >
                            <div className="grid grid-cols-12 gap-2 items-start">
                                {/* Service name + quick-fill */}
                                <div className="col-span-4 space-y-1">
                                    <Input
                                        value={item.service_name}
                                        onChange={(e) => updateItem(idx, 'service_name', e.target.value)}
                                        placeholder="Nama layanan..."
                                        className="h-8 text-sm"
                                    />
                                    <select
                                        onChange={(e) => e.target.value && fillFromService(idx, Number(e.target.value))}
                                        defaultValue=""
                                        className="w-full h-7 rounded-md border border-secondary-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-xs text-dark-600 dark:text-dark-400 px-2 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                        <option value="">↳ isi dari katalog...</option>
                                        {services.map((s) => (
                                            <option key={s.id} value={s.id}>{s.name}</option>
                                        ))}
                                    </select>
                                    {errors[`items.${idx}.service_name` as keyof typeof errors] && (
                                        <p className="text-xs text-red-600">
                                            {errors[`items.${idx}.service_name` as keyof typeof errors]}
                                        </p>
                                    )}
                                </div>

                                {/* Qty */}
                                <div className="col-span-1">
                                    <Input
                                        type="number"
                                        value={item.quantity}
                                        onChange={(e) => updateItem(idx, 'quantity', e.target.value)}
                                        min="0.001"
                                        step="any"
                                        className="h-8 text-sm text-right"
                                    />
                                </div>

                                {/* Unit */}
                                <div className="col-span-1">
                                    <Input
                                        value={item.unit}
                                        onChange={(e) => updateItem(idx, 'unit', e.target.value)}
                                        placeholder="pcs"
                                        className="h-8 text-sm"
                                    />
                                </div>

                                {/* Unit price */}
                                <div className="col-span-2">
                                    <CurrencyInput
                                        value={item.unit_price}
                                        onChange={(v) => updateItem(idx, 'unit_price', v)}
                                    />
                                </div>

                                {/* COGS */}
                                <div className="col-span-2">
                                    <CurrencyInput
                                        value={item.cogs_amount}
                                        onChange={(v) => updateItem(idx, 'cogs_amount', v)}
                                    />
                                </div>

                                {/* Tax deposit toggle */}
                                <div className="col-span-1 flex justify-center pt-1.5">
                                    <button
                                        type="button"
                                        onClick={() => updateItem(idx, 'is_tax_deposit', !item.is_tax_deposit)}
                                        className={cn(
                                            'h-5 w-5 rounded flex items-center justify-center border transition-colors',
                                            item.is_tax_deposit
                                                ? 'bg-yellow-500 border-yellow-500 text-white'
                                                : 'border-secondary-300 dark:border-dark-600 text-transparent hover:border-yellow-400',
                                        )}
                                    >
                                        <CheckCircle2 className="w-3 h-3" />
                                    </button>
                                </div>

                                {/* Line total + remove */}
                                <div className="col-span-1 flex items-start justify-end gap-1 pt-1">
                                    <span className="text-sm font-semibold text-dark-900 dark:text-dark-50 whitespace-nowrap">
                                        {formatCurrency(itemTotals[idx])}
                                    </span>
                                    {data.items.length > 1 && (
                                        <button
                                            type="button"
                                            onClick={() => removeItem(idx)}
                                            className="h-5 w-5 rounded flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors shrink-0"
                                        >
                                            <Trash2 className="w-3 h-3" />
                                        </button>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Discount + Summary */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Discount */}
                <div className="space-y-4">
                    <div className="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Diskon (Opsional)</h3>
                        <p className="text-xs text-dark-500 dark:text-dark-400 mt-0.5">Diskon fixed atau persentase dari subtotal</p>
                    </div>
                    <div className="space-y-3">
                        <div className="flex gap-2">
                            {(['fixed', 'percentage'] as const).map((type) => (
                                <button
                                    key={type}
                                    type="button"
                                    onClick={() => setData('discount_type', type)}
                                    className={cn(
                                        'flex-1 py-2 rounded-lg text-sm font-medium border transition-colors',
                                        data.discount_type === type
                                            ? 'bg-primary-600 text-white border-primary-600'
                                            : 'bg-white dark:bg-dark-800 text-dark-600 dark:text-dark-400 border-secondary-300 dark:border-dark-600 hover:bg-secondary-50 dark:hover:bg-dark-700',
                                    )}
                                >
                                    {type === 'fixed' ? 'Nominal (Rp)' : 'Persentase (%)'}
                                </button>
                            ))}
                        </div>
                        <div>
                            {data.discount_type === 'fixed' ? (
                                <CurrencyInput
                                    label="Jumlah Diskon"
                                    value={data.discount_value}
                                    onChange={(v) => setData('discount_value', v)}
                                />
                            ) : (
                                <div>
                                    <label className="block text-sm font-medium text-dark-900 dark:text-dark-300 mb-1.5">
                                        Persentase Diskon
                                    </label>
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
                                </div>
                            )}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-dark-900 dark:text-dark-300 mb-1.5">
                                Alasan Diskon
                            </label>
                            <Input
                                value={data.discount_reason}
                                onChange={(e) => setData('discount_reason', e.target.value)}
                                placeholder="Mis: Pelanggan setia, kontrak tahunan..."
                            />
                        </div>
                    </div>
                </div>

                {/* Summary */}
                <div className="space-y-4">
                    <div className="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Ringkasan</h3>
                        <p className="text-xs text-dark-500 dark:text-dark-400 mt-0.5">Kalkulasi otomatis dari item</p>
                    </div>
                    <div className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                        {[
                            { label: 'Subtotal', value: subtotal, bold: false },
                            ...(discountAmount > 0
                                ? [{ label: `Diskon${data.discount_type === 'percentage' ? ` (${data.discount_value}%)` : ''}`, value: -discountAmount, bold: false }]
                                : []),
                        ].map(({ label, value, bold }) => (
                            <div
                                key={label}
                                className="flex justify-between items-center px-4 py-2.5 border-b border-secondary-200 dark:border-dark-600 last:border-0"
                            >
                                <span className={cn('text-sm text-dark-600 dark:text-dark-400', bold && 'font-semibold text-dark-900 dark:text-dark-50')}>
                                    {label}
                                </span>
                                <span className={cn('text-sm font-medium text-dark-900 dark:text-dark-50', value < 0 && 'text-red-600 dark:text-red-400')}>
                                    {value < 0 ? `-${formatCurrency(Math.abs(value))}` : formatCurrency(value)}
                                </span>
                            </div>
                        ))}
                        <div className="flex justify-between items-center px-4 py-3 bg-primary-50 dark:bg-primary-900/20 border-b border-secondary-200 dark:border-dark-600">
                            <span className="text-sm font-bold text-dark-900 dark:text-dark-50">Total</span>
                            <span className="text-lg font-bold text-primary-700 dark:text-primary-300">{formatCurrency(totalAmount)}</span>
                        </div>
                        <div className="px-4 py-3 space-y-2 bg-secondary-50 dark:bg-dark-800">
                            {[
                                { label: 'HPP / COGS', value: totalCogs, cls: 'text-dark-600 dark:text-dark-400' },
                                { label: 'Titipan Pajak', value: totalTaxDepositsCalc, cls: 'text-yellow-600 dark:text-yellow-400' },
                                { label: 'Laba Kotor', value: grossProfit, cls: grossProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-red-600 dark:text-red-400 font-semibold' },
                                { label: 'Est. PPh 0.5%', value: pph05, cls: 'text-orange-600 dark:text-orange-400' },
                            ].map(({ label, value, cls }) => (
                                <div key={label} className="flex justify-between text-sm">
                                    <span className="text-dark-500 dark:text-dark-400">{label}</span>
                                    <span className={cn('font-medium', cls)}>{formatCurrency(value)}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            {/* Actions */}
            <div className="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-secondary-200 dark:border-dark-600">
                <Button
                    type="button"
                    variant="zinc"
                    onClick={() => router.get('/invoices')}
                    className="w-full sm:w-auto order-2 sm:order-1"
                >
                    Batal
                </Button>
                <Button
                    type="submit"
                    variant="primary"
                    loading={processing}
                    className="w-full sm:w-auto order-1 sm:order-2"
                >
                    {submitLabel}
                </Button>
            </div>
        </form>
    );
}

/* ─────────────────────────────────── page ─── */

function CreateInvoicePage({ clients, services, nextSeq, companyInitials }: Props) {
    return (
        <>
            <Head title="Buat Invoice" />
            <div className="space-y-6">
                {/* Header */}
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
