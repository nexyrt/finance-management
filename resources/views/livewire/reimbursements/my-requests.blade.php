<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
            <x-select.styled wire:model.live="statusFilter" label="Status" :options="$this->statusOptions" placeholder="All status..." />

            <x-select.styled wire:model.live="categoryFilter" label="Category" :options="$this->categoryOptions"
                placeholder="All categories..." />

            <x-date wire:model.live="dateRange" label="Date Range" range placeholder="Select range..." />
        </div>

        @if ($statusFilter || $categoryFilter || !empty($dateRange))
            <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                Clear Filters
            </x-button>
        @endif
    </div>

    {{-- Table --}}
    <x-table :headers="$this->headers" :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading
        striped>

        {{-- Attachment Column --}}
        @interact('column_attachment', $row)
            <div class="flex justify-center">
                @if ($row->hasAttachment())
                    @if ($row->isImageAttachment())
                        <button wire:click="previewAttachment({{ $row->id }})"
                            class="group relative w-10 h-10 rounded-lg overflow-hidden border-2 border-primary-200 dark:border-primary-700 hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-200 hover:shadow-lg">
                            <img src="{{ $row->attachment_url }}" alt="Receipt"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-200">
                            </div>
                        </button>
                    @else
                        <a href="{{ $row->attachment_url }}" target="_blank"
                            class="group relative w-10 h-10 rounded-lg overflow-hidden border-2 border-red-200 dark:border-red-700 hover:border-red-500 dark:hover:border-red-400 transition-all duration-200 hover:shadow-lg">
                            <div
                                class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 flex items-center justify-center">
                                <x-icon name="document-text" class="w-5 h-5 text-red-600 dark:text-red-400" />
                            </div>
                        </a>
                    @endif
                @else
                    <div class="w-10 h-10 rounded-lg bg-dark-100 dark:bg-dark-700 flex items-center justify-center">
                        <x-icon name="minus" class="w-4 h-4 text-dark-400" />
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Title + Description --}}
        @interact('column_title', $row)
            <div class="min-w-[200px]">
                <div class="font-semibold text-dark-900 dark:text-dark-50 mb-0.5">
                    {{ $row->title }}
                </div>
                @if ($row->description)
                    <div class="text-xs text-dark-500 dark:text-dark-400 line-clamp-2">
                        {{ $row->description }}
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Category --}}
        @interact('column_category', $row)
            <x-badge :text="$row->category_label" :color="match ($row->category_input) {
                'transport' => 'blue',
                'meals' => 'orange',
                'office_supplies' => 'green',
                'communication' => 'purple',
                'accommodation' => 'pink',
                'medical' => 'red',
                default => 'gray',
            }" light />
        @endinteract

        {{-- Date --}}
        @interact('column_expense_date', $row)
            <div class="min-w-[100px]">
                <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                    {{ $row->expense_date->format('d M Y') }}
                </div>
                <div class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->expense_date->diffForHumans() }}
                </div>
            </div>
        @endinteract

        {{-- Amount + Payment Progress --}}
        @interact('column_amount', $row)
            <div class="text-right min-w-[180px]">
                <div class="text-lg font-bold text-dark-900 dark:text-dark-50 mb-2">
                    {{ $row->formatted_amount }}
                </div>

                @if (in_array($row->status, ['approved', 'paid']))
                    @php
                        $paidAmount = $row->payments_sum_amount ?? 0;
                        $percentage = $row->amount > 0 ? ($paidAmount / $row->amount) * 100 : 0;
                        $remaining = $row->amount - $paidAmount;
                        $isPaid = $remaining <= 0;
                    @endphp

                    @if ($paidAmount > 0)
                        <div class="space-y-2">
                            <x-progress :percent="$percentage" :color="$isPaid ? 'green' : 'blue'" without-text sm />

                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($paidAmount, 0, ',', '.') }}
                                </span>
                                @if (!$isPaid)
                                    <span class="font-medium text-amber-600 dark:text-amber-400">
                                        Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </span>
                                @else
                                    <x-badge text="Lunas" color="green" sm />
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            Menunggu pembayaran
                        </div>
                    @endif
                @endif
            </div>
        @endinteract

        {{-- Status + Reviewer --}}
        @interact('column_status', $row)
            <div class="min-w-[140px] space-y-2">
                <x-badge :text="$row->status_label" :color="$row->status_badge_color" />

                @if ($row->reviewed_at && in_array($row->status, ['approved', 'rejected']))
                    <div class="flex items-center gap-2">
                        <div
                            class="w-6 h-6 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-[10px]">
                                {{ strtoupper(substr($row->reviewer->name ?? 'SYS', 0, 2)) }}
                            </span>
                        </div>
                        <span class="text-xs text-dark-500 dark:text-dark-400">
                            {{ $row->reviewer->name ?? 'System' }}
                        </span>
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="eye" color="blue" size="sm"
                    wire:click="$dispatch('load::reimbursement', { id: {{ $row->id }} })" title="View" />

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
                    <livewire:reimbursements.delete :reimbursement="$row" :key="'delete-' . $row->id" @deleted="$refresh" />
                @endif
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
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
                    <x-button wire:click="confirmBulkDelete" color="red" icon="trash" loading="confirmBulkDelete">
                        Delete
                    </x-button>
                    <x-button wire:click="$set('selected', [])" color="gray" outline icon="x-mark">
                        Cancel
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Preview Modal --}}
    <x-modal wire size="4xl" center>
        @if ($previewImage)
            <x-slot:title>
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <x-icon name="photo" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ $previewName }}</h3>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Ctrl + Scroll untuk zoom • Klik untuk reset
                        </p>
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
                reset() {
                    this.scale = 1;
                    this.originX = 50;
                    this.originY = 50;
                }
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
                        <x-button color="blue" outline icon="arrow-down-tray">
                            Download
                        </x-button>
                    </a>
                    <x-button wire:click="$set('modal', false)" color="secondary">
                        Close
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
