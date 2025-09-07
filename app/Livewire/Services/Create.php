<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Component;

class Create extends Component
{
    use Interactions;

    public bool $modal = false;
    public string $name = '';
    public string $type = '';
    public string $price = '0';

    public function resetForm(): void
    {
        $this->name = '';
        $this->type = '';
        $this->price = '0';
        $this->resetValidation();
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

        try {
            Service::create([
                'name' => $this->name,
                'type' => $this->type,
                'price' => (int) $this->price,
            ]);

            $this->resetForm();
            $this->modal = false;

            // Delay to prevent snapshot issues
            $this->dispatch('service-created');
            $this->toast()->success('Berhasil', 'Layanan berhasil ditambahkan')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.services.create');
    }
}