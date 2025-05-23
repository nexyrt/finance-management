<section class="w-full bg-zinc-800 text-gray-200 p-6">
    <input x-data x-init="flatpickr($el, {
        disable: ['2025-05-25', '2025-05-30'], // tanggal yang mau di-disable
        onChange: function(selectedDates, dateStr, instance) {
            @this.set('date', dateStr)
        }
    })" type="text" class="border rounded-xl p-2" placeholder="Pilih tanggal">
    <p>{{ $date }}</p>
</section>
