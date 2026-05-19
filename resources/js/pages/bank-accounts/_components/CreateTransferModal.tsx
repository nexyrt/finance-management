import { useState, useEffect } from 'react';
import { ArrowRight, Plus } from 'lucide-react';
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

export default function CreateTransferModal({ open, onOpenChange, defaultAccountId, accounts, onCreated }: Props) {
    const [fromAccountId, setFromAccountId] = useState<number | null>(defaultAccountId ?? null);
    const [toAccountId, setToAccountId] = useState<number | null>(null);
    const [categoryId, setCategoryId] = useState<number | null>(null);
    const [amount, setAmount] = useState(0);
    const [adminFee, setAdminFee] = useState(0);
    const [date, setDate] = useState<Date | null>(new Date());
    const [description, setDescription] = useState('');
    const [attachment, setAttachment] = useState<File | null>(null);
    const [categories, setCategories] = useState<{ label: string; value: number }[]>([]);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (defaultAccountId) setFromAccountId(defaultAccountId);
    }, [defaultAccountId]);

    useEffect(() => {
        if (!open) return;
        fetch('/api/transaction-categories?type=transfer', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setCategories(
                data.filter((d: { disabled?: boolean }) => !d.disabled)
                    .map((d: { label: string; value: number }) => ({ label: d.label, value: d.value }))
            ))
            .catch(() => {});
    }, [open]);

    function reset() {
        setToAccountId(null);
        setAmount(0);
        setAdminFee(0);
        setDate(new Date());
        setDescription('');
        setAttachment(null);
        setCategoryId(null);
    }

    const toAccountOptions = accounts.filter(a => a.value !== fromAccountId);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!fromAccountId || !toAccountId || !amount || !date) return;
        setSaving(true);

        const form = new FormData();
        form.append('from_account_id', String(fromAccountId));
        form.append('to_account_id', String(toAccountId));
        form.append('amount', String(amount));
        if (adminFee > 0) form.append('admin_fee', String(adminFee));
        form.append('transfer_date', date.toISOString().split('T')[0]);
        if (description) form.append('description', description);
        if (categoryId) form.append('category_id', String(categoryId));
        if (attachment) form.append('attachment', attachment);

        try {
            const res = await fetch('/bank-transactions/transfer', {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: form,
            });
            const data = await res.json();
            if (!res.ok) { toastErrors(data); return; }
            toast.success(data.message ?? 'Transfer berhasil dilakukan.');
            reset();
            onCreated(fromAccountId);
            onOpenChange(false);
        } catch { toast.error('Gagal terhubung ke server.'); }
        finally { setSaving(false); }
    }

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="2xl">
                <DialogHeader>
                    <div className="flex items-center gap-4 my-3">
                        <div className="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                            <ArrowRight className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">Transfer Antar Rekening</DialogTitle>
                            <p className="text-sm text-dark-500 dark:text-dark-400">Pindahkan dana antara rekening bank</p>
                        </div>
                    </div>
                </DialogHeader>

                <form id="create-transfer-form" onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div className="space-y-4">
                            <FormSection title="Rekening Transfer" description="Sumber dan tujuan dana" />

                            <Combobox
                                options={accounts}
                                value={fromAccountId ?? undefined}
                                onChange={v => { setFromAccountId(v ? Number(v) : null); setToAccountId(null); }}
                                placeholder="Pilih rekening sumber"
                                label="Dari Rekening *"
                            />

                            {/* Visual flow indicator */}
                            {fromAccountId && (
                                <div className="flex items-center justify-center gap-3 py-1">
                                    <div className="h-px flex-1 bg-linear-to-r from-transparent via-blue-300 dark:via-blue-700 to-blue-400 dark:to-blue-600" />
                                    <div className="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-700 flex items-center justify-center">
                                        <ArrowRight className="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div className="h-px flex-1 bg-linear-to-r from-blue-400 dark:from-blue-600 via-blue-300 dark:via-blue-700 to-transparent" />
                                </div>
                            )}

                            <Combobox
                                options={toAccountOptions}
                                value={toAccountId ?? undefined}
                                onChange={v => setToAccountId(v ? Number(v) : null)}
                                placeholder="Pilih rekening tujuan"
                                label="Ke Rekening *"
                            />

                            <DatePicker
                                label="Tanggal Transfer *"
                                value={date}
                                onChange={setDate}
                            />
                        </div>

                        <div className="space-y-4">
                            <FormSection title="Detail Transfer" description="Jumlah dan informasi tambahan" />
                            <CurrencyInput
                                label="Jumlah Transfer *"
                                value={amount}
                                onChange={setAmount}
                            />
                            <CurrencyInput
                                label="Biaya Admin"
                                value={adminFee}
                                onChange={setAdminFee}
                            />
                            <div className="space-y-1.5">
                                <label className="text-sm font-semibold text-dark-900 dark:text-dark-300">Kategori</label>
                                <Combobox
                                    options={[{ label: 'Tanpa kategori', value: 0 }, ...categories]}
                                    value={categoryId ?? 0}
                                    onChange={v => setCategoryId(v ? Number(v) : null)}
                                    placeholder="Pilih kategori transfer"
                                    searchPlaceholder="Cari kategori..."
                                />
                            </div>
                            <Input
                                label="Keterangan"
                                value={description}
                                onChange={e => setDescription(e.target.value)}
                                placeholder="Opsional"
                            />
                            <div className="space-y-1.5">
                                <label className="text-sm font-semibold text-dark-900 dark:text-dark-300">Lampiran</label>
                                <div className={[
                                    'border-2 border-dashed rounded-xl p-4 text-center',
                                    'border-secondary-200 dark:border-dark-600',
                                    'hover:border-blue-300 dark:hover:border-blue-600',
                                    'transition-colors duration-200',
                                ].join(' ')}>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp,application/pdf"
                                        onChange={e => setAttachment(e.target.files?.[0] ?? null)}
                                        className="hidden"
                                        id="transfer-attachment"
                                    />
                                    <label htmlFor="transfer-attachment" className="cursor-pointer">
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
                                                <p className="text-xs">Upload bukti transfer (opsional)</p>
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
                            form="create-transfer-form"
                            disabled={saving || !fromAccountId || !toAccountId || !amount || !date}
                            className="w-full sm:w-auto order-1 sm:order-2 bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            {saving ? 'Memproses...' : 'Eksekusi Transfer'}
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
