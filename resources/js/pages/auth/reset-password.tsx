import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Eye, EyeOff, KeyRound } from 'lucide-react';
import * as React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/auth-layout';

interface Props {
    token: string;
    email: string;
}

export default function ResetPassword({ token, email }: Props) {
    const [showPassword, setShowPassword] = React.useState(false);
    const [showConfirm, setShowConfirm] = React.useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/reset-password', {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    }

    return (
        <>
            <Head title="Atur Ulang Kata Sandi" />

            <div className="space-y-7">
                <div className="space-y-1.5">
                    <h2 className="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Atur ulang kata sandi
                    </h2>
                    <p className="text-sm text-dark-500 dark:text-dark-400">
                        Buat kata sandi baru yang kuat untuk akun Anda.
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <Input
                        label="Alamat Email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        autoComplete="email"
                        required
                    />

                    <div className="space-y-1">
                        <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                            Kata Sandi Baru
                        </label>
                        <div className="relative">
                            <input
                                type={showPassword ? 'text' : 'password'}
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                autoComplete="new-password"
                                placeholder="••••••••"
                                className={[
                                    'flex h-9 w-full rounded-lg border text-sm transition-colors',
                                    'bg-white dark:bg-dark-800',
                                    'text-dark-900 dark:text-dark-300',
                                    'placeholder:text-dark-400 dark:placeholder:text-dark-400',
                                    'focus:outline-none focus:ring-2 focus:ring-offset-0',
                                    'pr-9 pl-3 py-1.5',
                                    errors.password
                                        ? 'border-red-500 dark:border-red-500 focus:ring-red-500'
                                        : 'border-secondary-300 dark:border-dark-600 focus:ring-primary-500',
                                ].join(' ')}
                                required
                            />
                            <button
                                type="button"
                                onClick={() => setShowPassword(!showPassword)}
                                className="absolute inset-y-0 right-3 flex items-center text-dark-400 hover:text-dark-600 dark:hover:text-dark-200 transition-colors"
                                tabIndex={-1}
                            >
                                {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                            </button>
                        </div>
                        {errors.password && (
                            <p className="mt-1 text-xs text-red-600 dark:text-red-400">{errors.password}</p>
                        )}
                    </div>

                    <div className="space-y-1">
                        <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                            Konfirmasi Kata Sandi
                        </label>
                        <div className="relative">
                            <input
                                type={showConfirm ? 'text' : 'password'}
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                autoComplete="new-password"
                                placeholder="••••••••"
                                className={[
                                    'flex h-9 w-full rounded-lg border text-sm transition-colors',
                                    'bg-white dark:bg-dark-800',
                                    'text-dark-900 dark:text-dark-300',
                                    'placeholder:text-dark-400 dark:placeholder:text-dark-400',
                                    'focus:outline-none focus:ring-2 focus:ring-offset-0',
                                    'pr-9 pl-3 py-1.5',
                                    errors.password_confirmation
                                        ? 'border-red-500 dark:border-red-500 focus:ring-red-500'
                                        : 'border-secondary-300 dark:border-dark-600 focus:ring-primary-500',
                                ].join(' ')}
                                required
                            />
                            <button
                                type="button"
                                onClick={() => setShowConfirm(!showConfirm)}
                                className="absolute inset-y-0 right-3 flex items-center text-dark-400 hover:text-dark-600 dark:hover:text-dark-200 transition-colors"
                                tabIndex={-1}
                            >
                                {showConfirm ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                            </button>
                        </div>
                        {errors.password_confirmation && (
                            <p className="mt-1 text-xs text-red-600 dark:text-red-400">
                                {errors.password_confirmation}
                            </p>
                        )}
                    </div>

                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        loading={processing}
                        className="w-full mt-2"
                        icon={<KeyRound className="h-4 w-4" />}
                    >
                        Simpan Kata Sandi Baru
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

ResetPassword.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
