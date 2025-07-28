<div>
    <x-modal wire="showModal" title="Detail Invoice" size="4xl" center id="invoice-show-modal"
        x-on:close="$wire.resetData()">
        @if ($invoice)
            {{-- Compact Header --}}
            <div
                class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 -m-4 mb-6 p-4">
                <div class="flex items-center justify-between">
                    {{-- Left: Invoice Info --}}
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 {{ $invoice->client->type === 'individual'
                                ? 'bg-gradient-to-br from-blue-400 to-blue-600'
                                : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                            rounded-xl flex items-center justify-center shadow-lg">
                            <x-icon name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white font-mono">
                                {{ $invoice->invoice_number }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client->name }}</p>
                        </div>
                    </div>

                    {{-- Center: Status --}}
                    @php
                        $statusConfig = [
                            'draft' => ['color' => 'gray', 'text' => 'Draft', 'icon' => 'document'],
                            'sent' => ['color' => 'blue', 'text' => 'Terkirim', 'icon' => 'paper-airplane'],
                            'paid' => ['color' => 'green', 'text' => 'Lunas', 'icon' => 'check-circle'],
                            'partially_paid' => [
                                'color' => 'yellow',
                                'text' => 'Sebagian',
                                'icon' => 'currency-dollar',
                            ],
                            'overdue' => ['color' => 'red', 'text' => 'Terlambat', 'icon' => 'exclamation-triangle'],
                        ];
                        $config = $statusConfig[$invoice->status] ?? $statusConfig['draft'];
                    @endphp
                    <div
                        class="inline-flex items-center gap-2 px-3 py-2 bg-{{ $config['color'] }}-100 dark:bg-{{ $config['color'] }}-900/30 text-{{ $config['color'] }}-800 dark:text-{{ $config['color'] }}-200 rounded-lg">
                        <x-icon name="{{ $config['icon'] }}" class="w-4 h-4" />
                        <span class="font-medium">{{ $config['text'] }}</span>
                    </div>

                    {{-- Right: Amount --}}
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </p>
                        @if ($invoice->amount_paid > 0)
                            @php $paymentPercentage = ($invoice->amount_paid / $invoice->total_amount) * 100; @endphp
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-green-500 h-1.5 rounded-full"
                                        style="width: {{ min($paymentPercentage, 100) }}%"></div>
                                </div>
                                <span
                                    class="text-xs text-green-600 font-medium">{{ number_format($paymentPercentage, 0) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabs Content --}}
            <x-tab selected="overview">

                {{-- Tab 1: Overview --}}
                <x-tab.items tab="overview">
                    <x-slot:left>
                        <x-icon name="document-text" class="w-4 h-4" />
                    </x-slot:left>
                    Ringkasan

                    <div class="space-y-4">
                        {{-- Quick Info Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Invoice</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $invoice->issue_date->format('d M Y') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jatuh Tempo</p>
                                <p
                                    class="font-medium {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Item</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->items->count() }}
                                    item</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pembayaran</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->payments->count() }}x
                                </p>
                            </div>
                        </div>

                        {{-- Invoice Items Compact --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div
                                class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                <h4 class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                    <x-icon name="list-bullet" class="w-4 h-4" />
                                    Item Invoice
                                </h4>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($invoice->items as $item)
                                    <div
                                        class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 {{ $item->client->type === 'individual' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }} rounded-lg flex items-center justify-center">
                                                <x-icon
                                                    name="{{ $item->client->type === 'individual' ? 'user' : 'building-office' }}"
                                                    class="w-4 h-4" />
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white text-sm">
                                                    {{ $item->service_name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $item->client->name }} • Qty: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900 dark:text-white">Rp
                                                {{ number_format($item->amount, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">@ Rp
                                                {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div
                                class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900 dark:text-white">Total Invoice</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Rp
                                        {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab.items>

                {{-- Tab 2: Payments --}}
                <x-tab.items tab="payments">
                    <x-slot:left>
                        <x-icon name="credit-card" class="w-4 h-4" />
                    </x-slot:left>
                    Pembayaran
                    <x-slot:right>
                        <x-badge text="{{ $invoice->payments->count() }}" color="green" />
                    </x-slot:right>

                    @if ($invoice->payments->count() > 0)
                        {{-- Payment Summary --}}
                        @php
                            $totalPaid = $invoice->amount_paid;
                            $remaining = $invoice->amount_remaining;
                            $percentage = ($totalPaid / $invoice->total_amount) * 100;
                        @endphp

                        <div
                            class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Terbayar</p>
                                    <p class="text-xl font-bold text-green-700 dark:text-green-300">Rp
                                        {{ number_format($totalPaid, 0, ',', '.') }}</p>
                                </div>
                                @if ($remaining > 0)
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Sisa Tagihan</p>
                                        <p class="text-xl font-bold text-red-600 dark:text-red-400">Rp
                                            {{ number_format($remaining, 0, ',', '.') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="w-full bg-white dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                {{ number_format($percentage, 1) }}% dari total invoice</p>
                        </div>

                        {{-- Payment List --}}
                        <div class="space-y-3">
                            @foreach ($invoice->payments as $payment)
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                                <x-icon name="banknotes" class="w-5 h-5 text-green-600" />
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">Rp
                                                    {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $payment->payment_date->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $payment->bankAccount->bank_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ ucfirst($payment->payment_method) }}</p>
                                            @if ($payment->reference_number)
                                                <p class="text-xs font-mono text-gray-400">
                                                    {{ $payment->reference_number }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-icon name="credit-card" class="w-8 h-8 text-gray-400" />
                            </div>
                            <h3 class="font-medium text-gray-900 dark:text-white mb-2">Belum Ada Pembayaran</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Invoice ini belum menerima pembayaran</p>
                            @if (in_array($invoice->status, ['sent', 'overdue', 'partially_paid']))
                                <x-button wire:click="recordPayment" color="green" icon="plus" size="sm">
                                    Catat Pembayaran
                                </x-button>
                            @endif
                        </div>
                    @endif
                </x-tab.items>

                {{-- Tab 3: Details --}}
                <x-tab.items tab="details">
                    <x-slot:left>
                        <x-icon name="information-circle" class="w-4 h-4" />
                    </x-slot:left>
                    Detail

                    <div class="space-y-6">
                        {{-- Client Details --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <x-icon
                                    name="{{ $invoice->client->type === 'individual' ? 'user' : 'building-office' }}"
                                    class="w-4 h-4" />
                                Informasi Klien
                            </h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Nama</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->client->name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Tipe</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ ucfirst($invoice->client->type) }}</p>
                                </div>
                                @if ($invoice->client->email)
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Email</p>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $invoice->client->email }}</p>
                                    </div>
                                @endif
                                @if ($invoice->client->NPWP)
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">NPWP</p>
                                        <p class="font-medium text-gray-900 dark:text-white font-mono">
                                            {{ $invoice->client->NPWP }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Invoice Timeline --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <x-icon name="clock" class="w-4 h-4" />
                                Timeline
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Invoice Dibuat</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $invoice->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>

                                @if ($invoice->status !== 'draft')
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Invoice
                                                Dikirim</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $invoice->issue_date->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                @endif

                                @foreach ($invoice->payments as $payment)
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Pembayaran
                                                Diterima</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $payment->payment_date->format('d M Y') }} • Rp
                                                {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-red-600">Melewati Jatuh Tempo</p>
                                            <p class="text-xs text-red-500">{{ $invoice->due_date->format('d M Y') }}
                                                • {{ $invoice->due_date->diffInDays(now()) }} hari yang lalu</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-tab.items>

            </x-tab>
        @endif

        {{-- Compact Footer --}}
        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                {{-- Quick Actions --}}
                <div class="flex items-center gap-2">
                    @if ($invoice)
                        <x-button wire:click="downloadPdf" color="secondary" icon="arrow-down-tray" outline
                            size="sm">
                            PDF
                        </x-button>

                        @if ($invoice->status === 'draft')
                            <x-button wire:click="sendInvoice" color="blue" icon="paper-airplane" size="sm">
                                Kirim
                            </x-button>
                        @endif

                        @if (in_array($invoice->status, ['sent', 'overdue', 'partially_paid']))
                            <x-button wire:click="recordPayment" color="green" icon="currency-dollar">
                                Bayar
                            </x-button>
                        @endif
                    @endif
                </div>

                {{-- Main Actions --}}
                <div class="flex items-center gap-2">
                    @if ($invoice && $invoice->status === 'draft')
                        <x-button wire:click="editInvoice" color="secondary" icon="pencil" outline size="sm">
                            Edit
                        </x-button>
                    @endif

                    @if ($invoice)
                        <x-button wire:click="duplicateInvoice" color="secondary" icon="document-duplicate" outline
                            size="sm">
                            Duplikasi
                        </x-button>
                    @endif

                    <x-button x-on:click="$modalClose('invoice-show-modal')" color="secondary">
                        Tutup
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>