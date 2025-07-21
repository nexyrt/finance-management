<div>
    <!-- Trigger Button (for dropdown) -->
    <x-dropdown.items text="View" icon="eye" wire:click="openModal" />

    <!-- Client Details Modal -->
    <x-modal wire="showModal" title="Client Details" size="4xl">
        <div class="space-y-6">
            <!-- Client Header Info -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Client Avatar/Logo -->
                        <div class="h-16 w-16 flex-shrink-0">
                            @if ($client->logo)
                                <img class="h-16 w-16 rounded-full object-cover" src="{{ $client->logo }}"
                                    alt="{{ $client->name }}">
                            @else
                                <div
                                    class="h-16 w-16 rounded-full flex items-center justify-center
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
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getTotalInvoices() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Client Information -->
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <x-card>
                        <h4
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Basic Information
                        </h4>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Client Name:</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->name }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Type:</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($client->type) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                <x-badge text="{{ $client->status }}"
                                    color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                            </div>

                            @if ($client->NPWP)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">NPWP:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->NPWP }}</span>
                                </div>
                            @endif

                            @if ($client->KPP)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">KPP:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->KPP }}</span>
                                </div>
                            @endif

                            @if ($client->EFIN)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">EFIN:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->EFIN }}</span>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    <!-- Contact Information -->
                    <x-card>
                        <h4
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Contact Information
                        </h4>

                        <div class="space-y-3">
                            @if ($client->email)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                                    <a href="mailto:{{ $client->email }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $client->email }}
                                    </a>
                                </div>
                            @endif

                            @if ($client->person_in_charge)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Person in Charge:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->person_in_charge }}</span>
                                </div>
                            @endif

                            @if ($client->account_representative)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Account
                                        Representative:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->account_representative }}</span>
                                </div>
                            @endif

                            @if ($client->ar_phone_number)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">AR Phone:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->ar_phone_number }}</span>
                                </div>
                            @endif

                            @if ($client->address)
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Address:</span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                        {{ $client->address }}</p>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    <!-- Relationships -->
                    @if ($client->type === 'individual' && $client->ownedCompanies->count() > 0)
                        <x-card>
                            <h4
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Owned Companies
                            </h4>

                            <div class="space-y-2">
                                @foreach ($client->ownedCompanies as $company)
                                    <div
                                        class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <div class="flex items-center space-x-2">
                                            <x-icon name="building-office"
                                                class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->name }}</span>
                                        </div>
                                        <x-badge text="{{ $company->status }}"
                                            color="{{ $company->status === 'Active' ? 'green' : 'red' }}" />
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif

                    @if ($client->type === 'company' && $client->owners->count() > 0)
                        <x-card>
                            <h4
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Owners
                            </h4>

                            <div class="space-y-2">
                                @foreach ($client->owners as $owner)
                                    <div
                                        class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <div class="flex items-center space-x-2">
                                            <x-icon name="user" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $owner->name }}</span>
                                        </div>
                                        <x-badge text="{{ $owner->status }}"
                                            color="{{ $owner->status === 'Active' ? 'green' : 'red' }}" />
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif
                </div>

                <!-- Right Column: Financial & Invoice Information -->
                <div class="space-y-6">
                    <!-- Financial Summary -->
                    <x-card>
                        <h4
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Financial Summary
                        </h4>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Invoices</div>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $this->getTotalInvoices() }}</div>
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
                    </x-card>

                    <!-- Recent Invoices -->
                    <x-card>
                        <h4
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Recent Invoices
                        </h4>

                        @if ($this->getRecentInvoices()->count() > 0)
                            <div class="space-y-3">
                                @foreach ($this->getRecentInvoices() as $invoice)
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $invoice->invoice_number }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $invoice->issue_date->format('M d, Y') }}
                                            </div>
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
                                    <x-button size="sm" color="blue" outline>
                                        View All Invoices ({{ $this->getTotalInvoices() }})
                                    </x-button>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <x-icon name="document" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                                <p class="text-gray-500 dark:text-gray-400">No invoices yet</p>
                            </div>
                        @endif
                    </x-card>

                    <!-- Timeline/History -->
                    <x-card>
                        <h4
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Timeline
                        </h4>

                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900 dark:text-white">Client created</span>
                                    <span class="text-gray-500 dark:text-gray-400">on
                                        {{ $client->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            @if ($client->updated_at != $client->created_at)
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900 dark:text-white">Last updated</span>
                                        <span
                                            class="text-gray-500 dark:text-gray-400">{{ $client->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endif

                            @if ($this->getTotalInvoices() > 0)
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                    <div class="text-sm">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $this->getTotalInvoices() }}
                                            invoices</span>
                                        <span class="text-gray-500 dark:text-gray-400">generated</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-card>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <x-button wire:click="closeModal" color="secondary">
                    Close
                </x-button>
                <x-button wire:click="editClient" color="primary" icon="pencil">
                    Edit Client
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
