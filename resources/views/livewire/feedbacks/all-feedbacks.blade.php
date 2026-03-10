<div class="space-y-6">

    {{-- Filter Section --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">

            {{-- Main Filters Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <x-select.styled wire:model.live="typeFilter" :options="[
                    ['label' => __('feedback.type_bug'), 'value' => 'bug'],
                    ['label' => __('feedback.type_feature'), 'value' => 'feature'],
                    ['label' => __('feedback.type_feedback'), 'value' => 'feedback'],
                ]" :placeholder="__('feedback.filter_type')" />

                <x-select.styled wire:model.live="statusFilter" :options="[
                    ['label' => __('feedback.status_open'), 'value' => 'open'],
                    ['label' => __('feedback.status_in_progress'), 'value' => 'in_progress'],
                    ['label' => __('feedback.status_resolved'), 'value' => 'resolved'],
                    ['label' => __('feedback.status_closed'), 'value' => 'closed'],
                ]" :placeholder="__('common.status')" />

                <x-select.styled wire:model.live="priorityFilter" :options="[
                    ['label' => __('feedback.priority_low'), 'value' => 'low'],
                    ['label' => __('feedback.priority_medium'), 'value' => 'medium'],
                    ['label' => __('feedback.priority_high'), 'value' => 'high'],
                    ['label' => __('feedback.priority_critical'), 'value' => 'critical'],
                ]" :placeholder="__('feedback.filter_priority')" />
            </div>

            {{-- Search Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="w-full sm:w-64">
                    <x-input wire:model.live.debounce.300ms="search"
                             :placeholder="__('feedback.search_all')"
                             icon="magnifying-glass"
                             class="h-8" />
                </div>
            </div>

        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :rows="$this->rows" :$sort :quantity="[10, 25, 50]" paginate>

        @interact('column_type', $row)
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0
                    {{ $row->type === 'bug' ? 'bg-red-50 dark:bg-red-900/20' : '' }}
                    {{ $row->type === 'feature' ? 'bg-purple-50 dark:bg-purple-900/20' : '' }}
                    {{ $row->type === 'feedback' ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                    <x-icon name="{{ $row->type_icon }}" class="w-4 h-4
                        {{ $row->type === 'bug' ? 'text-red-600 dark:text-red-400' : '' }}
                        {{ $row->type === 'feature' ? 'text-purple-600 dark:text-purple-400' : '' }}
                        {{ $row->type === 'feedback' ? 'text-green-600 dark:text-green-400' : '' }}" />
                </div>
                <span class="text-sm font-medium text-dark-700 dark:text-dark-300">{{ $row->type_label }}</span>
            </div>
        @endinteract

        @interact('column_title', $row)
            <div class="max-w-xs">
                <p class="font-medium text-dark-900 dark:text-dark-50 truncate">{{ $row->title }}</p>
                <p class="text-xs text-dark-500 dark:text-dark-400 truncate mt-0.5">{{ Str::limit(strip_tags($row->description), 55) }}</p>
                @if ($row->hasAttachment())
                    <span class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 mt-1">
                        <x-icon name="paper-clip" class="w-3 h-3" />
                        {{ __('feedback.attachment_label') }}
                    </span>
                @endif
            </div>
        @endinteract

        @interact('column_user', $row)
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center shrink-0">
                    <span class="text-white font-semibold text-xs">{{ strtoupper(substr($row->user->name, 0, 2)) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $row->user->name }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ $row->user->email }}</p>
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
                <x-dropdown.items :text="__('feedback.status_open')" icon="clock"
                    wire:click="changeStatus({{ $row->id }}, 'open')"
                    :separator="$row->status === 'open'" />
                <x-dropdown.items :text="__('feedback.status_in_progress')" icon="arrow-path"
                    wire:click="changeStatus({{ $row->id }}, 'in_progress')"
                    :separator="$row->status === 'in_progress'" />
                <x-dropdown.items :text="__('feedback.status_resolved')" icon="check-circle"
                    wire:click="changeStatus({{ $row->id }}, 'resolved')"
                    :separator="$row->status === 'resolved'" />
                <x-dropdown.items :text="__('feedback.status_closed')" icon="x-circle"
                    wire:click="changeStatus({{ $row->id }}, 'closed')"
                    :separator="$row->status === 'closed'" />
            </x-dropdown>
        @endinteract

        @interact('column_created_at', $row)
            <div>
                <p class="text-sm text-dark-900 dark:text-dark-50">{{ $row->created_at->format('d M Y') }}</p>
                <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $row->created_at->diffForHumans() }}</p>
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
            <div class="h-16 w-16 bg-gray-100 dark:bg-dark-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <x-icon name="inbox" class="w-8 h-8 text-dark-400 dark:text-dark-500" />
            </div>
            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('feedback.no_feedback_yet') }}</h3>
            <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('feedback.no_feedback_admin') }}</p>
        </div>
    @endif

</div>
