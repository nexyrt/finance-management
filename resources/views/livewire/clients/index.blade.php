{{-- resources/views/livewire/clients/index.blade.php --}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.client_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.client_management_description') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Workflow Guide Button --}}
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#1e1e1e] text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.client_guide_btn') }}
            </button>

            <livewire:clients.create @client-created="$refresh" />
        </div>
    </div>

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.client_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.client_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-6">
            {{-- 3 Steps Timeline --}}
            <div class="relative">
                <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block"></div>

                <div class="space-y-4">
                    {{-- Step 1: Tambah Klien --}}
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                            <span class="text-white font-bold text-sm">1</span>
                        </div>
                        <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="user-plus" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.client_guide_step1_title') }}</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">{{ __('pages.client_guide_step1_desc') }}</p>
                                    <div class="space-y-1.5">
                                        @foreach (['client_guide_step1_tip1', 'client_guide_step1_tip2', 'client_guide_step1_tip3'] as $tip)
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                <span>{{ __('pages.' . $tip) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Kelola & Monitor --}}
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                            <span class="text-white font-bold text-sm">2</span>
                        </div>
                        <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="chart-bar" class="w-5 h-5 text-purple-600 dark:text-purple-400 shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.client_guide_step2_title') }}</h4>
                                    <p class="text-sm text-purple-700 dark:text-purple-300 mb-3">{{ __('pages.client_guide_step2_desc') }}</p>
                                    <div class="space-y-1.5">
                                        @foreach (['client_guide_step2_tip1', 'client_guide_step2_tip2', 'client_guide_step2_tip3'] as $tip)
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                <span>{{ __('pages.' . $tip) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Buat Invoice --}}
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                            <span class="text-white font-bold text-sm">3</span>
                        </div>
                        <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="document-plus" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.client_guide_step3_title') }}</h4>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-300 mb-3">{{ __('pages.client_guide_step3_desc') }}</p>
                                    <div class="space-y-1.5">
                                        @foreach (['client_guide_step3_tip1', 'client_guide_step3_tip2'] as $tip)
                                            <div class="flex items-start gap-2 text-xs text-emerald-600 dark:text-emerald-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                <span>{{ __('pages.' . $tip) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tipe Klien --}}
            <div class="border-t border-secondary-200 dark:border-white/10 pt-5">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">Tipe Klien</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40">
                        <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="user" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">{{ __('pages.client_guide_type_individual_label') }}</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-0.5">{{ __('pages.client_guide_type_individual_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40">
                        <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="building-office" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-purple-900 dark:text-purple-200">{{ __('pages.client_guide_type_company_label') }}</p>
                            <p class="text-xs text-purple-700 dark:text-purple-300 mt-0.5">{{ __('pages.client_guide_type_company_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tips & Peringatan --}}
            <div class="space-y-2">
                {{-- Status Tip --}}
                <div class="flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl">
                    <x-icon name="light-bulb" class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-amber-700 dark:text-amber-300">{{ __('pages.client_guide_tip_status') }}</p>
                </div>
                {{-- Delete Warning --}}
                <div class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/40 rounded-xl">
                    <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-red-700 dark:text-red-300"><strong>{{ __('common.warning') }}:</strong> {{ __('pages.client_guide_warning_delete') }}</p>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.client_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Clients --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_clients') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->total() ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        {{-- Active Clients --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.active_clients') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->where('status', 'Active')->count() ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        {{-- Companies --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="building-office" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.companies') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->where('type', 'company')->count() ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        {{-- Individuals --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="user" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.individuals') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $rows->where('type', 'individual')->count() ?? 0 }}</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filters --}}
    <div class="space-y-4">
        {{-- Filter Section --}}
        <div class="flex flex-col gap-4">
            {{-- Main Filters Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Type Filter --}}
                <x-select.styled wire:model.live="typeFilter" label="{{ __('pages.client_type') }}" :options="[
                    ['label' => __('pages.individuals'), 'value' => 'individual'],
                    ['label' => __('pages.companies'), 'value' => 'company'],
                ]" placeholder="{{ __('pages.all_types') }}" />

                {{-- Status Filter --}}
                <x-select.styled wire:model.live="statusFilter" label="{{ __('common.status') }}" :options="[
                    ['label' => __('common.active'), 'value' => 'Active'],
                    ['label' => __('common.inactive'), 'value' => 'Inactive'],
                ]" placeholder="{{ __('pages.all_status') }}" />

                {{-- Clear Filters --}}
                <div class="flex items-end">
                    <x-button wire:click="clearFilters" color="zinc" icon="x-mark" class="w-full">
                        {{ __('pages.clear_all_filters') }}
                    </x-button>
                </div>
            </div>

            {{-- Filter Status & Result Count --}}
            <div class="flex items-center gap-3">
                @php
                    $activeFilters = collect([$typeFilter, $statusFilter])->filter()->count();
                @endphp

                @if ($activeFilters > 0)
                    <x-badge text="{{ $activeFilters }} {{ __('pages.active_filters') }}" color="primary" size="sm" />
                @endif

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="hidden sm:inline">{{ __('common.showing') }} </span>{{ $rows->count() }}
                    <span class="hidden sm:inline">{{ __('common.of') }} {{ $rows->total() }}</span> {{ __('common.clients') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable
        wire:model="selected">
        {{-- Client Name --}}
        @interact('column_name', $row)
            <div class="flex items-center gap-3">
                @if ($row->logo)
                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $row->logo }}" alt="{{ $row->name }}">
                @else
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center
                        {{ $row->type === 'individual' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' }}">
                        <x-icon name="{{ $row->type === 'individual' ? 'user' : 'building-office' }}" class="w-5 h-5" />
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $row->name }}</p>
                    @if ($row->NPWP)
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate font-mono mt-1">{{ $row->NPWP }}</p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Type --}}
        @interact('column_type', $row)
            <x-badge text="{{ $row->type === 'individual' ? __('pages.individuals') : __('pages.companies') }}"
                color="{{ $row->type === 'individual' ? 'blue' : 'purple' }}" light />
        @endinteract

        {{-- Contact Info --}}
        @interact('column_person_in_charge', $row)
            <div class="space-y-1">
                @if ($row->email)
                    <a href="mailto:{{ $row->email }}"
                        class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        <x-icon name="envelope" class="w-4 h-4" />
                        <span class="truncate">{{ $row->email }}</span>
                    </a>
                @endif

                @if ($row->ar_phone_number)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="phone" class="w-4 h-4" />
                        <span class="truncate">{{ $row->ar_phone_number }}</span>
                    </div>
                @endif

                @if (!$row->email && !$row->ar_phone_number)
                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('pages.no_contact_info') }}</span>
                @endif
            </div>
        @endinteract

        {{-- Status --}}
        @interact('column_status', $row)
            <x-badge text="{{ $row->status }}" color="{{ $row->status === 'Active' ? 'green' : 'red' }}" light />
        @endinteract

        {{-- Invoices Count --}}
        @interact('column_invoices_count', $row)
            <div class="text-center">
                <x-badge color="{{ $row->invoices_count > 0 ? 'blue' : 'gray' }}" light>
                    <x-icon name="document-text" class="w-4 h-4" />
                    <span class="ml-1">{{ $row->invoices_count }}</span>
                </x-badge>
            </div>
        @endinteract

        {{-- Financial Summary --}}
        @interact('column_financial_summary', $row)
            <div class="text-right space-y-1">
                @php
                    $totalAmount = $row->invoices->sum('total_amount');
                    $paidAmount = $row->invoices->filter(fn($inv) => $inv->status === 'paid')->sum('total_amount');
                    $outstandingAmount = $totalAmount - $paidAmount;
                @endphp

                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </p>

                @if ($outstandingAmount > 0)
                    <x-badge color="red" light>
                        <x-icon name="exclamation-triangle" class="w-3 h-3" />
                        <span class="ml-1">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</span>
                    </x-badge>
                @elseif($totalAmount > 0)
                    <x-badge color="green" light>
                        <x-icon name="check-circle" class="w-3 h-3" />
                        <span class="ml-1">{{ __('pages.paid_off') }}</span>
                    </x-badge>
                @endif
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_actions', $row)
            <x-dropdown icon="ellipsis-vertical">
                <x-dropdown.items text="{{ __('pages.view_details') }}" icon="eye"
                    wire:click="$dispatch('show-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('pages.edit_client') }}" icon="pencil"
                    wire:click="$dispatch('edit-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="{{ __('common.delete') }}" icon="trash"
                    wire:click="$dispatch('delete-client', { clientId: {{ $row->id }} })" />
            </x-dropdown>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white" x-text="`${show.length} {{ __('pages.clients_selected') }}`"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('pages.select_action_all_clients') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-button wire:click="clearSelection" color="zinc" size="sm" icon="x-mark">
                        {{ __('common.cancel') }}
                    </x-button>

                    <x-button wire:click="bulkDelete" color="red" size="sm" icon="trash">
                        <span x-text="`{{ __('common.delete') }} ${show.length}`"></span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal components --}}
    <livewire:clients.edit />
    <livewire:clients.delete />
    <livewire:clients.show />
</div>
