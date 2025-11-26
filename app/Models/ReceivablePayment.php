<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivablePayment extends Model
{
    protected $fillable = [
        'receivable_id',
        'payment_date',
        'principal_paid',
        'interest_paid',
        'total_paid',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'principal_paid' => 'integer',
        'interest_paid' => 'integer',
        'total_paid' => 'integer',
    ];

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }
}