<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'billed_to_id',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_value',
        'discount_reason',
        'total_amount',
        'issue_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'discount_value' => 'integer',
        'total_amount' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'billed_to_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Essential payment tracking
    public function getAmountPaidAttribute(): int
    {
        return $this->payments()->sum('amount');
    }

    public function getAmountRemainingAttribute(): int
    {
        return $this->total_amount - $this->amount_paid;
    }

    // Essential profit tracking
    public function getTotalCogsAttribute(): int
    {
        return $this->items()->sum('cogs_amount');
    }

    public function getGrossProfitAttribute(): int
    {
        return $this->total_amount - $this->total_cogs;
    }

    public function getOutstandingProfitAttribute(): int
    {
        $totalPaid = $this->amount_paid;
        $totalCogs = $this->total_cogs;

        if ($totalPaid <= $totalCogs) {
            return $this->gross_profit;
        }

        $realizedProfit = $totalPaid - $totalCogs;
        return $this->gross_profit - $realizedProfit;
    }

    public function getPaidProfitAttribute(): int
    {
        return $this->gross_profit - $this->outstanding_profit;
    }

    // Update invoice status based on payments
    public function updateStatus(): void
    {
        $amountPaid = $this->amount_paid;

        if ($amountPaid == 0) {
            $this->status = 'draft';
        } elseif ($amountPaid >= $this->total_amount) {
            $this->status = 'paid';
        } else {
            $this->status = 'partially_paid';
        }

        $this->save();
    }
}