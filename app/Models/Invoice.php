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

    // ✅ NEW: Enhanced Status Methods for Flexible Edit Logic

    /**
     * Evaluate and return the correct status based on current conditions
     * This is the core business logic for status calculation
     */
    public function evaluateStatus(): string
    {
        $totalPaid = $this->amount_paid;
        $totalAmount = $this->total_amount;
        $dueDate = $this->due_date;

        // 1. Paid (including overpaid scenarios)
        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            return 'paid';
        }

        // 2. Partially paid
        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            return 'partially_paid';
        }

        // 3. No payment yet - check due date
        if ($totalPaid == 0) {
            return $dueDate->isPast() ? 'overdue' : 'sent';
        }

        return 'draft'; // Fallback (shouldn't happen in normal flow)
    }

    /**
     * Check if invoice has overpayment
     */
    public function getHasOverpaymentAttribute(): bool
    {
        return $this->amount_paid > $this->total_amount;
    }

    /**
     * Get overpayment amount
     */
    public function getOverpaymentAmountAttribute(): int
    {
        return max(0, $this->amount_paid - $this->total_amount);
    }

    /**
     * Update status based on current conditions
     * Call this after any changes that might affect status
     */
    public function updateStatus(): void
    {
        $newStatus = $this->evaluateStatus();
        
        if ($this->status !== $newStatus) {
            $oldStatus = $this->status;
            $this->update(['status' => $newStatus]);
            
            // Log status change
            \Log::info("Invoice {$this->invoice_number} status auto-updated", [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'total_amount' => $this->total_amount,
                'amount_paid' => $this->amount_paid,
                'due_date' => $this->due_date->format('Y-m-d'),
                'updated_by' => 'system_auto_evaluation'
            ]);
        }
    }

    /**
     * Get status change explanation
     */
    public function getStatusChangeExplanation(string $oldStatus, string $newStatus): string
    {
        $statusLabels = [
            'draft' => 'Draft',
            'sent' => 'Terkirim',
            'paid' => 'Lunas',
            'partially_paid' => 'Sebagian Dibayar',
            'overdue' => 'Terlambat'
        ];

        $messages = [
            'paid' => [
                'partially_paid' => 'Total amount bertambah, pembayaran tidak lagi mencukupi',
                'sent' => 'Total amount bertambah dan belum ada pembayaran tambahan',
                'overdue' => 'Total amount bertambah dan due date sudah lewat',
            ],
            'partially_paid' => [
                'paid' => 'Pembayaran sudah mencukupi total amount yang baru',
                'sent' => 'Tidak ada pembayaran (payment dihapus)',
                'overdue' => 'Due date sudah lewat',
            ],
            'sent' => [
                'paid' => 'Pembayaran sudah mencukupi',
                'partially_paid' => 'Menerima pembayaran sebagian',
                'overdue' => 'Due date sudah lewat',
            ],
            'overdue' => [
                'paid' => 'Pembayaran lunas',
                'partially_paid' => 'Menerima pembayaran sebagian',
                'sent' => 'Due date diperpanjang',
            ]
        ];

        return $messages[$oldStatus][$newStatus] ?? 
               "Status berubah dari {$statusLabels[$oldStatus]} menjadi {$statusLabels[$newStatus]}";
    }

    // ✅ ENHANCED: Format currency for display
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
        return 'Rp ' . number_format(abs($this->amount_remaining), 0, ',', '.');
    }

    public function getFormattedOverpaymentAttribute(): string
    {
        return 'Rp ' . number_format($this->overpayment_amount, 0, ',', '.');
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
        
        // ✅ AUTO-UPDATE STATUS after total recalculation
        $this->updateStatus();
    }

    // ✅ ENHANCED: Status checking methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status === 'partially_paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function canBeEdited(): bool
    {
        // ✅ ALL INVOICES CAN BE EDITED - No restrictions
        return true;
    }

    public function canReceivePayments(): bool
    {
        return in_array($this->status, ['sent', 'overdue', 'partially_paid']);
    }

    public function canBeSent(): bool
    {
        return $this->status === 'draft';
    }

    // Convert rupiah string to integer (remove formatting)
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    // ✅ ENHANCED: Model events for auto-calculation and status updates
    protected static function booted()
    {
        static::saving(function ($invoice) {
            // Auto-calculate discount and total when saving
            if ($invoice->isDirty(['subtotal', 'discount_type', 'discount_value'])) {
                $invoice->calculateDiscount();
            }
        });

        static::saved(function ($invoice) {
            // Auto-update status after saving if relevant fields changed
            if ($invoice->wasChanged(['total_amount', 'due_date']) && !$invoice->wasChanged(['status'])) {
                $invoice->updateStatus();
            }
        });
    }
}