<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'logo_path',
        'signature_path',
        'stamp_path',
        'is_pkp',
        'npwp',
        'ppn_rate',
        'bank_accounts',
        'finance_manager_name',
        'finance_manager_position'
    ];

    protected $casts = [
        'bank_accounts' => 'array',
        'is_pkp' => 'boolean',
        'ppn_rate' => 'decimal:2',
    ];

    // Model CompanyProfile.php
    public function getLogoBase64Attribute(): string
    {
        if (!$this->logo_path)
            return '';

        $fullPath = public_path($this->logo_path);
        return file_exists($fullPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath))
            : '';
    }

    public function getSignatureBase64Attribute(): string
    {
        if (!$this->signature_path)
            return '';

        $fullPath = public_path($this->signature_path);
        return file_exists($fullPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath))
            : '';
    }

    public function getStampBase64Attribute(): string
    {
        if (!$this->stamp_path)
            return '';

        $fullPath = public_path($this->stamp_path);
        return file_exists($fullPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath))
            : '';
    }

    public static function current(): ?self
    {
        return static::first();
    }
}