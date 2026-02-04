<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'email',
        'NPWP',
        'KPP',
        'logo',
        'status',
        'EFIN',
        'account_representative',
        'ar_phone_number',
        'person_in_charge',
        'address'
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'billed_to_id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function receivables(): MorphMany
    {
        return $this->morphMany(Receivable::class, 'debtor');
    }

    // Override delete method to handle cascade deletes
    public function delete()
    {
        // Delete invoice items related to this client
        $this->invoiceItems()->delete();

        // Delete invoices for this client
        $this->invoices()->delete();

        return parent::delete();
    }
}