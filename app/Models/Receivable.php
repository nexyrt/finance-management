<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends Model
{
    protected $fillable = [
        'receivable_number',
        'type',
        'debtor_type',
        'debtor_id',
        'principal_amount',
        'interest_rate',
        'installment_months',
        'installment_amount',
        'loan_date',
        'due_date',
        'status',
        'purpose',
        'notes',
        'disbursement_account', // ← TAMBAH
        'approved_by',
        'approved_at',
        'review_notes',
        'rejection_reason',
        'contract_attachment_path',
        'contract_attachment_name',
    ];

    protected $casts = [
        'principal_amount' => 'integer',
        'installment_amount' => 'integer',
        'loan_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function debtor(): MorphTo
    {
        return $this->morphTo();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }
}