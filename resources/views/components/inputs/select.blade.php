{{-- resources/views/components/inputs/select.blade.php --}}

@props([
    'options' => [],
    'placeholder' => 'Select an option',
    'selected' => '',
    'label' => null,
    'id' => 'select-' . uniqid(),
    'modalMode' => false,
])

@once
    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush
@endonce

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-zinc-50 mb-1">{{ $label }}</label>
    @endif

    <div 
        x-data="{
            open: false,
            selectedValue: @js($selected),
            options: @js($options),
            modalMode: {{ $modalMode ? 'true' : 'false' }},
            init() {
                this.$watch('selectedValue', value => {
                    if (this.$refs.hiddenInput) {
                        this.$refs.hiddenInput.dispatchEvent(new Event('input'));
                        this.$refs.hiddenInput.dispatchEvent(new Event('change'));
                    }
                });
                
                if (this.modalMode) {
                    this.$el.style.position = 'relative';
                    this.$el.style.zIndex = '50';
                }
            },
            toggleDropdown() {
                this.open = !this.open;
            },
            getSelectedLabel() {
                const option = this.options.find(opt => opt.value === this.selectedValue);
                return option ? option.label : this.selectedValue;
            }
        }" 
        class="relative font-sans" 
        x-cloak
        {{ $attributes->only(['class'])->merge(['class' => '']) }}
    >
        <!-- Hidden input -->
        <input 
            type="hidden" 
            x-ref="hiddenInput" 
            x-model="selectedValue" 
            {{ $attributes->except(['class']) }} 
        />

        <!-- Select trigger -->
        <button 
            @click="toggleDropdown()" 
            type="button" 
            x-ref="button" 
            id="{{ $id }}"
            class="flex items-center justify-between w-full px-3 py-2.5 text-left bg-zinc-800 border border-zinc-700 rounded-xl shadow-sm hover:border-zinc-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150 ease-in-out"
        >
            <!-- Selected value display -->
            <span 
                x-text="selectedValue ? getSelectedLabel() : '{{ $placeholder }}'"
                class="text-sm text-zinc-200 truncate"
            ></span>

            <!-- Chevron icon -->
            <svg 
                :class="{ 'transform rotate-180': open }"
                class="w-4 h-4 ml-2 transition-transform duration-200 ease-in-out text-zinc-400" 
                fill="none"
                stroke="currentColor" 
                viewBox="0 0 24 24" 
                xmlns="http://www.w3.org/2000/svg"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown -->
        <div 
            x-show="open" 
            @click.away="open = false" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            :class="{ 'z-[9999]': modalMode, 'z-50': !modalMode }"
            class="absolute w-full mt-1 bg-zinc-800 border border-zinc-700 rounded-md shadow-lg overflow-hidden"
            style="display: none;"
        >
            <div class="max-h-60 overflow-y-auto py-1">
                <template x-for="option in options" :key="option.value">
                    <div 
                        @click="selectedValue = option.value; open = false"
                        :class="{
                            'bg-indigo-600/50 text-white': selectedValue === option.value,
                            'text-zinc-200 hover:bg-zinc-700': selectedValue !== option.value
                        }"
                        class="px-3 py-2 text-sm cursor-pointer transition-colors duration-150 ease-in-out"
                    >
                        <span x-text="option.label"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
