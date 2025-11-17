<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReimbursementPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reimbursement_id',
        'bank_transaction_id',
        'amount',
        'payment_date',
        'notes',
        'paid_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'integer',
    ];

    // Relationships
    public function reimbursement(): BelongsTo
    {
        return $this->belongsTo(Reimbursement::class);
    }

    public function bankTransaction(): BelongsTo
    {
        return $this->belongsTo(BankTransaction::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Formatters
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}