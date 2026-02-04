<?php

namespace App\Livewire;

use Gemini\Laravel\Facades\Gemini;
use Livewire\Component;

class TestingPage extends Component
{
    public string $question = '';
    public string $response = '';

    public function listModels()
    {
        try {
            $geminiResponse = Gemini::models()->list();

            $models = [];
            foreach ($geminiResponse->models as $model) {
                if (in_array('generateContent', $model->supportedGenerationMethods ?? [])) {  // Filter yang support generateContent
                    $models[] = $model->name . ' (' . ($model->displayName ?? 'N/A') . ')';
                }
            }

            $this->response = "Model tersedia (support generateContent):\n" . implode("\n", $models);
        } catch (\Exception $e) {
            $this->response = "Gagal list models: " . $e->getMessage();
        }
    }

    public function askGemini()
    {
        $this->response = 'Sedang berpikir...';

        if (empty(trim($this->question))) {
            $this->response = 'Masukkan pertanyaan dulu!';
            return;
        }

        try {
            // Gunakan model yang tersedia dari list Anda
            $result = Gemini::generativeModel('models/gemini-2.5-flash')
                ->generateContent($this->question);

            $this->response = $result->text();
        } catch (\Exception $e) {
            $this->response = "Gagal: " . $e->getMessage() . "\nCoba model lain seperti 'models/gemini-2.0-flash'";
        }
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
