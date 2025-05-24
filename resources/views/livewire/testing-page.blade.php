<section class="w-full bg-zinc-800 text-gray-200 p-6">
    <input x-data x-init="flatpickr($el, {
        disable: [
            {
                from: '2025-05-05',
                to: '2025-05-10'
            },
            {
                from: '2023-10-10',
                to: '2023-10-15'
            }
        ],
        onChange: function(selectedDates, dateStr, instance) {
            @this.set('date', dateStr)
        }
    })" type="text" class="bg-zinc-600 rounded-xl px-3 py-2.5" placeholder="Pilih tanggal">
    <p>{{$date}}</p>
</section>
