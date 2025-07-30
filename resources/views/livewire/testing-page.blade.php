<div>
    <x-modal title="TallStackUi" id="testing-modal" center>
        <form wire:submit="submit">
            <x-input wire:model.live='salary' prefix="Rp." label="Salary" hint="Insert your desired salary"
                x-mask:dynamic="$money($input, ',')" value="200.000" />

            <x-button x-on:click="$modalClose('testing-modal')" color="red">
                Close
            </x-button>
            <x-button x-on:click="$modalClose('testing-modal')" type="submit">
                Submit
            </x-button>
        </form>
    </x-modal>

    
</div>
