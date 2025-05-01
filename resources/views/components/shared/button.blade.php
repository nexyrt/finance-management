@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors duration-150 ease-in-out';
    
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
        'xl' => 'px-6 py-3 text-lg',
    ][$size] ?? 'px-4 py-2 text-sm';
    
    $variantClasses = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 border border-transparent text-white',
        'secondary' => 'bg-zinc-700 hover:bg-zinc-600 border border-zinc-600 text-zinc-200',
        'success' => 'bg-green-600 hover:bg-green-700 border border-transparent text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 border border-transparent text-white',
        'warning' => 'bg-amber-600 hover:bg-amber-700 border border-transparent text-white',
        'info' => 'bg-blue-600 hover:bg-blue-700 border border-transparent text-white',
        'link' => 'bg-transparent hover:bg-zinc-800 border border-transparent text-indigo-400 hover:text-indigo-300',
        'outline' => 'bg-transparent hover:bg-zinc-800 border border-zinc-600 text-zinc-300',
        'ghost' => 'bg-transparent hover:bg-zinc-700/50 border border-transparent text-zinc-300',
    ][$variant] ?? 'bg-indigo-600 hover:bg-indigo-700 border border-transparent text-white';
    
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
    $widthClasses = $fullWidth ? 'w-full' : '';
@endphp

<button 
    type="{{ $type }}" 
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses} {$disabledClasses} {$widthClasses} {$class}"]) }}
>
    @if ($icon && $iconPosition === 'left')
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
        </svg>
    @endif
    
    {{ $slot }}
    
    @if ($icon && $iconPosition === 'right')
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
        </svg>
    @endif
</button>