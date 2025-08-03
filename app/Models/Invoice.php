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

    public function getAmountPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getAmountRemainingAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    // Convert rupiah string to integer (remove formatting)
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    // Calculate discount amount and total amount
    public function calculateDiscount(): void
    {
        if ($this->discount_value > 0) {
            if ($this->discount_type === 'percentage') {
                // discount_value is stored in basis points (e.g., 1500 = 15%)
                $this->discount_amount = (int) ($this->subtotal * ($this->discount_value / 10000));
            } else {
                // Fixed amount discount
                $this->discount_amount = min($this->discount_value, $this->subtotal);
            }
        } else {
            $this->discount_amount = 0;
        }

        $this->total_amount = $this->subtotal - $this->discount_amount;
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