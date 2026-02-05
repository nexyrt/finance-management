@php
    $personalize = $classes();
@endphp

@if ($simple)
    <div class="{{ $personalize['simple.wrapper'] }} px-3 pt-4 pb-1.5">
        <span class="{{ $personalize['simple.base'] }} text-[11px] font-normal uppercase tracking-wider text-slate-600 dark:text-slate-500"
              x-text="$store['tsui.side-bar'].open ? @js($text ?? $slot) : @js(str($text ?? $slot)->limit(5))"></span>
    </div>
@elseif ($line)
    <div class="{{ $personalize['line.wrapper.first'] }} px-3 pt-4 pb-1.5">
        <div class="{{ $personalize['line.wrapper.second'] }}" x-show="$store['tsui.side-bar'].open">
            <div class="{{ $personalize['line.border'] }}"></div>
        </div>
        <div class="{{ $personalize['line.wrapper.third'] }}">
            <span class="{{ $personalize['line.base'] }} text-[11px] font-normal uppercase tracking-wider text-slate-600 dark:text-slate-500"
                  x-text="$store['tsui.side-bar'].open ? @js($text ?? $slot) : @js(str($text ?? $slot)->limit(5))"></span>
        </div>
    </div>
@else
    <div class="{{ $personalize['line-right.wrapper.first'] }} px-3 pt-4 pb-1.5">
        <div class="{{ $personalize['line-right.wrapper.second'] }}" x-show="$store['tsui.side-bar'].open">
            <div class="{{ $personalize['line-right.border'] }}"></div>
        </div>
        <div class="{{ $personalize['line-right.wrapper.third'] }}">
            <span class="{{ $personalize['line-right.base'] }} text-[11px] font-normal uppercase tracking-wider text-slate-600 dark:text-slate-500"
                  x-text="$store['tsui.side-bar'].open ? @js($text ?? $slot) : @js(str($text ?? $slot)->limit(5))"></span>
        </div>
    </div>
@endif
