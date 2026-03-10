<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Testing Page</h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">Eksperimen komponen</p>
        </div>
    </div>

    {{-- Repeater --}}
    <x-card>
        <x-form.repeater
            wire:model="items"
            wire:call="save"
            label="Tambah Layanan"
            :fields="[
                ['key' => 'name',  'label' => 'Nama Layanan', 'type' => 'text',     'span' => 5, 'placeholder' => 'Nama layanan'],
                ['key' => 'price', 'label' => 'Harga',        'type' => 'currency', 'span' => 3],
                ['key' => 'type',  'label' => 'Tipe',         'type' => 'select',   'span' => 3,
                 'options' => ['Perizinan', 'Administrasi Perpajakan', 'Digital Marketing', 'Sistem Digital']],
            ]">
            Simpan Semua
        </x-form.repeater>
    </x-card>

</div>
