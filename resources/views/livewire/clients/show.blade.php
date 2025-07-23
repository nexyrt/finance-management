{{-- resources/views/livewire/clients/show.blade.php --}}

<x-modal wire="showViewModal" title="Client Details" size="5xl" center>
    @if($client)
        <div class="space-y-6">
            <!-- Client Header -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="h-16 w-16 flex-shrink-0">
                            @if ($client->logo)
                                <img class="h-16 w-16 rounded-full object-cover" src="{{ $client->logo }}" alt="{{ $client->name }}">
                            @else
                                <div class="h-16 w-16 rounded-full flex items-center justify-center
                                    {{ $client->type === 'individual' ? 'bg-blue-100 dark:bg-blue-900/20' : 'bg-purple-100 dark:bg-purple-900/20' }}">
                                    <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                        class="w-8 h-8 {{ $client->type === 'individual' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" />
                                </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h3>
                            <div class="flex items-center space-x-3 mt-2">
                                <x-badge text="{{ ucfirst($client->type) }}" 
                                         color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                                <x-badge text="{{ $client->status }}" 
                                         color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Invoices</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getTotalInvoices() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <x-tab selected="Overview">
                <!-- Overview Tab -->
                <x-tab.items tab="Overview">
                    <x-slot:right>
                        <x-icon name="information-circle" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <x-card>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Client Name:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Type:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($client->type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                    <x-badge text="{{ $client->status }}" color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                                </div>
                                @if ($client->NPWP)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">NPWP:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->NPWP }}</span>
                                    </div>
                                @endif
                                @if ($client->KPP)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">KPP:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->KPP }}</span>
                                    </div>
                                @endif
                                @if ($client->EFIN)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">EFIN:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->EFIN }}</span>
                                    </div>
                                @endif
                            </div>
                        </x-card>

                        <!-- Contact Information -->
                        <x-card>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h4>
                            <div class="space-y-3">
                                @if ($client->email)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                                        <a href="mailto:{{ $client->email }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $client->email }}
                                        </a>
                                    </div>
                                @endif
                                @if ($client->person_in_charge)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Person in Charge:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->person_in_charge }}</span>
                                    </div>
                                @endif
                                @if ($client->account_representative)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Account Representative:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->account_representative }}</span>
                                    </div>
                                @endif
                                @if ($client->ar_phone_number)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">AR Phone:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->ar_phone_number }}</span>
                                    </div>
                                @endif
                                @if ($client->address)
                                    <div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Address:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $client->address }}</p>
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    </div>
                </x-tab.items>

                <!-- Financial Tab -->
                <x-tab.items tab="Financial">
                    <x-slot:right>
                        <x-icon name="currency-dollar" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="space-y-6">
                        <!-- Financial Summary -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Invoices</div>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->getTotalInvoices() }}</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Amount</div>
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Paid Amount</div>
                                <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($this->getPaidAmount(), 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Outstanding</div>
                                <div class="text-lg font-bold text-red-600 dark:text-red-400">
                                    Rp {{ number_format($this->getOutstandingAmount(), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices -->
                        <x-card>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Invoices</h4>
                            @if ($client->invoices->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($client->invoices as $invoice)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->issue_date->format('M d, Y') }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                                </div>
                                                <x-badge text="{{ ucfirst($invoice->status) }}" 
                                                         color="{{ $invoice->status === 'paid' ? 'green' : ($invoice->status === 'overdue' ? 'red' : 'yellow') }}" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($this->getTotalInvoices() > 5)
                                    <div class="mt-4 text-center">
                                        <x-button size="sm" color="blue" outline>View All Invoices ({{ $this->getTotalInvoices() }})</x-button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <x-icon name="document" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                                    <p class="text-gray-500 dark:text-gray-400">No invoices yet</p>
                                </div>
                            @endif
                        </x-card>
                    </div>
                </x-tab.items>

                <!-- Relationships Tab -->
                <x-tab.items tab="Relationships">
                    <x-slot:right>
                        <x-icon name="users" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="space-y-6">
                        @if ($client->type === 'individual' && $client->ownedCompanies->count() > 0)
                            <x-card>
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Owned Companies</h4>
                                    <x-button size="sm" color="blue" outline wire:click="manageRelationships">
                                        Manage
                                    </x-button>
                                </div>
                                <div class="space-y-2">
                                    @foreach ($client->ownedCompanies as $company)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                            <div class="flex items-center space-x-3">
                                                <x-icon name="building-office" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->name }}</span>
                                                    @if($company->NPWP)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">NPWP: {{ $company->NPWP }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <x-badge text="{{ $company->status }}" color="{{ $company->status === 'Active' ? 'green' : 'red' }}" />
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>
                        @endif

                        @if ($client->type === 'company' && $client->owners->count() > 0)
                            <x-card>
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Owners</h4>
                                    <x-button size="sm" color="blue" outline wire:click="manageRelationships">
                                        Manage
                                    </x-button>
                                </div>
                                <div class="space-y-2">
                                    @foreach ($client->owners as $owner)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                            <div class="flex items-center space-x-3">
                                                <x-icon name="user" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $owner->name }}</span>
                                                    @if($owner->email)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $owner->email }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <x-badge text="{{ $owner->status }}" color="{{ $owner->status === 'Active' ? 'green' : 'red' }}" />
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>
                        @endif

                        @if (($client->type === 'individual' && $client->ownedCompanies->count() === 0) || 
                             ($client->type === 'company' && $client->owners->count() === 0))
                            <div class="text-center py-12">
                                <x-icon name="users" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Relationships</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    This {{ $client->type }} doesn't have any {{ $client->type === 'individual' ? 'owned companies' : 'owners' }} yet.
                                </p>
                                <x-button wire:click="manageRelationships" color="primary">
                                    Add {{ $client->type === 'individual' ? 'Companies' : 'Owners' }}
                                </x-button>
                            </div>
                        @endif
                    </div>
                </x-tab.items>
            </x-tab>
        </div>
    @endif

    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <x-button wire:click="$toggle('showViewModal')" color="secondary">Close</x-button>
            <x-button wire:click="editClient" color="primary" icon="pencil">Edit Client</x-button>
        </div>
    </x-slot:footer>
</x-modal>