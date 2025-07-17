<section class="w-full p-6 bg-white dark:bg-zinc-800">
    <!-- You can control the items of the quantity selector -->
    <x-table :$headers :$rows :$sort filter :quantity="[5, 10, 50]" loading paginate>
        @interact('column_status', $row)
            <x-badge text="{{$row->status}}" />
        @endinteract
    </x-table>
</section>
