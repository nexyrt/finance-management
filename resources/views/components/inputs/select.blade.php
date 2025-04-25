{{-- resources/views/components/inputs/select.blade.php --}}

@props([
    'options' => [],
    'placeholder' => 'Select an option',
    'selected' => '',
    'label' => null,
    'id' => 'select-' . uniqid(),
    'modalMode' => false, // Add this prop to handle modal context
])

@once
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
@endonce

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-zinc-50 mb-1">{{ $label }}</label>
    @endif

    <div x-data="{
        open: false,
        selectedValue: @js($selected),
        options: @js($options),
        modalMode: {{ $modalMode ? 'true' : 'false' }},
        init() {
            this.$watch('selectedValue', value => {
                // Use $refs to access the hidden input element
                if (this.$refs.hiddenInput) {
                    // Dispatch events that Livewire listens for
                    this.$refs.hiddenInput.dispatchEvent(new Event('input'));
                    this.$refs.hiddenInput.dispatchEvent(new Event('change'));
                }
            });
        },
        toggleDropdown() {
            this.open = !this.open;
        }
    }" class="relative font-sans" x-cloak
        {{ $attributes->only(['class'])->merge(['class' => '']) }}>
        <!-- Hidden input with x-ref that handles the actual form submission or Livewire binding -->
        <input type="hidden" x-ref="hiddenInput" x-model="selectedValue" {{ $attributes->except(['class']) }} />

        <!-- Custom select trigger -->
        <button @click="toggleDropdown()" type="button" x-ref="button" id="{{ $id }}"
            class="flex items-center justify-between w-full px-3 py-2 text-left bg-zinc-900 border border-zinc-700 rounded-md shadow-sm hover:border-zinc-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150 ease-in-out">
            <!-- Selected value display -->
            <span
                x-text="selectedValue ? (options.find(opt => opt.value === selectedValue)?.label || selectedValue) : '{{ $placeholder }}'"
                class="text-sm text-zinc-200 truncate">
            </span>

            <!-- Chevron icon that rotates -->
            <svg :class="{ 'transform rotate-180': open }"
                class="w-4 h-4 ml-2 transition-transform duration-200 ease-in-out text-zinc-400" fill="none"
                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown -->
        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="absolute z-50 w-full mt-1 bg-blue-900 bg-opacity-95 border border-zinc-700 rounded-md shadow-lg overflow-hidden"
            style="display: none;">
            <div class="max-h-60 overflow-y-auto py-1">
                <template x-for="option in options" :key="option.value">
                    <div @click="selectedValue = option.value; open = false"
                        :class="{
                            'bg-indigo-700/70 text-white': selectedValue === option.value,
                            'text-zinc-200 hover:bg-zinc-800': selectedValue !== option.value
                        }"
                        class="px-3 py-2 text-sm cursor-pointer transition-colors duration-150 ease-in-out">
                        <span x-text="option.label"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
