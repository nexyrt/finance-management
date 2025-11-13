<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\Client;
use App\Models\Service;
use App\Models\RecurringTemplate;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EditTemplate extends Component
{
    public RecurringTemplate $template;

    // Template data - will be synced from Alpine
    public $templateData = [
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

    public function mount(RecurringTemplate $template)
    {
        $this->template = $template->load('client');
    }

    public function update()
    {
        $this->validate([
            'templateData.template_name' => 'required|string|max:255',
            'templateData.client_id' => 'required|exists:clients,id',
            'templateData.start_date' => 'required|date',
            'templateData.end_date' => 'required|date|after:templateData.start_date',
            'templateData.frequency' => 'required|in:monthly,quarterly,semi_annual,annual',
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

            // Calculate next generation date if frequency or start date changed
            $nextGenerationDate = $this->template->next_generation_date;
            if (
                $this->template->start_date->format('Y-m-d') !== $this->templateData['start_date'] ||
                $this->template->frequency !== $this->templateData['frequency']
            ) {
                $nextGenerationDate = $this->calculateNextGenerationDate(
                    $this->templateData['start_date'],
                    $this->templateData['frequency']
                );
            }

            // Update template
            $this->template->update([
                'client_id' => $this->templateData['client_id'],
                'template_name' => $this->templateData['template_name'],
                'start_date' => $this->templateData['start_date'],
                'end_date' => $this->templateData['end_date'],
                'frequency' => $this->templateData['frequency'],
                'next_generation_date' => $nextGenerationDate,
                'invoice_template' => $invoiceTemplate,
            ]);

            DB::commit();

            session()->flash('success', "Template '{$this->template->template_name}' updated successfully!");

            return $this->redirect(route('recurring-invoices.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update recurring template: ' . $e->getMessage());
            session()->flash('error', 'Failed to update template. Please try again.');
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
        if (empty($value))
            return 0;
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    #[Computed]
    public function existingTemplateData()
    {
        return [
            'template_name' => $this->template->template_name,
            'client_id' => $this->template->client_id,
            'client_name' => $this->template->client->name ?? '',
            'start_date' => $this->template->start_date->format('Y-m-d'),
            'end_date' => $this->template->end_date->format('Y-m-d'),
            'frequency' => $this->template->frequency,
        ];
    }

    #[Computed]
    public function existingItems()
    {
        $templateData = $this->template->invoice_template;
        return collect($templateData['items'] ?? [])->map(function ($item, $index) {
            $client = Client::find($item['client_id']);
            return [
                'id' => $index + 1,
                'client_id' => $item['client_id'],
                'client_name' => $client->name ?? '',
                'service_name' => $item['service_name'],
                'quantity' => $item['quantity'],
                'unit_price' => number_format($item['unit_price'], 0, ',', '.'),
                'amount' => $item['amount'],
                'cogs_amount' => number_format($item['cogs_amount'] ?? 0, 0, ',', '.'),
                'profit' => $item['amount'] - ($item['cogs_amount'] ?? 0),
                'is_tax_deposit' => $item['is_tax_deposit'] ?? false,
            ];
        })->toArray();
    }

    #[Computed]
    public function existingDiscount()
    {
        $templateData = $this->template->invoice_template;
        return [
            'type' => $templateData['discount_type'] ?? 'fixed',
            'value' => ($templateData['discount_type'] ?? 'fixed') === 'percentage'
                ? ($templateData['discount_value'] ?? 0)
                : number_format($templateData['discount_value'] ?? 0, 0, ',', '.'),
            'reason' => $templateData['discount_reason'] ?? '',
        ];
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
                    'formatted_price' => 'Rp ' . number_format($service->price, 0, ',', '.')
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
        return view('livewire.recurring-invoices.edit-template');
    }
}