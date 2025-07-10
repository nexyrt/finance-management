<div class="max-w-7xl mx-auto p-4">
    <x-modal id="modal-id">
        <div wire:key="{{ uniqid() }}" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- ========== CLEAVE.JS STYLE ========== --}}
            <div x-data x-init="const cleave = new Cleave($refs.cleaveInput, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                prefix: 'Rp ',
                rawValueTrimPrefix: true
            });
            
            cleave.setRawValue(@entangle('cleaveAmount'));
            $refs.cleaveRaw.value = cleave.getRawValue();
            $refs.cleaveRaw.dispatchEvent(new Event('input'));">
                <label>Cleave.js Input</label>
                <x-input x-ref="cleaveInput" wire:ignore />
                <input type="hidden" x-ref="cleaveRaw" wire:model="cleaveAmount" />
            </div>

            {{-- ========== INTL.NUMBERFORMAT STYLE ========== --}}
            <div x-data="{
                value: '',
                raw: '',
                formatCurrency(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                    }).format(number);
                },
                init() {
                    const raw = @entangle('intlAmount');
                    this.raw = raw;
                    this.value = this.formatCurrency(raw);
                    $refs.intlRaw.value = raw;
                    $refs.intlRaw.dispatchEvent(new Event('input'));
                },
                updateRaw(e) {
                    let raw = e.target.value.replace(/[^0-9]/g, '');
                    this.raw = raw;
                    this.value = this.formatCurrency(raw);
                    $refs.intlRaw.value = raw;
                    $refs.intlRaw.dispatchEvent(new Event('input'));
                }
            }">
                <label>Intl.NumberFormat Input</label>
                <input x-model="value" @input="updateRaw" type="text" class="border p-2 rounded w-full" />
                <input type="hidden" x-ref="intlRaw" wire:model="intlAmount" />
            </div>


            <x-button class="mt-6 lg:col-span-2" text="Submit" wire:click="submit" />
        </div>
    </x-modal>

    <x-button x-on:click="$modalOpen('modal-id')">
        Open
    </x-button>
</div>
