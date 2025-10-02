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
        'code',
        'label',
        'parent_code',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'parent_code', 'code');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TransactionCategory::class, 'parent_code', 'code')
            ->orderBy('label');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class, 'category_id');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_code')->orderBy('label');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }



    public function isParent(): bool
    {
        return is_null($this->parent_code);
    }

    public function getFullPathAttribute(): string
    {
        return $this->isParent()
            ? $this->label
            : $this->parent->label . ' → ' . $this->label;
    }
}