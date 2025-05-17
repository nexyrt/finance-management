<section class="w-full">
    <!-- Improved header with more spacing and better typography -->
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-50">Testing Page</h1>
        <p class="mt-2 text-zinc-400">This is a testing page for Livewire components.</p>
    </header>

    <!-- Action panel with better styling -->
    @if ($selected)
        <div class="mb-6 bg-zinc-800/50 border border-zinc-700 rounded-md p-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-100">Selected Clients</h2>
                    <p class="mt-1 text-zinc-400">You have selected {{ count($selected) }} clients.</p>
                </div>
                <flux:button wire:click="deleteSelected" variant="danger" class="mt-3 sm:mt-0">Danger</flux:button>
            </div>
        </div>
    @endif

    <!-- Alert messages with improved styling -->
    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-900/40 border border-red-700/50 rounded-md text-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-900/40 border border-green-700/50 rounded-md text-green-200">
            {{ session('message') }}
        </div>
    @endif

    <!-- Improved table with border and better spacing -->
    <div class="overflow-hidden border border-zinc-700 rounded-md shadow-md">
        <table class="w-full border-collapse">
            <thead class="bg-zinc-800/80">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Client ID
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Email
                    </th>
                </tr>
            </thead>
            <tbody class="bg-zinc-900 divide-y divide-zinc-800">
                @foreach ($clients as $client)
                    <tr class="hover:bg-zinc-800/70 transition-colors duration-200">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-300">
                            <!-- KEY CHANGE: Check if client ID is in the selected array rather than using wire:model -->
                            <flux:checkbox value="{{ $client->id }}" wire:model.live="selected" wire:key='{{$client->id}}' />
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-300">{{ $client->id }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-zinc-200">{{ $client->name }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-300">
                            @if ($client->email)
                                <span class="text-blue-400">{{ $client->email }}</span>
                            @else
                                <span class="text-zinc-500">No email</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-zinc-800/50 border-t border-zinc-700">
            {{ $clients->links() }}
        </div>
    </div>
</section>
