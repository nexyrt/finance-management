<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'label',
        'parent_id',
    ];

    /**
     * Parent category relationship (using ID instead of code)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'parent_id');
    }

    /**
     * Children categories relationship
     */
    public function children(): HasMany
    {
        return $this->hasMany(TransactionCategory::class, 'parent_id')
            ->orderBy('label');
    }

    /**
     * Bank transactions relationship
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class, 'category_id');
    }

    /**
     * Reimbursements relationship
     */
    public function reimbursements(): HasMany
    {
        return $this->hasMany(Reimbursement::class, 'category_id');
    }

    /**
     * Scope: Get only parent categories (no parent_id)
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id')->orderBy('label');
    }

    /**
     * Scope: Filter by type (income/expense)
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if this is a parent category
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get full category path (e.g., "Parent → Child")
     */
    public function getFullPathAttribute(): string
    {
        return $this->isParent()
            ? $this->label
            : ($this->parent ? $this->parent->label . ' → ' . $this->label : $this->label);
    }
}