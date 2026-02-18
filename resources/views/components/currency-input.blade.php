@props([
    'label' => null,
    'hint' => null,
    'prefix' => 'Rp', // 'Rp' or empty string
    'placeholder' => null,
])

@php
    $wireModelValue = $attributes->wire('model')->value();
@endphp

<div x-data="{
    formatInput(el) {
        let raw = el.value.replace(/[^0-9]/g, '');
        this.syncToLivewire(raw);
        if (raw) {
            let formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            el.value = '{{ $prefix }} ' + formatted;
        } else {
            el.value = '';
        }
    },
    syncToLivewire(raw) {
        @if($wireModelValue)
            $wire.set('{{ $wireModelValue }}', raw ? raw : null);
        @endif
    },
    clearInput() {
        this.$el.querySelector('input').value = '';
    }
}"
x-on:currency-reset.window="clearInput()">
    <x-input
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        :label="$label"
        :hint="$hint"
        :placeholder="$placeholder"
        x-on:input="formatInput($el)"
        autocomplete="off"
    />
</div>
