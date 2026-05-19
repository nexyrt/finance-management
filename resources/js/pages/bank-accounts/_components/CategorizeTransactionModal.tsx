import { useState, useEffect } from 'react';
import { Tag } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { toast } from 'sonner';
import { toastErrors } from '@/lib/utils';

interface Props {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    transactionIds: number[];
    isBulk: boolean;
    onCategorized: () => void;
}

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export default function CategorizeTransactionModal({ open, onOpenChange, transactionIds, isBulk, onCategorized }: Props) {
    const [categoryId, setCategoryId] = useState<number | null>(null);
    const [categories, setCategories] = useState<{ label: string; value: number }[]>([]);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (!open) return;
        fetch('/api/transaction-categories', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setCategories(
                data.filter((d: { disabled?: boolean }) => !d.disabled)
                    .map((d: { label: string; value: number }) => ({ label: d.label, value: d.value }))
            ))
            .catch(() => {});
    }, [open]);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!categoryId || !transactionIds.length) return;
        setSaving(true);

        try {
            const res = await fetch('/bank-transactions/categorize', {
                method: 'PATCH',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ transaction_ids: transactionIds, category_id: categoryId }),
            });
            const data = await res.json();
            if (!res.ok) { toastErrors(data); return; }
            toast.success(data.message);
            setCategoryId(null);
            onCategorized();
            onOpenChange(false);
        } catch { toast.error('Gagal terhubung ke server.'); }
        finally { setSaving(false); }
    }

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="sm">
                <DialogHeader>
                    <div className="flex items-center gap-4 my-3">
                        <div className="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                            <Tag className="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">
                                {isBulk ? 'Kategorikan Massal' : 'Kategorikan Transaksi'}
                            </DialogTitle>
                            <p className="text-sm text-dark-500 dark:text-dark-400">
                                {isBulk
                                    ? `${transactionIds.length} transaksi dipilih`
                                    : 'Tetapkan kategori untuk transaksi ini'}
                            </p>
                        </div>
                    </div>
                </DialogHeader>

                <form id="categorize-form" onSubmit={handleSubmit} className="space-y-4 px-6 py-4">
                    <Combobox
                        options={categories}
                        value={categoryId ?? undefined}
                        onChange={v => setCategoryId(v ? Number(v) : null)}
                        placeholder="Pilih kategori"
                        searchPlaceholder="Cari kategori..."
                        label="Kategori *"
                    />
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
                            form="categorize-form"
                            disabled={saving || !categoryId}
                            className="w-full sm:w-auto order-1 sm:order-2"
                        >
                            {saving ? 'Menyimpan...' : 'Terapkan Kategori'}
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
