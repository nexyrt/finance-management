<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Company Profile')" :subheading="__('Manage company information for invoices')">
        <form wire:submit="updateCompanyProfile" class="my-6 space-y-6">
            <flux:input wire:model="name" label="Company Name" required />
            <flux:textarea wire:model="address" label="Address" rows="3" required />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="email" label="Email" type="email" required />
                <flux:input wire:model="phone" label="Phone" required />
            </div>

            <flux:separator />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="finance_manager_name" label="Finance Manager" required />
                <flux:input wire:model="finance_manager_position" label="Position" required />
            </div>

            <flux:separator />

            <flux:checkbox wire:model.boolean="is_pkp" label="PKP (Pengusaha Kena Pajak)" />

            @if ($is_pkp)
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="npwp" label="NPWP" />
                    <flux:input wire:model="ppn_rate" label="PPN Rate (%)" type="number" step="0.01" />
                </div>
            @endif

            <flux:separator />

            <div class="space-y-4">
                {{-- Logo --}}
                @if ($currentLogo)
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Logo</label>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 cursor-pointer"
                                    wire:click="$set('showLogoModal', true)">
                                    <img src="{{ asset('storage/' . $currentLogo) }}" class="h-16 rounded border"
                                        alt="Logo">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">letter-head.png
                                        </p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">Click to preview</p>
                                    </div>
                                </div>
                                <flux:button variant="danger" size="sm" wire:click="deleteExistingLogo">Delete
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endif
                <x-upload wire:model="logo" label="{{ $currentLogo ? 'Replace Logo' : 'Logo' }}" accept="image/*"
                    tip="PNG, JPG (Max 2MB)" />

                {{-- Signature --}}
                @if ($currentSignature)
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Signature</label>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 cursor-pointer"
                                    wire:click="$set('showSignatureModal', true)">
                                    <img src="{{ asset('storage/' . $currentSignature) }}" class="h-16 rounded border"
                                        alt="Signature">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            pdf-signature.png</p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">Click to preview</p>
                                    </div>
                                </div>
                                <flux:button variant="danger" size="sm" wire:click="deleteExistingSignature">Delete
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endif
                <x-upload wire:model="signature" label="{{ $currentSignature ? 'Replace Signature' : 'Signature' }}"
                    accept="image/*" tip="PNG, JPG (Max 2MB)" />

                {{-- Stamp --}}
                @if ($currentStamp)
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Stamp</label>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 cursor-pointer"
                                    wire:click="$set('showStampModal', true)">
                                    <img src="{{ asset('storage/' . $currentStamp) }}" class="h-16 rounded border"
                                        alt="Stamp">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            kisantra-stamp.png</p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">Click to preview</p>
                                    </div>
                                </div>
                                <flux:button variant="danger" size="sm" wire:click="deleteExistingStamp">Delete
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endif
                <x-upload wire:model="stamp" label="{{ $currentStamp ? 'Replace Stamp' : 'Stamp' }}" accept="image/*"
                    tip="PNG, JPG (Max 2MB)" />
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                <x-action-message on="company-updated">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>
    </x-settings.layout>

    {{-- Preview Modals --}}
    <flux:modal name="logo-preview" :show="$showLogoModal" wire:model="showLogoModal">
        @if ($currentLogo)
            <img src="{{ asset('storage/' . $currentLogo) }}" class="w-full" alt="Logo Preview">
        @endif
    </flux:modal>

    <flux:modal name="signature-preview" :show="$showSignatureModal" wire:model="showSignatureModal">
        @if ($currentSignature)
            <img src="{{ asset('storage/' . $currentSignature) }}" class="w-full" alt="Signature Preview">
        @endif
    </flux:modal>

    <flux:modal name="stamp-preview" :show="$showStampModal" wire:model="showStampModal">
        @if ($currentStamp)
            <img src="{{ asset('storage/' . $currentStamp) }}" class="w-full" alt="Stamp Preview">
        @endif
    </flux:modal>
</section>
