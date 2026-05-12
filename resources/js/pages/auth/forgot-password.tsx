import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Mail, SendHorizonal } from 'lucide-react';
import * as React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';

interface Props {
    status?: string;
}

export default function ForgotPassword({ status }: Props) {
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/forgot-password');
    }

    return (
        <>
            <Head title="Lupa Kata Sandi" />

            <div className="space-y-7">
                <div className="space-y-1.5">
                    <h2 className="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Lupa kata sandi?
                    </h2>
                    <p className="text-sm text-dark-500 dark:text-dark-400">
                        Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                    </p>
                </div>

                {status && (
                    <div className="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                        {status}
                    </div>
                )}

                <form onSubmit={submit} className="space-y-4">
                    <Input
                        label="Alamat Email"
                        type="email"
                        placeholder="nama@perusahaan.com"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        icon={<Mail className="h-4 w-4" />}
                        autoComplete="email"
                        autoFocus
                        required
                    />

                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        loading={processing}
                        className="w-full"
                        icon={<SendHorizonal className="h-4 w-4" />}
                    >
                        Kirim Tautan Reset
                    </Button>
                </form>

                <div className="flex justify-center">
                    <Link
                        href="/login"
                        className="inline-flex items-center gap-1.5 text-sm text-dark-500 hover:text-dark-800 dark:text-dark-400 dark:hover:text-dark-200 transition-colors"
                    >
                        <ArrowLeft className="h-3.5 w-3.5" />
                        Kembali ke halaman masuk
                    </Link>
                </div>
            </div>
        </>
    );
}

ForgotPassword.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
