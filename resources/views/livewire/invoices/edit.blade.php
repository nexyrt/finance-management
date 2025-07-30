<div>
    <x-modal title="Edit Invoice" id="edit-invoice-modal" center>
        <form wire:submit.prevent="updateInvoice">
            <x-input wire:model.live='invoiceNumber' label="Invoice Number" required />
            <x-input wire:model.live='clientName' label="Client Name" required />
            <x-input wire:model.live='amount' prefix="Rp." label="Amount" hint="Enter the invoice amount"
                x-mask:dynamic="$money($input, ',')" />

            <x-button x-on:click="$modalClose('edit-invoice-modal')" color="red">
                Close
            </x-button>
            <x-button type="submit">
                Update
            </x-button>
        </form>
    </x-modal>
</div>
