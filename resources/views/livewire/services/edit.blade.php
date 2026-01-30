{{-- resources/views/livewire/services/edit.blade.php --}}

<x-modal wire title="{{ __('pages.edit_service') }}" size="2xl">
    @if ($service)
        <form id="edit-form" wire:submit="save" class="space-y-4">
            <x-input wire:model="name" label="{{ __('pages.service_name') }}" hint="{{ __('pages.service_description') }}" required />

            <x-select.styled wire:model="type" label="{{ __('common.category') }}" :options="[
                ['label' => 'Perizinan', 'value' => 'Perizinan'],
                ['label' => 'Administrasi Perpajakan', 'value' => 'Administrasi Perpajakan'],
                ['label' => 'Digital Marketing', 'value' => 'Digital Marketing'],
                ['label' => 'Sistem Digital', 'value' => 'Sistem Digital'],
            ]"
                placeholder="Pilih kategori layanan..." required />

            <x-input wire:model="price" label="{{ __('pages.service_price') }}" hint="Masukkan harga dalam Rupiah (contoh: 500000)"
                prefix="Rp" x-mask:dynamic="$money($input, '.')" required />
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="gray">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="edit-form" color="primary" loading="save" icon="check">
                    {{ __('common.save') }}
                </x-button>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
