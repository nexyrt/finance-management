<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Reimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'category',
        'attachment_path',
        'attachment_name',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'paid_by',
        'paid_at',
        'bank_transaction_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'integer',
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function bankTransaction(): BelongsTo
    {
        return $this->belongsTo(BankTransaction::class);
    }

    // =====================================
    // SCOPES
    // =====================================

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

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeMonth($query, int $month, int $year = null)
    {
        $year = $year ?: now()->year;
        return $query->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);
    }

    public function scopeYear($query, int $year)
    {
        return $query->whereYear('expense_date', $year);
    }

    // =====================================
    // STATUS CHECKERS
    // =====================================

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

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canDelete(): bool
    {
        return $this->status === 'draft';
    }

    public function canSubmit(): bool
    {
        return $this->status === 'draft';
    }

    public function canReview(): bool
    {
        return $this->status === 'pending';
    }

    public function canPay(): bool
    {
        return $this->status === 'approved';
    }

    // =====================================
    // ACTIONS
    // =====================================

    public function submit(): bool
    {
        if (!$this->canSubmit()) {
            return false;
        }

        return $this->update(['status' => 'pending']);
    }

    public function approve(int $reviewerId, ?string $notes = null): bool
    {
        if (!$this->canReview()) {
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
        if (!$this->canReview()) {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function markAsPaid(int $payerId, int $bankTransactionId): bool
    {
        if (!$this->canPay()) {
            return false;
        }

        return $this->update([
            'status' => 'paid',
            'paid_by' => $payerId,
            'paid_at' => now(),
            'bank_transaction_id' => $bankTransactionId,
        ]);
    }

    // =====================================
    // ATTACHMENT HELPERS
    // =====================================

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }

    public function getAttachmentTypeAttribute(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        $extension = pathinfo($this->attachment_name, PATHINFO_EXTENSION);
        return strtolower($extension);
    }

    public function isImageAttachment(): bool
    {
        return in_array($this->attachment_type, ['jpg', 'jpeg', 'png', 'gif']);
    }

    public function isPdfAttachment(): bool
    {
        return $this->attachment_type === 'pdf';
    }

    // =====================================
    // FORMATTERS
    // =====================================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'paid' => 'green',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
            default => ucfirst($this->status),
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'transport' => 'Transport',
            'meals' => 'Meals & Entertainment',
            'office_supplies' => 'Office Supplies',
            'communication' => 'Communication',
            'accommodation' => 'Accommodation',
            'medical' => 'Medical',
            'other' => 'Other',
            default => ucfirst($this->category),
        };
    }

    // =====================================
    // UTILITY
    // =====================================

    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    public static function categories(): array
    {
        return [
            ['label' => 'Transport', 'value' => 'transport'],
            ['label' => 'Meals & Entertainment', 'value' => 'meals'],
            ['label' => 'Office Supplies', 'value' => 'office_supplies'],
            ['label' => 'Communication', 'value' => 'communication'],
            ['label' => 'Accommodation', 'value' => 'accommodation'],
            ['label' => 'Medical', 'value' => 'medical'],
            ['label' => 'Other', 'value' => 'other'],
        ];
    }

    public static function statuses(): array
    {
        return [
            ['label' => 'Draft', 'value' => 'draft'],
            ['label' => 'Pending Review', 'value' => 'pending'],
            ['label' => 'Approved', 'value' => 'approved'],
            ['label' => 'Rejected', 'value' => 'rejected'],
            ['label' => 'Paid', 'value' => 'paid'],
        ];
    }

    // =====================================
    // BOOT
    // =====================================

    protected static function boot()
    {
        parent::boot();

        // Delete attachment when model is deleted
        static::deleting(function ($reimbursement) {
            if ($reimbursement->attachment_path && Storage::exists($reimbursement->attachment_path)) {
                Storage::delete($reimbursement->attachment_path);
            }
        });
    }
}