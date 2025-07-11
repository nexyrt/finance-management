@props([
    'label' => 'Jumlah',
    'symbol' => 'Rp',
    'currencyCode' => 'IDR',
    'locale' => 'id-ID',
    'model' => $attributes->wire('model')->value(),
])

<div x-data="{
    display: '',
    formatCurrency(value) {
        if (!value || isNaN(value)) {
            return new Intl.NumberFormat('{{ $locale }}', {
                style: 'currency',
                currency: '{{ $currencyCode }}',
                minimumFractionDigits: 0,
            }).format(0);
        }

        return new Intl.NumberFormat('{{ $locale }}', {
            style: 'currency',
            currency: '{{ $currencyCode }}',
            minimumFractionDigits: 0,
        }).format(value);
    },
    init() {
        const raw = @entangle($attributes->wire('model')).live;

        if (!raw || isNaN(raw)) {
            this.display = this.formatCurrency(0);
            $refs.rawInput.value = 0;
        } else {
            this.display = this.formatCurrency(raw);
            $refs.rawInput.value = raw;
        }

        $refs.rawInput.dispatchEvent(new Event('input'));
    },
    update(e) {
        let onlyNumbers = e.target.value.replace(/[^0-9]/g, '');
        this.display = this.formatCurrency(onlyNumbers);
        $refs.rawInput.value = onlyNumbers;
        $refs.rawInput.dispatchEvent(new Event('input'));
    }
}" x-init="init()" class="space-y-2">
    <label class="block font-medium text-sm text-gray-700">{{ $label }}</label>

    <input type="text" x-model="display" @input="update" placeholder="{{ $symbol }} 0"
        class="w-full border rounded p-2" />

    <input type="hidden" x-ref="rawInput" wire:model="{{ $model }}" />
</div>
