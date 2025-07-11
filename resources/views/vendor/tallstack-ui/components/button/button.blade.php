@php
    $personalize = $classes();
@endphp

<{{ $tag }} @if ($href) href="{!! $href !!}" @else role="button" @endif {{ $attributes->except('type')->class([
        $personalize['wrapper.class'],
        $personalize['wrapper.sizes.' . $size],
        $colors['background'],
        $personalize['wrapper.border.radius.rounded'] => !$square && !$round,
        $personalize['wrapper.border.radius.circle'] => !$square && $round !== null,
    ]) }} type="{{ $attributes->get('type', $submit ? 'submit' : 'button') }}" @if ($livewire && $loading) wire:loading.attr="disabled" wire:loading.class="!cursor-wait" @endif>
    @if ($livewire && $loading && $position === 'left')
        <x-tallstack-ui::icon.generic.loading-button :$loading :$delay @class([
                'animate-spin',
                $personalize['icon.sizes.' . $size],
                $colors['icon'],
            ]) />
    @endif
    @if ($left)
        {!! $left !!}
    @elseif ($icon && $position === 'left')
        <x-dynamic-component :component="TallStackUi::prefix('icon')" internal :$icon @class([$personalize['icon.sizes.' . $size], $colors['icon']]) />
    @endif
    {{ $text ?? $slot }}
    @if ($right)
        {!! $right !!}
    @elseif ($icon && $position === 'right')
        <x-dynamic-component :component="TallStackUi::prefix('icon')" internal :$icon @class([$personalize['icon.sizes.' . $size], $colors['icon']]) />
    @endif
    @if ($livewire && $loading && $position === 'right')
        <x-tallstack-ui::icon.generic.loading-button :$loading :$delay @class([
            'animate-spin',
            $personalize['icon.sizes.' . $size],
            $colors['icon'],
        ]) />
    @endif
</{{ $tag }}>
