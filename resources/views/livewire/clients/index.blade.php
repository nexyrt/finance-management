{{-- resources/views/livewire/clients/index.blade.php --}}

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Client Management</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your clients and track their business relationships
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-button href="#" icon="plus" color="primary">
                Add Client
            </x-button>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Clients</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rows->total() }}</p>
                </div>
                <x-icon name="users" class="w-8 h-8 text-blue-500" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ $rows->where('status', 'Active')->count() }}</p>
                </div>
                <x-icon name="check-circle" class="w-8 h-8 text-green-500" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Companies</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $rows->where('type', 'company')->count() }}</p>
                </div>
                <x-icon name="building-office" class="w-8 h-8 text-purple-500" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Individuals</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $rows->where('type', 'individual')->count() }}</p>
                </div>
                <x-icon name="user" class="w-8 h-8 text-indigo-500" />
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-select.styled label="Filter by Type" wire:model.live="typeFilter" placeholder="All Types">
                    <x-select.option value="" text="All Types" />
                    <x-select.option value="individual" text="Individual" />
                    <x-select.option value="company" text="Company" />
                </x-select.styled>
            </div>

            <div>
                <x-select.styled label="Filter by Status" wire:model.live="statusFilter" placeholder="All Status">
                    <x-select.option value="" text="All Status" />
                    <x-select.option value="Active" text="Active" />
                    <x-select.option value="Inactive" text="Inactive" />
                </x-select.styled>
            </div>

            <div class="flex items-end">
                @if ($typeFilter || $statusFilter)
                    <x-button wire:click="$set('typeFilter', null); $set('statusFilter', null)" color="secondary"
                        icon="x-mark" class="w-full">
                        Clear Filters
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    @if (count($selected) > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-icon name="check-circle" class="w-5 h-5 text-blue-600" />
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        {{ count($selected) }} client(s) selected
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <x-button wire:click="bulkActivate" color="green" size="sm"
                        wire:confirm="Are you sure you want to activate the selected clients?">
                        Activate
                    </x-button>

                    <x-button wire:click="bulkDeactivate" color="yellow" size="sm"
                        wire:confirm="Are you sure you want to deactivate the selected clients?">
                        Deactivate
                    </x-button>

                    <x-button wire:click="bulkDelete" color="red" size="sm"
                        wire:confirm="Are you sure you want to delete the selected clients? This action cannot be undone.">
                        Delete
                    </x-button>
                </div>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <x-table :$headers :$rows :$sort filter :quantity="[5, 10, 25, 50]" loading paginate selectable wire:model.live="selected">

        {{-- Client Name with Avatar --}}
        @interact('column_name', $row)
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 flex-shrink-0">
                    @if ($row->logo)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $row->logo }}"
                            alt="{{ $row->name }}">
                    @else
                        <div
                            class="h-10 w-10 rounded-full flex items-center justify-center
                                {{ $row->type === 'individual' ? 'bg-blue-100 dark:bg-blue-900/20' : 'bg-purple-100 dark:bg-purple-900/20' }}">
                            <x-icon name="{{ $row->type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-5 h-5 {{ $row->type === 'individual' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" />
                        </div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ $row->name }}
                    </p>
                    @if ($row->NPWP)
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            NPWP: {{ $row->NPWP }}
                        </p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Type Column --}}
        @interact('column_type', $row)
            <x-badge text="{{ ucfirst($row->type) }}" color="{{ $row->type === 'individual' ? 'blue' : 'purple' }}" />
        @endinteract

        {{-- Contact Info --}}
        @interact('column_contact', $row)
            <div class="text-sm">
                @if ($row->email)
                    <a href="mailto:{{ $row->email }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline block truncate">
                        {{ $row->email }}
                    </a>
                @else
                    <span class="text-gray-400 dark:text-gray-500 italic">No email</span>
                @endif

                @if ($row->ar_phone_number)
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $row->ar_phone_number }}
                    </p>
                @endif
            </div>
        @endinteract

        {{-- Status Column --}}
        @interact('column_status', $row)
            <x-badge text="{{ $row->status }}" color="{{ $row->status === 'Active' ? 'green' : 'red' }}" />
        @endinteract

        {{-- Invoices Count --}}
        @interact('column_invoices_count', $row)
            <div class="text-center">
                <x-badge text="{{ $row->invoices_count }}" color="{{ $row->invoices_count > 0 ? 'blue' : 'gray' }}" />
            </div>
        @endinteract

        {{-- Financial Summary --}}
        @interact('column_financial_summary', $row)
            <div class="text-right text-sm">
                @php
                    $totalAmount = $row->invoices->sum('total_amount');
                    $paidAmount = $row->invoices->filter(fn($inv) => $inv->status === 'paid')->sum('total_amount');
                    $outstandingAmount = $totalAmount - $paidAmount;
                @endphp

                <div class="font-medium text-gray-900 dark:text-white">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </div>

                @if ($outstandingAmount > 0)
                    <div class="text-xs text-red-600 dark:text-red-400">
                        Outstanding: Rp {{ number_format($outstandingAmount, 0, ',', '.') }}
                    </div>
                @elseif($totalAmount > 0)
                    <div class="text-xs text-green-600 dark:text-green-400">
                        Fully Paid
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Account Representative --}}
        @interact('column_account_representative', $row)
            <div class="text-sm">
                @if ($row->account_representative)
                    <div class="font-medium text-gray-900 dark:text-white">
                        {{ $row->account_representative }}
                    </div>
                @else
                    <span class="text-gray-400 dark:text-gray-500 italic">Unassigned</span>
                @endif
            </div>
        @endinteract

        {{-- Created Date --}}
        @interact('column_created_at', $row)
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <div>{{ $row->created_at->format('M d, Y') }}</div>
                <div class="text-xs">{{ $row->created_at->format('H:i') }}</div>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_actions', $row)
            <div class="flex items-center space-x-1">
                {{-- View Button --}}
                <x-button.circle color="blue" icon="eye" size="sm" title="View Client" />

                {{-- Edit Button --}}
                <x-button.circle color="yellow" icon="pencil" size="sm" title="Edit Client" />

                {{-- Status Toggle --}}
                <x-button.circle color="{{ $row->status === 'Active' ? 'red' : 'green' }}"
                    icon="{{ $row->status === 'Active' ? 'pause' : 'play' }}" size="sm"
                    wire:click="toggleClientStatus({{ $row->id }})"
                    title="{{ $row->status === 'Active' ? 'Deactivate' : 'Activate' }} Client" />

                {{-- Delete Button --}}
                <x-button.circle color="red" icon="trash" size="sm"
                    wire:click="deleteClient({{ $row->id }})"
                    wire:confirm="Are you sure you want to delete this client?" title="Delete Client" />
            </div>
        @endinteract

    </x-table>
</div>
