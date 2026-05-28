import { Head, useForm } from '@inertiajs/react';
import { Eye, EyeOff, ShieldCheck } from 'lucide-react';
import * as React from 'react';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

export default function ConfirmPassword() {
    const [showPassword, setShowPassword] = React.useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({ password: '' });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/confirm-password', { onFinish: () => reset('password') });
    }

    return (
        <>
            <Head title="Konfirmasi Kata Sandi" />

            <div className="space-y-7">
                <div className="space-y-1.5">
                    <h2 className="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Konfirmasi kata sandi
                    </h2>
                    <p className="text-sm text-dark-500 dark:text-dark-400">
                        Ini adalah area aman dari aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
                    </p>
                </div>

                <div className="flex items-center justify-center w-16 h-16 mx-auto bg-yellow-50 dark:bg-yellow-900/20 rounded-2xl">
                    <ShieldCheck className="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div className="space-y-1">
                        <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                            Kata Sandi
                        </label>
                        <div className="relative">
                            <input
                                type={showPassword ? 'text' : 'password'}
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                autoComplete="current-password"
                                placeholder="••••••••"
                                autoFocus
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

                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        loading={processing}
                        className="w-full mt-2"
                        icon={<ShieldCheck className="h-4 w-4" />}
                    >
                        Konfirmasi
                    </Button>
                </form>
            </div>
        </>
    );
}

ConfirmPassword.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
