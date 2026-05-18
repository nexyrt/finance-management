import { useEffect, useState } from 'react';
import { Landmark } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/shared/currency-input';
import { FormSection } from '@/components/shared/form-section';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { toastError, toastErrors } from '@/lib/utils';
import { toast } from 'sonner';
import type { BankAccount } from '../index';

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

interface FormState {
    account_name: string;
    account_number: string;
    bank_name: string;
    branch: string;
    initial_balance: number;
}

const DEFAULT_FORM: FormState = {
    account_name: '',
    account_number: '',
    bank_name: '',
    branch: '',
    initial_balance: 0,
};

interface AccountFormModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    editAccount: BankAccount | null;
    onCreated: (account: BankAccount) => void;
    onUpdated: (account: BankAccount) => void;
}

export default function AccountFormModal({
    open,
    onOpenChange,
    editAccount,
    onCreated,
    onUpdated,
}: AccountFormModalProps) {
    const [form, setForm] = useState<FormState>(DEFAULT_FORM);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (open) {
            setForm(
                editAccount
                    ? {
                          account_name: editAccount.account_name,
                          account_number: editAccount.account_number,
                          bank_name: editAccount.bank_name,
                          branch: editAccount.branch ?? '',
                          initial_balance: editAccount.initial_balance,
                      }
                    : DEFAULT_FORM,
            );
        }
    }, [open, editAccount]);

    function set<K extends keyof FormState>(key: K, value: FormState[K]) {
        setForm((prev) => ({ ...prev, [key]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setSaving(true);

        const isEdit = editAccount !== null;
        const url = isEdit ? `/bank-accounts/${editAccount!.id}` : '/bank-accounts';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(form),
            });

            const data = await res.json();

            if (!res.ok) {
                if (data.errors) toastErrors(data.errors);
                else toastError(data.message ?? 'Terjadi kesalahan.');
                return;
            }

            toast.success(data.message ?? (isEdit ? 'Rekening diperbarui.' : 'Rekening ditambahkan.'));
            onOpenChange(false);
            if (isEdit) onUpdated(data.account);
            else onCreated(data.account);
        } catch {
            toastError('Gagal terhubung ke server.');
        } finally {
            setSaving(false);
        }
    }

    const isEdit = editAccount !== null;

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="2xl">
                <form id="account-form" onSubmit={handleSubmit}>
                    <DialogHeader>
                        <div className="flex items-center gap-4 py-2">
                            <div className="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center shrink-0">
                                <Landmark className="w-6 h-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">
                                    {isEdit ? 'Edit Rekening' : 'Tambah Rekening Baru'}
                                </DialogTitle>
                                <p className="text-sm text-dark-500 dark:text-dark-400">
                                    Informasi rekening bank dan saldo awal
                                </p>
                            </div>
                        </div>
                    </DialogHeader>

                    <div className="px-6 py-4">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <FormSection title="Identitas Rekening" description="Nama rekening dan informasi bank">
                                <Input
                                    label="Nama Rekening *"
                                    value={form.account_name}
                                    onChange={(e) => set('account_name', e.target.value)}
                                    placeholder="cth. Operasional BCA"
                                    error={undefined}
                                    required
                                />
                                <Input
                                    label="Nama Bank *"
                                    value={form.bank_name}
                                    onChange={(e) => set('bank_name', e.target.value)}
                                    placeholder="cth. BCA, BNI, Mandiri"
                                    error={undefined}
                                    required
                                />
                            </FormSection>

                            <FormSection title="Detail Rekening" description="Nomor rekening, cabang, dan saldo">
                                <Input
                                    label="Nomor Rekening *"
                                    value={form.account_number}
                                    onChange={(e) => set('account_number', e.target.value)}
                                    placeholder="cth. 1234567890"
                                    error={undefined}
                                    required
                                />
                                <Input
                                    label="Cabang"
                                    value={form.branch}
                                    onChange={(e) => set('branch', e.target.value)}
                                    placeholder="cth. Jakarta Pusat"
                                    error={undefined}
                                />
                                <CurrencyInput
                                    label="Saldo Awal *"
                                    value={form.initial_balance}
                                    onChange={(val) => set('initial_balance', val)}
                                    hint="Saldo rekening saat pertama kali dibuat di sistem."
                                />
                            </FormSection>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="zinc"
                            onClick={() => onOpenChange(false)}
                            disabled={saving}
                            className="w-full sm:w-auto order-2 sm:order-1"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            disabled={saving}
                            className="w-full sm:w-auto order-1 sm:order-2"
                        >
                            {saving ? 'Menyimpan...' : isEdit ? 'Simpan Perubahan' : 'Simpan Rekening'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
