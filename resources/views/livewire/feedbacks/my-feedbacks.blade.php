<div class="space-y-4">
    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari judul atau deskripsi..." icon="magnifying-glass" />
        </div>
        <div class="w-full sm:w-48">
            <x-select.styled wire:model.live="typeFilter" :options="[
                ['label' => 'Bug Report', 'value' => 'bug'],
                ['label' => 'Feature Request', 'value' => 'feature'],
                ['label' => 'Kritik/Saran', 'value' => 'feedback'],
            ]" placeholder="Semua Jenis" />
        </div>
        <div class="w-full sm:w-48">
            <x-select.styled wire:model.live="statusFilter" :options="[
                ['label' => 'Open', 'value' => 'open'],
                ['label' => 'In Progress', 'value' => 'in_progress'],
                ['label' => 'Resolved', 'value' => 'resolved'],
                ['label' => 'Closed', 'value' => 'closed'],
            ]" placeholder="Semua Status" />
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :rows="$this->rows" :$sort :quantity="[10, 25, 50]" paginate>
        @interact('column_type', $row)
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg flex items-center justify-center
                    {{ $row->type === 'bug' ? 'bg-red-100 dark:bg-red-900/20' : '' }}
                    {{ $row->type === 'feature' ? 'bg-blue-100 dark:bg-blue-900/20' : '' }}
                    {{ $row->type === 'feedback' ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                    <x-icon name="{{ $row->type_icon }}" class="w-4 h-4
                        {{ $row->type === 'bug' ? 'text-red-600 dark:text-red-400' : '' }}
                        {{ $row->type === 'feature' ? 'text-blue-600 dark:text-blue-400' : '' }}
                        {{ $row->type === 'feedback' ? 'text-gray-600 dark:text-gray-400' : '' }}" />
                </div>
                <x-badge :text="$row->type_label" :color="$row->type_badge_color" />
            </div>
        @endinteract

        @interact('column_title', $row)
            <div class="max-w-xs">
                <p class="font-medium text-dark-900 dark:text-white truncate">{{ $row->title }}</p>
                <p class="text-xs text-dark-500 truncate">{{ Str::limit($row->description, 50) }}</p>
                @if ($row->hasAttachment())
                    <span class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 mt-1">
                        <x-icon name="paper-clip" class="w-3 h-3" />
                        Lampiran
                    </span>
                @endif
            </div>
        @endinteract

        @interact('column_priority', $row)
            <x-badge :text="$row->priority_label" :color="$row->priority_badge_color" />
        @endinteract

        @interact('column_status', $row)
            <x-badge :text="$row->status_label" :color="$row->status_badge_color" />
        @endinteract

        @interact('column_created_at', $row)
            <div class="text-sm">
                <p class="text-dark-900 dark:text-white">{{ $row->created_at->format('d M Y') }}</p>
                <p class="text-xs text-dark-500">{{ $row->created_at->diffForHumans() }}</p>
            </div>
        @endinteract

        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button wire:click="showFeedback({{ $row->id }})" color="gray" size="xs" icon="eye" flat />

                @if ($row->canEdit())
                    <x-button wire:click="editFeedback({{ $row->id }})" color="blue" size="xs" icon="pencil" flat />
                @endif

                @if ($row->canDelete())
                    <x-button wire:click="deleteFeedback({{ $row->id }})" color="red" size="xs" icon="trash" flat />
                @endif
            </div>
        @endinteract
    </x-table>

    {{-- Empty State --}}
    @if ($this->rows->isEmpty())
        <div class="text-center py-12">
            <div class="h-16 w-16 bg-gray-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-icon name="inbox" class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-dark-900 dark:text-white mb-1">Belum ada feedback</h3>
            <p class="text-dark-500 mb-4">Anda belum pernah mengirim feedback</p>
            @can('create feedbacks')
                <x-button wire:click="$dispatch('open-feedback-form')" color="primary" icon="plus">
                    Kirim Feedback Pertama
                </x-button>
            @endcan
        </div>
    @endif
</div>
