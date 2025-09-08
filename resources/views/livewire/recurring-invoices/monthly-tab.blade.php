<div class="space-y-6">
    <!-- Month Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ now()->month($selectedMonth)->format('F Y') }}
                </h2>
                <livewire:recurring-invoices.monthly.create-invoice />
            </div>
            <div class="grid grid-cols-6 lg:grid-cols-12 gap-2">
                @foreach (range(1, 12) as $month)
                    <x-button wire:click="selectMonth({{ $month }})" loading="selectMonth({{ $month }})"
                        size="sm" :color="$selectedMonth === $month ? 'primary' : 'gray'" :outline="$selectedMonth !== $month">
                        {{ now()->month($month)->format('M') }}
                    </x-button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <x-icon name="document-text" class="w-8 h-8 text-blue-100" />
                <div>
                    <div class="text-2xl font-bold">{{ $this->monthlyStats['total_count'] }}</div>
                    <div class="text-blue-100 text-sm">Total</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <x-icon name="clock" class="w-8 h-8 text-amber-100" />
                <div>
                    <div class="text-2xl font-bold">{{ $this->monthlyStats['draft_count'] }}</div>
                    <div class="text-amber-100 text-sm">Draft</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <x-icon name="check-circle" class="w-8 h-8 text-green-100" />
                <div>
                    <div class="text-2xl font-bold">{{ $this->monthlyStats['published_count'] }}</div>
                    <div class="text-green-100 text-sm">Published</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <x-icon name="currency-dollar" class="w-8 h-8 text-purple-100" />
                <div>
                    <div class="text-lg font-bold">
                        Rp {{ number_format($this->monthlyStats['total_amount'] / 1000000, 1) }}M
                    </div>
                    <div class="text-purple-100 text-sm">Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Draft Alert -->
    @if ($this->monthlyStats['draft_count'] > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                    <div>
                        <p class="font-medium text-amber-900 dark:text-amber-200">
                            {{ $this->monthlyStats['draft_count'] }} drafts ready
                        </p>
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            Value: Rp {{ number_format($this->monthlyStats['draft_amount'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <x-button wire:click="bulkPublish" color="amber">
                    <x-icon name="paper-airplane" class="w-4 h-4 mr-2" />
                    Publish All
                </x-button>
            </div>
        </div>
    @endif

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <x-table :headers="[
            ['index' => 'client', 'label' => 'Client'],
            ['index' => 'template', 'label' => 'Template', 'sortable' => false],
            ['index' => 'scheduled_date', 'label' => 'Date'],
            ['index' => 'total_amount', 'label' => 'Amount'],
            ['index' => 'status', 'label' => 'Status', 'sortable' => false],
            ['index' => 'actions', 'label' => '', 'sortable' => false],
        ]" :rows="$this->monthlyInvoices" selectable wire:model="selected">

            @interact('column_client', $row)
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-primary-500 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">
                            {{ strtoupper(substr($row->client->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">
                            {{ $row->client->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $row->client->type }}
                        </div>
                    </div>
                </div>
            @endinteract

            @interact('column_template', $row)
                <div class="text-gray-900 dark:text-white">
                    {{ $row->template->template_name }}
                </div>
            @endinteract

            @interact('column_scheduled_date', $row)
                <div class="text-gray-900 dark:text-white">
                    {{ $row->scheduled_date->format('d M Y') }}
                </div>
            @endinteract

            @interact('column_total_amount', $row)
                <div class="font-bold text-gray-900 dark:text-white">
                    {{ $row->formatted_total_amount }}
                </div>
            @endinteract

            @interact('column_status', $row)
                <x-badge :text="ucfirst($row->status)" :color="$row->status === 'published' ? 'green' : 'amber'" />
            @endinteract

            @interact('column_actions', $row)
                <div class="flex justify-end gap-1">
                    @if ($row->status === 'draft')
                        <x-button wire:click="editInvoice({{ $row->id }})" color="blue" size="sm" outline
                            loading="editInvoice({{ $row->id }})">
                            <x-icon name="pencil" class="w-4 h-4" />
                        </x-button>
                        <livewire:recurring-invoices.monthly.delete-invoice :invoice="$row" :key="uniqid()"
                            @invoice-deleted="$refresh" />
                    @else
                        <x-button color="blue" size="sm" outline>
                            <x-icon name="eye" class="w-4 h-4" />
                        </x-button>
                    @endif
                </div>
            @endinteract
        </x-table>
    </div>

    <!-- Floating Bulk Actions -->
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 px-6 py-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white" x-text="`${show.length} selected`">
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Choose action</div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <x-button wire:click="bulkPublish" size="sm" color="green" loading="bulkPublish">
                        <x-icon name="paper-airplane" class="w-4 h-4 mr-1" />
                        Publish
                    </x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" loading="bulkDelete">
                        <x-icon name="trash" class="w-4 h-4 mr-1" />
                        Delete
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray">
                        <x-icon name="x-mark" class="w-4 h-4" />
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    <livewire:recurring-invoices.monthly.edit-invoice @invoice-updated="$refresh" />
</div>
