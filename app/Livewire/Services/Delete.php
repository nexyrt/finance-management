<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Component;

class Delete extends Component
{
    use Interactions;

    public Service $service;

    public function confirm(): void
    {
        $this->toast()
            ->question('Hapus Layanan?', 'Layanan "' . $this->service->name . '" akan dihapus permanen')
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $this->service->delete();
        $this->dispatch('service-deleted');
        $this->toast()->success('Berhasil', 'Layanan berhasil dihapus')->send();
    }

    public function render()
    {
        return view('livewire.services.delete');
    }
}