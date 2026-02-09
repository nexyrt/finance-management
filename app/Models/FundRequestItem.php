<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundRequestItem extends Model
{
    protected $fillable = [
        'fund_request_id', 'description', 'category_id',
        'amount', 'notes', 'quantity', 'unit_price',
    ];

    // ===== RELATIONSHIPS =====
    public function fundRequest(): BelongsTo
    {
        return $this->belongsTo(FundRequest::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    // ===== AUTO-UPDATE PARENT TOTAL =====
    protected static function booted()
    {
        static::created(function ($item) {
            $item->fundRequest->calculateTotalAmount();
        });

        static::updated(function ($item) {
            $item->fundRequest->calculateTotalAmount();
        });

        static::deleted(function ($item) {
            $item->fundRequest->calculateTotalAmount();
        });
    }
}
