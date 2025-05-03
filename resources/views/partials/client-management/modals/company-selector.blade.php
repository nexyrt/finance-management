<flux:modal name="company-selector" title="Select Companies" size="md">
    <h3 class="font-semibold text-lg text-zinc-100 pb-5">Select Companies</h3>
    <div class="mb-4">
        <flux:input wire:model.live.debounce.300ms="companySearch" placeholder="Search companies..." />
    </div>

    <div class="max-h-60 overflow-y-auto">
        @if (count($availableCompanies) > 0)
            <ul class="divide-y divide-zinc-700">
                @foreach ($availableCompanies as $company)
                    <li class="py-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                value="{{ $company['id'] }}"
                                wire:click="toggleCompany({{ $company['id'] }})"
                                {{ in_array($company['id'], $selectedCompanies) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded border-zinc-600 bg-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-zinc-800"
                            >
                            <span class="text-zinc-200">{{ $company['name'] }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="py-6 text-center text-zinc-400">
                <p>No companies found. Try a different search term.</p>
            </div>
        @endif
    </div>

    <div class="mt-6 flex justify-end">
        <flux:modal.close wire:click="closeCompanySelector">
            <x-shared.button variant="primary">
                Done
            </x-shared.button>
        </flux:modal.close>
    </div>
</flux:modal>