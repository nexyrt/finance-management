<section class="w-full">
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-white">Testing Page</h1>
        <p class="mt-2 text-gray-400">This is a testing page for Livewire components.</p>
    </header>


    @if ($selected)
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-white">Selected Clients</h2>
            <p class="mt-2 text-gray-400">You have selected {{ count($selected) }} clients.</p>
        </div>
    @endif

    <table class="w-full border-collapse">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/6"></th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/6">Client
                ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-2/5">Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-2/5">Email
            </th>
        </tr>
        </thead>
        <tbody class="bg-gray-700 divide-y divide-gray-600">
            @foreach ($clients as $client)
                <tr class="hover:bg-gray-600 transition-colors duration-200">
                    <td class="whitespace-nowrap text-sm text-gray-300">
                        <flux:checkbox value="{{ $client->id }}" wire:model.live="selected" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $client->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">{{ $client->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $client->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</section>
