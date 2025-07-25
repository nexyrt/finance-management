@php
    $personalize = $classes();
@endphp

<div x-cloak
     @if ($id) id="{{ $id }}" @endif
     @class(['relative', $configurations['zIndex']])
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     @if ($wire)
         x-data="tallstackui_modal(@entangle($entangle), @js($configurations['overflow'] ?? false))"
     @else
         x-data="tallstackui_modal(false, @js($configurations['overflow'] ?? false))"
     @endif
     x-show="show"
     @if (!$configurations['persistent']) x-on:keydown.escape.window="top_ui && (show = false)" @endif
     x-on:modal:{{ $open }}.window="show = true;"
     x-on:modal:{{ $close }}.window="show = false;"
     {{ $attributes->whereStartsWith('x-on:') }}>
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @class([$personalize['wrapper.first'], $personalize['blur.'.($configurations['blur'] === true ? 'sm' : $configurations['blur'])] ?? null => $configurations['blur']])></div>
    <div class="{{ $personalize['wrapper.second'] }}">
        <div @class([
                $personalize['wrapper.third'],
                $configurations['size'],
                $personalize['positions.top'] => !$configurations['center'],
                $personalize['positions.center'] => $configurations['center'],
            ])>
            <div x-show="show"
                 @if (!$configurations['persistent']) x-on:mousedown.away="top_ui && (show = false)" @endif
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @class([$personalize['wrapper.fourth'], $configurations['size'], $personalize['wrapper.scrollable'] => $configurations['scrollable']])>
                @if ($title)
                    <div class="{{ $personalize['title.wrapper'] }}">
                        <h3 class="{{ $personalize['title.text'] }}">{{ $title }}</h3>
                         <button type="button" x-on:click="show = false">
                            <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                                 :icon="TallStackUi::icon('x-mark')"
                                                 internal
                                                 class="{{ $personalize['title.close'] }}" />
                         </button>
                    </div>
                @endif
                <div @class([
                        $personalize['body'],
                        $personalize['body.scrollable'] => $configurations['scrollable'],
                        'soft-scrollbar' => $configurations['scrollable'] && $configurations['scrollbar'] === 'thin',
                        'custom-scrollbar' => $configurations['scrollable'] && $configurations['scrollbar'] === 'thick',
                    ])>
                    {{ $slot }}
                </div>
                @if ($footer)
                    <div @class([$personalize['footer'], $personalize['footer.scrollable'] => $configurations['scrollable']])>
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
