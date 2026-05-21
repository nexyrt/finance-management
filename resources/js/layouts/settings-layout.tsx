import { Link, usePage } from '@inertiajs/react';
import { Building2, KeyRound, User } from 'lucide-react';
import * as React from 'react';
import { PageHeader } from '@/components/shared/page-header';
import { cn } from '@/lib/utils';

interface SettingsLayoutProps {
    children: React.ReactNode;
    title: string;
    description?: string;
}

const NAV_ITEMS = [
    { href: '/settings/profile', label: 'Profil', icon: User, active: 'profile' },
    { href: '/settings/password', label: 'Kata Sandi', icon: KeyRound, active: 'password' },
    { href: '/settings/company', label: 'Profil Perusahaan', icon: Building2, active: 'company' },
];

export function SettingsLayout({ children, title, description }: SettingsLayoutProps) {
    const { url } = usePage();

    return (
        <div className="space-y-6">
            <PageHeader title="Pengaturan" description="Kelola akun, keamanan, dan profil perusahaan Anda" />

            <div className="flex flex-col lg:flex-row gap-6">
                {/* Sidebar Nav */}
                <aside className="lg:w-60 shrink-0">
                    <nav className="rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden bg-white dark:bg-dark-700">
                        {NAV_ITEMS.map((item) => {
                            const Icon = item.icon;
                            const isActive = url.startsWith(item.href);
                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={cn(
                                        'flex items-center gap-3 px-4 py-3 text-sm border-l-2 transition-colors',
                                        isActive
                                            ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-300 font-medium'
                                            : 'border-transparent text-dark-700 dark:text-dark-300 hover:bg-secondary-50 dark:hover:bg-dark-800',
                                    )}
                                >
                                    <Icon className={cn('w-4 h-4', isActive ? 'text-primary-600 dark:text-primary-400' : 'text-dark-400')} />
                                    {item.label}
                                </Link>
                            );
                        })}
                    </nav>
                </aside>

                {/* Content Card */}
                <main className="flex-1 min-w-0">
                    <div className="rounded-xl border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 overflow-hidden">
                        <div className="px-6 py-5 border-b border-secondary-200 dark:border-dark-600 bg-secondary-50/40 dark:bg-dark-800/40">
                            <h2 className="text-lg font-bold text-dark-900 dark:text-dark-50">{title}</h2>
                            {description && (
                                <p className="text-sm text-dark-500 dark:text-dark-400 mt-1">{description}</p>
                            )}
                        </div>
                        <div className="p-6">{children}</div>
                    </div>
                </main>
            </div>
        </div>
    );
}
