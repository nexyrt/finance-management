<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'bank_account_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'installment_number'
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            // Update invoice status
            $payment->invoice->updateStatus();

            // Update bank account balance
            $bankAccount = $payment->bankAccount;
            $bankAccount->current_balance += $payment->amount;
            $bankAccount->save();

            // Create bank transaction record
            BankTransaction::create([
                'bank_account_id' => $payment->bank_account_id,
                'amount' => $payment->amount,
                'transaction_date' => $payment->payment_date,
                'transaction_type' => 'credit', // ✅ cocok dengan enum
                'description' => 'Payment received for Invoice #' . $payment->invoice->invoice_number,
                'reference_number' => $payment->reference_number,
            ]);

        });
    }
}
