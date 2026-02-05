{{-- resources/views/livewire/testing-page.blade.php --}}

<div class="space-y-8">
    <h1 class="text-2xl font-bold">Testing x-currency-input</h1>

    {{-- Test 1: Tanpa Prefix --}}
    <div>
        <x-currency-input
            wire:model.live="amount1"
            label="Amount (no prefix)"
        />
        <p class="mt-2 text-sm">Value: {{ $amount1 }}</p>
    </div>

    {{-- Test 2: Dengan Prefix Rp --}}
    <div>
        <x-currency-input
            wire:model.live="amount2"
            label="Total (with Rp prefix)"
            prefix="Rp"
        />
        <p class="mt-2 text-sm">Value: {{ $amount2 }}</p>
    </div>

    {{-- Test 3: Initial Null --}}
    <div>
        <x-currency-input
            wire:model.live="amount3"
            label="Price (initial null)"
        />
        <p class="mt-2 text-sm">Value: {{ $amount3 ?? 'null' }}</p>
    </div>
</div>
