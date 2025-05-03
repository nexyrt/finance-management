<flux:modal name="owner-selector" title="Select Individual Owners" size="md">
    <h3 class="font-semibold text-lg text-zinc-100 pb-5">Select Individual Owners</h3>
    <div class="mb-4">
        <flux:input wire:model.live.debounce.300ms="ownerSearch" placeholder="Search individuals..." />
    </div>

    <div class="max-h-60 overflow-y-auto">
        @if (count($availableOwners) > 0)
            <ul class="divide-y divide-zinc-700">
                @foreach ($availableOwners as $owner)
                    <li class="py-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                value="{{ $owner['id'] }}"
                                wire:click="toggleOwner({{ $owner['id'] }})"
                                {{ in_array($owner['id'], $selectedOwners) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded border-zinc-600 bg-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-zinc-800"
                            >
                            <span class="text-zinc-200">{{ $owner['name'] }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="py-6 text-center text-zinc-400">
                <p>No individuals found. Try a different search term.</p>
            </div>
        @endif
    </div>

    <div class="mt-6 flex justify-end">
        <flux:modal.close wire:click="closeOwnerSelector">
            <x-shared.button variant="primary">
                Done
            </x-shared.button>
        </flux:modal.close>
    </div>
</flux:modal>