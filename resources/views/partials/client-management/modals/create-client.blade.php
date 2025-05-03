<flux:modal name="create-client" size="lg">
    <h3 class="font-semibold text-lg text-zinc-100 pb-5">Add New Client</h3>
    
    <form wire:submit.prevent="saveClient">
        <div class="space-y-5">
            {{-- Client Name and Type --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <flux:input 
                        label="Client Name" 
                        wire:model="name" 
                        placeholder="Enter client name"
                        :required="true" 
                    />
                    @error('name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div x-data="{}" class="w-full">
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Client Type <span class="text-red-500">*</span></label>
                    <div class="client-type-container grid grid-cols-2 gap-2">
                        <label class="relative inline-flex items-center cursor-pointer client-type-radio">
                            <input type="radio" wire:model.live="clientType" value="individual" class="sr-only peer">
                            <div class="w-full px-4 py-2 bg-zinc-800 border peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-zinc-700 rounded-md text-zinc-300 peer-checked:text-white transition-colors duration-150 ease-in-out">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="whitespace-nowrap">Individual</span>
                                </div>
                            </div>
                        </label>
                        <label class="relative inline-flex items-center cursor-pointer client-type-radio">
                            <input type="radio" wire:model.live="clientType" value="company" class="sr-only peer">
                            <div class="w-full px-4 py-2 bg-zinc-800 border peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-zinc-700 rounded-md text-zinc-300 peer-checked:text-white transition-colors duration-150 ease-in-out">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="whitespace-nowrap">Company</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('clientType')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <flux:input 
                        label="Email" 
                        type="email" 
                        wire:model="email"
                        placeholder="Enter email address" 
                    />
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <flux:input 
                        label="Phone Number" 
                        wire:model="phone" 
                        placeholder="Enter phone number" 
                    />
                    @error('phone')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Tax ID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <flux:input 
                        label="Tax ID" 
                        wire:model="taxId"
                        placeholder="Enter tax identification number" 
                    />
                    @error('taxId')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Address --}}
            <div>
                <flux:textarea 
                    label="Address" 
                    wire:model="address" 
                    rows="3"
                    placeholder="Enter client address" 
                />
                @error('address')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Relationship Management --}}
            <div x-data="{ clientType: @entangle('clientType').live }">
                {{-- Associated Companies (for individual clients) --}}
                <div x-show="clientType === 'individual'" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-2">
                        <label class="block text-sm font-medium text-zinc-300">Associated Companies</label>
                        <flux:modal.trigger name="company-selector" wire:click="openCompanySelector">
                            <x-shared.button variant="secondary" size="xs" icon="M12 4v16m8-8H4">
                                Manage
                            </x-shared.button>
                        </flux:modal.trigger>
                    </div>

                    <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                        @if (count($displayedCompanies) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($displayedCompanies as $company)
                                    <span class="inline-flex items-center px-2 py-1 bg-indigo-900/50 border border-indigo-700 rounded text-indigo-200 text-xs">
                                        {{ $company['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-zinc-500 text-sm">No companies associated. Click "Manage" to add.</p>
                        @endif
                    </div>
                </div>

                {{-- Individual Owners (for company clients) --}}
                <div x-show="clientType === 'company'" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-2">
                        <label class="block text-sm font-medium text-zinc-300">Individual Owners</label>
                        <flux:modal.trigger name="owner-selector" wire:click="openOwnerSelector">
                            <x-shared.button variant="secondary" size="xs" icon="M12 4v16m8-8H4">
                                Manage
                            </x-shared.button>
                        </flux:modal.trigger>
                    </div>

                    <div class="bg-zinc-800 border border-zinc-700 rounded-md p-3 min-h-16">
                        @if (count($displayedOwners) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($displayedOwners as $owner)
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-900/50 border border-blue-700 rounded text-blue-200 text-xs">
                                        {{ $owner['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-zinc-500 text-sm">No individual owners associated. Click "Manage" to add.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="mt-6 flex justify-end space-x-3">
            <flux:modal.close wire:click="clearEditForm">
                <x-shared.button variant="secondary" type="button">
                    Cancel
                </x-shared.button>
            </flux:modal.close>

            <flux:modal.close>
                <x-shared.button variant="primary" type="submit">
                    Create Client
                </x-shared.button>
            </flux:modal.close>
        </div>
    </form>
</flux:modal>