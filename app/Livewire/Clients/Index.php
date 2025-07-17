<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $quantity = 10;

    public ?string $search = null;

    public array $sort = [ 
        'column' => 'name',
        'direction' => 'asc',
    ];

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => 'Id'],
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'type', 'label' => 'Type'],
                ['index' => 'email', 'label' => 'Email'],
                ['index' => 'status', 'label' => 'Status'],
            ],
            'rows' => Client::query() // Changed to Client model
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('NPWP', 'like', "%{$this->search}%");
                })
                ->orderBy(...array_values($this->sort)) 
                ->paginate($this->quantity)
                ->withQueryString()
        ];
    }

    public function render()
    {
        $clients = $this->with();

        return view('livewire.clients.index', [
            'headers' => $clients['headers'],
            'rows' => $clients['rows'],
        ]);
    }
}