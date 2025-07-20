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

    {{-- Table --}}
    <x-table :$headers :$rows :$sort filter :quantity="[5, 10, 25, 50]" paginate selectable wire:model.live="selected">

        <x-slot:filters>
            Raw filters Slot
        </x-slot:filters>

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
        @interact('column_person_in_charge', $row)
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
            <x-dropdown icon="ellipsis-vertical" static>
                <x-dropdown.items text="View" icon="eye" />
                <livewire:clients.edit :client="$row" :key="uniqid()" />
                <livewire:clients.delete :client="$row" :key="uniqid()" />
            </x-dropdown>
        @endinteract

    </x-table>
</div>
