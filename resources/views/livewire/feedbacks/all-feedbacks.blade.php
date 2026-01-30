<div class="space-y-4">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4">
        <div class="flex-1">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari judul, deskripsi, atau nama pengirim..." icon="magnifying-glass" />
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 lg:w-auto">
            <x-select.styled wire:model.live="typeFilter" :options="[
                ['label' => 'Bug Report', 'value' => 'bug'],
                ['label' => 'Feature Request', 'value' => 'feature'],
                ['label' => 'Kritik/Saran', 'value' => 'feedback'],
            ]" placeholder="Jenis" />
            <x-select.styled wire:model.live="statusFilter" :options="[
                ['label' => 'Open', 'value' => 'open'],
                ['label' => 'In Progress', 'value' => 'in_progress'],
                ['label' => 'Resolved', 'value' => 'resolved'],
                ['label' => 'Closed', 'value' => 'closed'],
            ]" :placeholder="__('common.status')" />
            <x-select.styled wire:model.live="priorityFilter" :options="[
                ['label' => 'Low', 'value' => 'low'],
                ['label' => 'Medium', 'value' => 'medium'],
                ['label' => 'High', 'value' => 'high'],
                ['label' => 'Critical', 'value' => 'critical'],
            ]" placeholder="Prioritas" />
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

        @interact('column_user', $row)
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                    <span class="text-white font-semibold text-xs">{{ strtoupper(substr($row->user->name, 0, 2)) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-dark-900 dark:text-white">{{ $row->user->name }}</p>
                    <p class="text-xs text-dark-500">{{ $row->user->email }}</p>
                </div>
            </div>
        @endinteract

        @interact('column_priority', $row)
            <x-badge :text="$row->priority_label" :color="$row->priority_badge_color" />
        @endinteract

        @interact('column_status', $row)
            <x-dropdown>
                <x-slot:trigger>
                    <x-badge :text="$row->status_label" :color="$row->status_badge_color" class="cursor-pointer" />
                </x-slot:trigger>
                <x-dropdown.items text="Open" icon="clock"
                    wire:click="changeStatus({{ $row->id }}, 'open')"
                    :separator="$row->status === 'open'" />
                <x-dropdown.items text="In Progress" icon="arrow-path"
                    wire:click="changeStatus({{ $row->id }}, 'in_progress')"
                    :separator="$row->status === 'in_progress'" />
                <x-dropdown.items text="Resolved" icon="check-circle"
                    wire:click="changeStatus({{ $row->id }}, 'resolved')"
                    :separator="$row->status === 'resolved'" />
                <x-dropdown.items text="Closed" icon="x-circle"
                    wire:click="changeStatus({{ $row->id }}, 'closed')"
                    :separator="$row->status === 'closed'" />
            </x-dropdown>
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

                @if ($row->canRespond())
                    <x-button wire:click="respondFeedback({{ $row->id }})" color="green" size="xs" icon="chat-bubble-left-ellipsis" flat />
                @endif

                <x-button wire:click="deleteFeedback({{ $row->id }})" color="red" size="xs" icon="trash" flat />
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
            <p class="text-dark-500">Belum ada feedback yang dikirim oleh pengguna</p>
        </div>
    @endif
</div>
