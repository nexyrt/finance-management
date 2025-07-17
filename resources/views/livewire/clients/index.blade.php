<section class="w-full p-6 bg-white dark:bg-zinc-800">
    <!-- You can control the items of the quantity selector -->
    <x-table :$headers :$rows :$sort filter :quantity="[5, 10, 50]" paginate selectable wire:model.live="selected">
        @interact('column_status', $row)
            <x-badge text="{{ $row->status }}" />
        @endinteract
    </x-table>

    <!-- Menampilkan informasi row yang dipilih -->
    @if (count($selected) > 0)
        <div class="mt-4">
            <h3 class="text-lg font-semibold mb-2">Row yang dipilih ({{ count($selected) }} items):</h3>

            <!-- Opsi 1: Tampilkan sebagai daftar ID -->
            <div class="mb-3">
                <strong>IDs:</strong>
                @foreach ($selected as $id)
                    <x-badge text="{{ $id }}" class="mr-1" />
                @endforeach
            </div>
        </div>
    @else
        <p class="mt-4 text-gray-500">Tidak ada row yang dipilih</p>
    @endif

</section>
