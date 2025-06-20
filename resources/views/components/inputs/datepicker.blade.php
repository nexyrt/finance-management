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
    'variant' => 'outline',
    'invalid' => null,
])

@php
$invalid ??= ($name && $errors->has($name));

$classes = Flux::classes()
    ->add('w-fit border rounded-lg block disabled:shadow-none dark:shadow-none')
    ->add('appearance-none')
    ->add('text-base sm:text-sm py-2 h-10 leading-[1.375rem] px-3')
    ->add(match ($variant) { // Background...
        'outline' => 'bg-white dark:bg-white/10 dark:disabled:bg-white/[7%]',
        'filled'  => 'bg-zinc-800/5 dark:bg-white/10 dark:disabled:bg-white/[7%]',
    })
    ->add(match ($variant) { // Text color
        'outline' => 'text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500',
        'filled'  => 'text-zinc-700 placeholder-zinc-500 disabled:placeholder-zinc-400 dark:text-zinc-200 dark:placeholder-white/60 dark:disabled:placeholder-white/40',
    })
    ->add(match ($variant) { // Border...
        'outline' => $invalid ? 'border-red-500' : 'shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5',
        'filled'  => $invalid ? 'border-red-500' : 'border-0',
    })
    ->add('focus:ring-2 focus:ring-blue-500 focus:outline-none')
    ->add($class);
@endphp

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
    {{ $attributes->except('class')->class($classes) }}
    placeholder="{{ $placeholder }}"
    @if ($invalid) aria-invalid="true" data-invalid @endif
>