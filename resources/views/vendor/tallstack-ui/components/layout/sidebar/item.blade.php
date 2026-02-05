@php
    $personalize = $classes();
@endphp

@aware(['smart' => null, 'navigate' => null, 'navigateHover' => null, 'collapsible' => null])

@if ($visible)
    @if ($slot->isNotEmpty())
        <li x-data="{ show : @js($opened ?? \Illuminate\Support\Str::contains($slot, 'ts-ui-group-opened') ?? false) }">
            <button x-on:click="show = !show"
                    type="button"
                    class="{{ $personalize['group.button'] }}">
                @if ($icon instanceof \Illuminate\View\ComponentSlot)
                    {{ $icon }}
                @elseif ($icon)
                    <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                         :icon="TallStackUi::icon($icon)"
                                         internal
                                         class="{{ $personalize['group.icon.base'] }}" />
                @endif
                @if ($collapsible)
                    <span x-show="($store['tsui.side-bar'].open && !$store['tsui.side-bar'].mobile) || $store['tsui.side-bar'].mobile" x-transition class="{{ $personalize['group.text'] }}">{{ $text }}</span>
                @else
                    {{ $text }}
                @endif
                <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                     :icon="TallStackUi::icon('chevron-down')"
                                     internal
                                     class="{{ $personalize['group.icon.collapse.base'] }}"
                                     x-bind:class="{ '{{ $personalize['group.icon.collapse.rotate'] }}': show }" />
            </button>
            <ul x-show="show" class="{{ $personalize['group.group'] }}" x-data x-ref="parent">
                {{ $slot }}
            </ul>
        </li>
    @else
        <li class="{{ $personalize['item.wrapper.base'] }}" x-bind:class="{ '{{ $personalize['item.wrapper.border'] }}' : $refs.parent !== undefined }">
            <a @if ($route || $href) href="{{ $route ?? $href }}" @endif
            @class([
                $personalize['item.state.base'],
                'flex items-center gap-2.5 px-3 py-2 mb-0.5 rounded-md text-sm font-normal transition-all duration-150',
                'text-slate-800 hover:text-slate-900 hover:bg-slate-400/15',
                'dark:text-slate-300 dark:hover:text-slate-100 dark:hover:bg-slate-600/25',
                $personalize['item.state.normal'] => ! $current || (! $smart && ! $matches()),
                \Illuminate\Support\Arr::toCssClasses(['ts-ui-group-opened', $personalize['item.state.current'], 'bg-slate-400/25 text-slate-900 dark:bg-slate-600/40 dark:text-slate-50']) => $current || ($smart && $matches()),
            ]) x-bind:class="{'{{ $personalize['item.state.collapsed'] }}' : @js($collapsible) && ! $store['tsui.side-bar'].open && ! $store['tsui.side-bar'].mobile }"
                @if ($navigate && ! $href)
                    wire:navigate
                @elseif ($navigateHover && ! $href)
                   wire:navigate.hover
                @endif
                {{ $attributes }}>
                @if ($icon instanceof \Illuminate\View\ComponentSlot)
                    {{ $icon }}
                @elseif ($icon)
                    <x-dynamic-component :component="TallStackUi::prefix('icon')"
                                         :icon="TallStackUi::icon($icon)"
                                         internal
                                         class="{{ $personalize['item.icon'] }} w-[18px] h-[18px] shrink-0" />
                @endif
                @if ($collapsible)
                    <span x-cloak x-show="($store['tsui.side-bar'].open && !$store['tsui.side-bar'].mobile) || $store['tsui.side-bar'].mobile" x-transition class="{{ $personalize['item.text'] }}">{{ $text }}</span>
                @else
                    <span class="leading-tight">{{ $text }}</span>
                @endif
            </a>
        </li>
    @endif
@endif
