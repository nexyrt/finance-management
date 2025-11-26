<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'branch',
        'initial_balance',
    ];

    protected $casts = [
        'initial_balance' => 'integer',
    ];

    protected $appends = ['balance'];

    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function loanPayments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    // Current balance calculation
    public function getBalanceAttribute(): int
    {
        $payments = $this->payments()->sum('amount');
        $credits = $this->transactions()->where('transaction_type', 'credit')->sum('amount');
        $debits = $this->transactions()->where('transaction_type', 'debit')->sum('amount');

        return $this->initial_balance + $payments + $credits - $debits;
    }

    // Formatted currency display
    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    public function getFormattedInitialBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->initial_balance, 0, ',', '.');
    }

    // Parse currency string to integer
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }
}