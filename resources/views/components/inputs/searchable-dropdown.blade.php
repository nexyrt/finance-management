{{-- resources/views/components/searchable-dropdown.blade.php --}}

@props([
    'name' => '',
    'label' => '',
    'placeholder' => 'Type to search...',
    'options' => [],
    'valueField' => 'id',
    'textField' => 'name',
    'selected' => '',
    'required' => false,
    'disabled' => false,
    'clearable' => true,
    'noResultsText' => 'No results found',
    'containerClass' => '',
    'dropdownClass' => '',
    'wire' => null,
    'variant' => 'outline',
    'size' => null,
])

@php
    $inputId = 'searchable-' . Str::random(8);
    $dropdownId = $inputId . '-dropdown';
    $listId = $inputId . '-list';
    $hiddenId = $inputId . '-hidden';
    
    // Process options array
    $processedOptions = collect($options)->map(function ($option) use ($valueField, $textField) {
        if (is_array($option)) {
            return [
                'value' => $option[$valueField] ?? '',
                'text' => $option[$textField] ?? '',
            ];
        } elseif (is_object($option)) {
            return [
                'value' => $option->{$valueField} ?? '',
                'text' => $option->{$textField} ?? '',
            ];
        } else {
            return [
                'value' => $option,
                'text' => $option,
            ];
        }
    });
    
    // Find selected text
    $selectedText = '';
    if ($selected) {
        $selectedOption = $processedOptions->firstWhere('value', $selected);
        $selectedText = $selectedOption['text'] ?? '';
    }
@endphp

<div class="relative {{ $containerClass }}" 
     data-searchable-dropdown
     @if($disabled) data-disabled @endif>
     
    {{-- Label --}}
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    {{-- Input Container --}}
    <div class="relative">
        {{-- Visible Input --}}
        <input 
            type="text" 
            id="{{ $inputId }}"
            data-input
            placeholder="{{ $placeholder }}"
            value="{{ $selectedText }}"
            autocomplete="off"
            @if($disabled) disabled @endif
            @if($required) required @endif
            class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none {{ match($size) { 'sm' => 'text-sm py-1.5 h-8 leading-[1.125rem] ps-3', 'xs' => 'text-xs py-1.5 h-6 leading-[1.125rem] ps-3', default => 'text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-3' } }} {{ $clearable && !$disabled ? 'pe-16' : 'pe-10' }} {{ match($variant) { 'outline' => 'bg-white dark:bg-white/10 dark:disabled:bg-white/[7%]', 'filled' => 'bg-zinc-800/5 dark:bg-white/10 dark:disabled:bg-white/[7%]' } }} {{ match($variant) { 'outline' => 'text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500', 'filled' => 'text-zinc-700 placeholder-zinc-500 disabled:placeholder-zinc-400 dark:text-zinc-200 dark:placeholder-white/60 dark:disabled:placeholder-white/40' } }} {{ match($variant) { 'outline' => 'shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5', 'filled' => 'border-0' } }} focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
        
        {{-- Hidden Input for actual value --}}
        <input 
            type="hidden" 
            name="{{ $name }}"
            id="{{ $hiddenId }}"
            data-hidden
            value="{{ $selected }}"
            @if($wire) wire:model="{{ $wire }}" @endif
        >
        
        {{-- Clear Button --}}
        @if($clearable && !$disabled)
            <button 
                type="button"
                data-clear
                class="absolute {{ match($size) { 'sm' => 'right-8 top-1/2', 'xs' => 'right-6 top-1/2', default => 'right-10 top-1/2' } }} transform -translate-y-1/2 text-zinc-400/75 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                style="display: {{ $selected ? 'block' : 'none' }};"
            >
                <svg class="{{ match($size) { 'sm' => 'w-3 h-3', 'xs' => 'w-3 h-3', default => 'w-4 h-4' } }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
        
        {{-- Dropdown Icon --}}
        <div class="absolute {{ match($size) { 'sm' => 'right-2 top-1/2', 'xs' => 'right-1.5 top-1/2', default => 'right-3 top-1/2' } }} transform -translate-y-1/2 pointer-events-none">
            <svg class="{{ match($size) { 'sm' => 'w-3 h-3', 'xs' => 'w-3 h-3', default => 'w-4 h-4' } }} text-zinc-400/75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
    
    {{-- Dropdown Menu --}}
    <div id="{{ $dropdownId }}" 
         data-dropdown
         class="absolute top-full left-0 right-0 z-50 hidden bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto {{ $dropdownClass }}">
         
        {{-- Options List --}}
        <ul class="py-1 text-sm" 
            id="{{ $listId }}" 
            data-list>
            @foreach($processedOptions as $option)
                <li>
                    <a href="#" 
                       data-value="{{ $option['value'] }}" 
                       class="block px-4 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-colors">
                        {{ $option['text'] }}
                    </a>
                </li>
            @endforeach
        </ul>
        
        {{-- No Results Message --}}
        <div data-no-results 
             class="hidden px-4 py-2 text-zinc-500 dark:text-zinc-400 text-center">
            {{ $noResultsText }}
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            // Show/hide clear button based on selection
            document.addEventListener('dropdown:selected', function(e) {
                const clearBtn = e.target.querySelector('[data-clear]');
                if (clearBtn) {
                    clearBtn.style.display = 'block';
                }
            });
            
            document.addEventListener('dropdown:cleared', function(e) {
                const clearBtn = e.target.querySelector('[data-clear]');
                if (clearBtn) {
                    clearBtn.style.display = 'none';
                }
            });
        </script>
    @endpush
@endonce