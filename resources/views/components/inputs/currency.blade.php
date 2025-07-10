{{-- resources/views/components/currency-input.blade.php - FIXED VERSION --}}

@props([
    'label' => 'Nominal',
    'placeholder' => '50.000.000',
    'required' => false,
    'name' => 'amount',
    'size' => null,
    'invalid' => null,
    'value' => null,
    'hint' => null,
    'disabled' => false,
])

@php
    // Cek apakah ada wire:model di attributes
    $wireModelAttribute = null;
    foreach ($attributes->getAttributes() as $key => $value) {
        if (str_starts_with($key, 'wire:model')) {
            $wireModelAttribute = $value;
            break;
        }
    }
    
    // Set invalid state
    $invalid ??= ($name && $errors->has($name));
    
    // Classes untuk input berdasarkan styling Flux outline
    $inputClasses = collect([
        'w-full border rounded-lg block disabled:shadow-none dark:shadow-none disabled:cursor-not-allowed',
        'appearance-none transition-colors duration-200',
        // Size classes
        match ($size) {
            'sm' => 'text-sm py-1.5 h-8 leading-[1.125rem]',
            'xs' => 'text-xs py-1.5 h-6 leading-[1.125rem]',
            default => 'text-base sm:text-sm py-2 h-10 leading-[1.375rem]',
        },
        // Padding untuk prefix "Rp"
        'ps-10 pe-3',
        // Background outline variant
        'bg-white dark:bg-white/10 dark:disabled:bg-white/[7%]',
        // Text color outline variant
        'text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500',
        // Border outline variant
        $invalid 
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' 
            : 'border-zinc-200 border-b-zinc-300/80 dark:border-white/10 dark:disabled:border-white/5 focus:border-blue-500 focus:ring-blue-500/20',
        // Focus states
        'focus:outline-none focus:ring-2'
    ])->join(' ');
@endphp

<div x-data="{
    displayValue: '',
    isDisabled: {{ $disabled ? 'true' : 'false' }},
    wireModelProperty: '{{ $wireModelAttribute }}',
    
    init() {
        // Jika ada wire:model, prioritaskan nilai dari Livewire
        if (this.wireModelProperty) {
            this.loadFromWire();
        } else if ('{{ $value }}') {
            // Jika tidak ada wire:model, gunakan prop value
            this.displayValue = this.formatNumber('{{ $value }}');
        }
    },
    
    loadFromWire() {
        // Load dari Livewire property
        this.$nextTick(() => {
            try {
                let wireValue = $wire.get(this.wireModelProperty);
                console.log('Wire value loaded:', wireValue);
                
                if (wireValue && wireValue !== '' && wireValue !== '0') {
                    this.displayValue = this.formatNumber(wireValue);
                } else if ('{{ $value }}' && !wireValue) {
                    // Jika wire property kosong tapi ada prop value, set wire dengan prop value
                    $wire.set(this.wireModelProperty, '{{ $value }}');
                    this.displayValue = this.formatNumber('{{ $value }}');
                }
            } catch (error) {
                console.log('Error loading wire value:', error);
                // Fallback ke prop value jika ada error
                if ('{{ $value }}') {
                    this.displayValue = this.formatNumber('{{ $value }}');
                }
            }
        });
    },
    
    formatNumber(value) {
        if (!value || value === '0') return '';
        let numbers = value.toString().replace(/\D/g, '');
        if (!numbers) return '';
        return parseInt(numbers).toLocaleString('id-ID');
    },
    
    formatIDR(value) {
        if (this.isDisabled) return this.displayValue;
        
        // Hapus semua non-digit
        let numbers = value.replace(/\D/g, '');

        if (!numbers) {
            if (this.wireModelProperty) {
                $wire.set(this.wireModelProperty, '');
            }
            return '';
        }

        // Update Livewire property jika ada
        if (this.wireModelProperty) {
            $wire.set(this.wireModelProperty, numbers);
        }

        // Format dengan titik pemisah ribuan (format Indonesia)
        return parseInt(numbers).toLocaleString('id-ID');
    },
    
    handleInput(event) {
        if (this.isDisabled) return;
        this.displayValue = this.formatIDR(event.target.value);
    },
    
    handlePaste(event) {
        if (this.isDisabled) return;
        event.preventDefault();
        let paste = (event.clipboardData || window.clipboardData).getData('text');
        this.displayValue = this.formatIDR(paste);
    }
}" 
x-init="
    // Listen untuk perubahan dari Livewire
    if (wireModelProperty) {
        $wire.$watch(wireModelProperty, (value) => {
            console.log('Wire property changed:', value);
            if (value !== displayValue.replace(/\D/g, '')) {
                displayValue = formatNumber(value);
            }
        });
    }
"
class="space-y-2">
    
    <!-- Label -->
    @if($label)
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1">*</span>
            @endif
        </label>
    @endif
    
    <!-- Input Group dengan styling Flux -->
    <div class="w-full relative block group/input" data-flux-input>
        <!-- Prefix "Rp" -->
        <div class="pointer-events-none absolute top-0 bottom-0 flex items-center justify-center text-xs ps-3 start-0"
             :class="isDisabled ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-400/75 dark:text-zinc-500'">
            <span class="text-sm font-medium">Rp</span>
        </div>
        
        <!-- Input -->
        <input 
            type="text"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            x-model="displayValue"
            @input="handleInput($event)"
            @paste="handlePaste($event)"
            @focus="$event.target.select()"
            class="{{ $inputClasses }}"
            @if($invalid) aria-invalid="true" data-invalid @endif
            data-flux-control
            data-flux-group-target
            {{ $attributes->except(['class', 'label', 'placeholder', 'required', 'name', 'size', 'invalid', 'value', 'hint', 'disabled'])->merge(['name' => $name]) }}
        />
    </div>
    
    <!-- Hint Text -->
    @if($hint)
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ $hint }}
        </p>
    @endif
    
    <!-- Error Message -->
    @if($invalid && $errors->has($name))
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ $errors->first($name) }}
        </p>
    @endif
    
    <!-- Debug Info (hanya untuk development) -->
    @if(config('app.debug'))
        <div class="text-xs text-zinc-400 bg-zinc-50 dark:bg-zinc-800 p-2 rounded mt-2">
            <div>Display: <code x-text="displayValue"></code></div>
            <div>Raw Value: <code x-text="displayValue.replace(/\D/g, '')"></code></div>
            @if($wireModelAttribute)
                <div>Wire Model: <code>{{ $wireModelAttribute }}</code></div>
                <div>Wire Value: <code x-text="$wire.get('{{ $wireModelAttribute }}')"></code></div>
            @endif
        </div>
    @endif
</div>