<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{
    use Interactions;

    public ?Service $service = null;
    public string $name = '';
    public string $type = '';
    public string $price = '0';
    public bool $modal = false;

    #[On('load::service')]
    public function load(Service $service): void
    {
        $this->service = $service;
        $this->name = $service->name;
        $this->type = $service->type;
        $this->price = number_format($service->price, 0, ',', '.');
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

    public function messages(): array
    {
        return [
            'name.required' => 'Nama layanan wajib diisi',
            'price.required' => 'Harga layanan wajib diisi',
            'price.integer' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh negatif',
            'type.required' => 'Kategori layanan wajib dipilih',
            'type.in' => 'Kategori layanan tidak valid',
        ];
    }

    public function save(): void
    {
        // Convert price to int before validation
        $this->price = (string) (int) preg_replace('/[^0-9]/', '', $this->price);

        $this->validate();

        $this->service->update([
            'name' => $this->name,
            'type' => $this->type,
            'price' => (int) $this->price,
        ]);

        $this->modal = false;
        $this->dispatch('service-updated');
        $this->toast()->success('Berhasil', 'Layanan berhasil diperbarui')->send();
    }

    public function render()
    {
        return view('livewire.services.edit');
    }
}