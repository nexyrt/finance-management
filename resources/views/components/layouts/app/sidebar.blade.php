<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gray-50 dark:bg-dark-900">
    <x-toast />
    <x-dialog />

    {{-- Include the notification component --}}
    <flux:sidebar sticky stashable class="border-r border-primary-200 bg-primary-50 dark:border-primary-900 dark:bg-primary-950">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex justify-center items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            {{-- DASHBOARD --}}
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('common.dashboard') }}
            </flux:navlist.item>

            {{-- MASTER DATA --}}
            @canany(['view clients', 'view services'])
                <div class="px-3 pt-6 pb-2">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('common.master_data') }}
                    </div>
                </div>

                @can('view clients')
                    <flux:navlist.item icon="users" :href="route('clients')" :current="request()->routeIs('clients')"
                        wire:navigate>
                        {{ __('common.clients') }}
                    </flux:navlist.item>
                @endcan

                @can('view services')
                    <flux:navlist.item icon="puzzle-piece" :href="route('services')"
                        :current="request()->routeIs('services')" wire:navigate>
                        {{ __('common.services') }}
                    </flux:navlist.item>
                @endcan
            @endcanany

            {{-- FINANCE --}}
            @canany(['view invoices', 'view recurring-invoices', 'view bank-accounts', 'view cash-flow'])
                <div class="px-3 pt-6 pb-2">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('common.finance') }}
                    </div>
                </div>

                @can('view invoices')
                    <flux:navlist.item icon="document-text" :href="route('invoices.index')"
                        :current="request()->routeIs('invoices.*')" wire:navigate>
                        {{ __('common.invoices') }}
                    </flux:navlist.item>
                @endcan

                @can('view recurring-invoices')
                    <flux:navlist.item icon="arrow-path" :href="route('recurring-invoices.index')"
                        :current="request()->routeIs('recurring-invoices.*')" wire:navigate>
                        {{ __('common.recurring_invoices') }}
                    </flux:navlist.item>
                @endcan

                @can('view bank-accounts')
                    <flux:navlist.item icon="credit-card" :href="route('bank-accounts.index')"
                        :current="request()->routeIs('bank-accounts.*')" wire:navigate>
                        {{ __('common.bank_accounts') }}
                    </flux:navlist.item>
                @endcan

                @can('view cash-flow')
                    <flux:navlist.item icon="chart-bar" :href="route('cash-flow.index')"
                        :current="request()->routeIs('cash-flow.*')" wire:navigate>
                        {{ __('common.cash_flow') }}
                    </flux:navlist.item>
                @endcan
            @endcanany

            {{-- OPERATIONS --}}
            @canany(['view categories', 'view fund-requests', 'view reimbursements', 'view feedbacks'])
                <div class="px-3 pt-6 pb-2">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('common.operations') }}
                    </div>
                </div>

                @can('view categories')
                    <flux:navlist.item icon="tag" :href="route('transaction-categories.index')"
                        :current="request()->routeIs('transaction-categories.*')" wire:navigate>
                        {{ __('common.categories') }}
                    </flux:navlist.item>
                @endcan

                @can('view fund-requests')
                    <flux:navlist.item icon="document-currency-dollar" :href="route('fund-requests.index')"
                        :current="request()->routeIs('fund-requests.*')" wire:navigate>
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('common.fund_requests') }}</span>
                            @can('approve fund-requests')
                                @php
                                    $pendingFundRequests = \App\Models\FundRequest::where('status', 'pending')->count();
                                @endphp
                                @if ($pendingFundRequests > 0)
                                    <flux:badge color="yellow" size="sm">{{ $pendingFundRequests }}</flux:badge>
                                @endif
                            @endcan
                        </div>
                    </flux:navlist.item>
                @endcan

                @can('view reimbursements')
                    <flux:navlist.item icon="receipt-percent" :href="route('reimbursements.index')"
                        :current="request()->routeIs('reimbursements.*')" wire:navigate>
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('common.reimbursements') }}</span>
                            @can('approve reimbursements')
                                @php
                                    $pendingReimbursements = \App\Models\Reimbursement::pending()->count();
                                @endphp
                                @if ($pendingReimbursements > 0)
                                    <flux:badge color="yellow" size="sm">{{ $pendingReimbursements }}</flux:badge>
                                @endif
                            @endcan
                        </div>
                    </flux:navlist.item>
                @endcan

                @can('view feedbacks')
                    <flux:navlist.item icon="chat-bubble-left-ellipsis" :href="route('feedbacks.index')"
                        :current="request()->routeIs('feedbacks.*')" wire:navigate>
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('common.feedbacks') }}</span>
                            @can('manage feedbacks')
                                @php
                                    $openFeedbacks = \App\Models\Feedback::where('status', 'open')->count();
                                @endphp
                                @if ($openFeedbacks > 0)
                                    <flux:badge color="red" size="sm">{{ $openFeedbacks }}</flux:badge>
                                @endif
                            @endcan
                        </div>
                    </flux:navlist.item>
                @endcan
            @endcanany

            {{-- DEBT & RECEIVABLES --}}
            @canany(['view loans', 'view receivables'])
                <div class="px-3 pt-6 pb-2">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('common.debt_receivables') }}
                    </div>
                </div>

                @can('view loans')
                    <flux:navlist.item icon="banknotes" :href="route('loans.index')"
                        :current="request()->routeIs('loans.*')" wire:navigate>
                        {{ __('common.loans') }}
                    </flux:navlist.item>
                @endcan

                @can('view receivables')
                    <flux:navlist.item icon="currency-dollar" :href="route('receivables.index')"
                        :current="request()->routeIs('receivables.*')" wire:navigate>
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('common.receivables') }}</span>
                            @can('approve receivables')
                                @php
                                    $pendingReceivables = \App\Models\Receivable::pendingApproval()->count();
                                @endphp
                                @if ($pendingReceivables > 0)
                                    <flux:badge color="yellow" size="sm">{{ $pendingReceivables }}</flux:badge>
                                @endif
                            @endcan
                        </div>
                    </flux:navlist.item>
                @endcan
            @endcanany

            {{-- ADMINISTRATION --}}
            @canany(['manage users', 'view permissions'])
                <div class="px-3 pt-6 pb-2">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('common.administration') }}
                    </div>
                </div>

                @can('manage users')
                    <flux:navlist.item icon="users" :href="route('admin.users')"
                        :current="request()->routeIs('admin.users')" wire:navigate>
                        {{ __('common.users') }}
                    </flux:navlist.item>
                @endcan

                @can('view permissions')
                    <flux:navlist.item icon="shield-check" :href="route('permissions.index')"
                        :current="request()->routeIs('permissions.*')" wire:navigate>
                        {{ __('common.permissions') }}
                    </flux:navlist.item>
                @endcan
            @endcanany

            {{-- TESTING (Local Environment Only) --}}
            @env('local')
                <flux:navlist.item icon="beaker" :href="route('test')" :current="request()->routeIs('test')"
                    wire:navigate>
                    {{ __('common.testing_page') }}
                </flux:navlist.item>
            @endenv
        </flux:navlist>

        <flux:spacer />

        {{-- Desktop: Notification, Language Switcher & Theme Toggle (1 row) --}}
        <div class="hidden lg:flex justify-center items-center gap-2 mb-4">
            <livewire:notifications.bell />
            <livewire:language-switcher />

            {{-- Theme Toggle --}}
            <div x-data>
                <button @click="$flux.appearance = ($flux.appearance === 'dark' ? 'light' : 'dark')"
                    type="button"
                    class="relative p-2 text-dark-500 hover:text-dark-700 dark:text-dark-400 dark:hover:text-dark-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-dark-700">
                    <x-icon name="sun" class="w-6 h-6 dark:hidden" />
                    <x-icon name="moon" class="w-6 h-6 hidden dark:block" />
                </button>
            </div>
        </div>

        {{-- User Menu --}}
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('common.settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('common.log_out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>

    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        {{-- Mobile: Notification, Language Switcher & Theme Toggle --}}
        <div class="flex items-center gap-1">
            <livewire:notifications.bell />
            <livewire:language-switcher />

            {{-- Theme Toggle --}}
            <div x-data>
                <button @click="$flux.appearance = ($flux.appearance === 'dark' ? 'light' : 'dark')"
                    type="button"
                    class="relative p-2 text-dark-500 hover:text-dark-700 dark:text-dark-400 dark:hover:text-dark-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-dark-700">
                    <x-icon name="sun" class="w-6 h-6 dark:hidden" />
                    <x-icon name="moon" class="w-6 h-6 hidden dark:block" />
                </button>
            </div>
        </div>

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth() -> user() -> initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('common.settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('common.log_out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    {{-- Floating Feedback Button --}}
    <livewire:floating-feedback-button />

    {{-- Global Feedback Create Modal --}}
    <livewire:feedbacks.create />

    @fluxScripts
    @livewireScripts
    <wireui:scripts />
    @stack('scripts')

    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
</body>

</html>
