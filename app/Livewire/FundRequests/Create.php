<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use App\Models\FundRequestItem;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;

    // Auto-generated request number (editable by user)
    public string $requestNumber = '';

    // Header fields
    public string $title = '';
    public string $purpose = '';
    public string $priority = 'medium';
    public string $needed_by_date = '';
    public $attachment = null;

    // Items array
    public array $items = [];

    protected function rules(): array
    {
        return [
            'requestNumber' => 'required|string|max:50|unique:fund_requests,request_number',
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'needed_by_date' => 'required|date|after_or_equal:today',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.category_id' => 'required|exists:transaction_categories,id',
            'items.*.amount' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|integer|min:1',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => __('pages.title_required'),
            'purpose.required' => __('pages.purpose_required'),
            'priority.required' => __('pages.priority_required'),
            'needed_by_date.required' => __('pages.needed_by_required'),
            'needed_by_date.after_or_equal' => __('pages.needed_by_future'),
            'attachment.mimes' => __('pages.attachment_mimes'),
            'attachment.max' => __('pages.attachment_max_5mb'),
            'items.required' => __('pages.items_required'),
            'items.min' => __('pages.items_required'),
            'items.*.description.required' => __('pages.item_name_required'),
            'items.*.category_id.required' => __('pages.category_required'),
            'items.*.amount.required' => __('pages.item_unit_price_required'),
            'items.*.amount.min' => __('pages.item_unit_price_min'),
            'items.*.quantity.required' => __('pages.item_quantity_required'),
            'items.*.quantity.min' => __('pages.item_quantity_min'),
            'items.*.unit_price.required' => __('pages.item_unit_price_required'),
            'items.*.unit_price.min' => __('pages.item_unit_price_min'),
        ];
    }

    #[On('create::fund-request')]
    public function openModal(): void
    {
        $this->reset();
        $this->requestNumber = FundRequest::generateRequestNumber();
        $this->addItem();
        $this->modal = true;
    }

    public function addItem(): void
    {
        $this->items[] = [
            'description' => '',
            'category_id' => '',
            'amount' => 0,
            'notes' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key): void
    {
        // Auto-calculate amount when quantity or unit_price changes
        if (str_contains($key, 'quantity') || str_contains($key, 'unit_price')) {
            $index = (int) explode('.', $key)[0];
            if (isset($this->items[$index])) {
                $qty = (int) ($this->items[$index]['quantity'] ?? 1);
                $price = (int) ($this->items[$index]['unit_price'] ?? 0);
                $this->items[$index]['amount'] = $qty * $price;
            }
        }
    }

    #[Computed]
    public function categories(): array
    {
        return TransactionCategory::with('parent')
            ->where('type', 'expense')
            ->orderBy('label')
            ->get()
            ->map(fn ($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function totalAmount(): int
    {
        return array_reduce($this->items, function ($carry, $item) {
            return $carry + (int) ($item['amount'] ?? 0);
        }, 0);
    }

    public function saveAsDraft(): void
    {
        $this->validate();

        DB::transaction(function () {
            // Handle attachment upload
            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('fund-requests', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            // Create fund request
            $fundRequest = FundRequest::create([
                'request_number' => $this->requestNumber,
                'user_id' => auth()->id(),
                'title' => $this->title,
                'purpose' => $this->purpose,
                'total_amount' => 0, // Will be calculated
                'priority' => $this->priority,
                'needed_by_date' => $this->needed_by_date,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 'draft',
            ]);

            // Create items
            foreach ($this->items as $item) {
                FundRequestItem::create([
                    'fund_request_id' => $fundRequest->id,
                    'description' => $item['description'],
                    'category_id' => $item['category_id'],
                    'amount' => $item['amount'],
                    'notes' => $item['notes'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Total will be auto-calculated by model events
        });

        $this->modal = false;
        $this->reset();

        $this->toast()->success(__('common.success'), __('pages.fund_request_created'))->send();
        $this->dispatch('fund-request-created');
    }

    public function submitForApproval(): void
    {
        $this->validate();

        DB::transaction(function () {
            // Handle attachment upload
            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('fund-requests', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            // Create fund request
            $fundRequest = FundRequest::create([
                'request_number' => $this->requestNumber,
                'user_id' => auth()->id(),
                'title' => $this->title,
                'purpose' => $this->purpose,
                'total_amount' => 0, // Will be calculated
                'priority' => $this->priority,
                'needed_by_date' => $this->needed_by_date,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 'draft',
            ]);

            // Create items
            foreach ($this->items as $item) {
                FundRequestItem::create([
                    'fund_request_id' => $fundRequest->id,
                    'description' => $item['description'],
                    'category_id' => $item['category_id'],
                    'amount' => $item['amount'],
                    'notes' => $item['notes'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Submit for approval
            $fundRequest->submit();
        });

        $this->modal = false;
        $this->reset();

        $this->toast()->success(__('common.success'), __('pages.fund_request_submitted'))->send();
        $this->dispatch('fund-request-created');
    }

    public function render()
    {
        return view('livewire.fund-requests.create');
    }
}
