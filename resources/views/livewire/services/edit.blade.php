{{-- resources/views/livewire/services/edit.blade.php --}}

<x-modal wire title="Edit Layanan" size="2xl">
    @if ($service)
        <form id="edit-form" wire:submit="save" class="space-y-4">
            <x-input wire:model="name" label="Nama Layanan" hint="Masukkan nama layanan yang akan ditawarkan" required />

            <x-select.styled wire:model="type" label="Kategori Layanan" :options="[
                ['label' => 'Perizinan', 'value' => 'Perizinan'],
                ['label' => 'Administrasi Perpajakan', 'value' => 'Administrasi Perpajakan'],
                ['label' => 'Digital Marketing', 'value' => 'Digital Marketing'],
                ['label' => 'Sistem Digital', 'value' => 'Sistem Digital'],
            ]"
                placeholder="Pilih kategori layanan..." required />

            <x-input wire:model="price" label="Harga Layanan" hint="Masukkan harga dalam Rupiah (contoh: 500000)"
                prefix="Rp" x-mask:dynamic="$money($input, '.')" required />
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="gray">
                    Batal
                </x-button>
                <x-button type="submit" form="edit-form" color="primary" loading="save" icon="check">
                    Perbarui Layanan
                </x-button>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
