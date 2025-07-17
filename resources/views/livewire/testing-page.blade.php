<div class="max-w-7xl mx-auto p-4">
    <!-- Circles -->
    <x-step selected="1" circles {{-- [tl! highlight] --}} helpers navigate-previous>
        <x-step.items step="1" title="Starting" description="Step One">
            Step one...
        </x-step.items>
        <x-step.items step="2" title="Advancing" description="Step Two">
            Step two...
        </x-step.items>
        <x-step.items step="3" title="Finishing" description="Step Three">
            Step three... <b>finished!</b>
        </x-step.items>
    </x-step>

    <!-- Panels -->
    <x-step selected="1" panels {{-- [tl! highlight] --}} helpers navigate-previous>
        <x-step.items step="1" title="Starting" description="Step One">
            Step one...
        </x-step.items>
        <x-step.items step="2" title="Advancing" description="Step Two">
            Step two...
        </x-step.items>
        <x-step.items step="3" title="Finishing" description="Step Three">
            Step three... <b>finished!</b>
        </x-step.items>
    </x-step>
</div>
