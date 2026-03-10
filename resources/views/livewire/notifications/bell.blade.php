<div wire:poll.5s="refresh"
    x-data="{
        open: false,
        dropdownTop: 0,
        dropdownRight: 0,
        toggle() {
            if (!this.open) {
                const rect = this.$refs.bellBtn.getBoundingClientRect();
                this.dropdownTop = rect.bottom + 8;
                this.dropdownRight = window.innerWidth - rect.right;
            }
            this.open = !this.open;
        },
    }"
    @keydown.escape.window="open = false"
    @scroll.window="open = false">

    {{-- Bell Button --}}
    <button type="button"
        x-ref="bellBtn"
        @click="toggle"
        class="relative p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        @if ($this->unreadCount > 0)
            <span class="absolute top-1 right-1 flex items-center justify-center min-w-[16px] h-4 px-1 text-[9px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white dark:ring-[#18181b]">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-ref="dropdown"
            @mousedown.window="if (open && !$refs.dropdown.contains($event.target) && !$refs.bellBtn.contains($event.target)) open = false"
            :style="`top: ${dropdownTop}px; right: ${dropdownRight}px`"
            class="fixed w-80 z-9999
                   bg-white dark:bg-[#1c1c1f]
                   border border-zinc-200 dark:border-white/10
                   rounded-xl shadow-xl shadow-black/10 dark:shadow-black/40
                   overflow-hidden"
            style="display: none;">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100 dark:border-white/8">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('common.notifications') }}</span>
                    @if ($this->unreadCount > 0)
                        <span class="inline-flex items-center justify-center h-4 min-w-4 px-1 text-[9px] font-bold text-white bg-primary-500 rounded-full">
                            {{ $this->unreadCount }}
                        </span>
                    @endif
                </div>
                @if ($this->unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        class="text-[11px] text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition-colors">
                        {{ __('common.mark_all_as_read') }}
                    </button>
                @endif
            </div>

            {{-- Notification List --}}
            <div class="max-h-[360px] overflow-y-auto overscroll-contain">
                @forelse ($this->notifications as $notification)
                    <button type="button"
                        wire:click="openNotification({{ $notification->id }})"
                        @click="open = false"
                        class="w-full flex items-start gap-3 px-4 py-3 text-left transition-colors
                            {{ $notification->read_at ? 'bg-white dark:bg-transparent' : 'bg-primary-50/70 dark:bg-primary-500/5' }}
                            hover:bg-zinc-50 dark:hover:bg-white/4
                            border-b border-zinc-100 dark:border-white/5 last:border-0">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5 {{ $notification->icon_bg_color }}">
                            <x-icon name="{{ $notification->icon }}" class="w-4 h-4 {{ $notification->icon_color }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-zinc-900 dark:text-white leading-snug">{{ $notification->title }}</p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $notification->message }}</p>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if (!$notification->read_at)
                            <div class="shrink-0 mt-2">
                                <span class="block w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
                            </div>
                        @endif
                    </button>
                @empty
                    <div class="px-4 py-10 text-center">
                        <div class="w-10 h-10 bg-zinc-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-zinc-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.418m1.125-8.27c.24-.144.47-.298.686-.463m0 0a9.01 9.01 0 0 0-3.69-3.69M3 3l18 18" />
                            </svg>
                        </div>
                        <p class="mt-2 text-[13px] text-zinc-500 dark:text-zinc-400">{{ __('common.no_notifications') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer —  wire:click openDrawer() dispatch event ke Drawer.php --}}
            @if ($this->notifications->count() > 0)
                <div class="px-4 py-2.5 border-t border-zinc-100 dark:border-white/8 text-center">
                    <button wire:click="openDrawer" @click="open = false"
                        class="text-[11px] font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                        {{ __('common.view_all_notifications') }}
                    </button>
                </div>
            @endif
        </div>
    </template>
</div>
