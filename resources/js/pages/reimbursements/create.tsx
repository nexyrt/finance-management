import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { CurrencyInput } from '@/components/shared/currency-input';
import { FormSection } from '@/components/shared/form-section';
import { PageHeader } from '@/components/shared/page-header';
import { AppLayout } from '@/layouts/app-layout';
import * as reimbursementRoutes from '@/routes/reimbursements';

const CATEGORY_OPTIONS = [
    { label: 'Transport', value: 'transport' },
    { label: 'Meals & Entertainment', value: 'meals' },
    { label: 'Office Supplies', value: 'office_supplies' },
    { label: 'Communication', value: 'communication' },
    { label: 'Accommodation', value: 'accommodation' },
    { label: 'Medical', value: 'medical' },
    { label: 'Other', value: 'other' },
];

export default function ReimbursementsCreate() {
    const { data, setData, post, processing, errors, reset } = useForm<{
        title: string;
        description: string;
        amount: number;
        expense_date: string;
        category: string;
        attachment: File | null;
        action: 'draft' | 'submit';
    }>({
        title: '',
        description: '',
        amount: 0,
        expense_date: new Date().toISOString().slice(0, 10),
        category: '',
        attachment: null,
        action: 'draft',
    });

    const submit = (action: 'draft' | 'submit') => {
        setData('action', action);
        post(reimbursementRoutes.store.url(), { forceFormData: true });
    };

    return (
        <AppLayout>
            <Head title="Buat Reimbursement" />

            <div className="space-y-6 max-w-2xl mx-auto">
                <div className="flex items-center gap-3">
                    <Button variant="ghost" size="icon" onClick={() => router.visit(reimbursementRoutes.index.url())}>
                        <ArrowLeft className="w-4 h-4" />
                    </Button>
                    <PageHeader
                        title="Buat Reimbursement"
                        description="Ajukan penggantian biaya operasional."
                    />
                </div>

                <div className="bg-white dark:bg-dark-700 rounded-xl border border-secondary-200 dark:border-dark-600 p-6 space-y-6">
                    <FormSection title="Informasi Pengajuan" description="Detail biaya yang akan diganti." />

                    <div className="space-y-4">
                        <Input
                            label="Judul *"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            error={errors.title}
                            placeholder="cth: Transportasi meeting klien..."
                        />
                        <Textarea
                            label="Deskripsi"
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            error={errors.description}
                            placeholder="Detail biaya yang dikeluarkan..."
                            rows={3}
                        />
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <CurrencyInput
                                label="Jumlah *"
                                value={data.amount}
                                onChange={(v) => setData('amount', v)}
                                error={errors.amount}
                            />
                            <DatePicker
                                label="Tanggal Pengeluaran *"
                                value={data.expense_date ? new Date(data.expense_date) : null}
                                onChange={(d) => setData('expense_date', d ? d.toISOString().slice(0, 10) : '')}
                                error={errors.expense_date}
                            />
                        </div>
                        <Combobox
                            label="Kategori *"
                            options={CATEGORY_OPTIONS}
                            value={data.category || null}
                            onChange={(v) => setData('category', v ? String(v) : '')}
                            error={errors.category}
                            placeholder="Pilih kategori biaya"
                        />
                        <div>
                            <label className="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1.5">
                                Lampiran <span className="text-dark-400 dark:text-dark-500 font-normal">(opsional, maks 5MB)</span>
                            </label>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,application/pdf"
                                onChange={(e) => setData('attachment', e.target.files?.[0] ?? null)}
                                className="block w-full text-sm text-dark-700 dark:text-dark-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-900/20 dark:file:text-primary-300 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/30 cursor-pointer"
                            />
                            {errors.attachment && <p className="text-xs text-red-500 mt-1">{errors.attachment}</p>}
                        </div>
                    </div>

                    <div className="flex items-center justify-between pt-2 border-t border-secondary-200 dark:border-dark-600">
                        <Button variant="zinc" onClick={() => router.visit(reimbursementRoutes.index.url())}>
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
            </div>
        </AppLayout>
    );
}
