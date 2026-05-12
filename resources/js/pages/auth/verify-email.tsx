import { Head, useForm } from '@inertiajs/react';
import { LogOut, MailCheck, RefreshCw } from 'lucide-react';
import * as React from 'react';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

interface Props {
    status?: string;
}

export default function VerifyEmail({ status }: Props) {
    const { post: resend, processing: resending } = useForm({});
    const { post: logout, processing: loggingOut } = useForm({});

    function handleResend(e: React.FormEvent) {
        e.preventDefault();
        resend('/email/verification-notification');
    }

    function handleLogout(e: React.FormEvent) {
        e.preventDefault();
        logout('/logout');
    }

    return (
        <>
            <Head title="Verifikasi Email" />

            <div className="space-y-7">
                <div className="space-y-1.5">
                    <h2 className="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Verifikasi email Anda
                    </h2>
                    <p className="text-sm text-dark-500 dark:text-dark-400">
                        Terima kasih telah mendaftar! Sebelum memulai, harap verifikasi alamat email Anda
                        dengan mengklik tautan yang baru saja kami kirimkan. Jika Anda tidak menerima
                        email, kami dengan senang hati akan mengirimkan yang baru.
                    </p>
                </div>

                {status === 'verification-link-sent' && (
                    <div className="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                        Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                    </div>
                )}

                <div className="flex items-center justify-center w-16 h-16 mx-auto bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                    <MailCheck className="h-8 w-8 text-blue-600 dark:text-blue-400" />
                </div>

                <div className="space-y-3">
                    <form onSubmit={handleResend}>
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            loading={resending}
                            className="w-full"
                            icon={<RefreshCw className="h-4 w-4" />}
                        >
                            Kirim Ulang Email Verifikasi
                        </Button>
                    </form>

                    <form onSubmit={handleLogout}>
                        <Button
                            type="submit"
                            variant="outline"
                            size="lg"
                            loading={loggingOut}
                            className="w-full"
                            icon={<LogOut className="h-4 w-4" />}
                        >
                            Keluar
                        </Button>
                    </form>
                </div>
            </div>
        </>
    );
}

VerifyEmail.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
