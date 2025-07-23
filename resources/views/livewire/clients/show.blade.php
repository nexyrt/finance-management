{{-- resources/views/livewire/clients/show.blade.php --}}

<x-modal wire="showViewModal" title="Detail Klien" size="5xl" center>
    @if($client)
        <div class="space-y-6">
            <!-- Client Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <!-- Avatar -->
                    <div class="h-16 w-16 flex-shrink-0">
                        @if ($client->logo)
                            <img class="h-16 w-16 rounded-xl object-cover shadow-lg" src="{{ $client->logo }}" alt="{{ $client->name }}">
                        @else
                            <div class="h-16 w-16 rounded-xl flex items-center justify-center shadow-lg
                                {{ $client->type === 'individual' ? 'bg-gradient-to-br from-blue-500 to-blue-600' : 'bg-gradient-to-br from-purple-500 to-purple-600' }}">
                                <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                    class="w-8 h-8 text-white" />
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h3>
                        <div class="flex items-center space-x-3 mt-2">
                            <x-badge text="{{ $client->type === 'individual' ? 'Individu' : 'Perusahaan' }}" 
                                     color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                            <x-badge text="{{ $client->status === 'Active' ? 'Aktif' : 'Tidak Aktif' }}" 
                                     color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Invoice</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getTotalInvoices() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <x-tab selected="Ringkasan">
                <!-- Overview Tab -->
                <x-tab.items tab="Ringkasan">
                    <x-slot:right>
                        <x-icon name="information-circle" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="space-y-6">
                        <!-- Basic & Contact Information in Two Columns -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                    Informasi Dasar
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2">
                                        <span class="text-gray-600 dark:text-gray-400">Nama Klien:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-gray-600 dark:text-gray-400">Tipe:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $client->type === 'individual' ? 'Individu' : 'Perusahaan' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                        <x-badge text="{{ $client->status === 'Active' ? 'Aktif' : 'Tidak Aktif' }}" 
                                                 color="{{ $client->status === 'Active' ? 'green' : 'red' }}" />
                                    </div>
                                    @if ($client->NPWP)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">NPWP:</span>
                                            <span class="font-medium text-gray-900 dark:text-white font-mono">{{ $client->NPWP }}</span>
                                        </div>
                                    @endif
                                    @if ($client->KPP)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">KPP:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $client->KPP }}</span>
                                        </div>
                                    @endif
                                    @if ($client->EFIN)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">EFIN:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $client->EFIN }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                    Informasi Kontak
                                </h4>
                                <div class="space-y-3">
                                    @if ($client->email)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                            <a href="mailto:{{ $client->email }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                {{ $client->email }}
                                            </a>
                                        </div>
                                    @endif
                                    @if ($client->person_in_charge)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">Penanggung Jawab:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $client->person_in_charge }}</span>
                                        </div>
                                    @endif
                                    @if ($client->account_representative)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">Perwakilan Akun:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $client->account_representative }}</span>
                                        </div>
                                    @endif
                                    @if ($client->ar_phone_number)
                                        <div class="flex justify-between py-2">
                                            <span class="text-gray-600 dark:text-gray-400">Telepon AR:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $client->ar_phone_number }}</span>
                                        </div>
                                    @endif
                                    @if ($client->address)
                                        <div class="py-2">
                                            <span class="text-gray-600 dark:text-gray-400">Alamat:</span>
                                            <p class="font-medium text-gray-900 dark:text-white mt-1 leading-relaxed">{{ $client->address }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab.items>

                <!-- Financial Tab -->
                <x-tab.items tab="Keuangan">
                    <x-slot:right>
                        <x-icon name="currency-dollar" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="space-y-6">
                        <!-- Financial Summary -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                                <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Invoice</div>
                                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $this->getTotalInvoices() }}</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/50">
                                <div class="text-sm text-green-600 dark:text-green-400 font-medium">Total Jumlah</div>
                                <div class="text-lg font-bold text-green-700 dark:text-green-300">
                                    Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/50">
                                <div class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Terbayar</div>
                                <div class="text-lg font-bold text-emerald-700 dark:text-emerald-300">
                                    Rp {{ number_format($this->getPaidAmount(), 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-800/50">
                                <div class="text-sm text-red-600 dark:text-red-400 font-medium">Tertunggak</div>
                                <div class="text-lg font-bold text-red-700 dark:text-red-300">
                                    Rp {{ number_format($this->getOutstandingAmount(), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                Invoice Terbaru
                            </h4>
                            @if ($client->invoices->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($client->invoices->take(5) as $invoice)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                    <x-icon name="document-text" class="w-5 h-5 text-white" />
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->issue_date->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-semibold text-gray-900 dark:text-white">
                                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                                </div>
                                                <x-badge text="{{ $invoice->status === 'paid' ? 'Lunas' : ($invoice->status === 'overdue' ? 'Terlambat' : ucfirst($invoice->status)) }}" 
                                                         color="{{ $invoice->status === 'paid' ? 'green' : ($invoice->status === 'overdue' ? 'red' : 'yellow') }}" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($this->getTotalInvoices() > 5)
                                    <div class="mt-4 text-center">
                                        <x-button size="sm" color="blue" outline>Lihat Semua Invoice ({{ $this->getTotalInvoices() }})</x-button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <div class="h-16 w-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <x-icon name="document" class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">Belum ada invoice</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-tab.items>

                <!-- Relationships Tab -->
                <x-tab.items tab="Hubungan">
                    <x-slot:right>
                        <x-icon name="users" class="w-5 h-5" />
                    </x-slot:right>
                    
                    <div class="space-y-6">
                        @if ($client->type === 'individual' && $client->ownedCompanies->count() > 0)
                            <div>
                                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Perusahaan yang Dimiliki</h4>
                                    <x-button size="sm" color="blue" outline wire:click="manageRelationships">
                                        Kelola
                                    </x-button>
                                </div>
                                <div class="space-y-3">
                                    @foreach ($client->ownedCompanies as $company)
                                        <div class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-100 dark:border-purple-800/50">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-10 w-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <x-icon name="building-office" class="w-5 h-5 text-white" />
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $company->name }}</span>
                                                    @if($company->NPWP)
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">NPWP: {{ $company->NPWP }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <x-badge text="{{ $company->status === 'Active' ? 'Aktif' : 'Tidak Aktif' }}" 
                                                     color="{{ $company->status === 'Active' ? 'green' : 'red' }}" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($client->type === 'company' && $client->owners->count() > 0)
                            <div>
                                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Pemilik</h4>
                                    <x-button size="sm" color="blue" outline wire:click="manageRelationships">
                                        Kelola
                                    </x-button>
                                </div>
                                <div class="space-y-3">
                                    @foreach ($client->owners as $owner)
                                        <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                                    <x-icon name="user" class="w-5 h-5 text-white" />
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $owner->name }}</span>
                                                    @if($owner->email)
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $owner->email }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <x-badge text="{{ $owner->status === 'Active' ? 'Aktif' : 'Tidak Aktif' }}" 
                                                     color="{{ $owner->status === 'Active' ? 'green' : 'red' }}" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (($client->type === 'individual' && $client->ownedCompanies->count() === 0) || 
                             ($client->type === 'company' && $client->owners->count() === 0))
                            <div class="text-center py-12">
                                <div class="h-16 w-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="users" class="w-8 h-8 text-gray-400" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak Ada Hubungan</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    {{ $client->type === 'individual' ? 'Individu ini belum memiliki perusahaan yang dimiliki.' : 'Perusahaan ini belum memiliki pemilik yang terdaftar.' }}
                                </p>
                                <x-button wire:click="manageRelationships" color="primary">
                                    Tambah {{ $client->type === 'individual' ? 'Perusahaan' : 'Pemilik' }}
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
            <x-button wire:click="$toggle('showViewModal')" color="secondary">Tutup</x-button>
            <x-button wire:click="editClient" color="primary" icon="pencil">Edit Klien</x-button>
        </div>
    </x-slot:footer>
</x-modal>