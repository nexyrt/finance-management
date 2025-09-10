<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <x-select.styled wire:model.live="currentMonth" :options="$this->monthOptions" label="Month" />
            <x-select.styled wire:model.live="currentYear" :options="$this->yearOptions" label="Year" />
            <x-select.styled wire:model.live="selectedTemplate" :options="$this->templates" placeholder="All Templates"
                label="Template" />
            <x-select.styled wire:model.live="statusFilter" :options="[
                ['label' => 'All Status', 'value' => 'all'],
                ['label' => 'Draft', 'value' => 'draft'],
                ['label' => 'Published', 'value' => 'published'],
            ]" label="Status" />
        </div>

        <div class="flex gap-2">
            <x-button wire:click="generateInvoices" color="primary" icon="plus" loading="generateInvoices">
                Generate Invoices
            </x-button>
            <livewire:recurring-invoices.monthly.create-invoice @invoice-created="$refresh" />
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Invoices</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $this->invoices->total() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Published</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                        {{ $this->invoices->where('status', 'published')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="clock" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Draft</p>
                    <p class="text-xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $this->invoices->where('status', 'draft')->count() }}
                    </p>
                    <p class="text-xs text-amber-500 dark:text-amber-400">
                        Ready to publish
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Value</p>
                    <p class="text-xl font-bold text-purple-600 dark:text-purple-400">
                        Rp {{ number_format($this->stats['total_revenue'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-purple-500 dark:text-purple-400">
                        This month
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div x-data="{ selected: @entangle('selected').live }" x-show="selected.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-gray-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-50"
                            x-text="`${selected.length} invoices selected`"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Choose action for selected invoices</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="bulkPublish" size="sm" color="primary" icon="arrow-up-tray"
                        loading="bulkPublish" class="whitespace-nowrap">
                        Bulk Publish
                    </x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash" loading="bulkDelete"
                        class="whitespace-nowrap">
                        Bulk Delete
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <x-table :headers="[
            ['index' => 'client', 'label' => 'Client'],
            ['index' => 'template', 'label' => 'Template'],
            ['index' => 'scheduled_date', 'label' => 'Scheduled Date'],
            ['index' => 'amount', 'label' => 'Amount'],
            ['index' => 'status', 'label' => 'Status'],
            ['index' => 'actions', 'label' => 'Actions', 'sortable' => false],
        ]" :rows="$this->invoices" selectable wire:model="selected" paginate>

            @interact('column_client', $row)
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">
                            {{ strtoupper(substr($row->client->name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $row->client->name }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst($row->client->type) }}</div>
                    </div>
                </div>
            @endinteract

            @interact('column_template', $row)
                <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $row->template->template_name }}</div>
                    <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $row->template->frequency)) }}</div>
                </div>
            @endinteract

            @interact('column_scheduled_date', $row)
                <div class="text-sm">
                    <div class="font-medium">{{ $row->scheduled_date->format('d M Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $row->scheduled_date->format('l') }}</div>
                </div>
            @endinteract

            @interact('column_amount', $row)
                <div class="text-right">
                    <div class="font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                    </div>
                    @if ($row->invoice_data['discount_amount'] ?? 0 > 0)
                        <div class="text-xs text-red-500">
                            -Rp {{ number_format($row->invoice_data['discount_amount'], 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            @endinteract

            @interact('column_status', $row)
                <div class="space-y-1">
                    <x-badge :text="ucfirst($row->status)" :color="$row->status === 'published' ? 'green' : 'amber'" />
                    @if ($row->status === 'published' && $row->publishedInvoice)
                        <div class="text-xs text-green-600 dark:text-green-400">
                            #{{ $row->publishedInvoice->invoice_number }}
                        </div>
                    @endif
                </div>
            @endinteract

            @interact('column_actions', $row)
                <div class="flex gap-1">
                    <x-button.circle wire:click="viewInvoice({{ $row->id }})"
                        loading="viewInvoice({{ $row->id }})" color="blue" size="sm" icon="eye" />

                    @if ($row->status === 'draft')
                        <x-button.circle wire:click="editInvoice({{ $row->id }})"
                            loading="editInvoice({{ $row->id }})" color="green" size="sm" icon="pencil" />
                        <x-button.circle wire:click="publishInvoice({{ $row->id }})"
                            loading="publishInvoice({{ $row->id }})" color="primary" size="sm"
                            icon="arrow-up-tray" />
                    @endif

                    <livewire:recurring-invoices.monthly.delete-invoice :invoice="$row" :key="uniqid()"
                        @invoice-deleted="$refresh" />
                </div>
            @endinteract
        </x-table>
    </div>

    <!-- Include Child Components -->
    <livewire:recurring-invoices.monthly.view-invoice />
    <livewire:recurring-invoices.monthly.edit-invoice @invoice-updated="$refresh" />
</div>
