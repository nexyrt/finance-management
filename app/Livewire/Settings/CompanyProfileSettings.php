<?php

namespace App\Livewire\Settings;

use App\Models\CompanyProfile;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanyProfileSettings extends Component
{
    use WithFileUploads;

    public $name, $address, $email, $phone;
    public $is_pkp, $npwp, $ppn_rate;
    public $finance_manager_name, $finance_manager_position;
    public $bank_accounts = [];
    public $logo, $signature, $stamp;
    public $currentLogo, $currentSignature, $currentStamp;

    public function mount()
    {
        $company = CompanyProfile::first();
        if ($company) {
            $this->fill($company->only([
                'name',
                'address',
                'email',
                'phone',
                'is_pkp',
                'npwp',
                'ppn_rate',
                'finance_manager_name',
                'finance_manager_position',
                'bank_accounts'
            ]));

            $this->currentLogo = $company->logo_path;
            $this->currentSignature = $company->signature_path;
            $this->currentStamp = $company->stamp_path;
        }
    }

    public function updateCompanyProfile()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'is_pkp' => 'boolean',
            'npwp' => 'nullable|string',
            'ppn_rate' => 'required|numeric|min:0|max:100',
            'finance_manager_name' => 'required|string',
            'finance_manager_position' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'stamp' => 'nullable|image|max:2048',
        ]);

        $company = CompanyProfile::firstOrNew();
        $company->fill($validated);

        if ($this->logo) {
            $company->logo_path = $this->logo->storeAs('images', 'letter-head.png', 'public');
        }
        if ($this->signature) {
            $company->signature_path = $this->signature->storeAs('images', 'pdf-signature.png', 'public');
        }
        if ($this->stamp) {
            $company->stamp_path = $this->stamp->storeAs('images', 'kisantra-stamp.png', 'public');
        }

        $company->save();

        $this->dispatch('company-updated');
    }

    public function render()
    {
        return view('livewire.settings.company-profile-settings');
    }
}