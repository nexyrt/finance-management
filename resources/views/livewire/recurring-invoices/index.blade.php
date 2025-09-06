<div class="space-y-6">
    <!-- Header Section -->
    <div class="space-y-1">
        <h1
            class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
            Recurring Invoices
        </h1>
        <p class="text-gray-600 dark:text-zinc-400 text-lg">
            Kelola template dan jadwal invoice berulang Anda di sini.
        </p>
    </div>

    <!-- Stats Section -->
    <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-6 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="text-center sm:text-left">
                    <div class="text-2xl font-bold">{{ $this->templates->count() }}</div>
                    <div class="text-primary-100 text-sm">Active Templates</div>
                </div>
                <div class="hidden sm:block w-px bg-primary-400"></div>
                <div class="text-center sm:text-left">
                    <div class="text-2xl font-bold">
                        Rp {{ number_format($this->yearlyProjection['total_revenue'], 0, ',', '.') }}
                    </div>
                    <div class="text-primary-100 text-sm">Projected {{ $selectedYear }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <x-tab selected="Templates" wire:model="activeTab">
        <x-tab.items tab="Templates">
            <x-slot:left>
                <x-icon name="document-duplicate" class="w-5 h-5" />
            </x-slot:left>

            <div class="space-y-6 mt-6">
                <!-- Action Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <x-badge text="{{ $this->templates->where('status', 'active')->count() }} Active"
                            color="green" />
                        <x-badge text="{{ $this->templates->where('status', 'inactive')->count() }} Inactive"
                            color="gray" />
                    </div>
                    <livewire:recurring-invoices.create-template />
                </div>

                <!-- Templates Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                    @forelse($this->templates as $template)
                        <div
                            class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-gray-200 dark:border-dark-600 hover:shadow-lg hover:scale-105 transition-all duration-200 group">
                            <div class="p-4 lg:p-6">
                                <!-- Template Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-dark-50 text-sm lg:text-base truncate"
                                            title="{{ $template->client->name }}">
                                            {{ $template->client->name }}
                                        </h3>
                                    </div>
                                    <div class="ml-2 flex-shrink-0">
                                        <x-badge :text="ucfirst($template->status)" :color="$template->status === 'active' ? 'green' : 'gray'" xs />
                                    </div>
                                </div>

                                <!-- Template Stats -->
                                <div class="space-y-2 lg:space-y-3 mb-4">
                                    <div class="flex justify-between text-xs lg:text-sm">
                                        <span class="text-gray-500 dark:text-dark-400">Amount</span>
                                        <span class="font-medium text-gray-900 dark:text-dark-50 text-xs lg:text-sm">
                                            {{ $template->formatted_total_amount }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-xs lg:text-sm items-center">
                                        <span class="text-gray-500 dark:text-dark-400">Frequency</span>
                                        <x-badge :text="ucfirst(str_replace('_', ' ', $template->frequency))" color="blue" xs />
                                    </div>
                                    <div class="flex justify-between text-xs lg:text-sm">
                                        <span class="text-gray-500 dark:text-dark-400">Duration</span>
                                        <span class="text-gray-900 dark:text-dark-50 text-xs">
                                            {{ $template->start_date->format('M y') }} -
                                            {{ $template->end_date->format('M y') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-xs lg:text-sm">
                                        <span class="text-gray-500 dark:text-dark-400">Remaining</span>
                                        <span
                                            class="text-gray-900 dark:text-dark-50 text-xs">{{ $template->remaining_invoices }}
                                            invoices</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                @php
                                    $totalInvoices = $template->recurringInvoices->count();
                                    $published = $template->recurringInvoices->where('status', 'published')->count();
                                    $progress = $totalInvoices > 0 ? ($published / $totalInvoices) * 100 : 0;
                                @endphp
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-dark-400 mb-1">
                                        <span>Progress</span>
                                        <span>{{ $published }}/{{ $totalInvoices }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-dark-600 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-500 ease-out"
                                            style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <x-button wire:click="editTemplate({{ $template->id }})" color="green"
                                        size="sm" outline class="flex-1 text-xs">
                                        <x-icon name="pencil" class="w-3 h-3 lg:w-4 lg:h-4 lg:mr-1" />
                                        <span class="hidden lg:inline">Edit</span>
                                    </x-button>
                                    <x-button color="blue" size="sm" outline class="flex-1 text-xs">
                                        <x-icon name="eye" class="w-3 h-3 lg:w-4 lg:h-4 lg:mr-1" />
                                        <span class="hidden lg:inline">View</span>
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-12">
                                <x-icon name="document-plus" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                <h3 class="text-lg font-medium text-gray-900 dark:text-dark-50 mb-2">No Templates Yet
                                </h3>
                                <p class="text-gray-500 dark:text-dark-400 mb-6">Create your first recurring invoice
                                    template to get started</p>
                                <x-button wire:click="$toggle('modal')" color="primary" icon="plus">
                                    Create First Template
                                </x-button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-tab.items>

        <x-tab.items tab="Monthly View">
            <x-slot:left>
                <x-icon name="calendar-days" class="w-5 h-5" />
            </x-slot:left>

            <div class="space-y-6 mt-6">
                <!-- Month Selection -->
                <div
                    class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-gray-200 dark:border-dark-600 p-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-dark-50">
                            {{ now()->month($selectedMonth)->format('F Y') }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach (range(1, 12) as $month)
                                <x-button wire:click="selectMonth({{ $month }})" size="sm"
                                    :color="$selectedMonth === $month ? 'primary' : 'secondary'" :outline="$selectedMonth !== $month">
                                    {{ now()->month($month)->format('M') }}
                                </x-button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Monthly Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-gray-200 dark:border-dark-600 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-dark-400">Total Invoices</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-dark-50">
                                    {{ $this->monthlyStats['total_count'] }}</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-full">
                                <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-gray-200 dark:border-dark-600 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-dark-400">Draft</p>
                                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                    {{ $this->monthlyStats['draft_count'] }}</p>
                            </div>
                            <div class="bg-amber-100 dark:bg-amber-900/20 p-3 rounded-full">
                                <x-icon name="clock" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-gray-200 dark:border-dark-600 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-dark-400">Published</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $this->monthlyStats['published_count'] }}</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900/20 p-3 rounded-full">
                                <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-gray-200 dark:border-dark-600 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-dark-400">Total Value</p>
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($this->monthlyStats['total_amount'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-primary-100 dark:bg-primary-900/20 p-3 rounded-full">
                                <x-icon name="currency-dollar"
                                    class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                @if ($this->monthlyInvoices->where('status', 'draft')->count() > 0)
                    <div
                        class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <x-icon name="exclamation-triangle"
                                    class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                <div>
                                    <p class="font-medium text-amber-900 dark:text-amber-200">
                                        {{ $this->monthlyStats['draft_count'] }} drafts ready to publish
                                    </p>
                                    <p class="text-sm text-amber-700 dark:text-amber-300">
                                        Total value: Rp
                                        {{ number_format($this->monthlyStats['draft_amount'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <x-button wire:click="bulkPublish" color="amber" size="sm">
                                <x-icon name="paper-airplane" class="w-4 h-4 mr-2" />
                                Bulk Publish
                            </x-button>
                        </div>
                    </div>
                @endif

                {{-- Bulk Actions Bar --}}
                <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
                    class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
                    <div
                        class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                    <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="font-semibold text-dark-900 dark:text-dark-50"
                                        x-text="`${show.length} invoice dipilih`"></div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">
                                        Pilih aksi untuk invoice yang dipilih
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 justify-end">
                                <x-button wire:click="bulkPublish" size="sm" color="green"
                                    icon="paper-airplane" loading="bulkPublish" class="whitespace-nowrap">
                                    Publish All
                                </x-button>
                                <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                                    loading="bulkDelete" class="whitespace-nowrap">
                                    Hapus
                                </x-button>
                                <x-button wire:click="$set('selected', [])" size="sm" color="gray"
                                    icon="x-mark" class="whitespace-nowrap">
                                    Batal
                                </x-button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Invoices Table -->
                <x-table :headers="[
                    ['index' => 'client', 'label' => 'Client'],
                    ['index' => 'template', 'label' => 'Template', 'sortable' => false],
                    ['index' => 'scheduled_date', 'label' => 'Date'],
                    ['index' => 'total_amount', 'label' => 'Amount'],
                    ['index' => 'status', 'label' => 'Status', 'sortable' => false],
                    ['index' => 'actions', 'label' => 'Actions', 'sortable' => false],
                ]" :rows="$this->monthlyInvoices" selectable wire:model="selected" id="monthly-invoices">

                    @interact('column_client', $row)
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">
                                    {{ strtoupper(substr($row->client->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-dark-50">{{ $row->client->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-dark-400">{{ $row->client->type }}</div>
                            </div>
                        </div>
                    @endinteract

                    @interact('column_template', $row)
                        <div class="text-sm text-gray-900 dark:text-dark-50">
                            {{ $row->template->template_name }}
                        </div>
                    @endinteract

                    @interact('column_scheduled_date', $row)
                        <div class="text-sm text-gray-900 dark:text-dark-50">
                            {{ $row->scheduled_date->format('d M Y') }}
                        </div>
                    @endinteract

                    @interact('column_total_amount', $row)
                        <div class="text-sm font-medium text-gray-900 dark:text-dark-50">
                            {{ $row->formatted_total_amount }}
                        </div>
                    @endinteract

                    @interact('column_status', $row)
                        <x-badge :text="ucfirst($row->status)" :color="$row->status === 'published' ? 'green' : 'amber'" sm />
                    @endinteract

                    @interact('column_actions', $row)
                        <div class="flex justify-end gap-2">
                            @if ($row->status === 'draft')
                                <x-button wire:click="publishDraft({{ $row->id }})" color="green" size="sm"
                                    outline>
                                    <x-icon name="paper-airplane" class="w-4 h-4" />
                                </x-button>
                            @else
                                <x-button color="blue" size="sm" outline>
                                    <x-icon name="eye" class="w-4 h-4" />
                                </x-button>
                            @endif
                            <x-button color="gray" size="sm" outline>
                                <x-icon name="pencil" class="w-4 h-4" />
                            </x-button>
                        </div>
                    @endinteract
                </x-table>
            </div>
        </x-tab.items>

        <x-tab.items tab="Analytics">
            <x-slot:left>
                <x-icon name="chart-bar" class="w-5 h-5" />
            </x-slot:left>

            <div class="space-y-6 mt-6">
                <div class="text-center py-12">
                    <x-icon name="chart-bar" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-xl font-medium text-gray-900 dark:text-dark-50 mb-2">Analytics Dashboard</h3>
                    <p class="text-gray-500 dark:text-dark-400 mb-6">Advanced analytics and reporting features coming
                        soon</p>
                    <x-badge text="On Progress" color="amber" />
                </div>
            </div>
        </x-tab.items>
    </x-tab>

    <!-- Floating Components (Will be implemented separately) -->
    <div wire:ignore>
        <!-- Edit Template Modal - On Progress -->
        <!-- Publish Draft Modal - On Progress -->
    </div>
</div>
