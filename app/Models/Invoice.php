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

    // Format currency for display
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
    }

    public function getFormattedAmountRemainingAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_remaining, 0, ',', '.');
    }

    // Get discount percentage (for percentage type)
    public function getDiscountPercentageAttribute(): float
    {
        return $this->discount_type === 'percentage' ? $this->discount_value / 100 : 0;
    }

    // Calculate discount amount based on type and value
    public function calculateDiscount(): void
    {
        if ($this->discount_type === 'percentage') {
            // discount_value stored as percentage * 100 (e.g., 1500 = 15%)
            $this->discount_amount = (int) (($this->subtotal * $this->discount_value) / 10000);
        } else {
            // Fixed amount discount
            $this->discount_amount = $this->discount_value;
        }
        
        // Ensure discount doesn't exceed subtotal
        $this->discount_amount = min($this->discount_amount, $this->subtotal);
        
        // Calculate final total
        $this->total_amount = $this->subtotal - $this->discount_amount;
    }

    // Auto-calculate totals when items change
    public function recalculateTotal(): void
    {
        $this->subtotal = $this->items()->sum('amount');
        $this->calculateDiscount();
        $this->save();
    }

    public function updateStatus()
    {
        if ($this->amount_paid >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partially_paid';
        } elseif ($this->due_date < now()) {
            $this->status = 'overdue';
        }
        $this->save();
    }

    // Convert rupiah string to integer (remove formatting)
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    // Model events for auto-calculation
    protected static function booted()
    {
        static::saving(function ($invoice) {
            // Auto-calculate discount and total when saving
            if ($invoice->isDirty(['subtotal', 'discount_type', 'discount_value'])) {
                $invoice->calculateDiscount();
            }
        });
    }
}