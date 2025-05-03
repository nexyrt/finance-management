<div class="bg-zinc-900 border border-zinc-700 rounded-md shadow-sm overflow-hidden mx-4">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-700">
            <thead class="bg-zinc-800">
                <tr>
                    <th scope="col" class="pl-6 pr-3 py-3">
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                :checked="selectAll" 
                                @click="toggleAll"
                                class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded border-zinc-600 bg-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-zinc-800" 
                            />
                        </div>
                    </th>
                    <th 
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('name')"
                    >
                        <div class="flex items-center space-x-1">
                            <span>Name</span>
                            <span class="text-zinc-400">
                                @if ($sortField === 'name')
                                    @if ($sortDirection === 'asc')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                        Contact Information
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">
                        Tax ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse ($clients as $client)
                    <tr 
                        class="hover:bg-zinc-800/60 transition-colors duration-150 ease-in-out"
                        :class="{ 'bg-zinc-800/30': isSelected({{ $client->id }}) }"
                    >
                        <td class="pl-6 pr-3 py-4 whitespace-nowrap">
                            <input 
                                type="checkbox" 
                                :checked="isSelected({{ $client->id }})"
                                @click="toggleClient({{ $client->id }}, $event)"
                                class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded border-zinc-600 bg-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-zinc-800" 
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-100">{{ $client->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->type === 'individual' ? 'bg-blue-900 text-blue-200' : 'bg-purple-900 text-purple-200' }}">
                                {{ ucfirst($client->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-300">
                                @if ($client->email)
                                    <div class="flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $client->email }}</span>
                                    </div>
                                @endif
                                @if ($client->phone)
                                    <div class="flex items-center space-x-1 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span>{{ $client->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                            {{ $client->tax_id ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <flux:modal.trigger 
                                    name="view-client-{{ $client->id }}"
                                    wire:click="loadClientDetails({{ $client->id }})"
                                >
                                    <x-shared.icon-button variant="info">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </x-shared.icon-button>
                                </flux:modal.trigger>

                                <flux:modal.trigger 
                                    name="edit-client"
                                    wire:click="editClient({{ $client->id }})"
                                >
                                    <button class="text-amber-400 hover:text-amber-300 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </flux:modal.trigger>

                                <flux:modal.trigger 
                                    name="delete-client"
                                    wire:click="confirmDelete({{ $client->id }})"
                                >
                                    <button class="text-red-400 hover:text-red-300 transition-colors duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </flux:modal.trigger>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p>No clients found. Start by adding a new client.</p>
                                <flux:modal.trigger name="create-client" wire:click="prepareCreate">
                                    <x-shared.button variant="primary" class="mt-3">
                                        Add First Client
                                    </x-shared.button>
                                </flux:modal.trigger>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($perPage !== 'All')
        <div class="border-t border-zinc-700 px-4 py-3">
            <div class="flex flex-col sm:flex-row items-center justify-between">
                {{ $clients->links() }}
                
                <div class="mt-3 sm:mt-0 flex items-center space-x-2">
                    <form wire:submit.prevent="goToPage" class="flex items-center space-x-2">
                        <label for="gotopage" class="text-sm text-zinc-400">Go to page:</label>
                        <input 
                            type="number" 
                            id="gotopage" 
                            wire:model="gotoPage" 
                            min="1" 
                            max="{{ $clients->lastPage() }}" 
                            class="w-16 px-2 py-1 bg-zinc-900 border border-zinc-700 rounded-md text-zinc-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                        <x-shared.button type="submit" variant="secondary" size="xs">Go</x-shared.button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="border-t border-zinc-700 px-4 py-3 text-sm text-zinc-400 text-center">
            Showing all {{ count($clients) }} clients
        </div>
    @endif
</div>