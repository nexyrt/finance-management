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
        'amount',
        'cogs_amount'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'amount' => 'integer',
        'cogs_amount' => 'integer',
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

    public function getFormattedCogsAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->cogs_amount, 0, ',', '.');
    }

    public function getProfitAmountAttribute(): int
    {
        return $this->amount - $this->cogs_amount;
    }

    public function getFormattedProfitAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->profit_amount, 0, ',', '.');
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->profit_amount / $this->amount) * 100;
    }

    // Convert rupiah string to integer (remove formatting)
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }
}