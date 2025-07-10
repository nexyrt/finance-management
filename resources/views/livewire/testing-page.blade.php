<div class="max-w-7xl mx-auto p-4">
    <x-inputs.currency wire:model='price'/>

    <p>{{$amount}}</p>

    <x-button text="Submit" wire:click='submit'/>
</div>
