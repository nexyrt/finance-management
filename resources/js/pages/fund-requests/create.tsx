import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';
import * as React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { CurrencyInput } from '@/components/shared/currency-input';
import { FormSection } from '@/components/shared/form-section';
import { PageHeader } from '@/components/shared/page-header';
import { AppLayout } from '@/layouts/app-layout';
import { formatCurrency } from '@/lib/utils';
import * as fundRequestRoutes from '@/routes/fund-requests';
import type { FilterOption, FundRequestItem } from './types';

interface Props {
    categories: FilterOption[];
    nextNumber: string;
}

const PRIORITY_OPTIONS = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
    { label: 'Urgent', value: 'urgent' },
];

const EMPTY_ITEM = (): FundRequestItem => ({
    description: '',
    category_id: null,
    quantity: 1,
    unit_price: 0,
    amount: 0,
    notes: '',
});

export default function FundRequestsCreate({ categories, nextNumber }: Props) {
    const { data, setData, post, processing, errors } = useForm<{
        request_number: string;
        title: string;
        purpose: string;
        priority: string;
        needed_by_date: string;
        attachment: File | null;
        items: FundRequestItem[];
        action: 'draft' | 'submit';
    }>({
        request_number: nextNumber,
        title: '',
        purpose: '',
        priority: 'medium',
        needed_by_date: '',
        attachment: null,
        items: [EMPTY_ITEM()],
        action: 'draft',
    });

    const totalAmount = data.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);

    const addItem = () => setData('items', [...data.items, EMPTY_ITEM()]);

    const removeItem = (index: number) => {
        setData('items', data.items.filter((_, i) => i !== index));
    };

    const updateItem = (index: number, field: keyof FundRequestItem, value: string | number | null) => {
        const updated = data.items.map((item, i) => {
            if (i !== index) return item;
            const newItem = { ...item, [field]: value };
            if (field === 'quantity' || field === 'unit_price') {
                newItem.amount = (field === 'quantity' ? Number(value) : item.quantity) *
                    (field === 'unit_price' ? Number(value) : item.unit_price);
            }
            return newItem;
        });
        setData('items', updated);
    };

    const submit = (action: 'draft' | 'submit') => {
        setData('action', action);
        post(fundRequestRoutes.store.url(), { forceFormData: true });
    };

    return (
        <AppLayout>
            <Head title="Buat Permintaan Dana" />

            <div className="space-y-6 max-w-4xl mx-auto">
                <div className="flex items-center gap-3">
                    <Button variant="ghost" size="icon" onClick={() => router.visit(fundRequestRoutes.index.url())}>
                        <ArrowLeft className="w-4 h-4" />
                    </Button>
                    <PageHeader
                        title="Buat Permintaan Dana"
                        description="Ajukan pencairan dana operasional."
                    />
                </div>

                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-6 space-y-6">
                    <FormSection title="Informasi Permintaan" description="Header dari pengajuan dana." />

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input
                            label="Nomor Permintaan *"
                            value={data.request_number}
                            onChange={(e) => setData('request_number', e.target.value)}
                            error={errors.request_number}
                            hint="Auto-generate, dapat diubah"
                        />
                        <Combobox
                            label="Prioritas *"
                            options={PRIORITY_OPTIONS}
                            value={data.priority || null}
                            onChange={(v) => setData('priority', v ? String(v) : 'medium')}
                            error={errors.priority}
                        />
                        <Input
                            label="Judul *"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            error={errors.title}
                            placeholder="cth: Pembelian ATK Kantor..."
                        />
                        <DatePicker
                            label="Dibutuhkan Sebelum *"
                            value={data.needed_by_date ? new Date(data.needed_by_date) : null}
                            onChange={(d) => setData('needed_by_date', d ? d.toISOString().slice(0, 10) : '')}
                            error={errors.needed_by_date}
                        />
                        <div className="sm:col-span-2">
                            <Textarea
                                label="Tujuan / Keterangan *"
                                value={data.purpose}
                                onChange={(e) => setData('purpose', e.target.value)}
                                error={errors.purpose}
                                placeholder="Jelaskan tujuan penggunaan dana..."
                                rows={3}
                            />
                        </div>
                        <div className="sm:col-span-2">
                            <label className="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1.5">
                                Lampiran <span className="text-dark-400 dark:text-dark-500 font-normal">(opsional, maks 5MB)</span>
                            </label>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,application/pdf"
                                onChange={(e) => setData('attachment', e.target.files?.[0] ?? null)}
                                className="block w-full text-sm text-dark-700 dark:text-dark-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-900/20 dark:file:text-primary-300 hover:file:bg-primary-100 cursor-pointer"
                            />
                        </div>
                    </div>
                </div>

                {/* Items */}
                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden">
                    <div className="px-6 py-4 border-b border-secondary-200 dark:border-dark-600 flex items-center justify-between">
                        <div>
                            <h3 className="text-sm font-semibold text-dark-900 dark:text-dark-50">Item Biaya</h3>
                            <p className="text-xs text-dark-500 dark:text-dark-400 mt-0.5">Rincian pengeluaran yang diajukan</p>
                        </div>
                        <div className="flex items-center gap-3">
                            <Badge variant="blue" className="tabular-nums">
                                Total: {formatCurrency(totalAmount)}
                            </Badge>
                            <Button variant="outline" size="sm" onClick={addItem}>
                                <Plus className="w-3.5 h-3.5" />
                                Tambah Item
                            </Button>
                        </div>
                    </div>

                    {errors.items && (
                        <p className="px-6 py-2 text-xs text-red-500 bg-red-50 dark:bg-red-900/10">{errors.items}</p>
                    )}

                    <div className="divide-y divide-secondary-100 dark:divide-dark-600">
                        {data.items.map((item, index) => (
                            <ItemRow
                                key={index}
                                index={index}
                                item={item}
                                categories={categories}
                                errors={errors}
                                onChange={updateItem}
                                onRemove={data.items.length > 1 ? () => removeItem(index) : undefined}
                            />
                        ))}
                    </div>

                    <div className="px-6 py-3 bg-secondary-50/50 dark:bg-dark-800/50 border-t border-secondary-200 dark:border-dark-600 text-right">
                        <span className="text-sm text-dark-500 dark:text-dark-400">Total Pengajuan: </span>
                        <span className="text-base font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                            {formatCurrency(totalAmount)}
                        </span>
                    </div>
                </div>

                <div className="flex items-center justify-between">
                    <Button variant="zinc" onClick={() => router.visit(fundRequestRoutes.index.url())}>
                        Batal
                    </Button>
                    <div className="flex gap-2">
                        <Button
                            variant="outline"
                            onClick={() => submit('draft')}
                            disabled={processing}
                        >
                            Simpan Draft
                        </Button>
                        <Button
                            variant="primary"
                            onClick={() => submit('submit')}
                            disabled={processing}
                        >
                            Ajukan untuk Persetujuan
                        </Button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

/* ─── Item row ──────────────────────────────────────────── */

interface ItemRowProps {
    index: number;
    item: FundRequestItem;
    categories: FilterOption[];
    errors: Record<string, string>;
    onChange: (index: number, field: keyof FundRequestItem, value: string | number | null) => void;
    onRemove?: () => void;
}

function ItemRow({ index, item, categories, errors, onChange, onRemove }: ItemRowProps) {
    const itemTotal = item.quantity * item.unit_price;

    return (
        <div className="px-6 py-4 space-y-3">
            <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-dark-500 dark:text-dark-400">Item #{index + 1}</span>
                <div className="flex items-center gap-2">
                    <span className="text-sm font-semibold text-dark-900 dark:text-dark-50 tabular-nums">
                        {formatCurrency(itemTotal)}
                    </span>
                    {onRemove && (
                        <button
                            type="button"
                            onClick={onRemove}
                            className="text-dark-400 hover:text-red-500 transition-colors"
                        >
                            <Trash2 className="w-4 h-4" />
                        </button>
                    )}
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div className="sm:col-span-2">
                    <Input
                        label="Nama Item *"
                        value={item.description}
                        onChange={(e) => onChange(index, 'description', e.target.value)}
                        error={errors[`items.${index}.description`]}
                        placeholder="cth: Kertas A4 rim..."
                    />
                </div>
                <div className="sm:col-span-2">
                    <Combobox
                        label="Kategori *"
                        options={categories}
                        value={item.category_id}
                        onChange={(v) => onChange(index, 'category_id', v ? Number(v) : null)}
                        error={errors[`items.${index}.category_id`]}
                        placeholder="Pilih kategori"
                    />
                </div>
                <div>
                    <Input
                        label="Qty *"
                        type="number"
                        min={1}
                        value={item.quantity}
                        onChange={(e) => onChange(index, 'quantity', parseInt(e.target.value) || 1)}
                        error={errors[`items.${index}.quantity`]}
                    />
                </div>
                <div>
                    <CurrencyInput
                        label="Harga Satuan *"
                        value={item.unit_price}
                        onChange={(v) => onChange(index, 'unit_price', v)}
                        error={errors[`items.${index}.unit_price`]}
                    />
                </div>
                <div className="sm:col-span-2">
                    <Input
                        label="Catatan"
                        value={item.notes}
                        onChange={(e) => onChange(index, 'notes', e.target.value)}
                        placeholder="Catatan tambahan (opsional)..."
                    />
                </div>
            </div>
        </div>
    );
}
