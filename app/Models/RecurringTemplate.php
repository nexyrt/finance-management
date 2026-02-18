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
        'status',
        'invoice_template'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    // Returns all valid billing months based on interval model from start_date.
    // Each cycle advances from start_date by one interval; invoice is billed
    // in the month the cycle lands on. A cycle is valid if its date <= end_date.
    // Example: start=19 Feb, end=10 Dec, monthly → cycles: 19 Mar, 19 Apr, ..., 19 Nov (valid), 19 Dec (> 10 Dec, stop)
    public function getValidMonths(): array
    {
        $cycleDate = $this->start_date->copy();
        $endDate = $this->end_date->copy();
        $months = [];

        while (true) {
            $cycleDate = match ($this->frequency) {
                'monthly'     => $cycleDate->addMonth(),
                'quarterly'   => $cycleDate->addMonths(3),
                'semi_annual' => $cycleDate->addMonths(6),
                'annual'      => $cycleDate->addYear(),
                default       => $cycleDate->addMonth(),
            };

            if ($cycleDate->gt($endDate)) {
                break;
            }

            $months[] = [
                'year'  => $cycleDate->year,
                'month' => $cycleDate->month,
            ];
        }

        return $months;
    }

    // Check if a specific year/month is a valid billing period for this template
    public function isValidPeriodForGeneration(int $year, int $month): bool
    {
        foreach ($this->getValidMonths() as $validMonth) {
            if ($validMonth['year'] === $year && $validMonth['month'] === $month) {
                return true;
            }
        }
        return false;
    }

    // Total number of invoices this template should generate over its lifetime
    public function getTotalInvoicesCount(): int
    {
        return count($this->getValidMonths());
    }

    // Get remaining invoices to generate
    public function getRemainingInvoicesAttribute(): int
    {
        $generatedCount = $this->recurringInvoices()->count();
        return max(0, $this->getTotalInvoicesCount() - $generatedCount);
    }
}
