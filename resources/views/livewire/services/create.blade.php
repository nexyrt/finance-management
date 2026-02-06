{{-- resources/views/livewire/services/create.blade.php --}}

<div>
    <!-- Trigger Button -->
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary">
        {{ __('pages.add_service') }}
    </x-button>

    <!-- Modal -->
    <x-modal wire size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="plus" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.add_service') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Tambahkan layanan baru ke sistem</p>
                </div>
            </div>
        </x-slot:title>

        <form id="create-form" wire:submit="save" class="space-y-4">
            <x-input wire:model="name" label="{{ __('pages.service_name') }}" hint="{{ __('pages.service_description') }}" placeholder="Contoh: Konsultasi Pajak Tahunan" required />

            <x-select.styled wire:model="type" label="{{ __('common.category') }}" :options="[
                ['label' => 'Perizinan', 'value' => 'Perizinan'],
                ['label' => 'Administrasi Perpajakan', 'value' => 'Administrasi Perpajakan'],
                ['label' => 'Digital Marketing', 'value' => 'Digital Marketing'],
                ['label' => 'Sistem Digital', 'value' => 'Sistem Digital'],
            ]"
                placeholder="Pilih kategori layanan..." required />

            <x-currency-input wire:model="price" label="{{ __('pages.service_price') }}"
                hint="Masukkan harga dalam Rupiah (contoh: 500000)" placeholder="0" prefix="Rp" required />
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="create-form" color="primary" loading="save" icon="check" class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('common.save') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
