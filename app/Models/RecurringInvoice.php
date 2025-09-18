<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'client_id',
        'scheduled_date',
        'invoice_data',
        'status',
        'published_invoice_id'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'invoice_data' => 'array'
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(RecurringTemplate::class, 'template_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function publishedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'published_invoice_id');
    }

    // Get total amount from invoice data
    public function getTotalAmountAttribute(): int
    {
        return $this->invoice_data['total_amount'] ?? 0;
    }
    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // Get items from invoice data
    public function getItemsAttribute(): array
    {
        return $this->invoice_data['items'] ?? [];
    }

    // Publish to invoices table
    public function publish(): Invoice
    {
        if ($this->status === 'published') {
            return $this->publishedInvoice;
        }

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'billed_to_id' => $this->client_id,
            'subtotal' => $this->invoice_data['subtotal'],
            'discount_amount' => $this->invoice_data['discount_amount'] ?? 0,
            'discount_type' => $this->invoice_data['discount_type'] ?? 'fixed',
            'discount_value' => $this->invoice_data['discount_value'] ?? 0,
            'discount_reason' => $this->invoice_data['discount_reason'] ?? null,
            'total_amount' => $this->total_amount,
            'issue_date' => $this->scheduled_date,
            'due_date' => $this->scheduled_date->addDays(30),
            'status' => 'draft'
        ]);

        // Create invoice items
        foreach ($this->items as $itemData) {
            $invoice->items()->create([
                'client_id' => $this->client_id,
                'service_name' => $itemData['service_name'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'amount' => $itemData['amount'],
                'cogs_amount' => $itemData['cogs_amount'] ?? 0,
            ]);
        }

        // Update recurring invoice status
        $this->update([
            'status' => 'published',
            'published_invoice_id' => $invoice->id
        ]);

        return $invoice;
    }

    // Generate invoice number for published invoice
    private function generateInvoiceNumber(): string
    {
        $date = $this->scheduled_date;
        $currentMonth = $date->format('m');
        $currentYear = $date->format('y');

        $invoices = Invoice::whereYear('issue_date', $date->year)
            ->whereMonth('issue_date', $date->month)
            ->pluck('invoice_number');

        $maxSequence = 0;
        foreach ($invoices as $invoiceNumber) {
            if (preg_match('/INV\/(\d+)\/KSN\/\d{2}\.\d{2}/', $invoiceNumber, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
            }
        }

        $nextSequence = $maxSequence + 1;

        return sprintf(
            'INV/%02d/KSN/%02d.%s',
            $nextSequence,
            (int) $currentMonth,
            $currentYear
        );
    }

    // Scope for monthly filtering
    public function scopeForMonth($query, int $month, int $year = null)
    {
        $year = $year ?: now()->year;
        return $query->whereYear('scheduled_date', $year)
            ->whereMonth('scheduled_date', $month);
    }

    // Scope for year filtering
    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('scheduled_date', $year);
    }
}