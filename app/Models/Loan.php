<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'loan_number',
        'lender_name',
        'principal_amount',
        'interest_type',
        'interest_amount',
        'interest_rate',
        'term_months',
        'start_date',
        'maturity_date',
        'status',
        'purpose',
        'contract_attachment',
    ];

    protected $casts = [
        'principal_amount' => 'integer',
        'interest_amount' => 'integer',
        'start_date' => 'date',
        'maturity_date' => 'date',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }
}