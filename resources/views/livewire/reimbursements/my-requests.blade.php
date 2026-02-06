<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
            <x-select.styled wire:model.live="statusFilter" :label="__('common.status')" :options="$this->statusOptions" placeholder="All status..." />
            <x-select.styled wire:model.live="categoryFilter" :label="__('common.category')" :options="$this->categoryOptions"
                placeholder="All categories..." />
            <x-date wire:model.live="dateRange" label="Date Range" range placeholder="Select range..." />
        </div>
        @if ($statusFilter || $categoryFilter || !empty($dateRange))
            <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                Clear
            </x-button>
        @endif
    </div>

    {{-- Table --}}
    <x-table :headers="$this->headers" :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

        {{-- Request Info --}}
        @interact('column_request', $row)
            <div class="flex items-center gap-3">
                {{-- Thumbnail --}}
                @if ($row->hasAttachment() && $row->isImageAttachment())
                    <button wire:click="previewAttachment({{ $row->id }})"
                        class="group relative w-10 h-10 rounded-2xl overflow-hidden border-2 border-primary-200 dark:border-primary-700 hover:border-primary-500 transition-all hover:shadow-lg flex-shrink-0">
                        <img src="{{ $row->attachment_url }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                    </button>
                @elseif($row->hasAttachment())
                    <a href="{{ $row->attachment_url }}" target="_blank"
                        class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <x-icon name="document-text" class="w-5 h-5 text-white" />
                    </a>
                @else
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-zinc-400 to-zinc-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <x-icon name="document" class="w-5 h-5 text-white" />
                    </div>
                @endif

                {{-- Info --}}
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-dark-900 dark:text-dark-50">{{ $row->title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span
                            class="text-xs text-dark-500 dark:text-dark-400">{{ $row->expense_date->format('d/m/Y') }}</span>
                        <span class="text-xs text-dark-400">•</span>
                        <x-badge :text="$row->category_label" :color="match ($row->category_input) {
                            'transport' => 'blue',
                            'meals' => 'orange',
                            'office_supplies' => 'green',
                            'communication' => 'purple',
                            'accommodation' => 'pink',
                            'medical' => 'red',
                            default => 'gray',
                        }" xs />
                    </div>
                </div>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-bold text-lg text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>

                @if (in_array($row->status, ['approved', 'paid']))
                    @php
                        $paidAmount = $row->payments_sum_amount ?? 0;
                        $paymentPercentage = $row->amount > 0 ? ($paidAmount / $row->amount) * 100 : 0;
                    @endphp

                    @if ($paidAmount > 0)
                        <div class="mt-1">
                            <div
                                class="text-xs {{ $paymentPercentage >= 100 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                {{ number_format($paymentPercentage, 1) }}% Dibayar
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-dark-700 rounded-full h-1 mt-1">
                                <div class="{{ $paymentPercentage >= 100 ? 'bg-green-500' : 'bg-yellow-500' }} h-1 rounded-full"
                                    style="width: {{ min($paymentPercentage, 100) }}%">
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-dark-500 dark:text-dark-400">Belum dibayar</div>
                    @endif
                @endif
            </div>
        @endinteract

        {{-- Status --}}
        @interact('column_status', $row)
            <div class="space-y-1">
                <x-badge :text="match ($row->status) {
                    'draft' => 'Draft',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'paid' => 'Paid',
                    default => ucfirst($row->status),
                }" :color="match ($row->status) {
                    'draft' => 'gray',
                    'pending' => 'yellow',
                    'approved' => 'blue',
                    'rejected' => 'red',
                    'paid' => 'green',
                    default => 'gray',
                }" />

                @if ($row->reviewed_at && in_array($row->status, ['approved', 'rejected']))
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->reviewer->name ?? 'System' }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="$dispatch('load::reimbursement', { id: {{ $row->id }} })"
                    loading="$dispatch('load::reimbursement', { id: {{ $row->id }} })" title="View" />

                @if ($row->canEdit())
                    <x-button.circle icon="pencil" color="green" size="sm"
                        wire:click="$dispatch('edit::reimbursement', { id: {{ $row->id }} })" title="Edit" />
                @endif

                @if ($row->canSubmit())
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click="submitRequest({{ $row->id }})" loading="submitRequest({{ $row->id }})"
                        title="Submit" />
                @endif

                @if ($row->canDelete())
                    <livewire:reimbursements.delete :reimbursement="$row" :key="uniqid()" @deleted="$refresh" />
                @endif
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-2xl border border-dark-200 dark:border-dark-600 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="h-12 w-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <x-icon name="check-circle" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <div class="font-bold text-dark-900 dark:text-dark-50" x-text="`${show.length} selected`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">Only drafts can be deleted</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="confirmBulkDelete" color="red" icon="trash"
                        loading="confirmBulkDelete">Delete</x-button>
                    <x-button wire:click="$set('selected', [])" color="gray" outline icon="x-mark">Cancel</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <x-modal wire size="4xl" center>
        @if ($previewImage)
            <x-slot:title>
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="photo" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ $previewName }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Ctrl + Scroll zoom • Click reset</p>
                    </div>
                </div>
            </x-slot:title>

            <div class="bg-dark-50 dark:bg-dark-900 rounded-xl p-4" x-data="{
                scale: 1,
                maxScale: 5,
                minScale: 0.5,
                originX: 50,
                originY: 50,
                zoom(event, delta) {
                    const rect = event.target.getBoundingClientRect();
                    this.originX = ((event.clientX - rect.left) / rect.width) * 100;
                    this.originY = ((event.clientY - rect.top) / rect.height) * 100;
                    if (delta > 0) this.scale = Math.min(this.scale * 1.2, this.maxScale);
                    else this.scale = Math.max(this.scale / 1.2, this.minScale);
                },
                reset() { this.scale = 1;
                    this.originX = 50;
                    this.originY = 50; }
            }">
                <div class="flex justify-center overflow-hidden">
                    <img src="{{ $previewImage }}" alt="{{ $previewName }}"
                        class="max-w-full h-auto max-h-[70vh] rounded-lg cursor-pointer transition-transform duration-200"
                        :style="`transform: scale(${scale}); transform-origin: ${originX}% ${originY}%`"
                        @wheel.prevent="if ($event.ctrlKey) zoom($event, $event.deltaY > 0 ? -1 : 1)"
                        @click="reset()">
                </div>
                <div class="mt-2 text-center text-xs text-dark-500" x-show="scale !== 1">
                    Zoom: <span x-text="Math.round(scale * 100)"></span>%
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-between w-full">
                    <a href="{{ $previewImage }}" download="{{ $previewName }}">
                        <x-button color="blue" outline icon="arrow-down-tray">Download</x-button>
                    </a>
                    <x-button wire:click="$set('modal', false)" color="secondary">Close</x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
