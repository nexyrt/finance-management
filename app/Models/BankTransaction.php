<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'amount',
        'transaction_date',
        'transaction_type',
        'description',
        'reference_number',
        'attachment_path',
        'attachment_name',
        'category_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'integer',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Format currency for display
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Convert rupiah string to integer (remove formatting)
    public static function parseAmount(string $amount): int
    {
        return (int) preg_replace('/[^0-9]/', '', $amount);
    }

    // Attachment helpers
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path) && Storage::exists($this->attachment_path);
    }

    public function getAttachmentTypeAttribute(): ?string
    {
        if (!$this->hasAttachment())
            return null;

        $extension = pathinfo($this->attachment_name, PATHINFO_EXTENSION);
        return strtolower($extension);
    }

    public function isImageAttachment(): bool
    {
        return in_array($this->attachment_type, ['jpg', 'jpeg', 'png']);
    }

    public function isPdfAttachment(): bool
    {
        return $this->attachment_type === 'pdf';
    }

    // Delete attachment when model is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaction) {
            if ($transaction->attachment_path && Storage::exists($transaction->attachment_path)) {
                Storage::delete($transaction->attachment_path);
            }
        });
    }
}