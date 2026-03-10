<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;

class TestingPage extends Component
{
    public array $items = [];

    public function save(): void
    {
        $this->validate([
            'items'         => 'required|array|min:1',
            'items.*.name'  => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:1',
            'items.*.type'  => 'required|in:Perizinan,Administrasi Perpajakan,Digital Marketing,Sistem Digital',
        ]);

        $count = count($this->items);

        foreach ($this->items as $item) {
            Service::create([
                'name'  => $item['name'],
                'price' => (int) $item['price'],
                'type'  => $item['type'],
            ]);
        }

        $this->items = [];
        $this->addItem();

        session()->flash('success', $count . ' layanan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
