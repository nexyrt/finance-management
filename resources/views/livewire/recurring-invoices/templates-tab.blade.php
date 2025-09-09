<div class="space-y-6">
    <!-- Search & Create Action -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <x-input wire:model.live.debounce.300ms="search" placeholder="Search templates or clients..."
            icon="magnifying-glass" class="h-full py-3" />
        <livewire:recurring-invoices.create-template />
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($this->templates as $template)
            <div
                class="bg-white dark:bg-dark-800 rounded-xl border border-zinc-200 dark:border-dark-600 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                <!-- Status Indicator -->
                <div class="absolute top-4 right-4">
                    <div
                        class="w-3 h-3 rounded-full {{ $template->status === 'active' ? 'bg-green-400' : 'bg-gray-400' }}">
                    </div>
                </div>

                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">
                                {{ strtoupper(substr($template->client->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-dark-900 dark:text-dark-50 truncate">
                                {{ $template->client->name }}
                            </h3>
                            <p class="text-dark-500 dark:text-dark-400 text-sm truncate">
                                {{ $template->template_name }}
                            </p>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div
                        class="mb-4 p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-xl">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                {{ $template->formatted_total_amount }}
                            </div>
                            <div class="text-sm text-primary-500 dark:text-primary-300">
                                {{ ucfirst(str_replace('_', ' ', $template->frequency)) }}
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    @php
                        $total = $template->recurringInvoices->count();
                        $published = $template->recurringInvoices->where('status', 'published')->count();
                        $progress = $total > 0 ? ($published / $total) * 100 : 0;
                    @endphp
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-dark-700 dark:text-dark-300">Progress</span>
                            <span
                                class="text-dark-500 dark:text-dark-400">{{ $published }}/{{ $total }}</span>
                        </div>
                        <div class="w-full h-3 bg-zinc-200 dark:bg-dark-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-primary-500 to-blue-500 transition-all duration-700"
                                style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                            <div class="font-bold text-green-600 dark:text-green-400">{{ $published }}</div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Published</div>
                        </div>
                        <div class="text-center p-2 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                            <div class="font-bold text-amber-600 dark:text-amber-400">{{ $total - $published }}</div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Draft</div>
                        </div>
                        <div class="text-center p-2 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                            <div class="font-bold text-blue-600 dark:text-blue-400">{{ $template->remaining_invoices }}
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Remaining</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <x-button wire:click="viewTemplate({{ $template->id }})"
                            loading="viewTemplate({{ $template->id }})" color="blue" size="sm" outline
                            class="flex-1">
                            <x-icon name="eye" class="w-4 h-4" />
                        </x-button>
                        <x-button wire:click="editTemplate({{ $template->id }})"
                            loading="editTemplate({{ $template->id }})" color="green" size="sm" outline
                            class="flex-1">
                            <x-icon name="pencil" class="w-4 h-4" />
                        </x-button>
                        <livewire:recurring-invoices.delete-template :template="$template" :key="uniqid()"
                            @template-deleted="$refresh" />
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div
                    class="bg-white dark:bg-dark-800 rounded-xl border-2 border-dashed border-zinc-300 dark:border-dark-600 p-12 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <x-icon name="document-plus" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50 mb-2">
                        @if ($search)
                            No templates found for "{{ $search }}"
                        @else
                            No Templates Yet
                        @endif
                    </h3>
                    <p class="text-dark-500 dark:text-dark-400 mb-6">
                        @if ($search)
                            Try adjusting your search criteria
                        @else
                            Create your first recurring invoice template
                        @endif
                    </p>
                    @if (!$search)
                        <livewire:recurring-invoices.create-template />
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>
