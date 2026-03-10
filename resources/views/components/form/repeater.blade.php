@props([
    'fields'  => [],   // array of field definitions
    'label'   => null, // header title
    'default' => [],   // default empty row values
    'wireModel' => null,
    'wireCall'  => null,
])

@php
    $wireModelValue = $attributes->wire('model')->value() ?? $wireModel;
    $wireCallValue  = $attributes->wire('call')->value() ?? $wireCall;

    // Build default empty row from fields if not provided
    if (empty($default)) {
        foreach ($fields as $field) {
            $default[$field['key']] = match($field['type'] ?? 'text') {
                'currency'  => null,
                'checkbox'  => false,
                default     => '',
            };
        }
    }
@endphp

<div
    x-data="{
        items: @js($attributes->wire('model')->value() ? [] : []),
        fields: @js($fields),
        default: @js($default),

        init() {
            @if($wireModelValue)
                this.$watch('$wire.{{ $wireModelValue }}', (val) => {
                    if (val && JSON.stringify(val) !== JSON.stringify(this.items)) {
                        this.items = val.length ? val : [{ ...this.default }];
                    }
                });
                let initial = this.$wire.get('{{ $wireModelValue }}');
                this.items = (initial && initial.length) ? initial : [{ ...this.default }];
            @else
                this.items = [{ ...this.default }];
            @endif
        },

        addItem() {
            this.items.push({ ...this.default });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        updateField(index, key, value) {
            this.items[index][key] = value;
        },

        formatCurrency(index, key, el) {
            let raw = el.value.replace(/[^0-9]/g, '');
            this.items[index][key] = raw ? raw : null;
            el.value = raw ? 'Rp ' + raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
        },

        formatCurrencyDisplay(value) {
            if (!value) return '';
            return 'Rp ' + String(value).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        async submit() {
            @if($wireModelValue)
                await $wire.set('{{ $wireModelValue }}', this.items);
            @endif
            @if($wireCallValue)
                $wire.call('{{ $wireCallValue }}');
            @endif
        },

        reset() {
            this.items = [{ ...this.default }];
        },
    }"
    x-on:repeater-reset.window="reset()"
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        @if($label)
            <h3 class="text-base font-semibold text-dark-900 dark:text-dark-50">{{ $label }}</h3>
        @else
            <div></div>
        @endif
        <button
            type="button"
            x-on:click="addItem()"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Baris
        </button>
    </div>

    {{-- Column headers --}}
    @if(count($fields) > 0)
        <div class="hidden sm:grid gap-3 px-1 mb-2" style="grid-template-columns: {{ collect($fields)->map(fn($f) => ($f['span'] ?? 3) . 'fr')->join(' ') }} 32px">
            @foreach($fields as $field)
                <div class="text-xs font-medium text-dark-500 dark:text-dark-400 uppercase tracking-wide">
                    {{ $field['label'] ?? '' }}
                </div>
            @endforeach
            <div></div>
        </div>
    @endif

    {{-- Rows --}}
    <div class="space-y-3">
        <template x-for="(item, index) in items" :key="index">
            <div class="grid gap-3 items-start p-3 bg-secondary-50 dark:bg-[#27272a] rounded-xl"
                 style="grid-template-columns: {{ collect($fields)->map(fn($f) => ($f['span'] ?? 3) . 'fr')->join(' ') }} 32px">

                @foreach($fields as $field)
                    @php $key = $field['key']; $type = $field['type'] ?? 'text'; @endphp

                    <div>
                        {{-- Text --}}
                        @if($type === 'text')
                            <input
                                type="text"
                                :value="item.{{ $key }}"
                                x-on:input="updateField(index, '{{ $key }}', $event.target.value)"
                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-lg text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            />

                        {{-- Textarea --}}
                        @elseif($type === 'textarea')
                            <textarea
                                :value="item.{{ $key }}"
                                x-on:input="updateField(index, '{{ $key }}', $event.target.value)"
                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                rows="{{ $field['rows'] ?? 2 }}"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-lg text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition resize-none"
                            ></textarea>

                        {{-- Currency --}}
                        @elseif($type === 'currency')
                            <input
                                type="text"
                                :value="formatCurrencyDisplay(item.{{ $key }})"
                                x-on:input="formatCurrency(index, '{{ $key }}', $event.target)"
                                placeholder="{{ $field['placeholder'] ?? 'Rp 0' }}"
                                autocomplete="off"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-lg text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            />

                        {{-- Select --}}
                        @elseif($type === 'select')
                            <select
                                :value="item.{{ $key }}"
                                x-on:change="updateField(index, '{{ $key }}', $event.target.value)"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-lg text-dark-900 dark:text-dark-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <option value="">{{ $field['placeholder'] ?? 'Pilih...' }}</option>
                                @foreach($field['options'] ?? [] as $optValue => $optLabel)
                                    @if(is_int($optValue))
                                        <option value="{{ $optLabel }}" :selected="item.{{ $key }} === '{{ $optLabel }}'">{{ $optLabel }}</option>
                                    @else
                                        <option value="{{ $optValue }}" :selected="item.{{ $key }} === '{{ $optValue }}'">{{ $optLabel }}</option>
                                    @endif
                                @endforeach
                            </select>

                        {{-- Date --}}
                        @elseif($type === 'date')
                            <input
                                type="date"
                                :value="item.{{ $key }}"
                                x-on:change="updateField(index, '{{ $key }}', $event.target.value)"
                                class="w-full px-3 py-2 text-sm bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-lg text-dark-900 dark:text-dark-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                            />

                        {{-- Checkbox --}}
                        @elseif($type === 'checkbox')
                            <label class="flex items-center gap-2 cursor-pointer pt-2">
                                <input
                                    type="checkbox"
                                    :checked="item.{{ $key }}"
                                    x-on:change="updateField(index, '{{ $key }}', $event.target.checked)"
                                    class="w-4 h-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                                />
                                @if(isset($field['checkboxLabel']))
                                    <span class="text-sm text-dark-700 dark:text-dark-300">{{ $field['checkboxLabel'] }}</span>
                                @endif
                            </label>
                        @endif
                    </div>
                @endforeach

                {{-- Remove button --}}
                <div class="flex items-center justify-center pt-1">
                    <button
                        type="button"
                        x-on:click="removeItem(index)"
                        x-show="items.length > 1"
                        class="h-8 w-8 flex items-center justify-center rounded-lg text-dark-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <x-icon name="trash" class="w-4 h-4" />
                    </button>
                </div>

            </div>
        </template>
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-3 mt-3 border-t border-secondary-200 dark:border-white/10">
        <p class="text-sm text-dark-500 dark:text-dark-400">
            <span x-text="items.length"></span> baris
        </p>
        @if($wireCallValue)
            <x-button
                x-on:click="submit()"
                color="primary"
                size="sm"
                loading="{{ $wireCallValue }}">
                {{ $slot->isNotEmpty() ? $slot : 'Simpan' }}
            </x-button>
        @endif
    </div>

</div>
