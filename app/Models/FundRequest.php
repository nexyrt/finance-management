<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundRequest extends Model
{
    protected $fillable = [
        'user_id', 'title', 'purpose', 'total_amount',
        'priority', 'needed_by_date',
        'attachment_path', 'attachment_name',
        'status', 'reviewed_by', 'reviewed_at', 'review_notes',
        'disbursed_by', 'disbursed_at', 'disbursement_date',
        'bank_transaction_id', 'disbursement_notes',
    ];

    protected $casts = [
        'needed_by_date' => 'date',
        'reviewed_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'disbursement_date' => 'date',
    ];

    // ===== AUTO-CALCULATE TOTAL =====
    public function calculateTotalAmount(): void
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }

    // ===== STATUS CHECKERS =====
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDisbursed(): bool
    {
        return $this->status === 'disbursed';
    }

    // ===== ACTION METHODS =====
    public function submit(): bool
    {
        if (! $this->canSubmit()) {
            return false;
        }

        // Recalculate total before submitting
        $this->calculateTotalAmount();

        return $this->update(['status' => 'pending']);
    }

    public function approve(int $reviewerId, ?string $notes = null): bool
    {
        if (! $this->canReview()) {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function reject(int $reviewerId, ?string $notes = null): bool
    {
        if (! $this->canReview()) {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function disburse(
        int $bankTransactionId,
        string $disbursementDate,
        int $disbursedBy,
        ?string $notes = null
    ): bool {
        if (! $this->canDisburse()) {
            return false;
        }

        return $this->update([
            'status' => 'disbursed',
            'bank_transaction_id' => $bankTransactionId,
            'disbursement_date' => $disbursementDate,
            'disbursed_by' => $disbursedBy,
            'disbursed_at' => now(),
            'disbursement_notes' => $notes,
        ]);
    }

    // ===== CAN-DO CHECKERS =====
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canDelete(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if ($user && $user->hasRole('admin')) {
            return true;
        }

        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canSubmit(): bool
    {
        // Must have at least 1 item with valid amount
        return $this->status === 'draft'
            && $this->items()->count() > 0
            && $this->items()->sum('amount') > 0;
    }

    public function canReview(): bool
    {
        return $this->status === 'pending';
    }

    public function canDisburse(): bool
    {
        return $this->status === 'approved';
    }

    // ===== RELATIONSHIPS =====
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FundRequestItem::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function bankTransaction(): BelongsTo
    {
        return $this->belongsTo(BankTransaction::class);
    }

    // ===== SCOPES =====
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'disbursed');
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeNeededBy($query, $date)
    {
        return $query->where('needed_by_date', '<=', $date);
    }

    // ===== FILE HANDLING =====
    protected static function booted()
    {
        static::deleting(function ($fundRequest) {
            // Delete attachment file when fund request is deleted
            if ($fundRequest->attachment_path && \Storage::disk('public')->exists($fundRequest->attachment_path)) {
                \Storage::disk('public')->delete($fundRequest->attachment_path);
            }
        });
    }
}
