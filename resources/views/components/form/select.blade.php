@php
    /*
     * x-form.select — Alpine-only select component.
     *
     * Cara pakai:
     *   <x-form.select x-model="myVar" :options="$list" label="Pilih Status" />
     *
     * Props:
     *   $label       — label teks di atas input
     *   $hint        — teks kecil di bawah input
     *   $placeholder — teks saat belum ada pilihan (default: 'Pilih...')
     *   $options     — array opsi:
     *                  1. Flat:      ['Aktif', 'Nonaktif']
     *                  2. Key-value: [['label' => 'Aktif', 'value' => 'active'], ...]
     *                  3. Custom key: [['name' => 'Aktif', 'id' => 1], ...] + $select
     *   $select      — mapping key "label:key|value:key" (default: "label:label|value:value")
     *   $api         — URL endpoint JSON untuk load options via fetch
     *
     * Binding via x-model (Alpine v3.10+ x-modelable):
     *   <div x-data="{ status: '' }">
     *       <x-form.select x-model="status" :options="[...]" />
     *       <span x-text="status"></span>
     *   </div>
     */

    $label       = $label ?? null;
    $hint        = $hint ?? null;
    $placeholder = $placeholder ?? 'Pilih...';
    $api         = $api ?? null;

    // Resolve key mapping
    $selectMap = [];
    foreach (explode('|', $select ?? 'label:label|value:value') as $part) {
        $parts = explode(':', $part, 2);
        if (count($parts) === 2) {
            $selectMap[$parts[0]] = $parts[1];
        }
    }
    $labelKey = $selectMap['label'] ?? 'label';
    $valueKey = $selectMap['value'] ?? 'value';

    // Normalize options
    $normalizedOptions = collect($options ?? [])
        ->map(function ($opt) use ($labelKey, $valueKey) {
            if (! is_array($opt)) {
                return ['label' => (string) $opt, 'value' => (string) $opt, 'disabled' => false];
            }
            return [
                'label'    => (string) ($opt[$labelKey] ?? ''),
                'value'    => (string) ($opt[$valueKey] ?? ''),
                'disabled' => (bool) ($opt['disabled'] ?? false),
            ];
        })
        ->values()
        ->toArray();
@endphp

{{--
    x-modelable="value" + {{ $attributes->whereStartsWith('x-model') }} pada elemen yang SAMA
    adalah cara resmi Alpine v3 untuk custom component dengan x-model binding dua arah.
    Alpine akan otomatis proxy getter/setter antara 'value' internal dan variabel parent.
--}}
<div
    x-data="{
        open: false,
        search: '',
        value: '',
        loading: false,
        options: {{ Js::from($normalizedOptions) }},
        api: {{ Js::from($api) }},

        init() {
            if (this.api) {
                this.fetchOptions();
            }
        },

        async fetchOptions(q = '') {
            this.loading = true;
            try {
                const url = new URL(this.api, window.location.origin);
                if (q) url.searchParams.set('search', q);
                const res = await fetch(url);
                this.options = await res.json();
            } catch (e) {
                console.error('x-form.select fetch error:', e);
            } finally {
                this.loading = false;
            }
        },

        get filtered() {
            if (this.api) return this.options;
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },

        get selectedLabel() {
            if (this.value === '' || this.value === null || this.value === undefined) return null;
            const found = this.options.find(o => String(o.value) === String(this.value));
            return found ? found.label : null;
        },

        onSearch(val) {
            this.search = val;
            if (this.api) {
                clearTimeout(this._searchTimer);
                this._searchTimer = setTimeout(() => this.fetchOptions(val), 300);
            }
        },

        select(val) {
            this.value = val;
            this.open = false;
            this.search = '';
        },

        clear() {
            this.value = '';
            this.open = false;
            this.search = '';
        },
    }"
    x-modelable="value"
    {{ $attributes->whereStartsWith('x-model') }}
    @keydown.escape="open = false; search = ''"
    @click.outside="open = false; search = ''"
    class="relative"
>
    {{-- Label --}}
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-dark-300 mb-1.5">
            {{ $label }}
        </label>
    @endif

    {{-- Trigger button --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full flex items-center justify-between gap-2 rounded-md ring-1 ring-gray-300 dark:ring-dark-600 bg-white dark:bg-dark-800 px-3 py-1.5 text-sm text-left outline-none transition-all duration-150"
        :class="open ? 'ring-2 ring-primary-600 dark:ring-primary-600' : 'focus:ring-2 focus:ring-primary-600'"
    >
        <span class="truncate"
              :class="selectedLabel ? 'text-gray-700 dark:text-dark-200' : 'text-gray-400 dark:text-dark-400'"
              x-text="selectedLabel ?? '{{ $placeholder }}'">
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" class="w-4 h-4 text-gray-400 dark:text-dark-400 shrink-0 transition-transform duration-150"
             :class="open ? 'rotate-180' : ''">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-[0.97] -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-[0.97] -translate-y-1"
        class="absolute z-50 mt-1 w-full min-w-[160px] rounded-xl bg-white dark:bg-dark-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
        style="display: none;"
    >
        {{-- Search --}}
        <div class="p-1.5 border-b border-gray-100 dark:border-dark-600">
            <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-gray-50 dark:bg-dark-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 dark:text-dark-400 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input
                    type="text"
                    :value="search"
                    @input="onSearch($event.target.value)"
                    placeholder="Cari..."
                    @keydown.enter.prevent
                    @click.stop
                    class="w-full bg-transparent text-sm text-gray-700 dark:text-dark-200 placeholder-gray-400 dark:placeholder-dark-400 border-0 outline-none focus:ring-0 p-0 leading-5"
                />
                <button type="button" x-show="search" @click.stop="search = ''; api && fetchOptions('')" class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 hover:text-gray-600 dark:text-dark-400 dark:hover:text-dark-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Options --}}
        <ul class="max-h-52 overflow-y-auto py-1">

            {{-- Loading --}}
            <li x-show="loading" class="px-3 py-4 text-center">
                <svg class="w-4 h-4 animate-spin text-primary-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </li>

            {{-- Clear --}}
            <li x-show="!loading && value !== '' && value !== null && value !== undefined">
                <button type="button" @click="clear()"
                    class="w-full text-left px-3 py-2 text-sm italic text-gray-400 dark:text-dark-500 hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                    — {{ $placeholder }}
                </button>
            </li>

            <template x-for="opt in filtered" :key="opt.value">
                <li x-show="!loading">
                    <button
                        type="button"
                        @click="!opt.disabled && select(opt.value)"
                        :disabled="opt.disabled"
                        class="w-full text-left px-3 py-2 text-sm transition-colors"
                        :class="{
                            'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium': String(value) === String(opt.value),
                            'text-gray-700 dark:text-dark-200 hover:bg-gray-50 dark:hover:bg-dark-700': String(value) !== String(opt.value) && !opt.disabled,
                            'text-gray-300 dark:text-dark-600 cursor-not-allowed': opt.disabled,
                        }"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span x-text="opt.label" class="truncate"></span>
                            <svg x-show="String(value) === String(opt.value)"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                 stroke="currentColor" class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        </div>
                    </button>
                </li>
            </template>

            {{-- Empty --}}
            <li x-show="!loading && filtered.length === 0"
                class="px-3 py-5 text-center text-sm text-gray-400 dark:text-dark-500">
                Tidak ada hasil
            </li>
        </ul>
    </div>

    {{-- Hint --}}
    @if ($hint)
        <p class="mt-1.5 text-xs text-gray-500 dark:text-dark-400">{{ $hint }}</p>
    @endif
</div>
