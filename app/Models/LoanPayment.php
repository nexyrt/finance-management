<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPayment extends Model
{
    protected $fillable = [
        'loan_id',
        'bank_account_id',
        'payment_date',
        'principal_paid',
        'interest_paid',
        'total_paid',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'principal_paid' => 'integer',
        'interest_paid' => 'integer',
        'total_paid' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}