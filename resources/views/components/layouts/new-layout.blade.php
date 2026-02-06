<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    darkTheme: localStorage.getItem('tallstackui.theme') === 'dark' || (!localStorage.getItem('tallstackui.theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
}" x-init="
    // Watch for theme changes
    $watch('darkTheme', value => {
        localStorage.setItem('tallstackui.theme', value ? 'dark' : 'light');
        if (value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
">

<head>
    @include('partials.head')

    <style>
        .sidebar-transition {
            transition: width 300ms ease-in-out;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Menu Item Base Styles */
        .menu-item {
            @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium;
            @apply transition-all duration-200;
            position: relative;
            /* Default text color - light gray on light mode, lighter gray on dark mode */
            color: rgb(55 65 81);
            /* gray-700 */
        }

        /* Dark mode text color */
        .dark .menu-item {
            color: rgb(209 213 219);
            /* gray-300 */
        }

        /* Hover State (non-active only) */
        .menu-item:not(.active):hover {
            @apply bg-gray-100;
            transform: translateX(2px);
        }

        .dark .menu-item:not(.active):hover {
            @apply bg-dark-800;
        }

        /* Active State */
        .menu-item.active {
            @apply bg-primary-50;
            @apply border-l-2 border-primary-600;
            @apply -ml-0.5 pl-[calc(0.75rem+2px)];
            color: rgb(37 99 235);
            /* primary-600 */
        }

        /* Dark mode active state */
        .dark .menu-item.active {
            background-color: rgba(37, 99, 235, 0.2);
            /* primary-900/20 */
            border-color: rgb(96 165 250);
            /* primary-400 */
            color: rgb(96 165 250);
            /* primary-400 */
        }

        /* Active Border Animation */
        .menu-item.active::before {
            content: '';
            position: absolute;
            left: -0.5px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom,
                    transparent 0%,
                    currentColor 10%,
                    currentColor 90%,
                    transparent 100%);
            animation: slideIn 300ms ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scaleY(0.5);
            }

            to {
                opacity: 1;
                transform: scaleY(1);
            }
        }

        /* Icon Transitions */
        .menu-item svg {
            @apply transition-transform duration-200;
        }

        .menu-item:hover svg {
            transform: scale(1.1);
        }

        /* Section Headers */
        nav h3 {
            color: rgb(107 114 128);
            /* gray-500 */
        }

        .dark nav h3 {
            color: rgb(156 163 175);
            /* gray-400 */
        }

        /* Accessibility: Reduce Motion */
        @media (prefers-reduced-motion: reduce) {

            .menu-item,
            .menu-item svg,
            .menu-item::before {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body class="min-h-screen flex bg-white dark:bg-dark-800" x-data="{
    isMobileMenuOpen: false,
    isCollapsed: false,
    closeMobileMenu() {
        this.isMobileMenuOpen = false;
    },

    isActivePath(path) {
        return window.location.pathname === path;
    }
}" x-init="$watch('isMobileMenuOpen', value => {
    document.body.style.overflow = value ? 'hidden' : 'unset';
});
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        isMobileMenuOpen = false;
    }
});">

    <!-- Mobile Overlay -->
    <div x-show="isMobileMenuOpen" x-cloak @click="closeMobileMenu()" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <!-- ==================== SIDEBAR ==================== -->
    <div class="fixed lg:relative z-50 lg:z-0 transform lg:transform-none transition-transform duration-300 ease-in-out"
        :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <aside
            class="h-screen bg-white dark:bg-dark-700 border-r border-gray-200 dark:border-dark-600 flex flex-col sidebar-transition w-64 lg:w-64"
            :class="{ 'lg:!w-16': isCollapsed }">
            <!-- Logo & Brand -->
            <div class="h-14 md:h-16 flex items-center border-b border-gray-200 dark:border-dark-600"
                :class="isCollapsed ? 'lg:justify-center lg:px-2' : 'px-4'">
                <div class="flex items-center gap-3 flex-1" :class="{ 'lg:justify-center': isCollapsed }">
                    <div
                        class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-white dark:bg-dark-800 flex items-center justify-center overflow-hidden shadow-sm border border-gray-200 dark:border-dark-600 flex-shrink-0">
                        <img src="{{ asset('images/kisantra.png') }}" alt="Kisantra Logo"
                            class="w-7 h-7 md:w-8 md:h-8 object-contain" />
                    </div>
                    <div class="flex flex-col" :class="{ 'lg:hidden': isCollapsed }">
                        <span class="font-bold text-sm md:text-base text-gray-900 dark:text-white tracking-tight">
                            KISANTRA
                        </span>
                        <span
                            class="text-[9px] md:text-[10px] text-gray-500 dark:text-gray-400 font-medium tracking-wide">
                            Finance Management
                        </span>
                    </div>
                </div>

                <!-- Mobile Close Button -->
                <button @click="closeMobileMenu()"
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 "
                        :class="{ 'lg:hidden': isCollapsed }">
                        Dashboard
                    </h3>

                    <a href="{{ route('dashboard') }}" wire:navigate @click="closeMobileMenu()"
                        :title="isCollapsed ? 'Dashboard' : undefined"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800 menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        :class="{
                            'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                '{{ route('dashboard') }}'),
                            'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('dashboard') }}'),
                            'lg:justify-center lg:px-2': isCollapsed
                        }">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h7v7H3zm11 0h7v7h-7zm0 11h7v7h-7zM3 14h7v7H3z" />
                        </svg>
                        <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Dashboard</span>
                    </a>
                </div>

                <!-- Manajemen -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                        :class="{ 'lg:hidden': isCollapsed }">
                        Manajemen
                    </h3>

                    @can('view clients')
                        <a href="{{ route('clients') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Klien' : undefined"
                            class="menu-item {{ request()->routeIs('clients') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('clients') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('clients') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm6.5-3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM23 21v-2a4 4 0 0 0-3-3.87" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Klien</span>
                        </a>
                    @endcan

                    @can('view services')
                        <a href="{{ route('services') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Layanan' : undefined"
                            class="menu-item {{ request()->routeIs('services') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('services') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('services') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16M8 7H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-4" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Layanan</span>
                        </a>
                    @endcan
                </div>

                <!-- Keuangan -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                        :class="{ 'lg:hidden': isCollapsed }">
                        Keuangan
                    </h3>

                    @can('view invoices')
                        <a href="{{ route('invoices.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Invoice' : undefined"
                            class="menu-item {{ request()->routeIs('invoices.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('invoices.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('invoices.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Invoice</span>
                        </a>
                    @endcan

                    @can('view recurring-invoices')
                        <a href="{{ route('recurring-invoices.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Invoice Berulang' : undefined"
                            class="menu-item {{ request()->routeIs('recurring-invoices.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('recurring-invoices.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath(
                                    '{{ route('recurring-invoices.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 10c0-4.97-4.03-9-9-9-2.4 0-4.58.94-6.2 2.47M3 14c0 4.97 4.03 9 9 9 2.4 0 4.58-.94 6.2-2.47M3 3v7h7M21 21v-7h-7" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Invoice Berulang</span>
                        </a>
                    @endcan

                    @can('view bank-accounts')
                        <a href="{{ route('bank-accounts.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Rekening Bank' : undefined"
                            class="menu-item {{ request()->routeIs('bank-accounts.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('bank-accounts.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath(
                                    '{{ route('bank-accounts.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21h18M9 8h1m-1 4h1m4-4h1m-1 4h1M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Rekening Bank</span>
                        </a>
                    @endcan

                    @can('view cash-flow')
                        <a href="{{ route('cash-flow.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Arus Kas' : undefined"
                            class="menu-item {{ request()->routeIs('cash-flow.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('cash-flow.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('cash-flow.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M22 7L13.5 15.5L8.5 10.5L2 17M22 7h-6m6 0v6" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Arus Kas</span>
                        </a>
                    @endcan

                    @can('view categories')
                        <a href="{{ route('transaction-categories.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Kategori' : undefined"
                            class="menu-item {{ request()->routeIs('transaction-categories.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('transaction-categories.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath(
                                    '{{ route('transaction-categories.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2zM2 10h20" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Kategori</span>
                        </a>
                    @endcan

                    @can('view reimbursements')
                        <a href="{{ route('reimbursements.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Reimbursement' : undefined"
                            class="menu-item {{ request()->routeIs('reimbursements.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('reimbursements.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath(
                                    '{{ route('reimbursements.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 10c0-4.97-4.03-9-9-9-2.4 0-4.58.94-6.2 2.47M3 14c0 4.97 4.03 9 9 9 2.4 0 4.58-.94 6.2-2.47M3 3v7h7M21 21v-7h-7" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Reimbursement</span>
                        </a>
                    @endcan
                </div>

                <!-- Utang & Piutang -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                        :class="{ 'lg:hidden': isCollapsed }">
                        Utang & Piutang
                    </h3>

                    @can('view loans')
                        <a href="{{ route('loans.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Pinjaman' : undefined"
                            class="menu-item {{ request()->routeIs('loans.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('loans.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('loans.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 4H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM1 10h22" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Pinjaman</span>
                        </a>
                    @endcan

                    @can('view receivables')
                        <a href="{{ route('receivables.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Piutang' : undefined"
                            class="menu-item {{ request()->routeIs('receivables.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('receivables.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('receivables.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12V7H5a2 2 0 0 1 0-4h14v4M3 5v14a2 2 0 0 0 2 2h16v-5M18 12h.01" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Piutang</span>
                        </a>
                    @endcan
                </div>

                <!-- Administrasi -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                        :class="{ 'lg:hidden': isCollapsed }">
                        Administrasi
                    </h3>

                    @can('view feedbacks')
                        <a href="{{ route('feedbacks.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Umpan Balik' : undefined"
                            class="menu-item {{ request()->routeIs('feedbacks.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('feedbacks.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('feedbacks.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Umpan Balik</span>
                        </a>
                    @endcan

                    @can('view permissions')
                        <a href="{{ route('permissions.index') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Hak Akses' : undefined"
                            class="menu-item {{ request()->routeIs('permissions.index') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('permissions.index') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('permissions.index') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Hak Akses</span>
                        </a>
                    @endcan

                    @can('manage users')
                        <a href="{{ route('admin.users') }}" wire:navigate @click="closeMobileMenu()"
                            :title="isCollapsed ? 'Pengguna' : undefined"
                            class="menu-item {{ request()->routeIs('admin.users') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-dark-800"
                            :class="{
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border-l-2 border-primary-600 dark:border-primary-400 -ml-0.5 pl-[calc(0.75rem+2px)]': isActivePath(
                                    '{{ route('admin.users') }}'),
                                'text-gray-700 dark:text-gray-300': !isActivePath('{{ route('admin.users') }}'),
                                'lg:justify-center lg:px-2': isCollapsed
                            }">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M12.5 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 18a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 0v1.5m0 0V21m-2.5-3.5l-1.3-.75M22 13.5v-1.5m2.5 3.5l1.3-.75" />
                            </svg>
                            <span class="truncate" :class="{ 'lg:hidden': isCollapsed }">Pengguna</span>
                        </a>
                    @endcan
                </div>
            </nav>

            <!-- User Profile Dropdown -->
            <div class="border-t border-gray-200 dark:border-dark-600 p-3" x-data="{ userDropdownOpen: false }">
                <div class="relative">
                    <button @click="userDropdownOpen = !userDropdownOpen"
                        class="flex items-center gap-3 w-full p-2 rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 text-gray-900 dark:text-white"
                        :class="{ 'lg:justify-center': isCollapsed }">
                        <div
                            class="h-8 w-8 md:h-9 md:w-9 border-2 border-primary-200 dark:border-primary-800 flex-shrink-0 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-xs md:text-sm font-medium flex items-center justify-center">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="flex-1 text-left min-w-0" :class="{ 'lg:hidden': isCollapsed }">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0"
                            :class="{ 'lg:hidden': isCollapsed }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userDropdownOpen" @click.away="userDropdownOpen = false" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute bottom-full left-0 right-0 mb-2 w-56 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 shadow-lg rounded-md"
                        :class="{ 'lg:left-auto lg:right-0': !isCollapsed }">
                        <div class="p-2">
                            <div class="px-2 py-1.5">
                                <p class="text-sm font-medium leading-none text-gray-900 dark:text-white">
                                    {{ auth()->user()->name ?? 'User' }}</p>
                                <p class="text-xs leading-none text-gray-500 dark:text-gray-400 mt-1">
                                    {{ auth()->user()->email ?? 'user@email.com' }}</p>
                            </div>
                            <div class="h-px bg-gray-200 dark:bg-dark-600 my-1"></div>
                            <a href="{{ route('settings.profile') }}" wire:navigate
                                class="flex items-center px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 dark:hover:bg-dark-700 cursor-pointer text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('settings.appearance') }}" wire:navigate
                                class="flex items-center px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 dark:hover:bg-dark-700 cursor-pointer text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10c0 1.821-.487 3.53-1.338 5M16.5 8.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM10.5 8.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM16.5 14.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM12 18.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                                </svg>
                                <span>Tampilan</span>
                            </a>
                            <a href="{{ route('settings.company') }}" wire:navigate
                                class="flex items-center px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 dark:hover:bg-dark-700 cursor-pointer text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 21h18M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16M9 9h1m-1 4h1m4-4h1m-1 4h1" />
                                </svg>
                                <span>Profil Perusahaan</span>
                            </a>
                            <div class="h-px bg-gray-200 dark:bg-dark-600 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 dark:hover:bg-dark-700 cursor-pointer text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collapse Toggle - Desktop Only -->
            <button @click="isCollapsed = !isCollapsed"
                class="hidden lg:flex absolute top-20 -right-3 w-6 h-6 rounded-full bg-white dark:bg-dark-700 border border-gray-200 dark:border-dark-600 items-center justify-center shadow-sm hover:bg-gray-50 dark:hover:bg-dark-800 transition-colors text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <svg class="w-4 h-4" x-show="isCollapsed" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6" />
                </svg>
                <svg class="w-4 h-4" x-show="!isCollapsed" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6" />
                </svg>
            </button>
        </aside>
    </div>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <!-- Header -->
        <header
            class="h-14 md:h-16 border-b border-gray-200 dark:border-dark-600 bg-white/95 dark:bg-dark-900 backdrop-blur px-4 md:px-6 flex items-center justify-between gap-4">
            <!-- Mobile Menu Button -->
            <button @click="isMobileMenuOpen = true"
                class="lg:hidden text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white flex-shrink-0 p-2 hover:bg-gray-100 dark:hover:bg-dark-700 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12h18M3 6h18M3 18h18" />
                </svg>
            </button>

            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM21 21l-4.35-4.35" />
                </svg>
                <input type="search" placeholder="Cari..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-dark-700 border-0 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-600 text-sm md:text-base text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" />
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-1 md:gap-2 flex-shrink-0">
                <!-- Theme Toggle (from TallStackUI) -->
                <x-theme-switch sm only-icons />

                <!-- Notifications -->
                <button
                    class="relative text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white h-9 w-9 md:h-10 md:w-10 flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-dark-700">
                    <svg class="w-4 h-4 md:w-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" />
                    </svg>
                    <span
                        class="absolute top-1 right-1 md:top-1.5 md:right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 dark:bg-dark-900">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <wireui:scripts />
    @stack('scripts')

    {{-- Dark Mode Script - Persist across wire:navigate --}}
    <script>
        // Initialize dark mode from localStorage
        function initDarkMode() {
            const isDark = localStorage.getItem('tallstackui.theme') === 'dark' ||
                          (!localStorage.getItem('tallstackui.theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);

            // Apply dark class to html element
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Sync Alpine.js state if Alpine is loaded
            // Wait a tick to ensure Alpine is fully initialized
            setTimeout(() => {
                const htmlEl = document.querySelector('html');
                if (htmlEl && htmlEl.__x && htmlEl.__x.$data) {
                    htmlEl.__x.$data.darkTheme = isDark;
                }
            }, 0);
        }

        // Run on initial load
        initDarkMode();

        // Re-run after Livewire navigation
        document.addEventListener('livewire:navigated', () => {
            initDarkMode();
        });

        // Re-run after Alpine is initialized (for initial page load)
        document.addEventListener('alpine:init', () => {
            initDarkMode();
        });

        // Watch for localStorage changes (for theme switcher in other tabs)
        window.addEventListener('storage', (e) => {
            if (e.key === 'tallstackui.theme') {
                initDarkMode();
            }
        });
    </script>
</body>

</html>
