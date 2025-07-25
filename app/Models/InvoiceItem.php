<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'client_id',
        'service_name',
        'quantity',
        'unit_price',
        'amount'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'amount' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Format currency for display
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Convert rupiah string to integer (remove formatting)
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    // Auto-calculate amount when quantity or unit_price changes
    protected static function booted()
    {
        static::saving(function ($invoiceItem) {
            $invoiceItem->amount = $invoiceItem->quantity * $invoiceItem->unit_price;
        });

        // Recalculate invoice total when item is saved/deleted
        static::saved(function ($invoiceItem) {
            $invoiceItem->invoice->recalculateTotal();
        });

        static::deleted(function ($invoiceItem) {
            $invoiceItem->invoice->recalculateTotal();
        });
    }
}