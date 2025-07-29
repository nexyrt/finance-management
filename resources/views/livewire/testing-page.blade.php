<div>
    <section class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-1">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                        Testing Maskable Inputs
                    </h1>
                    <p class="text-gray-600 dark:text-zinc-400 text-lg">
                        Form client dengan berbagai jenis maskable inputs
                    </p>
                </div>
            </div>
        </div>

        <x-tab selected="client-form">
            {{-- Tab 1: Client Form --}}
            <x-tab.items tab="client-form">
                <x-slot:left>
                    <x-icon name="user-plus" class="w-5 h-5" />
                </x-slot:left>
                Form Client
                <x-slot:right>
                    <x-badge text="New" color="blue" />
                </x-slot:right>

                <form wire:submit="submit" class="space-y-8">
                    {{-- Client Type Selection --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tipe Client</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Pilih apakah client adalah individu atau perusahaan</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-radio wire:model.live="client_type" value="individual" label="👤 Individu" />
                            <x-radio wire:model.live="client_type" value="company" label="🏢 Perusahaan" />
                        </div>
                    </div>

                    {{-- Basic Information --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Informasi Dasar</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Data kontak dan identitas client</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <x-input 
                                label="{{ $client_type === 'individual' ? 'Nama Lengkap' : 'Nama Penanggung Jawab' }}" 
                                hint="Masukkan nama {{ $client_type === 'individual' ? 'lengkap' : 'penanggung jawab' }}"
                                wire:model="name" 
                                required 
                            />

                            {{-- Email --}}
                            <x-input 
                                label="Email" 
                                hint="Masukkan alamat email yang valid"
                                type="email"
                                wire:model="email" 
                                required 
                            />

                            {{-- Phone with Indonesian mask --}}
                            <x-input 
                                label="Nomor Telepon" 
                                hint="Contoh: 0812-3456-7890"
                                x-mask="9999-9999-9999"
                                wire:model="phone" 
                                required 
                            />

                            {{-- Tax ID --}}
                            <x-input 
                                label="{{ $client_type === 'individual' ? 'NIK/NPWP' : 'NPWP Perusahaan' }}" 
                                hint="{{ $client_type === 'individual' ? 'Opsional untuk individu' : 'Wajib untuk perusahaan' }}"
                                x-mask:dynamic="$client_type === 'individual' ? '9999999999999999' : '99.999.999.9-999.999'"
                                wire:model="tax_id" 
                                :required="$client_type === 'company'"
                            />
                        </div>
                    </div>

                    {{-- Individual Specific Fields --}}
                    @if($client_type === 'individual')
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200/50 dark:border-blue-700/50">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <x-icon name="user" class="w-5 h-5 inline mr-2" />
                                Data Individu
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Informasi khusus untuk client individu</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Birth Date --}}
                            <x-input 
                                label="Tanggal Lahir" 
                                hint="Format: DD/MM/YYYY"
                                x-mask="99/99/9999"
                                wire:model="birth_date"
                                placeholder="27/02/1992"
                            />

                            {{-- Salary --}}
                            <x-input 
                                label="Gaji/Penghasilan" 
                                hint="Opsional, untuk referensi kredit"
                                x-mask:dynamic="$money($input, ',')"
                                wire:model="salary"
                                placeholder="5.000.000"
                            />

                            {{-- Credit Card (for testing) --}}
                            <x-input 
                                label="Kartu Kredit" 
                                hint="Opsional, untuk pembayaran"
                                x-mask:dynamic="creditCardMask"
                                wire:model="credit_card"
                                placeholder="4111 1111 1111 1111"
                            />
                        </div>
                    </div>
                    @endif

                    {{-- Company Specific Fields --}}
                    @if($client_type === 'company')
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 border border-purple-200/50 dark:border-purple-700/50">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <x-icon name="building-office" class="w-5 h-5 inline mr-2" />
                                Data Perusahaan
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Informasi khusus untuk client perusahaan</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Company Name --}}
                            <x-input 
                                label="Nama Perusahaan" 
                                hint="Nama resmi perusahaan"
                                wire:model="company_name" 
                                required 
                            />

                            {{-- Company Registration --}}
                            <x-input 
                                label="Nomor Registrasi" 
                                hint="NIB/TDP/SIUP perusahaan"
                                x-mask="999999999999999"
                                wire:model="company_registration" 
                                required 
                            />

                            {{-- Website --}}
                            <x-input 
                                label="Website" 
                                hint="Website perusahaan (opsional)"
                                type="url"
                                wire:model="website"
                                placeholder="https://www.example.com"
                                class="md:col-span-2"
                            />
                        </div>
                    </div>
                    @endif

                    {{-- Address Information --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <x-icon name="map-pin" class="w-5 h-5 inline mr-2" />
                                Alamat
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Alamat lengkap client</p>
                        </div>

                        <div class="space-y-6">
                            {{-- Full Address --}}
                            <x-textarea 
                                label="Alamat Lengkap" 
                                hint="Jalan, nomor, RT/RW, kelurahan"
                                wire:model="address" 
                                rows="3"
                                required 
                            />

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- City --}}
                                <x-input 
                                    label="Kota" 
                                    hint="Nama kota/kabupaten"
                                    wire:model="city" 
                                    required 
                                />

                                {{-- Postal Code --}}
                                <x-input 
                                    label="Kode Pos" 
                                    hint="5 digit kode pos"
                                    x-mask="99999"
                                    wire:model="postal_code" 
                                    required 
                                />

                                {{-- Country --}}
                                <x-input 
                                    label="Negara" 
                                    hint="Negara tempat tinggal"
                                    wire:model="country" 
                                    required 
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end gap-4">
                        <x-button 
                            type="button" 
                            wire:click="resetForm" 
                            color="secondary" 
                            icon="arrow-path"
                        >
                            Reset Form
                        </x-button>
                        
                        <x-button 
                            type="submit" 
                            color="primary" 
                            icon="check" 
                            wire:loading.attr="disabled"
                            wire:target="submit"
                        >
                            <span wire:loading.remove wire:target="submit">Simpan Client</span>
                            <span wire:loading wire:target="submit">Menyimpan...</span>
                        </x-button>
                    </div>
                </form>

            </x-tab.items>

            {{-- Tab 2: Preview/Results --}}
            <x-tab.items tab="preview">
                <x-slot:left>
                    <x-icon name="eye" class="w-5 h-5" />
                </x-slot:left>
                Preview Data
                <x-slot:right>
                    <x-badge text="Live" color="green" />
                </x-slot:right>

                <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Live Preview Data</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><strong>Tipe:</strong> {{ ucfirst($client_type) }}</div>
                        <div><strong>Nama:</strong> {{ $name ?: '-' }}</div>
                        <div><strong>Email:</strong> {{ $email ?: '-' }}</div>
                        <div><strong>Telepon:</strong> {{ $phone ?: '-' }}</div>
                        <div><strong>Tax ID:</strong> {{ $tax_id ?: '-' }}</div>
                        @if($client_type === 'individual')
                            <div><strong>Tanggal Lahir:</strong> {{ $birth_date ?: '-' }}</div>
                            <div><strong>Gaji:</strong> {{ $salary ? 'Rp ' . number_format($salary, 0, ',', '.') : '-' }}</div>
                        @else
                            <div><strong>Nama Perusahaan:</strong> {{ $company_name ?: '-' }}</div>
                            <div><strong>No. Registrasi:</strong> {{ $company_registration ?: '-' }}</div>
                        @endif
                        <div class="md:col-span-2"><strong>Alamat:</strong> {{ $address ?: '-' }}</div>
                        <div><strong>Kota:</strong> {{ $city ?: '-' }}</div>
                        <div><strong>Kode Pos:</strong> {{ $postal_code ?: '-' }}</div>
                    </div>
                </div>

            </x-tab.items>
        </x-tab>
    </section>

    {{-- Credit Card Mask Script --}}
    <script>
        function creditCardMask(input) {
            // American Express: starts with 34 or 37, format: 9999 999999 99999
            if (input.startsWith('34') || input.startsWith('37')) {
                return '9999 999999 99999';
            }
            // Visa, MasterCard, etc: format: 9999 9999 9999 9999
            return '9999 9999 9999 9999';
        }
    </script>
</div>
