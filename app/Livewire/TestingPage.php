<?php

namespace App\Livewire;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class TestingPage extends Component
{
    use Interactions;

    public $items = [];

    public function mount()
    {
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'name' => '',
            'quantity' => 1,
            'price' => 0,
            'total' => 0
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updated($propertyName)
    {
        if (str_contains($propertyName, 'items.')) {
            $parts = explode('.', $propertyName);
            $index = $parts[1];
            
            if (in_array($parts[2], ['quantity', 'price'])) {
                $this->calculateTotal($index);
            }
        }
    }

    public function calculateTotal($index)
    {
        $qty = (int) $this->items[$index]['quantity'];
        $price = (int) $this->items[$index]['price'];
        $this->items[$index]['total'] = $qty * $price;
    }

    public function getGrandTotalProperty()
    {
        return collect($this->items)->sum('total');
    }

    public function save()
    {
        $grandTotal = $this->grandTotal;
        
        $this->dialog()
            ->question('Confirm Save', "Total Amount: Rp " . number_format($grandTotal, 0, ',', '.') . "\n\nProceed to save items?")
            ->confirm('Save', 'confirmed', 'Items saved successfully!')
            ->cancel('Cancel')
            ->send();
    }

    public function confirmed(string $message)
    {
        // Save logic here
        $this->dialog()->success('Success', $message)->send();
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}