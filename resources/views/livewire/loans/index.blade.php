{{-- resources/views/livewire/loans/index.blade.php --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
                {{ __('pages.loan_management') }}
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                {{ __('pages.track_and_manage_loans') }}
            </p>
        </div>
        <livewire:loans.create @created="$refresh" />
    </div>

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
            <x-select.styled wire:model.live="statusFilter" label="{{ __('common.status') }}" :options="$this->statusOptions"
                placeholder="{{ __('pages.all') }} {{ strtolower(__('common.status')) }}..." />
        </div>

        @if ($statusFilter)
            <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                {{ __('pages.clear_filter') }}
            </x-button>
        @endif
    </div>

    <x-table :$headers :$sort :rows="$this->rows" paginate filter loading>

        @interact('column_loan_number', $row)
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                    <x-icon name="banknotes" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->loan_number }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->lender_name }}</div>
                </div>
            </div>
        @endinteract

        @interact('column_principal_amount', $row)
            @php
                $totalPrincipalPaid = $row->payments()->sum('principal_paid');
                $remainingPrincipal = $row->principal_amount - $totalPrincipalPaid;
                $percentage = $row->principal_amount > 0 ? ($totalPrincipalPaid / $row->principal_amount) * 100 : 0;
            @endphp
            <div>
                @if ($totalPrincipalPaid > 0)
                    <div class="mt-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-green-600 dark:text-green-400">Terbayar:
                                {{ number_format($percentage, 0) }}%</span>
                            <span class="text-dark-500 dark:text-dark-400">Rp
                                {{ number_format($totalPrincipalPaid, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-dark-200 dark:bg-dark-700 rounded-full h-1.5">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-1.5 rounded-full transition-all"
                                style="width: {{ min($percentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-2 text-sm">
                    <span class="text-dark-500 dark:text-dark-400">Sisa:</span>
                    <span class="font-semibold text-orange-600 dark:text-orange-400">
                        Rp {{ number_format($remainingPrincipal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endinteract

        @interact('column_interest_amount', $row)
            @php
                $totalInterest =
                    $row->interest_type === 'fixed'
                        ? $row->interest_amount ?? 0
                        : round((($row->principal_amount * ($row->interest_rate ?? 0)) / 100 / 12) * $row->term_months);
                $totalInterestPaid = $row->payments()->sum('interest_paid');
                $remainingInterest = $totalInterest - $totalInterestPaid;
                $interestPercentage = $totalInterest > 0 ? ($totalInterestPaid / $totalInterest) * 100 : 0;
            @endphp
            <div>
                @if ($row->interest_type === 'percentage')
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        {{ $row->interest_rate }}% × {{ $row->term_months }} bulan
                    </div>
                @endif

                @if ($totalInterestPaid > 0)
                    <div class="mt-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-green-600 dark:text-green-400">Terbayar:
                                {{ number_format($interestPercentage, 0) }}%</span>
                            <span class="text-dark-500 dark:text-dark-400">Rp
                                {{ number_format($totalInterestPaid, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-dark-200 dark:bg-dark-700 rounded-full h-1.5">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-1.5 rounded-full transition-all"
                                style="width: {{ min($interestPercentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-2 text-sm">
                    <span class="text-dark-500 dark:text-dark-400">Sisa:</span>
                    <span class="font-semibold text-orange-600 dark:text-orange-400">
                        Rp {{ number_format($remainingInterest, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endinteract

        @interact('column_term_months', $row)
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-100 dark:bg-dark-700 rounded-lg">
                    <x-icon name="calendar" class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                    <span class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->term_months }}</span>
                    <span class="text-xs text-dark-500 dark:text-dark-400">bulan</span>
                </div>
            </div>
        @endinteract

        @interact('column_start_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->start_date?->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                    Jatuh tempo: {{ $row->maturity_date?->format('d M Y') }}
                </div>
                @if ($row->maturity_date?->isPast() && $row->status === 'active')
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1 font-semibold">
                        ⚠ Overdue
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_status', $row)
            <x-badge :text="$row->status === 'paid_off' ? 'Lunas' : 'Aktif'" :color="$row->status === 'paid_off' ? 'green' : 'blue'" />
        @endinteract

        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                @if ($row->status === 'active')
                    <x-button.circle icon="currency-dollar" color="green" size="sm"
                        wire:click="$dispatch('load::pay-loan', { loan: '{{ $row->id }}' })" title="Bayar" />
                @endif

                <x-button.circle icon="pencil" color="blue" size="sm"
                    wire:click="$dispatch('load::loan', { loan: '{{ $row->id }}' })" title="Edit" />

                <livewire:loans.delete :loan="$row" :key="uniqid()" @deleted="$refresh" />
            </div>
        @endinteract
    </x-table>

    {{-- Child Components --}}
    <livewire:loans.update @updated="$refresh" />
    <livewire:loans.pay-loan @paid="$refresh" />
</div>
