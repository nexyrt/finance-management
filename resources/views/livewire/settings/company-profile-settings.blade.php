<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Company Profile')" :subheading="__('Manage company information for invoices')">
        <form wire:submit="updateCompanyProfile" class="my-6 space-y-6">
            <x-input wire:model="name" label="Company Name" required />
            <x-textarea wire:model="address" label="Address" rows="3" required />

            <div class="grid grid-cols-2 gap-4">
                <x-input wire:model="email" label="Email" type="email" required />
                <x-input wire:model="phone" label="Phone" required />
            </div>

            <hr class="border-gray-200 dark:border-dark-600" />

            <div class="grid grid-cols-2 gap-4">
                <x-input wire:model="finance_manager_name" label="Finance Manager" required />
                <x-input wire:model="finance_manager_position" label="Position" required />
            </div>

            <hr class="border-gray-200 dark:border-dark-600" />

            <x-checkbox wire:model.boolean="is_pkp" label="PKP (Pengusaha Kena Pajak)" />

            @if ($is_pkp)
                <div class="grid grid-cols-2 gap-4">
                    <x-input wire:model="npwp" label="NPWP" />
                    <x-input wire:model="ppn_rate" label="PPN Rate (%)" type="number" step="0.01" />
                </div>
            @endif

            <hr class="border-gray-200 dark:border-dark-600" />

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
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">letter-head.png
                                        </p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">Click to preview</p>
                                    </div>
                                </div>
                                <x-button color="red" sm wire:click="deleteExistingLogo">Delete</x-button>
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
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            pdf-signature.png</p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">Click to preview</p>
                                    </div>
                                </div>
                                <x-button color="red" sm wire:click="deleteExistingSignature">Delete</x-button>
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
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            kisantra-stamp.png</p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">Click to preview</p>
                                    </div>
                                </div>
                                <x-button color="red" sm wire:click="deleteExistingStamp">Delete</x-button>
                            </div>
                        </div>
                    </div>
                @endif
                <x-upload wire:model="stamp" label="{{ $currentStamp ? 'Replace Stamp' : 'Stamp' }}" accept="image/*"
                    tip="PNG, JPG (Max 2MB)" />
            </div>

            <div class="flex items-center gap-4">
                <x-button color="primary" type="submit">{{ __('Save') }}</x-button>
                <x-action-message on="company-updated">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>
    </x-settings.layout>

    {{-- Preview Modals --}}
    <x-modal title="Logo Preview" wire="showLogoModal" size="lg" center>
        @if ($currentLogo)
            <img src="{{ asset('storage/' . $currentLogo) }}" class="w-full" alt="Logo Preview">
        @endif
    </x-modal>

    <x-modal title="Signature Preview" wire="showSignatureModal" size="lg" center>
        @if ($currentSignature)
            <img src="{{ asset('storage/' . $currentSignature) }}" class="w-full" alt="Signature Preview">
        @endif
    </x-modal>

    <x-modal title="Stamp Preview" wire="showStampModal" size="lg" center>
        @if ($currentStamp)
            <img src="{{ asset('storage/' . $currentStamp) }}" class="w-full" alt="Stamp Preview">
        @endif
    </x-modal>
</section>
