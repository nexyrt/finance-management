@props([
    'type' => 'button',
    'variant' => 'default',
    'disabled' => false,
    'class' => '',
])

@php
    $baseClasses = 'transition-colors duration-150 ease-in-out';
    
    $variantClasses = [
        'default' => 'text-zinc-400 hover:text-zinc-300',
        'primary' => 'text-indigo-400 hover:text-indigo-300',
        'success' => 'text-green-400 hover:text-green-300',
        'danger' => 'text-red-400 hover:text-red-300',
        'warning' => 'text-amber-400 hover:text-amber-300',
        'info' => 'text-blue-400 hover:text-blue-300',
    ][$variant] ?? 'text-zinc-400 hover:text-zinc-300';
    
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
@endphp

<button 
    type="{{ $type }}" 
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$disabledClasses} {$class}"]) }}
>
    {{ $slot }}
</button>