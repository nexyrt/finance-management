<div class="space-y-6">
    <x-date wire:model.live="dateRange" label="Periode" range placeholder="Pilih range tanggal..." />
    <x-button wire:click="filter">Filter</x-button>
</div>
