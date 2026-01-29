<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-dark-900">
    <x-toast />
    <x-dialog />

    {{-- Include the notification component --}}
    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex justify-center items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            {{-- ================================================================== --}}
            {{-- DASHBOARD --}}
            {{-- ================================================================== --}}
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate class="py-5">
                {{ __('Dashboard') }}
            </flux:navlist.item>

            {{-- ================================================================== --}}
            {{-- MASTER DATA --}}
            {{-- ================================================================== --}}
            @canany(['view clients', 'view services'])
                <flux:navlist.group heading="Master Data" expandable class="mt-1">
                    {{-- Clients --}}
                    @can('view clients')
                        <flux:navlist.item icon="users" :href="route('clients')" :current="request()->routeIs('clients')"
                            wire:navigate class="py-5">
                            {{ __('Clients') }}
                        </flux:navlist.item>
                    @endcan

                    {{-- Services --}}
                    @can('view services')
                        <flux:navlist.item icon="puzzle-piece" :href="route('services')"
                            :current="request()->routeIs('services')" wire:navigate class="py-5">
                            {{ __('Services') }}
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- ================================================================== --}}
            {{-- FINANCE --}}
            {{-- ================================================================== --}}
            @canany(['view invoices', 'view recurring-invoices', 'view bank-accounts', 'view cash-flow'])
                <flux:navlist.group heading="Finance" expandable class="mt-1">
                    {{-- Invoices --}}
                    @can('view invoices')
                        <flux:navlist.item icon="document-text" :href="route('invoices.index')"
                            :current="request()->routeIs('invoices.*')" wire:navigate class="py-5">
                            {{ __('Invoices') }}
                        </flux:navlist.item>
                    @endcan

                    {{-- Recurring Invoices --}}
                    @can('view recurring-invoices')
                        <flux:navlist.item icon="arrow-path" :href="route('recurring-invoices.index')"
                            :current="request()->routeIs('recurring-invoices.*')" wire:navigate class="py-5">
                            {{ __('Recurring Invoices') }}
                        </flux:navlist.item>
                    @endcan

                    {{-- Bank Accounts --}}
                    @can('view bank-accounts')
                        <flux:navlist.item icon="credit-card" :href="route('bank-accounts.index')"
                            :current="request()->routeIs('bank-accounts.*')" wire:navigate class="py-5">
                            {{ __('Bank Accounts') }}
                        </flux:navlist.item>
                    @endcan

                    {{-- Cash Flow --}}
                    @can('view cash-flow')
                        <flux:navlist.item icon="chart-bar" :href="route('cash-flow.index')"
                            :current="request()->routeIs('cash-flow.*')" wire:navigate class="py-5">
                            {{ __('Cash Flow') }}
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- ================================================================== --}}
            {{-- CATEGORIES --}}
            {{-- ================================================================== --}}
            @can('view categories')
                <flux:navlist.item icon="tag" :href="route('transaction-categories.index')"
                    :current="request()->routeIs('transaction-categories.*')" wire:navigate class="py-5">
                    {{ __('Categories') }}
                </flux:navlist.item>
            @endcan

            {{-- ================================================================== --}}
            {{-- REIMBURSEMENTS --}}
            {{-- ================================================================== --}}
            @can('view reimbursements')
                <flux:navlist.item icon="receipt-percent" :href="route('reimbursements.index')"
                    :current="request()->routeIs('reimbursements.*')" wire:navigate class="py-5">
                    <div class="flex items-center justify-between w-full">
                        <span>{{ __('Reimbursements') }}</span>
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

            {{-- ================================================================== --}}
            {{-- FEEDBACKS --}}
            {{-- ================================================================== --}}
            @can('view feedbacks')
                <flux:navlist.item icon="chat-bubble-left-ellipsis" :href="route('feedbacks.index')"
                    :current="request()->routeIs('feedbacks.*')" wire:navigate class="py-5">
                    <div class="flex items-center justify-between w-full">
                        <span>{{ __('Feedbacks') }}</span>
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

            {{-- ================================================================== --}}
            {{-- DEBT & RECEIVABLES --}}
            {{-- ================================================================== --}}
            @canany(['view loans', 'view receivables'])
                <flux:navlist.group heading="Debt & Receivables" expandable class="mt-1">
                    {{-- Loans (Company Debt) --}}
                    @can('view loans')
                        <flux:navlist.item icon="banknotes" :href="route('loans.index')"
                            :current="request()->routeIs('loans.*')" wire:navigate class="py-5">
                            {{ __('Loans') }}
                        </flux:navlist.item>
                    @endcan

                    {{-- Receivables (Employee/Client Loans) --}}
                    @can('view receivables')
                        <flux:navlist.item icon="currency-dollar" :href="route('receivables.index')"
                            :current="request()->routeIs('receivables.*')" wire:navigate class="py-5">
                            <div class="flex items-center justify-between w-full">
                                <span>{{ __('Receivables') }}</span>
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
                </flux:navlist.group>
            @endcanany

            {{-- ================================================================== --}}
            {{-- ADMINISTRATION --}}
            {{-- ================================================================== --}}
            @canany(['manage users', 'view permissions'])
                <flux:navlist.group heading="Administration" expandable class="mt-1">
                    {{-- Users --}}
                    @can('manage users')
                        <flux:navlist.item icon="users" :href="route('admin.users')"
                            :current="request()->routeIs('admin.users')" wire:navigate class="py-5">
                            {{ __('Users') }}
                        </flux:navlist.item>
                    @endcan

                    {{-- Permissions --}}
                    @can('view permissions')
                        <flux:navlist.item icon="shield-check" :href="route('permissions.index')"
                            :current="request()->routeIs('permissions.*')" wire:navigate class="py-5">
                            {{ __('Permissions') }}
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- ================================================================== --}}
            {{-- TESTING (Local Environment Only) --}}
            {{-- ================================================================== --}}
            @env('local')
                <flux:navlist.item icon="beaker" :href="route('test')" :current="request()->routeIs('test')"
                    wire:navigate class="py-5">
                    {{ __('Testing Page') }}
                </flux:navlist.item>
            @endenv
        </flux:navlist>

        <flux:spacer />

        {{-- Desktop Notification Bell --}}
        <div class="hidden lg:flex justify-center mb-4">
            <livewire:notifications.bell />
        </div>

        {{-- Theme Switcher --}}
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
        </flux:radio.group>

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
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>

    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        {{-- Notification Bell --}}
        <livewire:notifications.bell />

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
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
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
    @stack('scripts')

    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
</body>

</html>
