{{-- Client Detail View Modals (Dynamic) --}}
@foreach ($allClients as $client)
    <flux:modal name="view-client-{{ $client->id }}" class="xl:max-w-5xl" wire:key="client-modal-{{ $client->id }}">
        <h3 class="font-semibold text-lg text-zinc-100 pb-5">Client Details</h3>
        
        @if ($viewingClient && $viewingClient->id === $client->id)
            <!-- Client information in a responsive grid layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <!-- Basic Information -->
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                    <div class="bg-zinc-700 px-4 py-2 flex justify-between items-center">
                        <h4 class="font-medium text-zinc-100">Contact Information</h4>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $viewingClient->type === 'individual' ? 'bg-blue-900 text-blue-200' : 'bg-purple-900 text-purple-200' }} lg:hidden">
                            {{ ucfirst($viewingClient->type) }}
                        </span>
                    </div>
                    <div class="p-4 space-y-3">
                        @if ($viewingClient->email)
                            <div>
                                <div class="text-xs text-zinc-400">Email</div>
                                <div class="text-zinc-200 break-all">{{ $viewingClient->email }}</div>
                            </div>
                        @endif

                        @if ($viewingClient->phone)
                            <div>
                                <div class="text-xs text-zinc-400">Phone</div>
                                <div class="text-zinc-200">{{ $viewingClient->phone }}</div>
                            </div>
                        @endif

                        @if ($viewingClient->address)
                            <div>
                                <div class="text-xs text-zinc-400">Address</div>
                                <div class="text-zinc-200">{{ $viewingClient->address }}</div>
                            </div>
                        @endif

                        @if ($viewingClient->tax_id)
                            <div>
                                <div class="text-xs text-zinc-400">Tax ID</div>
                                <div class="text-zinc-200">{{ $viewingClient->tax_id }}</div>
                            </div>
                        @endif

                        @if (!$viewingClient->email && !$viewingClient->phone && !$viewingClient->address && !$viewingClient->tax_id)
                            <div class="text-zinc-500 italic">No contact information available</div>
                        @endif
                    </div>
                </div>

                <!-- Relationships -->
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                    <div class="bg-zinc-700 px-4 py-2">
                        <h4 class="font-medium text-zinc-100">
                            {{ $viewingClient->type === 'individual' ? 'Associated Companies' : 'Individual Owners' }}
                        </h4>
                    </div>
                    <div class="p-4">
                        @if ($viewingClient->type === 'individual')
                            @if ($viewingClient->ownedCompanies->count() > 0)
                                <div class="space-y-2">
                                    @foreach ($viewingClient->ownedCompanies as $company)
                                        <div class="flex items-center space-x-2 p-2 bg-zinc-700/50 rounded-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="text-zinc-200 truncate">{{ $company->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-zinc-500 italic">No associated companies</div>
                            @endif
                        @else
                            @if ($viewingClient->owners->count() > 0)
                                <div class="space-y-2">
                                    @foreach ($viewingClient->owners as $owner)
                                        <div class="flex items-center space-x-2 p-2 bg-zinc-700/50 rounded-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="text-zinc-200 truncate">{{ $owner->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-zinc-500 italic">No individual owners</div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                    <div class="bg-zinc-700 px-4 py-2">
                        <h4 class="font-medium text-zinc-100">Invoice Summary</h4>
                    </div>
                    <div class="p-4">
                        @if ($viewingClient->invoices->count() > 0)
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                    <div class="text-lg font-bold text-zinc-100">{{ $viewingClient->invoices->count() }}</div>
                                    <div class="text-xs text-zinc-400">Total Invoices</div>
                                </div>

                                <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                    <div class="text-lg font-bold text-green-400">
                                        {{ $viewingClient->invoices->where('status', 'paid')->count() }}
                                    </div>
                                    <div class="text-xs text-zinc-400">Paid</div>
                                </div>

                                <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                    <div class="text-lg font-bold text-amber-400">
                                        {{ $viewingClient->invoices->where('status', 'partially_paid')->count() }}
                                    </div>
                                    <div class="text-xs text-zinc-400">Partially Paid</div>
                                </div>

                                <div class="bg-zinc-700/50 p-3 rounded-md text-center">
                                    <div class="text-lg font-bold text-red-400">
                                        {{ $viewingClient->invoices->where('status', 'overdue')->count() }}
                                    </div>
                                    <div class="text-xs text-zinc-400">Overdue</div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="text-xs text-zinc-400 mb-2">Recent Invoices</div>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach ($viewingClient->invoices->sortByDesc('issue_date')->take(5) as $invoice)
                                        <div class="flex justify-between items-center p-2 bg-zinc-700/30 rounded-md">
                                            <div class="flex items-center space-x-2 min-w-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-sm text-zinc-200 truncate">{{ $invoice->invoice_number }}</span>
                                            </div>
                                            <div class="flex items-center flex-shrink-0">
                                                <span class="text-sm text-zinc-300 mr-2 hidden sm:inline">{{ $invoice->total_amount }}</span>
                                                <span class="px-2 py-0.5 text-xs rounded-full whitespace-nowrap
                                                    {{ $invoice->status === 'paid'
                                                        ? 'bg-green-900 text-green-200'
                                                        : ($invoice->status === 'partially_paid'
                                                            ? 'bg-amber-900 text-amber-200'
                                                            : ($invoice->status === 'overdue'
                                                                ? 'bg-red-900 text-red-200'
                                                                : 'bg-blue-900 text-blue-200')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-zinc-500 italic">No invoices found for this client</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Services Section - Responsive table -->
            <div class="mt-6">
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                    <div class="bg-zinc-700 px-4 py-2">
                        <h4 class="font-medium text-zinc-100">Services History</h4>
                    </div>
                    <div class="p-4">
                        @if ($viewingClient->serviceClients->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-zinc-700">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                                Service
                                            </th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider hidden sm:table-cell">
                                                Date
                                            </th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                                Amount
                                            </th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-700">
                                        @foreach ($viewingClient->serviceClients->sortByDesc('service_date') as $serviceClient)
                                            <tr class="hover:bg-zinc-700/30">
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-zinc-200">
                                                    {{ $serviceClient->service->name }}
                                                    <!-- Show date on mobile -->
                                                    <div class="text-xs text-zinc-400 sm:hidden mt-1">
                                                        {{ $serviceClient->service_date->format('M d, Y') }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-zinc-300 hidden sm:table-cell">
                                                    {{ $serviceClient->service_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-zinc-300">
                                                    {{ number_format($serviceClient->amount, 2) }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-center">
                                                    @if ($serviceClient->invoiceItems->count() > 0)
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-900 text-green-200">
                                                            Invoiced
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-900 text-yellow-200">
                                                            Not Invoiced
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-zinc-500 italic">No services history found for this client</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3">
                <flux:modal.trigger name="edit-client" wire:click="editClient({{ $viewingClient->id }})">
                    <x-shared.button 
                        variant="warning"
                        icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        class="w-full sm:w-auto"
                    >
                        Edit Client
                    </x-shared.button>
                </flux:modal.trigger>

                <flux:modal.close wire:click="clearViewingClient">
                    <x-shared.button variant="secondary" class="w-full sm:w-auto">
                        Close
                    </x-shared.button>
                </flux:modal.close>
            </div>
        @else
            <div class="p-16 flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
                <p class="mt-4 text-zinc-400">Loading client details...</p>
            </div>
        @endif
    </flux:modal>
@endforeach