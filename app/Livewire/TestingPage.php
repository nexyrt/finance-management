<?php

namespace App\Livewire;

use GuzzleHttp\Client;
use Livewire\Component;

class TestingPage extends Component
{
    public array $accounts = [];
    public string $error = '';

    public function mount(): void
    {
        $this->loadAccounts();
    }

    public function loadAccounts(): void
    {
        try {
            $client = new Client(['verify' => false, 'cookies' => true]);

            // Login dulu untuk mendapatkan session
            $client->post('https://finance.kisantra.com/login', [
                'form_params' => [
                    'email'    => 'admin@email.com', // ganti dengan email Anda
                    'password' => 'password',   // ganti dengan password Anda
                    '_token'   => $this->getCsrfToken($client),
                ],
            ]);

            // Ambil data setelah login
            $response = $client->get('https://finance.kisantra.com/api/bank-accounts');
            $this->accounts = json_decode($response->getBody()->getContents(), true) ?? [];
            $this->error = '';
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    private function getCsrfToken(Client $client): string
    {
        $response = $client->get('https://finance.kisantra.com/login');
        $html = $response->getBody()->getContents();
        preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches);
        return $matches[1] ?? '';
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
