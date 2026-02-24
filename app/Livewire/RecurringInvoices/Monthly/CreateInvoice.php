<?php

namespace App\Livewire\RecurringInvoices\Monthly;

use TallStackUi\Traits\Interactions;
use App\Models\Client;
use App\Models\RecurringInvoice;
use App\Models\RecurringTemplate;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CreateInvoice extends Component
{
    use Interactions;

    public array $invoiceData = [
        'template_id' => null,
        'scheduled_date' => '',
    ];

    public array $items = [];

    public array $discount = [
        'type' => 'fixed',
        'value' => 0,
        'reason' => '',
    ];

    public function mount(): void
    {
        $this->invoiceData['scheduled_date'] = now()->format('Y-m-d');
    }

    #[Computed]
    public function clients(): array
    {
        return Client::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'logo'])
            ->toArray();
    }

    #[Computed]
    public function services(): array
    {
        return Service::orderBy('name')
            ->get(['id', 'name', 'price', 'type'])
            ->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'type' => $service->type,
                'formatted_price' => 'Rp ' . number_format($service->price, 0, ',', '.'),
            ])
            ->toArray();
    }

    #[Computed]
    public function availableTemplates(): array
    {
        return RecurringTemplate::where('status', 'active')
            ->with('client')
            ->orderBy('template_name')
            ->get()
            ->map(fn($template) => [
                'id' => $template->id,
                'label' => $template->client->name . ' — ' . $template->template_name,
                'frequency' => $template->frequency,
                'invoice_template' => $template->invoice_template,
            ])
            ->toArray();
    }

    public function checkDuplicate(): bool
    {
        if (!$this->invoiceData['template_id'] || !$this->invoiceData['scheduled_date']) {
            return false;
        }

        $date = Carbon::parse($this->invoiceData['scheduled_date']);

        $exists = RecurringInvoice::where('template_id', $this->invoiceData['template_id'])
            ->whereYear('scheduled_date', $date->year)
            ->whereMonth('scheduled_date', $date->month)
            ->exists();

        if ($exists) {
            $this->toast()
                ->warning(__('pages.ri_duplicate_title'), __('pages.ri_duplicate_desc'))
                ->send();
            return true;
        }

        return false;
    }

    public function save(): void
    {
        $this->validate([
            'invoiceData.template_id' => 'required|exists:recurring_templates,id',
            'invoiceData.scheduled_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required',
            'discount.type' => 'in:fixed,percentage',
            'discount.value' => 'nullable|numeric|min:0',
        ]);

        if ($this->checkDuplicate()) {
            return;
        }

        try {
            DB::beginTransaction();

            $parsedItems = [];
            $subtotal = 0;

            foreach ($this->items as $item) {
                $unitPrice = $this->parseAmount($item['unit_price']);
                $quantity = (int) $item['quantity'];
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

                if (!$isTaxDeposit) {
                    $subtotal += $amount;
                }
            }

            $discountAmount = 0;
            $discountValue = 0;
            if ($this->discount['type'] === 'fixed') {
                $discountValue = $this->discount['value'];
                $discountAmount = (int) $this->discount['value'];
            } else {
                $discountValue = $this->discount['value'];
                $discountAmount = (int) round(($subtotal * $this->discount['value']) / 100);
            }

            $totalAmount = max(0, $subtotal - $discountAmount);

            $template = RecurringTemplate::with('client')->find($this->invoiceData['template_id']);
            $scheduledDate = Carbon::parse($this->invoiceData['scheduled_date']);

            RecurringInvoice::create([
                'template_id' => $template->id,
                'client_id' => $template->client_id,
                'scheduled_date' => $scheduledDate,
                'status' => 'draft',
                'invoice_data' => [
                    'items' => $parsedItems,
                    'subtotal' => $subtotal,
                    'discount_type' => $this->discount['type'] ?? 'fixed',
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'discount_reason' => $this->discount['reason'] ?? '',
                    'total_amount' => $totalAmount,
                ],
            ]);

            DB::commit();

            $this->toast()
                ->success(__('common.success'), __('pages.ri_create_invoice_success'))
                ->send();

            $this->redirect(route('recurring-invoices.index'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create recurring invoice: ' . $e->getMessage());

            $this->toast()
                ->error(__('common.error'), __('pages.ri_create_invoice_failed'))
                ->send();
        }
    }

    private function parseAmount($value): int
    {
        if (empty($value)) return 0;
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly.create-invoice');
    }
}
