<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{
    use Interactions;

    public ?Service $service = null;
    public string $name = '';
    public string $type = '';
    public int $price = 0;
    public bool $modal = false;

    // Kategori service — data dinamis dari DB, gunakan translate_text() di PHP
    #[Computed]
    public function categoryOptions(): array
    {
        $types = ['Perizinan', 'Administrasi Perpajakan', 'Digital Marketing', 'Sistem Digital'];

        return array_map(fn($type) => [
            'label' => translate_text($type),
            'value' => $type,
        ], $types);
    }

    #[On('load::service')]
    public function load(int $serviceId): void
    {
        $service = Service::find($serviceId);
        if (!$service) return;

        $this->service = $service;
        $this->name = $service->name;
        $this->type = $service->type;
        $this->price = $service->price;
        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'in:Perizinan,Administrasi Perpajakan,Digital Marketing,Sistem Digital'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->service->update([
            'name' => $this->name,
            'type' => $this->type,
            'price' => (int) $this->price,
        ]);

        $this->modal = false;
        $this->dispatch('service-updated');
        $this->toast()->success(__('common.success'), __('common.updated_successfully'))->send();
    }

    public function render()
    {
        return view('livewire.services.edit');
    }
}