<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\InvoiceItem; // Add this import

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'address',
        'tax_id'
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

    public function serviceClients(): HasMany
    {
        return $this->hasMany(ServiceClient::class, 'client_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'billed_to_id');
    }

    public function scopeIndividuals($query)
    {
        return $query->where('type', 'individual');
    }

    public function scopeCompanies($query)
    {
        return $query->where('type', 'company');
    }

    // Override delete method to handle relationships properly
    public function delete()
    {
        // First, delete all invoice items that reference service clients of this client
        $serviceClientIds = $this->serviceClients()->pluck('id');
        InvoiceItem::whereIn('service_client_id', $serviceClientIds)->delete();

        // Now delete the service clients
        $this->serviceClients()->delete();

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