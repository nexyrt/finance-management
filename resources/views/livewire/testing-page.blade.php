<div class="max-w-7xl mx-auto p-4">
    <x-select.styled :options="$users" searchable />

    <x-wireui-currency label="Currency" placeholder="Currency" thousands="." decimal="," precision="4" />
</div>
