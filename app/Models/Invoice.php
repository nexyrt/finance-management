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
        'total_amount',
        'issue_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
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
}
