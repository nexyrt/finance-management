@props([
    'name' => 'date',
    'placeholder' => 'Pilih tanggal',
    'disabledDates' => [],
    'class' => '',
    'mode' => 'single', // single, multiple, range
    'dateFormat' => 'Y-m-d',
    'enableTime' => false,
    'minDate' => null,
    'maxDate' => null,
])

<input 
    x-data 
    x-init="flatpickr($el, {
        @if(!empty($disabledDates))
        disable: @js($disabledDates),
        @endif
        @if($minDate)
        minDate: '{{ $minDate }}',
        @endif
        @if($maxDate)
        maxDate: '{{ $maxDate }}',
        @endif
        mode: '{{ $mode }}',
        dateFormat: '{{ $dateFormat }}',
        enableTime: {{ $enableTime ? 'true' : 'false' }},
        appendTo: document.body,
        static: true,
        positionElement: $el,
        onChange(_, dateStr) {
            @this.set('{{ $name }}', dateStr)
        }
    })" 
    type="text" 
    {{ $attributes->merge(['class' => 'bg-zinc-700 rounded-lg px-3 py-1.5 text-zinc-700 dark:text-zinc-300 placeholder-gray-400 border-[1px] border-zinc-400 focus:ring-2 focus:ring-blue-500 focus:outline-none ' . $class]) }}
    placeholder="{{ $placeholder }}"
>