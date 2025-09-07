{{-- resources/views/livewire/services/create.blade.php --}}

<div>
    <!-- Trigger Button -->
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary">
        Tambah Layanan
    </x-button>

    <!-- Modal -->
    <x-modal wire size="2xl" center persistent>
        <x-slot:title>
            Tambah Layanan Baru
        </x-slot:title>

        <form id="create-form" wire:submit="save" class="space-y-4">
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
                <x-button type="submit" form="create-form" color="primary" loading="save" icon="check">
                    Simpan Layanan
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
