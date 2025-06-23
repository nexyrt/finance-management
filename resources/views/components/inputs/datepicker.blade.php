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
    'size' => null,
])

@php
$invalid ??= ($name && $errors->has($name));

$classes = Flux::classes()
    ->add('appearance-none')
    ->add('w-full ps-3 pe-3 block')
    ->add(match ($size) {
        default => 'h-8 text-base sm:text-sm rounded-md',
        'sm' => 'h-8 text-sm leading-[1.125rem] rounded-md',
        'xs' => 'h-6 text-xs leading-[1.125rem] rounded-md',
    })
    ->add('shadow-xs border')
    ->add('bg-white dark:bg-white/10 dark:disabled:bg-white/[7%]')
    ->add('text-zinc-700 dark:text-zinc-300 disabled:text-zinc-500 dark:disabled:text-zinc-400')
    ->add('placeholder-zinc-300')
    ->add('disabled:shadow-none')
    ->add($invalid
        ? 'border border-red-500'
        : 'border border-zinc-200 border-b-zinc-300/80 dark:border-white/10'
    )
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