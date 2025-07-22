{{-- resources/views/livewire/clients/relationship.blade.php --}}

<x-modal wire="showModal" title="Manage Client Relationships" size="2xl">
    @if($client)
        <div class="space-y-6">
            <!-- Client Info Header -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 flex-shrink-0">
                        <div class="h-12 w-12 rounded-full flex items-center justify-center
                            {{ $client->type === 'individual' ? 'bg-blue-100 dark:bg-blue-900/20' : 'bg-purple-100 dark:bg-purple-900/20' }}">
                            <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-6 h-6 {{ $client->type === 'individual' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" />
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $client->name }}</h3>
                        <div class="flex items-center space-x-2">
                            <x-badge text="{{ ucfirst($client->type) }}" 
                                     color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Managing {{ $relationshipType === 'company' ? 'owned companies' : 'owners' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Relationships Display -->
            @if($client->type === 'individual' && $client->ownedCompanies->count() > 0)
                <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Current Owned Companies:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($client->ownedCompanies as $company)
                            <x-badge text="{{ $company->name }}" color="blue" />
                        @endforeach
                    </div>
                </div>
            @elseif($client->type === 'company' && $client->owners->count() > 0)
                <div class="bg-purple-50 dark:bg-purple-900/10 rounded-lg p-4">
                    <h4 class="font-medium text-purple-900 dark:text-purple-100 mb-2">Current Owners:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($client->owners as $owner)
                            <x-badge text="{{ $owner->name }}" color="purple" />
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Relationship Selection -->
            <div class="space-y-4">
                <div class="border-t pt-4">
                    @if($client->type === 'individual')
                        <x-select.styled 
                            label="Select Companies to Own"
                            wire:model="selectedClients"
                            :options="$availableClients"
                            multiple
                            searchable
                            placeholder="Choose companies..."
                            hint="Select which companies this individual owns"
                        />
                    @else
                        <x-select.styled 
                            label="Select Owners"
                            wire:model="selectedClients"
                            :options="$availableClients"
                            multiple
                            searchable
                            placeholder="Choose owners..."
                            hint="Select individuals who own this company"
                        />
                    @endif
                </div>

                <!-- Relationship Summary -->
                @if(count($selectedClients) > 0)
                    <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded-lg p-3">
                        <div class="flex items-center">
                            <x-icon name="check-circle" class="h-5 w-5 text-green-400 mr-2" />
                            <div class="text-sm text-green-700 dark:text-green-300">
                                <span class="font-medium">{{ count($selectedClients) }} relationship(s) selected</span>
                                @if($client->type === 'individual')
                                    <p class="mt-1">{{ $client->name }} will own the selected companies</p>
                                @else
                                    <p class="mt-1">Selected individuals will own {{ $client->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- No Available Clients -->
                @if(empty($availableClients))
                    <div class="text-center py-6">
                        <x-icon name="users" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                        <p class="text-gray-500 dark:text-gray-400">
                            No available {{ $relationshipType === 'company' ? 'companies' : 'individuals' }} for relationships
                        </p>
                    </div>
                @endif
            </div>

            <!-- Relationship Rules Info -->
            <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                <div class="flex">
                    <x-icon name="information-circle" class="h-5 w-5 text-amber-400 mr-2 mt-0.5" />
                    <div class="text-sm text-amber-700 dark:text-amber-300">
                        <p class="font-medium mb-1">Relationship Rules:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Individuals can own multiple companies</li>
                            <li>Companies can have multiple individual owners</li>
                            <li>Only active clients can form relationships</li>
                            <li>Changes are saved immediately when you click Save</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-slot:footer>
        <x-button wire:click="close" color="secondary">Cancel</x-button>
        <x-button wire:click="save" color="primary" spinner="save">
            Save Relationships
        </x-button>
    </x-slot:footer>
</x-modal>