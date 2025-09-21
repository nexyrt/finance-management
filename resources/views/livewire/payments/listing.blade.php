<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 flex-1">
            <div>
                <x-select.styled wire:model.live="paymentMethodFilter" label="Metode Pembayaran" :options="[
                    ['label' => 'Transfer Bank', 'value' => 'bank_transfer'],
                    ['label' => 'Tunai', 'value' => 'cash'],
                ]"
                    placeholder="Semua metode..." />
            </div>

            <div>
                <x-select.styled wire:model.live="bankAccountFilter" label="Rekening Bank" :disabled="$constrainedBankAccountId !== null"
                    :options="$this->bankAccounts
                        ->map(
                            fn($account) => [
                                'label' => $account->bank_name . ' - ' . $account->account_name,
                                'value' => $account->id,
                            ],
                        )
                        ->toArray()" placeholder="Semua rekening..." searchable />
            </div>

            <div>
                <x-select.styled wire:model.live="invoiceStatusFilter" label="Status Invoice" :options="[
                    ['label' => 'Dibayar', 'value' => 'paid'],
                    ['label' => 'Sebagian', 'value' => 'partially_paid'],
                    ['label' => 'Terkirim', 'value' => 'sent'],
                    ['label' => 'Terlambat', 'value' => 'overdue'],
                ]"
                    placeholder="Semua status..." />
            </div>

            <div>
                <x-date month-year-only wire:model.live="selectedMonth" label="Bulan" placeholder="Pilih bulan..." />
            </div>

            <div>
                <x-date wire:model.live="dateRange" label="Range Tanggal" range placeholder="Pilih range..." />
            </div>
        </div>

        <div class="flex gap-2">
            <x-button wire:click="exportExcel" size="sm" color="green" icon="document-text" outline>
                Excel
            </x-button>
            <x-button wire:click="exportPdf" size="sm" color="red" icon="document" outline>
                PDF
            </x-button>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->payments" selectable wire:model="selected" paginate filter loading>

        {{-- Payment Date Column --}}
        @interact('column_payment_date', $row)
            <div>
                <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ \Carbon\Carbon::parse($row->payment_date)->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ \Carbon\Carbon::parse($row->payment_date)->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Invoice Number Column --}}
        @interact('column_invoice_number', $row)
            <div class="cursor-pointer" wire:click="viewInvoice({{ $row->invoice_id }})"
                loading="viewInvoice({{ $row->invoice_id }})">
                <div class="font-mono font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700">
                    {{ $row->invoice_number }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    Status:
                    @php
                        $statusConfig = [
                            'paid' => ['text' => 'Lunas', 'color' => 'green'],
                            'partially_paid' => ['text' => 'Sebagian', 'color' => 'yellow'],
                            'sent' => ['text' => 'Terkirim', 'color' => 'blue'],
                            'overdue' => ['text' => 'Terlambat', 'color' => 'red'],
                        ];
                        $config = $statusConfig[$row->invoice_status] ?? [
                            'text' => ucfirst($row->invoice_status),
                            'color' => 'gray',
                        ];
                    @endphp
                    <span class="text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400 font-medium">
                        {{ $config['text'] }}
                    </span>
                </div>
            </div>
        @endinteract

        {{-- Client Column --}}
        @interact('column_client_name', $row)
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 {{ $row->client_type === 'individual'
                        ? 'bg-gradient-to-br from-primary-400 to-primary-600'
                        : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                        class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="font-medium text-dark-900 dark:text-dark-50">{{ $row->client_name }}</p>
                    <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">
                        {{ $row->client_type === 'individual' ? 'Individu' : 'Perusahaan' }}
                    </div>
                </div>
            </div>
        @endinteract

        {{-- Amount Column --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-green-600 dark:text-green-400">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                @if ($row->reference_number)
                    <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                        Ref: {{ $row->reference_number }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Payment Method Column --}}
        @interact('column_payment_method', $row)
            <x-badge :text="$row->payment_method === 'bank_transfer' ? 'Transfer' : 'Tunai'" :color="$row->payment_method === 'bank_transfer' ? 'blue' : 'green'" :icon="$row->payment_method === 'bank_transfer' ? 'credit-card' : 'banknotes'" />
        @endinteract

        {{-- Bank Account Column --}}
        @interact('column_bank_account', $row)
            <div>
                <div class="font-medium text-dark-900 dark:text-dark-50">{{ $row->bank_name }}</div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->account_name }}</div>
            </div>
        @endinteract

        {{-- Actions Column --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="viewInvoice({{ $row->invoice_id }})" loading="viewInvoice({{ $row->invoice_id }})"
                    title="Lihat Invoice" />

                <x-button.circle icon="pencil" color="green" size="sm" wire:click="editPayment({{ $row->id }})"
                    loading="editPayment({{ $row->id }})" title="Edit Payment" />

                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deletePayment({{ $row->id }})" loading="deletePayment({{ $row->id }})"
                    title="Hapus Payment" />
            </div>
        @endinteract

    </x-table>

    {{-- Di bagian bawah blade file --}}
    <livewire:invoices.show @invoice-updated="$refresh" />
    <livewire:payments.edit @payment-updated="$refresh" />
</div>
