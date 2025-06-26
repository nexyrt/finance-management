{{-- resources/views/livewire/testing-page.blade.php --}}

<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Searchable Dropdown Testing Page
        </h1>
        
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                <p class="text-blue-800 dark:text-blue-200">{{ session('message') }}</p>
            </div>
        @endif
        
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif
        
        {{-- Form --}}
        <form wire:submit="submitForm" class="space-y-6">
            
            {{-- Services Dropdown (from database) --}}
            <div>
                <x-inputs.searchable-dropdown
                    name="selectedService"
                    label="Select Service"
                    placeholder="Type to search services..."
                    :options="$services"
                    value-field="id"
                    text-field="name"
                    wire:model="selectedService"
                    required
                    class="w-full"
                />
                @error('selectedService')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Clients Dropdown (from database) --}}
            <div>
                <x-inputs.searchable-dropdown
                    name="selectedClient"
                    label="Select Client"
                    placeholder="Type to search clients..."
                    :options="$clients"
                    value-field="id"
                    text-field="name"
                    wire:model="selectedClient"
                    required
                    class="w-full"
                />
                @error('selectedClient')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Bank Accounts Dropdown (from database) --}}
            <div>
                <x-inputs.searchable-dropdown
                    name="selectedBank"
                    label="Select Bank Account"
                    placeholder="Type to search bank accounts..."
                    :options="$bankAccounts"
                    value-field="id"
                    text-field="account_name"
                    wire:model="selectedBank"
                    required
                    class="w-full"
                />
                @error('selectedBank')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Cities Dropdown (from array) --}}
            <div>
                <x-inputs.searchable-dropdown
                    name="selectedCity"
                    label="Select City"
                    placeholder="Type to search cities..."
                    :options="$cities"
                    value-field="id"
                    text-field="name"
                    wire:model="selectedCity"
                    required
                    class="w-full"
                />
                @error('selectedCity')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Custom Options Example (mixed data types) --}}
            <div>
                <x-inputs.searchable-dropdown
                    name="customOption"
                    label="Custom Options Example"
                    placeholder="Type to search..."
                    :options="[
                        ['value' => 'option1', 'label' => 'Option One'],
                        ['value' => 'option2', 'label' => 'Option Two'],
                        'Simple String Option',
                        (object)['id' => 'obj1', 'name' => 'Object Option']
                    ]"
                    value-field="value"
                    text-field="label"
                    class="w-full"
                />
            </div>
            
            {{-- Submit Button --}}
            <div class="flex justify-end space-x-4">
                <button type="button" 
                        wire:click="$refresh"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Refresh
                </button>
                
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Submit Form
                </button>
            </div>
        </form>
        
        {{-- Display Current Values --}}
        <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Current Selected Values:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Service:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">
                        {{ $selectedService ?: 'None selected' }}
                        @if($selectedService)
                            ({{ $services->find($selectedService)?->name ?? 'Unknown' }})
                        @endif
                    </span>
                </div>
                
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Client:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">
                        {{ $selectedClient ?: 'None selected' }}
                        @if($selectedClient)
                            ({{ $clients->find($selectedClient)?->name ?? 'Unknown' }})
                        @endif
                    </span>
                </div>
                
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Bank:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">
                        {{ $selectedBank ?: 'None selected' }}
                        @if($selectedBank)
                            ({{ $bankAccounts->find($selectedBank)?->account_name ?? 'Unknown' }})
                        @endif
                    </span>
                </div>
                
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">City:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">
                        {{ $selectedCity ?: 'None selected' }}
                        @if($selectedCity)
                            ({{ collect($cities)->firstWhere('id', $selectedCity)['name'] ?? 'Unknown' }})
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        {{-- Usage Instructions --}}
        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
            <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Usage Instructions:</h4>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                <li>• Type to filter options in real-time</li>
                <li>• Use arrow keys to navigate options</li>
                <li>• Press Enter to select highlighted option</li>
                <li>• Press Escape to close dropdown</li>
                <li>• Click X button to clear selection</li>
                <li>• All selections are automatically saved via Livewire</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Listen for form reset event from Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('form-reset', () => {
            // Small delay to ensure Livewire has finished updating the DOM
            setTimeout(() => {
                // Re-initialize all searchable dropdowns
                if (window.initSearchableDropdowns) {
                    window.initSearchableDropdowns();
                }
                
                // Clear any existing dropdown states
                document.querySelectorAll('[data-inputs.searchable-dropdown]').forEach(dropdown => {
                    const input = dropdown.querySelector('[data-input]');
                    const clearBtn = dropdown.querySelector('[data-clear]');
                    
                    if (input) {
                        input.value = '';
                    }
                    
                    if (clearBtn) {
                        clearBtn.style.display = 'none';
                    }
                });
            }, 100);
        });
    });
    
    // Additional debugging - remove this in production
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized');
        
        // Debug: Log when dropdowns are reinitialized
        const originalInit = window.initSearchableDropdowns;
        if (originalInit) {
            window.initSearchableDropdowns = function() {
                console.log('Reinitializing searchable dropdowns...');
                originalInit();
            };
        }
    });
</script>
@endpush