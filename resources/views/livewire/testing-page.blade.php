<section class="w-full p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="text-zinc-100">Invoice Management</flux:heading>
            <flux:text class="text-zinc-400 mt-1">Kelola invoice dan pembayaran klien Anda</flux:text>
        </div>

        <flux:modal.trigger name="create-invoice">
            <flux:button variant="primary" icon="plus" size="sm">
                Buat Invoice Baru
            </flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-zinc-400 text-sm">Total Invoice</flux:text>
                    <flux:heading size="lg" class="text-zinc-100 mt-1">{{ $stats['total'] }}</flux:heading>
                </div>
                <div class="bg-blue-500/10 p-3 rounded-lg">
                    <flux:icon.book-open-text class="w-6 h-6 text-blue-500" />
                </div>
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-zinc-400 text-sm">Pending</flux:text>
                    <flux:heading size="lg" class="text-zinc-100 mt-1">{{ $stats['pending'] }}</flux:heading>
                </div>
                <div class="bg-yellow-500/10 p-3 rounded-lg">
                    <flux:icon.layout-grid class="w-6 h-6 text-yellow-500" />
                </div>
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-zinc-400 text-sm">Paid</flux:text>
                    <flux:heading size="lg" class="text-zinc-100 mt-1">{{ $stats['paid'] }}</flux:heading>
                </div>
                <div class="bg-green-500/10 p-3 rounded-lg">
                    <flux:icon.folder-git-2 class="w-6 h-6 text-green-500" />
                </div>
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-zinc-400 text-sm">Overdue</flux:text>
                    <flux:heading size="lg" class="text-zinc-100 mt-1">{{ $stats['overdue'] }}</flux:heading>
                </div>
                <div class="bg-red-500/10 p-3 rounded-lg">
                    <flux:icon.chevrons-up-down class="w-6 h-6 text-red-500" />
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-zinc-700">
        <nav class="flex space-x-8">
            <button wire:click="$set('activeTab', 'list')"
                class="pb-4 px-1 border-b-2 {{ $activeTab === 'list' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-400 hover:text-zinc-300' }} font-medium text-sm">
                Daftar Invoice
            </button>
            <button wire:click="$set('activeTab', 'create')"
                class="pb-4 px-1 border-b-2 {{ $activeTab === 'create' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-400 hover:text-zinc-300' }} font-medium text-sm">
                Buat Invoice
            </button>
            <button wire:click="$set('activeTab', 'add-items')"
                class="pb-4 px-1 border-b-2 {{ $activeTab === 'add-items' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-400 hover:text-zinc-300' }} font-medium text-sm">
                Tambah Item
            </button>
            <button wire:click="$set('activeTab', 'payments')"
                class="pb-4 px-1 border-b-2 {{ $activeTab === 'payments' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-400 hover:text-zinc-300' }} font-medium text-sm">
                Status Pembayaran
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    @if ($activeTab === 'list')
        <!-- Filters and Search -->
        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-4 flex-1">
                    <flux:input icon="magnifying-glass" placeholder="Cari invoice..." class="w-full sm:w-80"
                        wire:model.live="search" />

                    <x-inputs.select name="statusFilter" :options="$statusOptions" size="md" placeholder="Filter Status" />

                    <x-inputs.datepicker name="filterDate" placeholder="Filter tanggal" mode="range" class="w-64" />
                </div>

                <div class="flex gap-2">
                    <flux:button variant="subtle" icon="funnel" size="sm">
                        Filter
                    </flux:button>
                    <flux:button variant="subtle" icon="arrow-down-tray" size="sm">
                        Export
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-700">
                <flux:heading size="md" class="text-zinc-100">Daftar Invoice</flux:heading>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Invoice
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Due Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-700">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-zinc-900/30">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-100">{{ $invoice->invoice_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-300">{{ $invoice->client->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-100">
                                        {{ $this->formatCurrency($invoice->total_amount) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge variant="outline"
                                        class="{{ $this->getStatusBadgeClass($invoice->status) }}">
                                        {{ $this->getStatusLabel($invoice->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                    {{ $this->formatDate($invoice->due_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item icon="eye"
                                                wire:click="viewInvoice({{ $invoice->id }})">View</flux:menu.item>
                                            <flux:menu.item icon="document-duplicate"
                                                wire:click="duplicateInvoice({{ $invoice->id }})">Duplicate
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="trash" variant="danger"
                                                wire:click="deleteInvoice({{ $invoice->id }})">Delete
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-zinc-400">
                                    Tidak ada invoice ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-zinc-700">
                {{ $invoices->links() }}
            </div>
        </div>
    @endif

    @if ($activeTab === 'create')
        <!-- Form Buat Invoice -->
        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <flux:heading size="md" class="text-zinc-100 mb-6">Buat Invoice Baru</flux:heading>

            <form wire:submit="createInvoice" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-inputs.select name="newInvoice.billed_to_id" :options="$clients" label="Client"
                            placeholder="Pilih Client" />
                        @error('newInvoice.billed_to_id')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-inputs.select name="newInvoice.payment_terms" :options="[
                            ['value' => 'full', 'label' => 'Full Payment'],
                            ['value' => 'installment', 'label' => 'Installment'],
                        ]" label="Payment Terms" />
                    </div>

                    <div>
                        <x-inputs.datepicker name="newInvoice.issue_date" placeholder="Tanggal Invoice"
                            class="w-full" />
                        <flux:label class="text-zinc-300 text-sm">Issue Date</flux:label>
                        @error('newInvoice.issue_date')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-inputs.datepicker name="newInvoice.due_date" placeholder="Tanggal Jatuh Tempo"
                            class="w-full" />
                        <flux:label class="text-zinc-300 text-sm">Due Date</flux:label>
                        @error('newInvoice.due_date')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($newInvoice['payment_terms'] === 'installment')
                        <div>
                            <flux:label>Installment Count</flux:label>
                            <flux:input type="number" wire:model="newInvoice.installment_count" min="1" />
                            @error('newInvoice.installment_count')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">
                        Buat Invoice
                    </flux:button>
                </div>
            </form>
        </div>
    @endif

    @if ($activeTab === 'add-items')
        <!-- Form Tambah Item -->
        <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
            <flux:heading size="md" class="text-zinc-100 mb-6">Tambah Item ke Invoice</flux:heading>

            <div class="space-y-6">
                <div>
                    <x-inputs.select name="selectedInvoiceId" :options="$draftInvoices" label="Pilih Invoice"
                        placeholder="Pilih Invoice Draft" />
                </div>

                @if ($selectedInvoiceId && count($availableServiceClients) > 0)
                    <div>
                        <flux:label class="text-zinc-300 mb-3 block">Pilih Service Items</flux:label>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach ($availableServiceClients as $serviceClient)
                                <label
                                    class="flex items-center space-x-3 p-3 bg-zinc-700 rounded-lg hover:bg-zinc-600 cursor-pointer">
                                    <input type="checkbox" wire:model="selectedServiceClientIds"
                                        value="{{ $serviceClient['id'] }}"
                                        class="rounded border-zinc-600 text-blue-600 focus:ring-blue-500 bg-zinc-800">
                                    <div class="flex-1">
                                        <div class="text-zinc-100 font-medium">{{ $serviceClient['service']['name'] }}
                                        </div>
                                        <div class="text-zinc-400 text-sm">
                                            {{ $this->formatDate($serviceClient['service_date']) }} -
                                            {{ $this->formatCurrency($serviceClient['amount']) }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <flux:button wire:click="addItemsToInvoice" variant="primary">
                            Tambah Item
                        </flux:button>
                    </div>
                @elseif($selectedInvoiceId)
                    <div class="text-center py-8 text-zinc-400">
                        Tidak ada service items yang tersedia untuk invoice ini
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($activeTab === 'payments')
        <!-- Payment Status Overview -->
        <div class="space-y-6">
            @foreach ($invoices as $invoice)
                @if ($invoice->status !== 'draft')
                    <div class="bg-zinc-800 rounded-xl p-6 border border-zinc-700">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <flux:heading size="md" class="text-zinc-100">{{ $invoice->invoice_number }}
                                </flux:heading>
                                <flux:text class="text-zinc-400">{{ $invoice->client->name }}</flux:text>
                            </div>
                            <flux:badge class="{{ $this->getStatusBadgeClass($invoice->status) }}">
                                {{ $this->getStatusLabel($invoice->status) }}
                            </flux:badge>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <flux:text class="text-zinc-400 text-sm">Total Amount</flux:text>
                                <flux:text class="text-zinc-100 font-medium">
                                    {{ $this->formatCurrency($invoice->total_amount) }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-400 text-sm">Amount Paid</flux:text>
                                <flux:text class="text-zinc-100 font-medium">
                                    {{ $this->formatCurrency($invoice->amount_paid) }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-400 text-sm">Amount Remaining</flux:text>
                                <flux:text class="text-zinc-100 font-medium">
                                    {{ $this->formatCurrency($invoice->amount_remaining) }}</flux:text>
                            </div>
                        </div>

                        @if ($invoice->amount_remaining > 0)
                            <flux:button size="sm" variant="primary"
                                wire:click="viewInvoice({{ $invoice->id }})">
                                Add Payment
                            </flux:button>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Flux Modal: Create Invoice -->
    <flux:modal name="create-invoice" class="md:w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Buat Invoice Baru</flux:heading>
                <flux:text class="mt-2">Buat invoice baru untuk klien Anda.</flux:text>
            </div>

            <form wire:submit="createInvoice" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-inputs.select name="newInvoice.billed_to_id" :options="$clients" label="Client"
                            placeholder="Pilih Client" :modal-mode="true" />
                        @error('newInvoice.billed_to_id')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-inputs.select name="newInvoice.payment_terms" :options="[
                            ['value' => 'full', 'label' => 'Full Payment'],
                            ['value' => 'installment', 'label' => 'Installment'],
                        ]" label="Payment Terms"
                            :modal-mode="true" />
                    </div>

                    <div>
                        <flux:label>Issue Date</flux:label>
                        <x-inputs.datepicker name="newInvoice.issue_date" placeholder="Tanggal Invoice"
                            class="w-full" />
                        @error('newInvoice.issue_date')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label>Due Date</flux:label>
                        <x-inputs.datepicker name="newInvoice.due_date" placeholder="Tanggal Jatuh Tempo"
                            class="w-full" />
                        @error('newInvoice.due_date')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($newInvoice['payment_terms'] === 'installment')
                        <div class="md:col-span-2">
                            <flux:label>Installment Count</flux:label>
                            <flux:input type="number" wire:model="newInvoice.installment_count" min="1" />
                            @error('newInvoice.installment_count')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        Buat Invoice
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Flux Modal: View Invoice -->
    <flux:modal name="view-invoice" class="md:w-4xl" wire:model.self="showViewModal">
        @if ($viewingInvoice)
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ $viewingInvoice->invoice_number }}</flux:heading>
                    <flux:badge class="{{ $this->getStatusBadgeClass($viewingInvoice->status) }}">
                        {{ $this->getStatusLabel($viewingInvoice->status) }}
                    </flux:badge>
                </div>

                <!-- Invoice Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:text class="text-zinc-400 text-sm">Client</flux:text>
                        <flux:text class="text-zinc-100 font-medium">{{ $viewingInvoice->client->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-400 text-sm">Issue Date</flux:text>
                        <flux:text class="text-zinc-100">{{ $this->formatDate($viewingInvoice->issue_date) }}
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-400 text-sm">Due Date</flux:text>
                        <flux:text class="text-zinc-100">{{ $this->formatDate($viewingInvoice->due_date) }}
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-zinc-400 text-sm">Payment Terms</flux:text>
                        <flux:text class="text-zinc-100">{{ ucfirst($viewingInvoice->payment_terms) }}</flux:text>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div>
                    <flux:heading size="md" class="text-zinc-100 mb-4">Invoice Items</flux:heading>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-900/50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-zinc-400 uppercase">Service
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-zinc-400 uppercase">Date
                                    </th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-zinc-400 uppercase">Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                @foreach ($viewingInvoice->items as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-zinc-100">{{ $item->serviceClient->service->name }}
                                        </td>
                                        <td class="px-4 py-2 text-zinc-300">
                                            {{ $this->formatDate($item->serviceClient->service_date) }}</td>
                                        <td class="px-4 py-2 text-right text-zinc-100">
                                            {{ $this->formatCurrency($item->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-zinc-900/50">
                                <tr>
                                    <td colspan="2" class="px-4 py-2 text-right font-medium text-zinc-100">Total:
                                    </td>
                                    <td class="px-4 py-2 text-right font-bold text-zinc-100">
                                        {{ $this->formatCurrency($viewingInvoice->total_amount) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment History -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="md" class="text-zinc-100">Payment History</flux:heading>
                        @if ($viewingInvoice->amount_remaining > 0)
                            <flux:modal.trigger name="add-payment">
                                <flux:button size="sm" variant="primary">
                                    Add Payment
                                </flux:button>
                            </flux:modal.trigger>
                        @endif
                    </div>

                    @if ($viewingInvoice->payments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-zinc-900/50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-400 uppercase">
                                            Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-400 uppercase">
                                            Method</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-400 uppercase">
                                            Bank Account</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-400 uppercase">
                                            Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-700">
                                    @foreach ($viewingInvoice->payments as $payment)
                                        <tr>
                                            <td class="px-4 py-2 text-zinc-100">
                                                {{ $this->formatDate($payment->payment_date) }}</td>
                                            <td class="px-4 py-2 text-zinc-300">
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td class="px-4 py-2 text-zinc-300">
                                                {{ $payment->bankAccount->account_name }}</td>
                                            <td class="px-4 py-2 text-right text-zinc-100">
                                                {{ $this->formatCurrency($payment->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-zinc-400">
                            No payments recorded yet
                        </div>
                    @endif
                </div>

                <!-- Payment Summary -->
                <div class="bg-zinc-900/50 rounded-lg p-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <flux:text class="text-zinc-400 text-sm">Total Amount</flux:text>
                            <flux:text class="text-zinc-100 font-bold">
                                {{ $this->formatCurrency($viewingInvoice->total_amount) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400 text-sm">Amount Paid</flux:text>
                            <flux:text class="text-green-400 font-bold">
                                {{ $this->formatCurrency($viewingInvoice->amount_paid) }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400 text-sm">Amount Remaining</flux:text>
                            <flux:text class="text-red-400 font-bold">
                                {{ $this->formatCurrency($viewingInvoice->amount_remaining) }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Flux Modal: Add Payment -->
    <flux:modal name="add-payment" class="md:w-lg">
        @if ($viewingInvoice)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Payment</flux:heading>
                    <flux:text class="mt-2">Record a payment for {{ $viewingInvoice->invoice_number }}</flux:text>
                </div>

                <form wire:submit="addPayment" class="space-y-4">
                    <div>
                        <flux:label>Amount</flux:label>
                        <flux:input type="number" step="0.01" wire:model="paymentForm.amount"
                            max="{{ $viewingInvoice->amount_remaining }}" placeholder="Jumlah pembayaran" />
                        <flux:text class="text-zinc-400 text-sm">Max:
                            {{ $this->formatCurrency($viewingInvoice->amount_remaining) }}</flux:text>
                        @error('paymentForm.amount')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label>Payment Date</flux:label>
                        <x-inputs.datepicker name="paymentForm.payment_date" placeholder="Tanggal Pembayaran"
                            class="w-full" />
                        @error('paymentForm.payment_date')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-inputs.select name="paymentForm.payment_method" :options="$paymentMethodOptions" label="Payment Method"
                            :modal-mode="true" />
                    </div>

                    <div>
                        <x-inputs.select name="paymentForm.bank_account_id" :options="$bankAccounts" label="Bank Account"
                            placeholder="Select Bank Account" :modal-mode="true" />
                        @error('paymentForm.bank_account_id')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:label>Reference Number</flux:label>
                        <flux:input wire:model="paymentForm.reference_number" placeholder="Optional reference" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">
                            Add Payment
                        </flux:button>
                    </div>
                </form>
            </div>
        @endif
    </flux:modal>

    <!-- Quick Actions Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:modal.trigger name="create-invoice">
            <div
                class="bg-zinc-800 rounded-xl p-6 border border-zinc-700 hover:border-zinc-600 transition-colors cursor-pointer w-full">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500/10 p-3 rounded-lg">
                        <flux:icon.plus class="w-6 h-6 text-blue-500" />
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-zinc-100">Buat Invoice</flux:heading>
                        <flux:text class="text-zinc-400 text-sm">Buat invoice baru untuk klien</flux:text>
                    </div>
                </div>
            </div>
        </flux:modal.trigger>

        <div wire:click="$set('activeTab', 'add-items')"
            class="bg-zinc-800 rounded-xl p-6 border border-zinc-700 hover:border-zinc-600 transition-colors cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="bg-green-500/10 p-3 rounded-lg">
                    <flux:icon.plus class="w-6 h-6 text-green-500" />
                </div>
                <div>
                    <flux:heading size="sm" class="text-zinc-100">Tambah Item</flux:heading>
                    <flux:text class="text-zinc-400 text-sm">Tambah item ke invoice</flux:text>
                </div>
            </div>
        </div>

        <div wire:click="$set('activeTab', 'list')"
            class="bg-zinc-800 rounded-xl p-6 border border-zinc-700 hover:border-zinc-600 transition-colors cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-500/10 p-3 rounded-lg">
                    <flux:icon.eye class="w-6 h-6 text-purple-500" />
                </div>
                <div>
                    <flux:heading size="sm" class="text-zinc-100">Lihat Invoice</flux:heading>
                    <flux:text class="text-zinc-400 text-sm">Review detail invoice</flux:text>
                </div>
            </div>
        </div>

        <div wire:click="$set('activeTab', 'payments')"
            class="bg-zinc-800 rounded-xl p-6 border border-zinc-700 hover:border-zinc-600 transition-colors cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="bg-orange-500/10 p-3 rounded-lg">
                    <flux:icon.credit-card class="w-6 h-6 text-orange-500" />
                </div>
                <div>
                    <flux:heading size="sm" class="text-zinc-100">Status Pembayaran</flux:heading>
                    <flux:text class="text-zinc-400 text-sm">Cek status pembayaran</flux:text>
                </div>
            </div>
        </div>
    </div>
</section>
