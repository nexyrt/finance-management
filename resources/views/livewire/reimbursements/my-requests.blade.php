<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Submitted --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_my_total') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['total'], 0, ',', '.') }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Rp {{ number_format($this->stats['total_amount'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </x-card>

        {{-- Draft --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gray-50 dark:bg-gray-800/50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="pencil-square" class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_draft') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['draft_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-dark-400 dark:text-dark-500">{{ __('pages.reimb_stat_draft_hint') }}</p>
                </div>
            </div>
        </x-card>

        {{-- Pending / Approved --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_in_progress') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ number_format($this->stats['pending_count'] + $this->stats['approved_count'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">
                        {{ $this->stats['pending_count'] }} {{ __('pages.reimb_stat_pending_short') }} · {{ $this->stats['approved_count'] }} {{ __('pages.reimb_stat_approved_short') }}
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Total Paid --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="banknotes" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_stat_received') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ number_format($this->stats['paid_count'], 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Rp {{ number_format($this->stats['total_paid'] / 1000000, 1, ',', '.') }}jt</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filter Section --}}
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <x-select.styled wire:model.live="statusFilter"
                :label="__('common.status')"
                :options="$this->statusOptions"
                :placeholder="__('pages.reimb_all_status_placeholder')" />

            <x-select.styled wire:model.live="categoryFilter"
                :label="__('common.category')"
                :options="$this->categoryOptions"
                :placeholder="__('pages.reimb_all_categories_placeholder')" />

            <x-date wire:model.live="dateRange"
                :label="__('pages.reimb_date_range_label')"
                range
                :placeholder="__('pages.reimb_date_range_placeholder')" />
        </div>

        {{-- Search + Status Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="w-full sm:w-64">
                <x-input wire:model.live.debounce.300ms="search"
                    :placeholder="__('common.search') . '...'"
                    icon="magnifying-glass"
                    class="h-8" />
            </div>
            <div class="flex items-center gap-3 flex-1">
                @if ($statusFilter || $categoryFilter || !empty($dateRange) || $search)
                    <x-badge :text="collect([$statusFilter, $categoryFilter, !empty($dateRange) ? 'date' : null, $search ? 'search' : null])->filter()->count() . ' ' . __('pages.reimb_active_filters')" color="primary" size="sm" />
                    <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                        {{ __('pages.reimb_clear_filters_btn') }}
                    </x-button>
                @endif
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->rows->count() }} <span class="hidden sm:inline">{{ __('common.of') }} {{ $this->rows->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :headers="$this->headers" :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

        {{-- Request Info --}}
        @interact('column_request', $row)
            <div class="flex items-center gap-3">
                {{-- Thumbnail --}}
                @if ($row->hasAttachment() && $row->isImageAttachment())
                    <button wire:click="previewAttachment({{ $row->id }})"
                        class="group relative w-10 h-10 rounded-xl overflow-hidden border-2 border-primary-200 dark:border-primary-700 hover:border-primary-500 transition-all hover:shadow flex-shrink-0">
                        <img src="{{ $row->attachment_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                    </button>
                @elseif($row->hasAttachment())
                    <a href="{{ $row->attachment_url }}" target="_blank"
                        class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center shadow flex-shrink-0">
                        <x-icon name="document-text" class="w-5 h-5 text-white" />
                    </a>
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-zinc-300 to-zinc-500 dark:from-zinc-600 dark:to-zinc-800 rounded-xl flex items-center justify-center flex-shrink-0">
                        <x-icon name="document" class="w-5 h-5 text-white" />
                    </div>
                @endif

                {{-- Info --}}
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-dark-900 dark:text-dark-50 text-sm">{{ $row->title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-dark-400 dark:text-dark-500">{{ $row->expense_date->format('d/m/Y') }}</span>
                        <span class="text-xs text-dark-300 dark:text-dark-600">•</span>
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
                <div class="font-bold text-base text-dark-900 dark:text-dark-50">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>

                @if (in_array($row->status, ['approved', 'paid']))
                    @php
                        $paidAmount = $row->payments_sum_amount ?? 0;
                        $paymentPercentage = $row->amount > 0 ? ($paidAmount / $row->amount) * 100 : 0;
                    @endphp
                    @if ($paidAmount > 0)
                        <div class="mt-1.5">
                            <div class="w-full bg-gray-100 dark:bg-dark-700 rounded-full h-1.5 mb-1">
                                <div class="{{ $paymentPercentage >= 100 ? 'bg-emerald-500' : 'bg-amber-500' }} h-1.5 rounded-full transition-all"
                                    style="width: {{ min($paymentPercentage, 100) }}%"></div>
                            </div>
                            <div class="text-xs {{ $paymentPercentage >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ __('pages.reimb_paid_percentage', ['percent' => number_format($paymentPercentage, 1)]) }}
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">{{ __('pages.reimb_not_paid_yet') }}</div>
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
                    <div class="text-xs text-dark-400 dark:text-dark-500">
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
                    :title="__('pages.reimb_view_tooltip')" />

                @if ($row->canEdit())
                    <x-button.circle icon="pencil" color="green" size="sm"
                        wire:click="$dispatch('edit::reimbursement', { id: {{ $row->id }} })"
                        :title="__('pages.reimb_edit_tooltip')" />
                @endif

                @if ($row->canSubmit())
                    <x-button.circle icon="paper-airplane" color="cyan" size="sm"
                        wire:click="submitRequest({{ $row->id }})"
                        loading="submitRequest({{ $row->id }})"
                        :title="__('pages.reimb_submit_tooltip')" />
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
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-2xl border border-dark-200 dark:border-dark-600 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-primary-600 rounded-xl flex items-center justify-center shadow">
                        <x-icon name="check-circle" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <div class="font-bold text-dark-900 dark:text-dark-50 text-sm" x-text="`${show.length} {{ __('common.selected') }}`"></div>
                        <div class="text-xs text-dark-400 dark:text-dark-500">{{ __('pages.reimb_only_drafts_hint') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="confirmBulkDelete" color="red" icon="trash" size="sm"
                        loading="confirmBulkDelete">{{ __('pages.reimb_delete_btn') }}</x-button>
                    <x-button wire:click="$set('selected', [])" color="gray" outline icon="x-mark" size="sm">{{ __('pages.reimb_cancel_btn') }}</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <x-modal wire size="4xl" center>
        @if ($previewImage)
            <x-slot:title>
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="photo" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ $previewName }}</h3>
                        <p class="text-xs text-dark-400 dark:text-dark-500">{{ __('pages.reimb_image_zoom_hint') }}</p>
                    </div>
                </div>
            </x-slot:title>

            <div class="bg-gray-50 dark:bg-dark-900 rounded-xl p-4" x-data="{
                scale: 1, maxScale: 5, minScale: 0.5,
                originX: 50, originY: 50,
                zoom(event, delta) {
                    const rect = event.target.getBoundingClientRect();
                    this.originX = ((event.clientX - rect.left) / rect.width) * 100;
                    this.originY = ((event.clientY - rect.top) / rect.height) * 100;
                    if (delta > 0) this.scale = Math.min(this.scale * 1.2, this.maxScale);
                    else this.scale = Math.max(this.scale / 1.2, this.minScale);
                },
                reset() { this.scale = 1; this.originX = 50; this.originY = 50; }
            }">
                <div class="flex justify-center overflow-hidden">
                    <img src="{{ $previewImage }}" alt="{{ $previewName }}"
                        class="max-w-full h-auto max-h-[70vh] rounded-lg cursor-pointer transition-transform duration-200"
                        :style="`transform: scale(${scale}); transform-origin: ${originX}% ${originY}%`"
                        @wheel.prevent="if ($event.ctrlKey) zoom($event, $event.deltaY > 0 ? -1 : 1)"
                        @click="reset()">
                </div>
                <div class="mt-2 text-center text-xs text-dark-400" x-show="scale !== 1">
                    Zoom: <span x-text="Math.round(scale * 100)"></span>%
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-between w-full">
                    <a href="{{ $previewImage }}" download="{{ $previewName }}">
                        <x-button color="blue" outline icon="arrow-down-tray">{{ __('pages.reimb_download_btn') }}</x-button>
                    </a>
                    <x-button wire:click="$set('modal', false)" color="zinc">{{ __('pages.reimb_close_btn') }}</x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
