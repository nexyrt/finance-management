@props([
    'label' => null,
    'hint' => null,
    'prefix' => '', // 'Rp' or empty string
    'placeholder' => null,
    'wireModel' => null,
])

@php
    // Extract wire:model from attributes if exists
    $wireModelValue = $attributes->wire('model')->value();
@endphp

<div x-data="{
    value: @if($wireModelValue) @entangle($wireModelValue).live @else 0 @endif,
    prefix: @js($prefix),

    init() {
        // Format initial value from database
        this.formatInitialValue();
    },

    formatInitialValue() {
        let input = this.$el.querySelector('input');
        if (input && this.value) {
            let formatted = this.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = this.prefix ? this.prefix + ' ' + formatted : formatted;
        }
    },

    formatInput(el) {
        let raw = el.value.replace(/[^0-9]/g, '');
        this.value = raw ? parseInt(raw) : 0;

        if (raw) {
            let formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            el.value = this.prefix ? this.prefix + ' ' + formatted : formatted;
        } else {
            el.value = '';
        }
    }
}">
    <x-input
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        :label="$label"
        :hint="$hint"
        :placeholder="$placeholder"
        x-on:input="formatInput($el)"
    />
</div>
