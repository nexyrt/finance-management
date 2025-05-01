<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->belongsToMany(
            Client::class, 
            'client_relationships', 
            'owner_id', 
            'company_id'
        )->select(['clients.*']); // Explicitly select all columns from clients table
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class, 
            'client_relationships', 
            'company_id', 
            'owner_id'
        )->select(['clients.*']); // Explicitly select all columns from clients table
    }

    public function serviceClients(): HasMany
    {
        return $this->hasMany(ServiceClient::class);
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
}