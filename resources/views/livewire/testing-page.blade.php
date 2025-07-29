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
                                label="Nama {{ $client_type === 'individual' ? 'Lengkap' : 'Perusahaan' }}" 
                                hint="Masukkan nama {{ $client_type === 'individual' ? 'lengkap' : 'perusahaan' }}"
                                wire:model="name" 
                                required 
                            />

                            {{-- Email --}}
                            <x-input 
                                label="Email" 
                                hint="Masukkan alamat email yang valid (opsional)"
                                type="email"
                                wire:model="email" 
                            />

                            {{-- NPWP with proper Indonesian format --}}
                            <x-input 
                                label="NPWP" 
                                hint="Format: 99.999.999.9-999.999"
                                x-mask="99.999.999.9-999.999"
                                wire:model="NPWP"
                                placeholder="01.234.567.8-901.000"
                            />

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <x-radio wire:model.live="status" value="Active" label="✅ Aktif" />
                                    <x-radio wire:model.live="status" value="Inactive" label="❌ Tidak Aktif" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Information --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Informasi Tambahan</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Data tambahan untuk keperluan administrasi</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- KPP --}}
                            <x-input 
                                label="KPP (Kantor Pelayanan Pajak)" 
                                hint="Nama KPP tempat terdaftar (opsional)"
                                wire:model="KPP"
                            />

                            {{-- EFIN --}}
                            <x-input 
                                label="EFIN" 
                                hint="Electronic Filing Identification Number (opsional)"
                                wire:model="EFIN"
                            />

                            {{-- Person in Charge --}}
                            <x-input 
                                label="Penanggung Jawab" 
                                hint="Nama penanggung jawab (opsional)"
                                wire:model="person_in_charge"
                            />

                            {{-- Account Representative --}}
                            <x-input 
                                label="Account Representative" 
                                hint="Nama perwakilan akun (opsional)"
                                wire:model="account_representative"
                            />

                            {{-- AR Phone Number --}}
                            <x-input 
                                label="Telepon AR" 
                                hint="Nomor telepon account representative"
                                x-mask="9999-9999-9999"
                                wire:model="ar_phone_number"
                                placeholder="0812-3456-7890"
                                class="md:col-span-2"
                            />
                        </div>
                    </div>

                    {{-- Address Information --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <x-icon name="map-pin" class="w-5 h-5 inline mr-2" />
                                Alamat
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Alamat lengkap client</p>
                        </div>

                        {{-- Full Address --}}
                        <x-textarea 
                            label="Alamat Lengkap" 
                            hint="Alamat lengkap client (opsional)"
                            wire:model="address" 
                            rows="3"
                        />
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
                        <div><strong>NPWP:</strong> {{ $NPWP ?: '-' }}</div>
                        <div><strong>KPP:</strong> {{ $KPP ?: '-' }}</div>
                        <div><strong>EFIN:</strong> {{ $EFIN ?: '-' }}</div>
                        <div><strong>Penanggung Jawab:</strong> {{ $person_in_charge ?: '-' }}</div>
                        <div><strong>Account Representative:</strong> {{ $account_representative ?: '-' }}</div>
                        <div><strong>Telepon AR:</strong> {{ $ar_phone_number ?: '-' }}</div>
                        <div><strong>Status:</strong> {{ $status }}</div>
                        <div class="md:col-span-2"><strong>Alamat:</strong> {{ $address ?: '-' }}</div>
                    </div>
                </div>

            </x-tab.items>
        </x-tab>
    </section>
</div>
