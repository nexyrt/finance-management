<?php

namespace App\Livewire;

use App\Models\BankAccount;
use Livewire\Component;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TestingPage extends Component
{
    // Section 1: Data dari Model
    public array $bankAccounts = [];

    // Section 2: Google Translate
    public string $inputText = '';
    public string $translatedText = '';
    public string $targetLang = 'zh';

    public function loadBankAccounts(): void
    {
        $this->bankAccounts = BankAccount::orderBy('bank_name')
            ->orderBy('account_name')
            ->get()
            ->map(fn($a) => [
                'label' => $a->account_name . ' (' . $a->bank_name . ')',
                'value' => $a->id,
            ])
            ->toArray();
    }

    public function translateText(): void
    {
        if (blank($this->inputText)) {
            return;
        }

        try {
            $tr = new GoogleTranslate($this->targetLang);
            $tr->setSource('id');

            $this->translatedText = $tr->preserveParameters()
                ->translate($this->inputText) ?? '(tidak ada hasil)';

        } catch (\Exception $e) {
            $this->translatedText = 'Error: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
