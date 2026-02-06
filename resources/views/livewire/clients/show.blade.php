{{-- resources/views/livewire/clients/show.blade.php --}}

<x-modal wire="showViewModal" title="{{ __('pages.client_details') }}" size="5xl" center>
    @if($client)
        <div class="space-y-6">
            <!-- Client Header -->
            <div class="flex items-center justify-between pb-6 border-b border-secondary-200 dark:border-dark-600">
                <div class="flex items-center gap-4">
                    <!-- Avatar -->
                    <div class="h-16 w-16 flex-shrink-0">
                        @if ($client->logo)
                            <img class="h-16 w-16 rounded-xl object-cover" src="{{ $client->logo }}" alt="{{ $client->name }}">
                        @else
                            <div class="h-16 w-16 rounded-xl flex items-center justify-center {{ $client->type === 'individual' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-purple-50 dark:bg-purple-900/20' }}">
                                <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                    class="w-8 h-8 {{ $client->type === 'individual' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" />
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $client->name }}</h3>
                        <div class="flex items-center gap-3 mt-2">
                            <x-badge text="{{ $client->type === 'individual' ? __('pages.individual') : __('pages.company') }}"
                                     color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                            <x-badge text="{{ $client->status === 'Active' ? __('common.active') : __('common.inactive') }}"
                                     color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="flex items-center gap-4 border border-secondary-200 dark:border-dark-600 rounded-xl p-4">
                    <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="document-duplicate" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_invoices') }}</div>
                        <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->getTotalInvoices() }}</div>
                        <div class="text-sm text-dark-500 dark:text-dark-400 mt-0.5">
                            Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <x-tab selected="{{ __('pages.overview') }}">
                <!-- Overview Tab -->
                <x-tab.items tab="{{ __('pages.overview') }}">
                    <x-slot:right>
                        <x-icon name="information-circle" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="space-y-6">
                        <!-- Basic & Contact Information in Two Columns -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="border border-secondary-200 dark:border-dark-600 rounded-xl p-6">
                                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4 mb-4">
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                        {{ __('pages.basic_info') }}
                                    </h4>
                                </div>

                                {{-- Content --}}
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.client_name_label') }}</span>
                                        <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $client->name }}</span>
                                    </div>

                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.type') }}</span>
                                        <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $client->type === 'individual' ? __('pages.individual') : __('pages.company') }}</span>
                                    </div>

                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('common.status') }}</span>
                                        <x-badge text="{{ $client->status === 'Active' ? __('common.active') : __('common.inactive') }}"
                                                 color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                                    </div>

                                    @if ($client->NPWP)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.tax_id') }}</span>
                                            <span class="text-sm font-medium font-mono text-dark-900 dark:text-dark-50">{{ $client->NPWP }}</span>
                                        </div>
                                    @endif

                                    @if ($client->KPP)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.kpp') }}</span>
                                            <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $client->KPP }}</span>
                                        </div>
                                    @endif

                                    @if ($client->EFIN)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.efin') }}</span>
                                            <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $client->EFIN }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="border border-secondary-200 dark:border-dark-600 rounded-xl p-6">
                                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4 mb-4">
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                        {{ __('pages.contact_info') }}
                                    </h4>
                                </div>

                                <div class="space-y-3">
                                    @if ($client->email)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('common.email') }}</span>
                                            <a href="mailto:{{ $client->email }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $client->email }}
                                            </a>
                                        </div>
                                    @endif

                                    @if ($client->person_in_charge)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.person_in_charge_label') }}</span>
                                            <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $client->person_in_charge }}</span>
                                        </div>
                                    @endif

                                    @if ($client->account_representative)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.account_representative_label') }}</span>
                                            <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $client->account_representative }}</span>
                                        </div>
                                    @endif

                                    @if ($client->ar_phone_number)
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.ar_phone_label') }}</span>
                                            <span class="text-sm font-medium font-mono text-dark-900 dark:text-dark-50">{{ $client->ar_phone_number }}</span>
                                        </div>
                                    @endif

                                    @if ($client->address)
                                        <div class="py-2">
                                            <span class="text-sm text-dark-600 dark:text-dark-400 block mb-2">{{ __('pages.address_label') }}</span>
                                            <p class="text-sm text-dark-900 dark:text-dark-50">{{ $client->address }}</p>
                                        </div>
                                    @endif

                                    @if (!$client->email && !$client->person_in_charge && !$client->account_representative && !$client->ar_phone_number && !$client->address)
                                        <div class="text-center py-8">
                                            <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.no_contact_info_yet') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab.items>

                <!-- Financial Tab -->
                <x-tab.items tab="{{ __('pages.financial') }}">
                    <x-slot:right>
                        <x-icon name="currency-dollar" class="w-5 h-5" />
                    </x-slot:right>

                    <div class="space-y-6">
                        <!-- Financial Summary -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="flex items-center gap-4 p-4 border border-secondary-200 dark:border-dark-600 rounded-xl">
                                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <x-icon name="document-duplicate" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_invoices') }}</div>
                                    <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->getTotalInvoices() }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 border border-secondary-200 dark:border-dark-600 rounded-xl">
                                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_amount') }}</div>
                                    <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 border border-secondary-200 dark:border-dark-600 rounded-xl">
                                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <x-icon name="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.paid_amount') }}</div>
                                    <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($this->getPaidAmount(), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-4 border border-secondary-200 dark:border-dark-600 rounded-xl">
                                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <x-icon name="exclamation-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                                </div>
                                <div>
                                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.outstanding_amount') }}</div>
                                    <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                        Rp {{ number_format($this->getOutstandingAmount(), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices -->
                        <div>
                            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4 mb-4">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                    {{ __('pages.recent_invoices') }}
                                </h4>
                            </div>
                            @if ($client->invoices->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($client->invoices->take(5) as $invoice)
                                        <div class="flex items-center justify-between p-4 border border-secondary-200 dark:border-dark-600 rounded-xl">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                                    <x-icon name="document-text" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                                </div>
                                                <div>
                                                    <div class="font-medium text-dark-900 dark:text-dark-50">{{ $invoice->invoice_number }}</div>
                                                    <div class="text-sm text-dark-600 dark:text-dark-400">{{ $invoice->issue_date->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-medium text-dark-900 dark:text-dark-50">
                                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                                </div>
                                                @php
                                                    $statusText = match($invoice->status) {
                                                        'paid' => __('pages.paid'),
                                                        'partially_paid' => __('pages.partially_paid'),
                                                        'overdue' => __('common.overdue'),
                                                        'draft' => __('pages.draft'),
                                                        default => ucfirst($invoice->status)
                                                    };
                                                    $statusColor = match($invoice->status) {
                                                        'paid' => 'green',
                                                        'overdue' => 'red',
                                                        'partially_paid' => 'yellow',
                                                        'draft' => 'gray',
                                                        default => 'blue'
                                                    };
                                                @endphp
                                                <x-badge :text="$statusText" :color="$statusColor" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($this->getTotalInvoices() > 5)
                                    <div class="mt-4 text-center">
                                        <x-button size="sm" color="blue" outline>{{ __('pages.view_all_invoices', ['count' => $this->getTotalInvoices()]) }}</x-button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.no_invoices_yet') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-tab.items>
            </x-tab>
        </div>
    @endif

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <x-button wire:click="$toggle('showViewModal')" color="secondary" outline class="w-full sm:w-auto order-2 sm:order-1">
                {{ __('common.close') }}
            </x-button>
            <x-button wire:click="editClient" color="blue" icon="pencil" class="w-full sm:w-auto order-1 sm:order-2">
                {{ __('pages.edit_client') }}
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>