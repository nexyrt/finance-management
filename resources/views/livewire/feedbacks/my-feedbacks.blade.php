<div class="space-y-6">

    {{-- First-time Empty State --}}
    @if ($this->totalCount === 0)
        <div class="text-center py-16">
            <div class="h-16 w-16 bg-gray-100 dark:bg-dark-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <x-icon name="chat-bubble-left-right" class="w-8 h-8 text-dark-400 dark:text-dark-500" />
            </div>
            <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('feedback.no_feedback_yet') }}</h3>
            <p class="text-dark-500 dark:text-dark-400 mb-6">{{ __('feedback.no_feedback_user') }}</p>
            @can('create feedbacks')
                <x-button wire:click="$dispatch('open-feedback-form')" color="primary" icon="plus" size="lg">
                    {{ __('feedback.send_first_feedback') }}
                </x-button>
            @endcan
        </div>

    @else

        {{-- Filter Section --}}
        <div class="space-y-4">
            <div class="flex flex-col gap-4">

                {{-- Filters Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <x-select.styled wire:model.live="typeFilter" :options="[
                        ['label' => __('feedback.type_bug'), 'value' => 'bug'],
                        ['label' => __('feedback.type_feature'), 'value' => 'feature'],
                        ['label' => __('feedback.type_feedback'), 'value' => 'feedback'],
                    ]" :placeholder="__('feedback.filter_all_types')" />

                    <x-select.styled wire:model.live="statusFilter" :options="[
                        ['label' => __('feedback.status_open'), 'value' => 'open'],
                        ['label' => __('feedback.status_in_progress'), 'value' => 'in_progress'],
                        ['label' => __('feedback.status_resolved'), 'value' => 'resolved'],
                        ['label' => __('feedback.status_closed'), 'value' => 'closed'],
                    ]" :placeholder="__('common.status')" />
                </div>

                {{-- Search Row --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                                 :placeholder="__('feedback.search_own')"
                                 icon="magnifying-glass"
                                 class="h-8" />
                    </div>
                </div>

            </div>
        </div>

        {{-- Table --}}
        <x-table :$headers :rows="$this->rows" :$sort :quantity="[10, 25, 50]" paginate filter loading>

            @interact('column_type', $row)
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ $row->type === 'bug' ? 'bg-red-50 dark:bg-red-900/20' : '' }}
                        {{ $row->type === 'feature' ? 'bg-purple-50 dark:bg-purple-900/20' : '' }}
                        {{ $row->type === 'feedback' ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                        <x-icon name="{{ $row->type_icon }}" class="w-4 h-4
                            {{ $row->type === 'bug' ? 'text-red-600 dark:text-red-400' : '' }}
                            {{ $row->type === 'feature' ? 'text-purple-600 dark:text-purple-400' : '' }}
                            {{ $row->type === 'feedback' ? 'text-green-600 dark:text-green-400' : '' }}" />
                    </div>
                    <x-badge :text="$row->type_label" :color="$row->type_badge_color" />
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

            @interact('column_priority', $row)
                <x-badge :text="$row->priority_label" :color="$row->priority_badge_color" />
            @endinteract

            @interact('column_status', $row)
                <x-badge :text="$row->status_label" :color="$row->status_badge_color" />
            @endinteract

            @interact('column_created_at', $row)
                <div>
                    <p class="text-sm text-dark-900 dark:text-dark-50">{{ $row->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $row->created_at->diffForHumans() }}</p>
                </div>
            @endinteract

            @interact('column_actions', $row)
                <div class="flex items-center gap-1">
                    <x-button wire:click="showFeedback({{ $row->id }})" color="gray" size="xs" icon="eye" flat :title="__('feedback.view_detail')" />

                    @if ($row->canEdit())
                        <x-button wire:click="editFeedback({{ $row->id }})" color="blue" size="xs" icon="pencil" flat :title="__('common.edit')" />
                    @endif

                    @if ($row->canDelete())
                        <x-button wire:click="deleteFeedback({{ $row->id }})" color="red" size="xs" icon="trash" flat :title="__('common.delete')" />
                    @endif
                </div>
            @endinteract

        </x-table>

    @endif

</div>
