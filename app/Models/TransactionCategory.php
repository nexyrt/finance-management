<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionCategory extends Model
{
    use HasFactory;

    /**
     * Valid Profit & Loss groups a category can map to.
     * Only meaningful for type=income / type=expense; financing & transfer
     * are excluded from the P&L by their type.
     *
     * @var list<string>
     */
    public const PL_GROUPS = ['revenue', 'cogs', 'opex', 'other_income', 'other_expense', 'tax'];

    protected $fillable = [
        'type',
        'pl_group',
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
     * Fund request items relationship
     */
    public function fundRequestItems(): HasMany
    {
        return $this->hasMany(FundRequestItem::class, 'category_id');
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
        if ($this->isParent()) {
            return $this->label;
        }

        if ($this->relationLoaded('parent') && $this->parent) {
            return $this->parent->label.' → '.$this->label;
        }

        return $this->label;
    }
}
