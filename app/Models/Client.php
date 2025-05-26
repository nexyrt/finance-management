<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\InvoiceItem;

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

    public function ownedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_relationships', 'owner_id', 'company_id')
            ->withTimestamps();
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_relationships', 'company_id', 'owner_id')
            ->withTimestamps();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'billed_to_id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Override delete method to handle relationships properly
    public function delete()
    {
        // Delete invoice items related to this client
        $this->invoiceItems()->delete();

        // Delete invoices for this client
        $this->invoices()->delete();

        // Delete client relationships
        if ($this->type === 'individual') {
            // Remove owned companies relationships
            $this->ownedCompanies()->detach();
        } else {
            // Remove owners relationships
            $this->owners()->detach();
        }

        return parent::delete();
    }
}