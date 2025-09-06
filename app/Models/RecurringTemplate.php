<?php

// RecurringTemplate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RecurringTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'template_name',
        'start_date',
        'end_date',
        'frequency',
        'next_generation_date',
        'status',
        'invoice_template'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_generation_date' => 'date',
        'invoice_template' => 'array'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function recurringInvoices(): HasMany
    {
        return $this->hasMany(RecurringInvoice::class, 'template_id');
    }

    // Generate next due date based on frequency
    public function calculateNextGenerationDate(): Carbon
    {
        $current = $this->next_generation_date;

        return match ($this->frequency) {
            'monthly' => $current->addMonth(),
            'quarterly' => $current->addMonths(3),
            'semi_annual' => $current->addMonths(6),
            'annual' => $current->addYear(),
            default => $current->addMonth()
        };
    }

    // Check if template is due for generation
    public function isDueForGeneration(): bool
    {
        return $this->status === 'active' &&
            $this->next_generation_date <= now()->toDateString() &&
            $this->end_date >= now()->toDateString();
    }

    // Get template items formatted
    public function getFormattedItemsAttribute(): array
    {
        return $this->invoice_template['items'] ?? [];
    }

    // Calculate total from template
    public function getTotalAmountAttribute(): int
    {
        return $this->invoice_template['total_amount'] ?? 0;
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // Get remaining invoices to generate
    public function getRemainingInvoicesAttribute(): int
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        $totalMonths = $start->diffInMonths($end) + 1;
        $generatedCount = $this->recurringInvoices()->count();

        return max(0, $totalMonths - $generatedCount);
    }
}
