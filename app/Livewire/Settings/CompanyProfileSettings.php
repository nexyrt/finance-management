<?php

namespace App\Livewire\Settings;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanyProfileSettings extends Component
{
    use WithFileUploads;

    public $name, $address, $email, $phone;
    public $is_pkp = false, $npwp, $ppn_rate;
    public $finance_manager_name, $finance_manager_position;
    public $bank_accounts = [];

    public $logo, $signature, $stamp;
    public $currentLogo, $currentSignature, $currentStamp;
    public $showLogoModal = false, $showSignatureModal = false, $showStampModal = false;

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

    public function rules()
    {
        return [
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
        ];
    }

    public function updateCompanyProfile()
    {
        $validated = $this->validate();

        $company = CompanyProfile::firstOrNew();
        $company->fill([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_pkp' => $this->is_pkp,
            'npwp' => $validated['npwp'],
            'ppn_rate' => $validated['ppn_rate'],
            'finance_manager_name' => $validated['finance_manager_name'],
            'finance_manager_position' => $validated['finance_manager_position'],
            'bank_accounts' => $this->bank_accounts,
        ]);

        if ($this->logo) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = $this->logo->storeAs('images', 'letter-head.png', 'public');
        }

        if ($this->signature) {
            if ($company->signature_path && Storage::disk('public')->exists($company->signature_path)) {
                Storage::disk('public')->delete($company->signature_path);
            }
            $company->signature_path = $this->signature->storeAs('images', 'pdf-signature.png', 'public');
        }

        if ($this->stamp) {
            if ($company->stamp_path && Storage::disk('public')->exists($company->stamp_path)) {
                Storage::disk('public')->delete($company->stamp_path);
            }
            $company->stamp_path = $this->stamp->storeAs('images', 'kisantra-stamp.png', 'public');
        }

        $company->save();

        $this->currentLogo = $company->logo_path;
        $this->currentSignature = $company->signature_path;
        $this->currentStamp = $company->stamp_path;

        $this->reset(['logo', 'signature', 'stamp']);
        $this->dispatch('company-updated');
    }

    public function deleteExistingLogo()
    {
        $company = CompanyProfile::first();
        if ($company && $company->logo_path) {
            if (Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = null;
            $company->save();
            $this->currentLogo = null;
        }
    }

    public function deleteExistingSignature()
    {
        $company = CompanyProfile::first();
        if ($company && $company->signature_path) {
            if (Storage::disk('public')->exists($company->signature_path)) {
                Storage::disk('public')->delete($company->signature_path);
            }
            $company->signature_path = null;
            $company->save();
            $this->currentSignature = null;
        }
    }

    public function deleteExistingStamp()
    {
        $company = CompanyProfile::first();
        if ($company && $company->stamp_path) {
            if (Storage::disk('public')->exists($company->stamp_path)) {
                Storage::disk('public')->delete($company->stamp_path);
            }
            $company->stamp_path = null;
            $company->save();
            $this->currentStamp = null;
        }
    }

    public function render()
    {
        return view('livewire.settings.company-profile-settings');
    }
}