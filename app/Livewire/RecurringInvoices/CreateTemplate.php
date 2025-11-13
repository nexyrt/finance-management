<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\Client;
use App\Models\RecurringTemplate;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CreateTemplate extends Component
{
    // Template data - will be synced from Alpine
    public $template = [
        'template_name' => '',
        'client_id' => null,
        'start_date' => null,
        'end_date' => null,
        'frequency' => 'monthly',
    ];

    // Items array - will be synced from Alpine
    public $items = [];

    // Discount - will be synced from Alpine
    public $discount = [
        'type' => 'fixed',
        'value' => 0,
        'reason' => '',
    ];

    public function save()
    {
        $this->validate([
            'template.template_name' => 'required|string|max:255',
            'template.client_id' => 'required|exists:clients,id',
            'template.start_date' => 'required|date',
            'template.end_date' => 'required|date|after:template.start_date',
            'template.frequency' => 'required|in:monthly,quarterly,semi_annual,annual',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required',
            'discount.type' => 'in:fixed,percentage',
            'discount.value' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Parse items
            $parsedItems = [];
            $subtotal = 0;

            foreach ($this->items as $item) {
                $unitPrice = $this->parseAmount($item['unit_price']);
                $quantity = $item['quantity'];
                $amount = $unitPrice * $quantity;
                $cogsAmount = $this->parseAmount($item['cogs_amount'] ?? '0');
                $isTaxDeposit = $item['is_tax_deposit'] ?? false;

                $parsedItems[] = [
                    'client_id' => $item['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                    'is_tax_deposit' => $isTaxDeposit,
                ];

                $subtotal += $amount;
            }

            // Calculate discount
            $discountAmount = 0;
            $discountValue = 0;
            if ($this->discount['type'] === 'fixed') {
                $discountValue = $this->discount['value'];
                $discountAmount = $this->discount['value'];
            } else {
                $discountValue = $this->discount['value'];
                $discountAmount = ($subtotal * $this->discount['value']) / 100;
            }

            $totalAmount = max(0, $subtotal - $discountAmount);

            // Prepare invoice template JSON
            $invoiceTemplate = [
                'items' => $parsedItems,
                'subtotal' => $subtotal,
                'discount_type' => $this->discount['type'] ?? 'fixed',
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'discount_reason' => $this->discount['reason'],
                'total_amount' => $totalAmount,
            ];

            // Calculate next generation date
            $nextGenerationDate = $this->calculateNextGenerationDate(
                $this->template['start_date'],
                $this->template['frequency']
            );

            // Create recurring template
            RecurringTemplate::create([
                'client_id' => $this->template['client_id'],
                'template_name' => $this->template['template_name'],
                'start_date' => $this->template['start_date'],
                'end_date' => $this->template['end_date'],
                'frequency' => $this->template['frequency'],
                'next_generation_date' => $nextGenerationDate,
                'status' => 'active',
                'invoice_template' => json_encode($invoiceTemplate),
            ]);

            DB::commit();

            session()->flash('success', "Recurring template '{$this->template['template_name']}' created successfully!");

            return $this->redirect(route('recurring-invoices.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create recurring template: '.$e->getMessage());
            session()->flash('error', 'Failed to create recurring template. Please try again.');
        }
    }

    private function calculateNextGenerationDate($startDate, $frequency)
    {
        $date = \Carbon\Carbon::parse($startDate);

        return match ($frequency) {
            'monthly' => $date->addMonth(),
            'quarterly' => $date->addMonths(3),
            'semi_annual' => $date->addMonths(6),
            'annual' => $date->addYear(),
            default => $date->addMonth(),
        };
    }

    private function parseAmount($value): int
    {
        if (empty($value)) {
            return 0;
        }

        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    #[Computed]
    public function clients()
    {
        return Client::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'logo'])
            ->toArray();
    }

    #[Computed]
    public function services()
    {
        return Service::orderBy('name')
            ->get(['id', 'name', 'price', 'type'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'type' => $service->type,
                    'formatted_price' => 'Rp '.number_format($service->price, 0, ',', '.'),
                ];
            })
            ->toArray();
    }

    #[Computed]
    public function frequencyOptions()
    {
        return [
            ['value' => 'monthly', 'label' => 'Monthly'],
            ['value' => 'quarterly', 'label' => 'Quarterly (Every 3 months)'],
            ['value' => 'semi_annual', 'label' => 'Semi-Annual (Every 6 months)'],
            ['value' => 'annual', 'label' => 'Annual (Every year)'],
        ];
    }

    public function render()
    {
        return view('livewire.recurring-invoices.create-template');
    }
}
