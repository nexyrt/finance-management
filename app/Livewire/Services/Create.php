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
    public int $price = 0;

    public function resetForm(): void
    {
        $this->name = '';
        $this->type = '';
        $this->price = 0;
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

    public function save(): void
    {
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
            $this->toast()->success(__('common.success'), __('common.created_successfully'))->send();

        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.services.create');
    }
}
