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
                <x-upload wire:model="logo" label="Logo" accept="image/*" tip="PNG, JPG (Max 2MB)" />
                @if ($currentLogo)
                    <img src="{{ asset($currentLogo) }}" class="h-16 border rounded" alt="Current">
                @endif

                <x-upload wire:model="signature" label="Signature" accept="image/*" tip="PNG, JPG (Max 2MB)" />
                @if ($currentSignature)
                    <img src="{{ asset($currentSignature) }}" class="h-16 border rounded" alt="Current">
                @endif

                <x-upload wire:model="stamp" label="Stamp" accept="image/*" tip="PNG, JPG (Max 2MB)" />
                @if ($currentStamp)
                    <img src="{{ asset($currentStamp) }}" class="h-16 border rounded" alt="Current">
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                <x-action-message on="company-updated">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
