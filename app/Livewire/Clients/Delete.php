<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public ?Client $client = null;
    public bool $clientDeleteModal = false;

    #[On('delete-client')]
    public function delete($clientId)
    {
        $this->client = Client::with(['invoices' => function($query) {
            $query->select('id', 'billed_to_id', 'invoice_number', 'total_amount', 'status', 'issue_date');
        }])->find($clientId);
        
        if ($this->client) {
            $this->clientDeleteModal = true;
        }
    }

    public function confirm()
    {
        if ($this->client) {
            $name = $this->client->name;
            $this->client->delete();
            $this->clientDeleteModal = false;
            $this->dispatch('client-deleted');
            $this->dialog()->success(__('common.success'), __('common.deleted_successfully'))->send();
        }
    }
}