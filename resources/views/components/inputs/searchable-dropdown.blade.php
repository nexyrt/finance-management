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
    'class' => '',
    'containerClass' => '',
    'dropdownClass' => '',
    'wire' => null,
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
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
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
            {{ $attributes->merge(['class' => 'w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ' . $class]) }}
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
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                style="display: {{ $selected ? 'block' : 'none' }};"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
        
        {{-- Dropdown Icon --}}
        <div class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
    
    {{-- Dropdown Menu --}}
    <div id="{{ $dropdownId }}" 
         data-dropdown
         class="absolute top-full left-0 right-0 z-50 hidden bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto {{ $dropdownClass }}">
         
        {{-- Options List --}}
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" 
            id="{{ $listId }}" 
            data-list>
            @foreach($processedOptions as $option)
                <li>
                    <a href="#" 
                       data-value="{{ $option['value'] }}" 
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer transition-colors">
                        {{ $option['text'] }}
                    </a>
                </li>
            @endforeach
        </ul>
        
        {{-- No Results Message --}}
        <div data-no-results 
             class="hidden px-4 py-2 text-gray-500 dark:text-gray-400 text-center">
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