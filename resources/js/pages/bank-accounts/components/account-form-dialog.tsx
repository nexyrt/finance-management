import { useForm } from '@inertiajs/react';
import { Building2, Pencil } from 'lucide-react';
import * as React from 'react';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { CurrencyInput } from '@/components/shared/currency-input';
import * as bankAccountsRoutes from '@/routes/bank-accounts';
import type { AccountListItem } from '../types';

interface Props {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    /** When provided, dialog operates in edit mode. */
    account?: AccountListItem | null;
}

interface AccountForm {
    account_name: string;
    account_number: string;
    bank_name: string;
    branch: string;
    initial_balance: number;
}

export function AccountFormDialog({ open, onOpenChange, account }: Props) {
    const isEdit = !!account;

    const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm<AccountForm>({
        account_name: account?.account_name ?? '',
        account_number: account?.account_number ?? '',
        bank_name: account?.bank_name ?? '',
        branch: account?.branch ?? '',
        initial_balance: account?.initial_balance ?? 0,
    });

    /* Reset form when dialog opens with a different account context. */
    React.useEffect(() => {
        if (open) {
            setData({
                account_name: account?.account_name ?? '',
                account_number: account?.account_number ?? '',
                bank_name: account?.bank_name ?? '',
                branch: account?.branch ?? '',
                initial_balance: account?.initial_balance ?? 0,
            });
            clearErrors();
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [open, account?.id]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const opts = {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(isEdit ? 'Akun berhasil diperbarui' : 'Akun berhasil ditambahkan');
                onOpenChange(false);
                if (!isEdit) reset();
            },
            onError: () => {
                toast.error('Periksa kembali isian form');
            },
        };

        if (isEdit && account) {
            put(bankAccountsRoutes.update.url({ bankAccount: account.id }), opts);
        } else {
            post(bankAccountsRoutes.store.url(), opts);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="2xl">
                <DialogHeader>
                    <div className="flex items-center gap-4 py-2">
                        <div className={`h-12 w-12 rounded-xl flex items-center justify-center shrink-0 ${
                            isEdit
                                ? 'bg-blue-50 dark:bg-blue-900/20'
                                : 'bg-primary-50 dark:bg-primary-900/20'
                        }`}>
                            {isEdit ? (
                                <Pencil className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            ) : (
                                <Building2 className="w-6 h-6 text-primary-600 dark:text-primary-400" />
                            )}
                        </div>
                        <div>
                            <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">
                                {isEdit ? 'Edit Rekening Bank' : 'Tambah Rekening Bank'}
                            </DialogTitle>
                            <p className="text-sm text-dark-600 dark:text-dark-400 mt-0.5">
                                {isEdit
                                    ? 'Perbarui informasi rekening bank.'
                                    : 'Tambahkan rekening bank baru ke daftar akun Anda.'}
                            </p>
                        </div>
                    </div>
                </DialogHeader>

                <form id="account-form" onSubmit={handleSubmit} className="px-6 py-5 space-y-5">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="sm:col-span-2">
                            <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                                Nama Pemilik Rekening *
                            </label>
                            <Input
                                value={data.account_name}
                                onChange={(e) => setData('account_name', e.target.value)}
                                placeholder="Mis. PT Kisantra Solidaritas"
                                error={errors.account_name}
                            />
                        </div>

                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                                Nomor Rekening *
                            </label>
                            <Input
                                value={data.account_number}
                                onChange={(e) => setData('account_number', e.target.value)}
                                placeholder="1234567890"
                                error={errors.account_number}
                            />
                        </div>

                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                                Nama Bank *
                            </label>
                            <Input
                                value={data.bank_name}
                                onChange={(e) => setData('bank_name', e.target.value)}
                                placeholder="BCA, Mandiri, BNI..."
                                error={errors.bank_name}
                            />
                        </div>

                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                                Cabang
                            </label>
                            <Input
                                value={data.branch}
                                onChange={(e) => setData('branch', e.target.value)}
                                placeholder="Cabang Sudirman"
                                error={errors.branch}
                            />
                        </div>

                        <div>
                            <CurrencyInput
                                label="Saldo Awal *"
                                value={data.initial_balance}
                                onChange={(v) => setData('initial_balance', v)}
                                error={errors.initial_balance}
                                hint={isEdit ? 'Mengubah saldo awal akan menggeser saldo total.' : undefined}
                            />
                        </div>
                    </div>
                </form>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="zinc"
                        onClick={() => onOpenChange(false)}
                        disabled={processing}
                        className="w-full sm:w-auto order-2 sm:order-1"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        form="account-form"
                        loading={processing}
                        variant={isEdit ? 'blue' : 'primary'}
                        className="w-full sm:w-auto order-1 sm:order-2"
                    >
                        {isEdit ? 'Simpan Perubahan' : 'Tambah Rekening'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
