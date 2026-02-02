<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
                {{ __('pages.receivable_management') }}
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                {{ __('pages.manage_receivables_tracking') }}
            </p>
        </div>
        <livewire:receivables.create @created="$refresh" />
    </div>

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
            <x-select.styled wire:model.live="typeFilter" label="{{ __('common.type') }}" :options="$this->typeOptions" placeholder="{{ __('pages.all') }} {{ strtolower(__('common.type')) }}..." />

            <x-select.styled wire:model.live="statusFilter" label="{{ __('common.status') }}" :options="$this->statusOptions"
                placeholder="{{ __('pages.all') }} {{ strtolower(__('common.status')) }}..." />
        </div>

        @if ($typeFilter || $statusFilter)
            <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                {{ __('pages.clear_filter') }}
            </x-button>
        @endif
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

        @interact('column_receivable_number', $row)
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-sm">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->receivable_number }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->type === 'employee_loan' ? 'Karyawan' : 'Perusahaan' }}
                    </div>
                </div>
            </div>
        @endinteract

        @interact('column_debtor', $row)
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-secondary-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->type === 'employee_loan' ? 'user' : 'building-office' }}"
                        class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                </div>
                <div>
                    <div class="font-medium text-dark-900 dark:text-dark-50">{{ $row->debtor?->name }}</div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->purpose }}</div>
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
                $totalInterest = round(($row->principal_amount * $row->interest_rate) / 100);
                $totalInterestPaid = $row->payments()->sum('interest_paid');
                $remainingInterest = $totalInterest - $totalInterestPaid;
                $interestPercentage = $totalInterest > 0 ? ($totalInterestPaid / $totalInterest) * 100 : 0;
            @endphp
            <div>
                <div class="text-sm font-medium text-dark-500 dark:text-dark-400">Total</div>
                <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($totalInterest, 0, ',', '.') }}
                </div>

                @if ($row->interest_rate > 0)
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        {{ $row->interest_rate }}% per tahun
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

                @if ($totalInterest > 0)
                    <div class="mt-2 text-sm">
                        <span class="text-dark-500 dark:text-dark-400">Sisa:</span>
                        <span class="font-semibold text-orange-600 dark:text-orange-400">
                            Rp {{ number_format($remainingInterest, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_installment_months', $row)
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-100 dark:bg-dark-700 rounded-lg">
                    <x-icon name="calendar" class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                    <span
                        class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->installment_months ?? '-' }}</span>
                    <span class="text-xs text-dark-500 dark:text-dark-400">bulan</span>
                </div>
                @if ($row->installment_amount)
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        Rp {{ number_format($row->installment_amount, 0, ',', '.') }}/bln
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_loan_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->loan_date?->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                    Jatuh tempo: {{ $row->due_date?->format('d M Y') }}
                </div>
                @if ($row->due_date?->isPast() && $row->status === 'active')
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1 font-semibold">
                        ⚠ Overdue
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_status', $row)
            <x-badge :text="match ($row->status) {
                'draft' => 'Draft',
                'pending_approval' => 'Menunggu',
                'active' => 'Aktif',
                'paid_off' => 'Lunas',
                'rejected' => 'Ditolak',
                default => ucfirst($row->status),
            }" :color="match ($row->status) {
                'draft' => 'gray',
                'pending_approval' => 'yellow',
                'active' => 'blue',
                'paid_off' => 'green',
                'rejected' => 'red',
                default => 'gray',
            }" />
        @endinteract

        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                @if ($row->status === 'draft')
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click="submitReceivable({{ $row->id }})" title="Ajukan" />
                @endif

                @if ($row->status === 'pending_approval' && auth()->user()->can('approve receivables'))
                    <x-button.circle icon="check" color="green" size="sm"
                        wire:click="$dispatch('approve::receivable', { receivable: '{{ $row->id }}' })"
                        title="Setujui" />
                @endif

                @if ($row->status === 'active')
                    <x-button.circle icon="currency-dollar" color="green" size="sm"
                        wire:click="$dispatch('load::pay-receivable', { receivable: '{{ $row->id }}' })"
                        title="Bayar" />
                @endif

                @if (in_array($row->status, ['draft', 'rejected']))
                    <x-button.circle icon="pencil" color="blue" size="sm"
                        wire:click="$dispatch('load::receivable', { receivable: '{{ $row->id }}' })" title="Edit" />
                @endif

                @if ($row->status === 'draft')
                    <livewire:receivables.delete :receivable="$row" :key="uniqid()" @deleted="$refresh" />
                @endif
            </div>
        @endinteract
    </x-table>

    {{-- Child Components --}}
    <livewire:receivables.update @updated="$refresh" />
    <livewire:receivables.approve @approved="$refresh" />
    <livewire:receivables.pay-receivable @paid="$refresh" />
</div>
