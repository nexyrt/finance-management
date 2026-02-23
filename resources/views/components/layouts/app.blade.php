<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkTheme: localStorage.getItem('tallstackui.theme') === 'dark' ||
            (!localStorage.getItem('tallstackui.theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    x-init="
        $watch('darkTheme', value => {
            localStorage.setItem('tallstackui.theme', value ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', value);
        });
        document.documentElement.classList.toggle('dark', darkTheme);
    ">

<head>
    @include('partials.head')

    <style>
        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
        .dark ::-webkit-scrollbar-thumb { background: #3f3f46; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #52525b; }

        /* ── Sidebar nav item ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 0.625rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #4b5563;
            transition: background 150ms ease, color 150ms ease, transform 100ms ease;
            position: relative;
            white-space: nowrap;
        }
        .dark .nav-item { color: #a1a1aa; }

        .nav-item:hover:not(.nav-active) {
            background: #f3f4f6;
            color: #111827;
            transform: translateX(1px);
        }
        .dark .nav-item:hover:not(.nav-active) {
            background: rgba(255,255,255,0.05);
            color: #e4e4e7;
        }

        .nav-item.nav-active {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }
        .dark .nav-item.nav-active {
            background: rgba(37,99,235,0.15);
            color: #60a5fa;
        }

        /* Active left accent */
        .nav-item.nav-active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 2.5px;
            background: currentColor;
            border-radius: 0 2px 2px 0;
        }

        /* ── Collapsed sidebar (driven by Alpine :class, not .sidebar-collapsed) ── */
        [data-sidebar-collapsed="true"] .nav-label,
        [data-sidebar-collapsed="true"] .nav-section-title,
        [data-sidebar-collapsed="true"] .brand-text,
        [data-sidebar-collapsed="true"] .user-info { display: none; }
        [data-sidebar-collapsed="true"] .nav-item { justify-content: center; padding: 0.5rem; }
        [data-sidebar-collapsed="true"] .nav-item::before { display: none; }

        /* ── Reduce motion ── */
        @media (prefers-reduced-motion: reduce) {
            .nav-item, .nav-item::before { transition: none !important; animation: none !important; }
        }

        /* ── Breadcrumb separator ── */
        .breadcrumb-sep { opacity: 0.35; }

        /* ── Header blur ── */
        .header-blur { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        /* ── Notification drawer slide animation ── */
        @keyframes drawer-in  { from { transform: translateX(100%); } to { transform: translateX(0); } }
        @keyframes drawer-out { from { transform: translateX(0); } to { transform: translateX(100%); } }
        @keyframes fade-in    { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-out   { from { opacity: 1; } to { opacity: 0; } }

        .drawer-panel-enter  { animation: drawer-in  0.3s cubic-bezier(0.32,0.72,0,1) forwards; }
        .drawer-panel-leave  { animation: drawer-out 0.25s cubic-bezier(0.32,0.72,0,1) forwards; }
        .drawer-backdrop-enter { animation: fade-in  0.2s ease forwards; }
        .drawer-backdrop-leave { animation: fade-out 0.2s ease forwards; }
    </style>
</head>

<body class="min-h-screen bg-gray-50 dark:bg-[#111113] text-gray-900 dark:text-gray-100 antialiased"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebar.collapsed') === 'true',

        toggleCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar.collapsed', this.sidebarCollapsed);
        },

        closeSidebar() { this.sidebarOpen = false; },

        isActive(path) { return window.location.pathname === path; },
        isActivePrefix(prefix) { return window.location.pathname.startsWith(prefix); }
    }"
    x-init="
        $watch('sidebarOpen', v => { document.body.style.overflow = v ? 'hidden' : ''; });
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) sidebarOpen = false; });
    ">

    <x-toast />
    <x-dialog />

    <div class="flex h-screen overflow-hidden">

        {{-- ════════════════════════════════════
             MOBILE OVERLAY
        ════════════════════════════════════ --}}
        <div x-show="sidebarOpen" x-cloak @click="closeSidebar()"
            x-transition:enter="transition-opacity duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden">
        </div>

        {{-- ════════════════════════════════════
             SIDEBAR
        ════════════════════════════════════ --}}
        <aside
            class="fixed lg:relative z-50 lg:z-auto h-full flex flex-col
                   bg-white dark:bg-[#18181b]
                   border-r border-gray-100 dark:border-white/[0.06]
                   transition-all duration-300 ease-in-out flex-shrink-0"
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full lg:translate-x-0': !sidebarOpen,
                'w-56': !sidebarCollapsed,
                'w-16': sidebarCollapsed
            }"
            :data-sidebar-collapsed="sidebarCollapsed">

            {{-- Brand --}}
            <div class="h-14 flex items-center gap-2.5 px-3 flex-shrink-0 border-b border-gray-100 dark:border-white/[0.06]">
                <div class="w-8 h-8 rounded-lg bg-primary-600 dark:bg-primary-500 flex items-center justify-center flex-shrink-0 shadow-sm shadow-primary-600/30">
                    <img src="{{ asset('images/kisantra.png') }}" alt="Logo" class="w-5 h-5 object-contain" />
                </div>
                <div class="brand-text flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 dark:text-white tracking-tight leading-tight">KISANTRA</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-medium tracking-wide leading-tight">{{ __('common.finance_management') }}</p>
                </div>
                {{-- Mobile close --}}
                <button @click="closeSidebar()" class="lg:hidden p-1 rounded-md text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-4">

                {{-- Dashboard --}}
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" wire:navigate @click="closeSidebar()"
                        class="nav-item {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}"
                        :title="sidebarCollapsed ? '{{ __('common.dashboard') }}' : undefined">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h7v7H3zm11 0h7v7h-7zm0 11h7v7h-7zM3 14h7v7H3z"/>
                        </svg>
                        <span class="nav-label">{{ __('common.dashboard') }}</span>
                    </a>
                </div>

                {{-- Master Data --}}
                @canany(['view clients', 'view services'])
                    <div class="space-y-0.5">
                        <p class="nav-section-title px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-zinc-600 mb-1">{{ __('common.master_data') }}</p>

                        @can('view clients')
                            <a href="{{ route('clients') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('clients') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.clients') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm6.5-3.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7zM23 21v-2a4 4 0 00-3-3.87"/>
                                </svg>
                                <span class="nav-label">{{ __('common.clients') }}</span>
                            </a>
                        @endcan

                        @can('view services')
                            <a href="{{ route('services') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('services') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.services') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 20V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v16M8 7H4a2 2 0 00-2 2v11a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2h-4"/>
                                </svg>
                                <span class="nav-label">{{ __('common.services') }}</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                {{-- Finance --}}
                @canany(['view invoices', 'view recurring-invoices', 'view bank-accounts'])
                    <div class="space-y-0.5">
                        <p class="nav-section-title px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-zinc-600 mb-1">{{ __('common.finance') }}</p>

                        @can('view invoices')
                            <a href="{{ route('invoices.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('invoices*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.invoices') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8"/>
                                </svg>
                                <span class="nav-label">{{ __('common.invoices') }}</span>
                            </a>
                        @endcan

                        @can('view recurring-invoices')
                            <a href="{{ route('recurring-invoices.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('recurring-invoices*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.recurring_invoices') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0-4.97-4.03-9-9-9-2.4 0-4.58.94-6.2 2.47M3 14c0 4.97 4.03 9 9 9 2.4 0 4.58-.94 6.2-2.47M3 3v7h7M21 21v-7h-7"/>
                                </svg>
                                <span class="nav-label">{{ __('common.recurring_invoices') }}</span>
                            </a>
                        @endcan

                        @can('view bank-accounts')
                            <a href="{{ route('bank-accounts.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('bank-accounts*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.bank_accounts') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M9 8h1m-1 4h1m4-4h1m-1 4h1M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16"/>
                                </svg>
                                <span class="nav-label">{{ __('common.bank_accounts') }}</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                {{-- Arus Kas --}}
                @can('view cash-flow')
                    <div class="space-y-0.5">
                        <p class="nav-section-title px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-zinc-600 mb-1">{{ __('common.cash_flow') }}</p>

                        <a href="{{ route('cash-flow.income') }}" wire:navigate @click="closeSidebar()"
                            class="nav-item {{ request()->routeIs('cash-flow.income') ? 'nav-active' : '' }}"
                            :title="sidebarCollapsed ? '{{ __('pages.income') }}' : undefined">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM8 12l4-4 4 4M12 16V8"/>
                            </svg>
                            <span class="nav-label">{{ __('pages.income') }}</span>
                        </a>

                        <a href="{{ route('cash-flow.expenses') }}" wire:navigate @click="closeSidebar()"
                            class="nav-item {{ request()->routeIs('cash-flow.expenses') ? 'nav-active' : '' }}"
                            :title="sidebarCollapsed ? '{{ __('pages.expenses') }}' : undefined">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM16 12l-4 4-4-4M12 8v8"/>
                            </svg>
                            <span class="nav-label">{{ __('pages.expenses') }}</span>
                        </a>

                        <a href="{{ route('cash-flow.transfers') }}" wire:navigate @click="closeSidebar()"
                            class="nav-item {{ request()->routeIs('cash-flow.transfers') ? 'nav-active' : '' }}"
                            :title="sidebarCollapsed ? '{{ __('pages.transfers_and_adjustments') }}' : undefined">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                            </svg>
                            <span class="nav-label">{{ __('pages.transfers_and_adjustments') }}</span>
                        </a>
                    </div>
                @endcan

                {{-- Operasional --}}
                @canany(['view categories', 'view fund requests', 'view reimbursements'])
                    <div class="space-y-0.5">
                        <p class="nav-section-title px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-zinc-600 mb-1">{{ __('common.operations') }}</p>

                        @can('view categories')
                            <a href="{{ route('transaction-categories.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('transaction-categories*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.categories') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 20a2 2 0 002-2V8a2 2 0 00-2-2h-7.9a2 2 0 01-1.69-.9L9.6 3.9A2 2 0 007.93 3H4a2 2 0 00-2 2v13a2 2 0 002 2zM2 10h20"/>
                                </svg>
                                <span class="nav-label">{{ __('common.categories') }}</span>
                            </a>
                        @endcan

                        @can('view fund requests')
                            <a href="{{ route('fund-requests.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('fund-requests*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.fund_requests') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="nav-label">{{ __('common.fund_requests') }}</span>
                            </a>
                        @endcan

                        @can('view reimbursements')
                            <a href="{{ route('reimbursements.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('reimbursements*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.reimbursements') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0-4.97-4.03-9-9-9-2.4 0-4.58.94-6.2 2.47M3 14c0 4.97 4.03 9 9 9 2.4 0 4.58-.94 6.2-2.47M3 3v7h7M21 21v-7h-7"/>
                                </svg>
                                <span class="nav-label">{{ __('common.reimbursements') }}</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                {{-- Utang & Piutang --}}
                @canany(['view loans', 'view receivables'])
                    <div class="space-y-0.5">
                        <p class="nav-section-title px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-zinc-600 mb-1">{{ __('common.debt_receivables') }}</p>

                        @can('view loans')
                            <a href="{{ route('loans.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('loans*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.loans') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 4H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM1 10h22"/>
                                </svg>
                                <span class="nav-label">{{ __('common.loans') }}</span>
                            </a>
                        @endcan

                        @can('view receivables')
                            <a href="{{ route('receivables.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('receivables*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.receivables') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12V7H5a2 2 0 010-4h14v4M3 5v14a2 2 0 002 2h16v-5M18 12h.01"/>
                                </svg>
                                <span class="nav-label">{{ __('common.receivables') }}</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                {{-- Administrasi --}}
                @canany(['view feedbacks', 'view permissions', 'manage users'])
                    <div class="space-y-0.5">
                        <p class="nav-section-title px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-zinc-600 mb-1">{{ __('common.administration') }}</p>

                        @can('view feedbacks')
                            <a href="{{ route('feedbacks.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('feedbacks*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.feedbacks') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                </svg>
                                <span class="nav-label">{{ __('common.feedbacks') }}</span>
                            </a>
                        @endcan

                        @can('view permissions')
                            <a href="{{ route('permissions.index') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('permissions*') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.permissions') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <span class="nav-label">{{ __('common.permissions') }}</span>
                            </a>
                        @endcan

                        @can('manage users')
                            <a href="{{ route('admin.users') }}" wire:navigate @click="closeSidebar()"
                                class="nav-item {{ request()->routeIs('admin.users') ? 'nav-active' : '' }}"
                                :title="sidebarCollapsed ? '{{ __('common.users') }}' : undefined">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M12.5 7a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                                <span class="nav-label">{{ __('common.users') }}</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

            </nav>

            {{-- User profile --}}
            <div class="flex-shrink-0 border-t border-gray-100 dark:border-white/[0.06] p-2"
                x-data="{ userMenu: false }">
                <div class="relative">
                    <button @click="userMenu = !userMenu"
                        class="flex items-center gap-2.5 w-full p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-colors text-left"
                        :title="sidebarCollapsed ? '{{ auth()->user()->name ?? 'User' }}' : undefined">
                        {{-- Avatar --}}
                        <div class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center flex-shrink-0 text-primary-700 dark:text-primary-300 text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="user-info flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-zinc-500 truncate leading-tight capitalize">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                        </div>
                        <svg class="user-info w-3.5 h-3.5 text-gray-400 dark:text-zinc-500 flex-shrink-0 transition-transform duration-150" :class="{ 'rotate-180': userMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="userMenu" x-cloak @click.away="userMenu = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute bottom-full left-0 right-0 mb-1 bg-white dark:bg-[#27272a] border border-gray-100 dark:border-white/[0.08] rounded-xl shadow-xl shadow-black/10 dark:shadow-black/30 overflow-hidden"
                        :class="sidebarCollapsed ? 'left-auto right-0 w-48' : ''">

                        {{-- User info header --}}
                        <div class="px-3 py-2.5 border-b border-gray-100 dark:border-white/[0.06]">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5">{{ auth()->user()->email ?? '' }}</p>
                        </div>

                        <div class="p-1">
                            <a href="{{ route('settings.profile') }}" wire:navigate
                                class="flex items-center gap-2 px-2.5 py-1.5 text-xs text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-white/[0.06] rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                                {{ __('common.my_profile') }}
                            </a>
                            <a href="{{ route('settings.company') }}" wire:navigate
                                class="flex items-center gap-2 px-2.5 py-1.5 text-xs text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-white/[0.06] rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4 21V5a2 2 0 012-2h12a2 2 0 012 2v16M9 9h1m-1 4h1m4-4h1m-1 4h1"/>
                                </svg>
                                {{ __('common.company_profile') }}
                            </a>
                        </div>

                        <div class="p-1 border-t border-gray-100 dark:border-white/[0.06]">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-2 w-full px-2.5 py-1.5 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                                    </svg>
                                    {{ __('common.log_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collapse toggle (desktop only) --}}
            <button @click="toggleCollapse()"
                class="hidden lg:flex absolute top-[4.25rem] -right-3 w-6 h-6 rounded-full bg-white dark:bg-[#27272a] border border-gray-200 dark:border-white/10 shadow-sm items-center justify-center hover:bg-gray-50 dark:hover:bg-[#3f3f46] transition-colors text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-200"
                :title="sidebarCollapsed ? '{{ __('common.expand_sidebar') }}' : '{{ __('common.collapse_sidebar') }}'">
                <svg class="w-3 h-3 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
        </aside>

        {{-- ════════════════════════════════════
             MAIN AREA
        ════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- ── Header ── --}}
            <header class="h-14 flex-shrink-0 flex items-center gap-3 px-4 md:px-6
                           bg-white/80 dark:bg-[#18181b]/80 header-blur
                           border-b border-gray-100 dark:border-white/[0.06]">

                {{-- Mobile hamburger --}}
                <button @click="sidebarOpen = true"
                    class="lg:hidden p-1.5 rounded-lg text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 6h18M3 18h18"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                @php
                    $breadcrumbs = [];
                    $routeName   = request()->route()?->getName() ?? '';

                    $map = [
                        'dashboard'                     => [['label' => __('common.dashboard')]],
                        'clients'                       => [['label' => __('common.master_data')], ['label' => __('common.clients')]],
                        'services'                      => [['label' => __('common.master_data')], ['label' => __('common.services')]],
                        'invoices.index'                => [['label' => __('common.finance')], ['label' => __('common.invoices')]],
                        'invoices.create'               => [['label' => __('common.finance')], ['label' => __('common.invoices'), 'url' => route('invoices.index')], ['label' => __('common.create')]],
                        'invoices.edit'                 => [['label' => __('common.finance')], ['label' => __('common.invoices'), 'url' => route('invoices.index')], ['label' => __('common.edit')]],
                        'recurring-invoices.index'      => [['label' => __('common.finance')], ['label' => __('common.recurring_invoices')]],
                        'recurring-invoices.template.create' => [['label' => __('common.finance')], ['label' => __('common.recurring_invoices'), 'url' => route('recurring-invoices.index')], ['label' => __('common.create')]],
                        'recurring-invoices.template.edit'   => [['label' => __('common.finance')], ['label' => __('common.recurring_invoices'), 'url' => route('recurring-invoices.index')], ['label' => __('common.edit')]],
                        'recurring-invoices.monthly.edit'    => [['label' => __('common.finance')], ['label' => __('common.recurring_invoices'), 'url' => route('recurring-invoices.index')], ['label' => __('common.edit')]],
                        'bank-accounts.index'           => [['label' => __('common.finance')], ['label' => __('common.bank_accounts')]],
                        'cash-flow.income'              => [['label' => __('common.cash_flow')], ['label' => __('pages.income')]],
                        'cash-flow.expenses'            => [['label' => __('common.cash_flow')], ['label' => __('pages.expenses')]],
                        'cash-flow.transfers'           => [['label' => __('common.cash_flow')], ['label' => __('pages.transfers_and_adjustments')]],
                        'transaction-categories.index'  => [['label' => __('common.operations')], ['label' => __('common.categories')]],
                        'fund-requests.index'           => [['label' => __('common.operations')], ['label' => __('common.fund_requests')]],
                        'reimbursements.index'          => [['label' => __('common.operations')], ['label' => __('common.reimbursements')]],
                        'loans.index'                   => [['label' => __('common.debt_receivables')], ['label' => __('common.loans')]],
                        'receivables.index'             => [['label' => __('common.debt_receivables')], ['label' => __('common.receivables')]],
                        'feedbacks.index'               => [['label' => __('common.administration')], ['label' => __('common.feedbacks')]],
                        'permissions.index'             => [['label' => __('common.administration')], ['label' => __('common.permissions')]],
                        'admin.users'                   => [['label' => __('common.administration')], ['label' => __('common.users')]],
                        'settings.profile'              => [['label' => __('common.settings')], ['label' => __('common.profile')]],
                        'settings.password'             => [['label' => __('common.settings')], ['label' => __('common.password')]],
                        'settings.company'              => [['label' => __('common.settings')], ['label' => __('common.company_profile')]],
                    ];
                    $breadcrumbs = $map[$routeName] ?? [['label' => __('common.dashboard')]];
                @endphp

                <nav class="flex items-center gap-1 text-sm min-w-0 flex-1">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="flex-shrink-0 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h7v7H3zm11 0h7v7h-7zm0 11h7v7h-7zM3 14h7v7H3z"/>
                        </svg>
                    </a>

                    @foreach ($breadcrumbs as $i => $crumb)
                        <svg class="breadcrumb-sep w-3 h-3 text-gray-300 dark:text-zinc-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                        </svg>

                        @if ($loop->last)
                            <span class="text-gray-700 dark:text-zinc-200 font-medium truncate text-sm">{{ $crumb['label'] }}</span>
                        @elseif (isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" wire:navigate
                                class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors text-sm truncate hidden sm:block">
                                {{ $crumb['label'] }}
                            </a>
                        @else
                            <span class="text-gray-400 dark:text-zinc-600 text-sm truncate hidden sm:block">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>

                {{-- Right actions --}}
                <div class="flex items-center gap-1 flex-shrink-0">

                    {{-- Language switcher --}}
                    @livewire('language-switcher')

                    {{-- Theme toggle --}}
                    <button @click="darkTheme = !darkTheme"
                        class="p-2 rounded-lg text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-colors"
                        :title="darkTheme ? '{{ __('common.light_mode') }}' : '{{ __('common.dark_mode') }}'">
                        {{-- Sun --}}
                        <svg x-show="darkTheme" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="12" cy="12" r="5"/>
                            <path stroke-linecap="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                        </svg>
                        {{-- Moon --}}
                        <svg x-show="!darkTheme" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                    </button>

                    {{-- Notifications --}}
                    @livewire('notifications.bell')

                </div>
            </header>

            {{-- ── Main content ── --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-[#111113]"
                :class="sidebarCollapsed ? '' : ''">
                <div class="p-4 md:p-6 max-w-[1600px] mx-auto">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>

    {{-- Notification drawer (body-level, outside overflow containers) --}}
    <livewire:notifications.drawer />

    {{-- Floating feedback button --}}
    <livewire:floating-feedback-button />
    <livewire:feedbacks.create />

    @livewireScripts
    @stack('scripts')

    <script>
        // Re-apply dark mode after wire:navigate (Livewire SPA navigation)
        function applyTheme() {
            const isDark = localStorage.getItem('tallstackui.theme') === 'dark' ||
                (!localStorage.getItem('tallstackui.theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
        document.addEventListener('alpine:init', applyTheme);
        window.addEventListener('storage', e => { if (e.key === 'tallstackui.theme') applyTheme(); });

    </script>
</body>
</html>
