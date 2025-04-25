<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientRelationship extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'company_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'owner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'company_id');
    }
}
