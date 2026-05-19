import { useState, useEffect } from 'react';
import { ArrowUpRight, Plus } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { CurrencyInput } from '@/components/shared/currency-input';
import { FormSection } from '@/components/shared/form-section';
import { toast } from 'sonner';
import { toastErrors } from '@/lib/utils';

interface Props {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    defaultAccountId?: number;
    accounts: { label: string; value: number }[];
    onCreated: (accountId: number) => void;
}

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export default function CreateIncomeModal({ open, onOpenChange, defaultAccountId, accounts, onCreated }: Props) {
    const [accountId, setAccountId] = useState<number | null>(defaultAccountId ?? null);
    const [categoryId, setCategoryId] = useState<number | null>(null);
    const [amount, setAmount] = useState(0);
    const [date, setDate] = useState<Date | null>(new Date());
    const [description, setDescription] = useState('');
    const [referenceNumber, setReferenceNumber] = useState('');
    const [attachment, setAttachment] = useState<File | null>(null);
    const [categories, setCategories] = useState<{ label: string; value: number }[]>([]);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (defaultAccountId) setAccountId(defaultAccountId);
    }, [defaultAccountId]);

    useEffect(() => {
        if (!open) return;
        fetch('/api/transaction-categories?type=credit', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setCategories(
                data.filter((d: { disabled?: boolean }) => !d.disabled)
                    .map((d: { label: string; value: number }) => ({ label: d.label, value: d.value }))
            ))
            .catch(() => {});
    }, [open]);

    function reset() {
        setAmount(0);
        setDate(new Date());
        setDescription('');
        setReferenceNumber('');
        setAttachment(null);
        setCategoryId(null);
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!accountId || !amount || !date) return;
        setSaving(true);

        const form = new FormData();
        form.append('bank_account_id', String(accountId));
        form.append('transaction_type', 'credit');
        form.append('amount', String(amount));
        form.append('transaction_date', date.toISOString().split('T')[0]);
        if (description) form.append('description', description);
        if (referenceNumber) form.append('reference_number', referenceNumber);
        if (categoryId) form.append('category_id', String(categoryId));
        if (attachment) form.append('attachment', attachment);

        try {
            const res = await fetch('/bank-transactions', {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: form,
            });
            const data = await res.json();
            if (!res.ok) { toastErrors(data); return; }
            toast.success(data.message ?? 'Pemasukan berhasil disimpan.');
            reset();
            onCreated(accountId);
            onOpenChange(false);
        } catch { toast.error('Gagal terhubung ke server.'); }
        finally { setSaving(false); }
    }

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="2xl">
                <DialogHeader>
                    <div className="flex items-center gap-4 my-3">
                        <div className="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center">
                            <ArrowUpRight className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Pemasukan</DialogTitle>
                            <p className="text-sm text-dark-500 dark:text-dark-400">Catat transaksi kredit pada rekening</p>
                        </div>
                    </div>
                </DialogHeader>

                <form id="create-income-form" onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {/* Left */}
                        <div className="space-y-4">
                            <FormSection title="Detail Transaksi" description="Informasi utama pemasukan" />
                            <Combobox
                                options={accounts}
                                value={accountId ?? undefined}
                                onChange={v => setAccountId(v ? Number(v) : null)}
                                placeholder="Pilih rekening"
                                label="Rekening *"
                            />
                            <div className="space-y-1.5">
                                <label className="text-sm font-semibold text-dark-900 dark:text-dark-300">Kategori</label>
                                <div className="flex items-center gap-2">
                                    <Combobox
                                        options={[{ label: 'Tanpa kategori', value: 0 }, ...categories]}
                                        value={categoryId ?? 0}
                                        onChange={v => setCategoryId(v ? Number(v) : null)}
                                        placeholder="Pilih kategori"
                                        searchPlaceholder="Cari kategori..."
                                        className="flex-1"
                                    />
                                </div>
                            </div>
                            <CurrencyInput
                                label="Jumlah *"
                                value={amount}
                                onChange={setAmount}
                            />
                            <DatePicker
                                label="Tanggal Transaksi *"
                                value={date}
                                onChange={setDate}
                            />
                        </div>

                        {/* Right */}
                        <div className="space-y-4">
                            <FormSection title="Detail Tambahan" description="Deskripsi dan dokumen" />
                            <Input
                                label="Deskripsi"
                                value={description}
                                onChange={e => setDescription(e.target.value)}
                                placeholder="Contoh: Pembayaran jasa konsultasi"
                            />
                            <Input
                                label="Nomor Referensi"
                                value={referenceNumber}
                                onChange={e => setReferenceNumber(e.target.value)}
                                placeholder="Opsional"
                            />
                            <div className="space-y-1.5">
                                <label className="text-sm font-semibold text-dark-900 dark:text-dark-300">Lampiran</label>
                                <div className={[
                                    'border-2 border-dashed rounded-xl p-4 text-center cursor-pointer',
                                    'border-secondary-200 dark:border-dark-600',
                                    'hover:border-primary-300 dark:hover:border-primary-600',
                                    'transition-colors duration-200',
                                ].join(' ')}>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp,application/pdf"
                                        onChange={e => setAttachment(e.target.files?.[0] ?? null)}
                                        className="hidden"
                                        id="income-attachment"
                                    />
                                    <label htmlFor="income-attachment" className="cursor-pointer">
                                        {attachment ? (
                                            <div className="flex items-center justify-center gap-2 text-sm text-dark-700 dark:text-dark-300">
                                                <span className="truncate max-w-48">{attachment.name}</span>
                                                <button
                                                    type="button"
                                                    onClick={e => { e.preventDefault(); setAttachment(null); }}
                                                    className="text-rose-500 hover:text-rose-700 text-xs"
                                                >✕</button>
                                            </div>
                                        ) : (
                                            <div className="flex flex-col items-center gap-1 text-dark-400">
                                                <Plus className="w-5 h-5" />
                                                <p className="text-xs">Upload bukti (JPG, PNG, PDF, maks 5MB)</p>
                                            </div>
                                        )}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <DialogFooter>
                    <div className="flex flex-col sm:flex-row justify-end gap-3">
                        <Button
                            type="button"
                            variant="zinc"
                            onClick={() => onOpenChange(false)}
                            className="w-full sm:w-auto order-2 sm:order-1"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            form="create-income-form"
                            disabled={saving || !accountId || !amount || !date}
                            className="w-full sm:w-auto order-1 sm:order-2 bg-emerald-600 hover:bg-emerald-700 text-white"
                        >
                            {saving ? 'Menyimpan...' : 'Simpan Pemasukan'}
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
