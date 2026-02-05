<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          darkTheme: localStorage.getItem('tallstackui.theme') === 'dark' || (!localStorage.getItem('tallstackui.theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="
          $watch('darkTheme', value => {
              localStorage.setItem('tallstackui.theme', value ? 'dark' : 'light');
              if (value) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          });
          // Apply initial state
          if (darkTheme) {
              document.documentElement.classList.add('dark');
          } else {
              document.documentElement.classList.remove('dark');
          }
      ">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gray-50 dark:bg-dark-900">
    <x-toast />
    <x-dialog />

    <x-layout>
        {{-- Header --}}
        <x-slot:header>
            <x-layout.header>
                <x-slot:right>
                    <div class="flex items-center gap-2">
                        {{-- Notification Bell --}}
                        <livewire:notifications.bell />

                        {{-- Language Switcher --}}
                        <livewire:language-switcher />

                        {{-- Theme Switcher --}}
                        <x-theme-switch only-icons />

                        {{-- User Dropdown --}}
                        <x-dropdown text="Hello, {{ auth()->user()->name }}!" position="bottom-end">
                            <x-dropdown.items text="{{ __('common.settings') }}" icon="cog" href="{{ route('settings.profile') }}" wire:navigate />

                            <x-dropdown.items separator />

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown.items text="{{ __('common.log_out') }}"
                                                icon="arrow-right-start-on-rectangle"
                                                onclick="event.preventDefault(); this.closest('form').submit();" />
                            </form>
                        </x-dropdown>
                    </div>
                </x-slot:right>
            </x-layout.header>
        </x-slot:header>

        {{-- Sidebar Menu --}}
        <x-slot:menu>
            <x-side-bar smart navigate thin-scroll class="bg-white dark:bg-primary-950 border-r border-zinc-200 dark:border-primary-900">
                {{-- Logo --}}
                <x-slot:brand>
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex justify-center items-center py-4">
                        <x-app-logo />
                    </a>
                </x-slot:brand>

                {{-- DASHBOARD --}}
                <x-side-bar.item text="{{ __('common.dashboard') }}" icon="home" :route="route('dashboard')" />

                {{-- MASTER DATA --}}
                @canany(['view clients', 'view services'])
                    <x-side-bar.separator text="{{ __('common.master_data') }}" />

                    @can('view clients')
                        <x-side-bar.item text="{{ __('common.clients') }}" icon="users" :route="route('clients')" />
                    @endcan

                    @can('view services')
                        <x-side-bar.item text="{{ __('common.services') }}" icon="puzzle-piece" :route="route('services')" />
                    @endcan
                @endcanany

                {{-- FINANCE --}}
                @canany(['view invoices', 'view recurring-invoices', 'view bank-accounts', 'view cash-flow'])
                    <x-side-bar.separator text="{{ __('common.finance') }}" />

                    @can('view invoices')
                        <x-side-bar.item text="{{ __('common.invoices') }}" icon="document-text" :route="route('invoices.index')" />
                    @endcan

                    @can('view recurring-invoices')
                        <x-side-bar.item text="{{ __('common.recurring_invoices') }}" icon="arrow-path" :route="route('recurring-invoices.index')" />
                    @endcan

                    @can('view bank-accounts')
                        <x-side-bar.item text="{{ __('common.bank_accounts') }}" icon="credit-card" :route="route('bank-accounts.index')" />
                    @endcan

                    @can('view cash-flow')
                        <x-side-bar.item text="{{ __('common.cash_flow') }}" icon="chart-bar" :route="route('cash-flow.index')" />
                    @endcan
                @endcanany

                {{-- OPERATIONS --}}
                <x-side-bar.separator text="{{ __('Operations') }}" />

                @can('view categories')
                    <x-side-bar.item text="{{ __('common.categories') }}" icon="tag" :route="route('transaction-categories.index')" />
                @endcan

                @can('view reimbursements')
                    <x-side-bar.item text="{{ __('common.reimbursements') }}" icon="receipt-percent" :route="route('reimbursements.index')" />
                @endcan

                @can('view feedbacks')
                    <x-side-bar.item text="{{ __('common.feedbacks') }}" icon="chat-bubble-left-ellipsis" :route="route('feedbacks.index')" />
                @endcan

                {{-- DEBT & RECEIVABLES --}}
                @canany(['view loans', 'view receivables'])
                    <x-side-bar.separator text="{{ __('common.debt_receivables') }}" />

                    @can('view loans')
                        <x-side-bar.item text="{{ __('common.loans') }}" icon="banknotes" :route="route('loans.index')" />
                    @endcan

                    @can('view receivables')
                        <x-side-bar.item text="{{ __('common.receivables') }}" icon="currency-dollar" :route="route('receivables.index')" />
                    @endcan
                @endcanany

                {{-- ADMINISTRATION --}}
                @canany(['manage users', 'view permissions'])
                    <x-side-bar.separator text="{{ __('common.administration') }}" />

                    @can('manage users')
                        <x-side-bar.item text="{{ __('common.users') }}" icon="users" :route="route('admin.users')" />
                    @endcan

                    @can('view permissions')
                        <x-side-bar.item text="{{ __('common.permissions') }}" icon="shield-check" :route="route('permissions.index')" />
                    @endcan
                @endcanany

                {{-- TESTING (Local Environment Only) --}}
                @env('local')
                    <x-side-bar.separator text="{{ __('Development') }}" />
                    <x-side-bar.item text="{{ __('common.testing_page') }}" icon="beaker" :route="route('test')" />
                @endenv
            </x-side-bar>
        </x-slot:menu>

        {{-- Main Content --}}
        {{ $slot }}
    </x-layout>

    {{-- Floating Feedback Button --}}
    <livewire:floating-feedback-button />

    {{-- Global Feedback Create Modal --}}
    <livewire:feedbacks.create />

    @livewireScripts
    <wireui:scripts />
    @stack('scripts')

    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    {{-- Fix theme persistence on Livewire navigation --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            // Re-apply theme from localStorage after Livewire navigation
            const theme = localStorage.getItem('tallstackui.theme');
            const isDark = theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Update Alpine.js variable if it exists
            if (window.Alpine && Alpine.store) {
                // Try to update the darkTheme variable in root Alpine context
                const htmlElement = document.documentElement;
                if (htmlElement.__x) {
                    htmlElement.__x.$data.darkTheme = isDark;
                }
            }
        });
    </script>
</body>

</html>
