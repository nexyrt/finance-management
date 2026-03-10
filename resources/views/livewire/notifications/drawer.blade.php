<div
    x-data="{
        show: @entangle('slide').live,
        leaving: false,
        close() {
            this.leaving = true;
            setTimeout(() => {
                this.leaving = false;
                this.$wire.set('slide', false);
            }, 220);
        }
    }"
    @keydown.escape.window="close()">

    {{-- Backdrop --}}
    <div
        x-show="show || leaving"
        x-cloak
        :class="leaving ? 'drawer-backdrop-leave' : 'drawer-backdrop-enter'"
        @click="close()"
        style="position:fixed;inset:0;z-index:9990;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px)">
    </div>

    {{-- Drawer Panel --}}
    <div
        x-show="show || leaving"
        x-cloak
        :class="leaving ? 'drawer-panel-leave' : 'drawer-panel-enter'"
        style="position:fixed;top:0;right:0;height:100%;width:min(100%,24rem);z-index:9995;flex-direction:column"
        class="flex bg-white dark:bg-[#1c1c1f] border-l border-zinc-200 dark:border-white/10 shadow-2xl">

        @if ($slide)
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-100 dark:border-white/8 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-600 dark:text-primary-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('common.all_notifications') }}</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">{{ $this->total }} {{ __('common.total') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($this->unreadCount > 0)
                        <button wire:click="markAllAsRead"
                            class="text-[11px] font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                            {{ __('common.mark_all_as_read') }}
                        </button>
                    @endif
                    <button @click="close()"
                        class="p-1.5 text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/5 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Notification List --}}
            <div class="flex-1 overflow-y-auto overscroll-contain divide-y divide-zinc-100 dark:divide-white/5">
                @forelse ($this->notifications as $notification)
                    <button type="button"
                        wire:click="openNotification({{ $notification->id }})"
                        class="w-full flex items-start gap-3 px-5 py-4 text-left transition-colors
                            {{ $notification->read_at ? 'bg-white dark:bg-transparent' : 'bg-primary-50/70 dark:bg-primary-500/5' }}
                            hover:bg-zinc-50 dark:hover:bg-white/3">
                        <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center mt-0.5 {{ $notification->icon_bg_color }}">
                            <x-icon name="{{ $notification->icon }}" class="w-4 h-4 {{ $notification->icon_color }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-[13px] font-semibold text-zinc-900 dark:text-white leading-snug">{{ $notification->title }}</p>
                                @if (!$notification->read_at)
                                    <span class="shrink-0 block w-1.5 h-1.5 bg-primary-500 rounded-full mt-1.5"></span>
                                @endif
                            </div>
                            <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $notification->message }}</p>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </button>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                        <div class="w-14 h-14 bg-zinc-100 dark:bg-white/5 rounded-2xl flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-zinc-400 dark:text-zinc-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.418m1.125-8.27c.24-.144.47-.298.686-.463m0 0a9.01 9.01 0 0 0-3.69-3.69M3 3l18 18" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('common.no_notifications') }}</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Semua notifikasi akan muncul di sini</p>
                    </div>
                @endforelse
            </div>

            {{-- Load More --}}
            @if ($this->notifications->count() < $this->total)
                <div class="px-5 py-3 border-t border-zinc-100 dark:border-white/8 shrink-0">
                    <button wire:click="loadMore"
                        class="w-full py-2 text-[12px] font-medium text-primary-600 dark:text-primary-400
                               hover:text-primary-700 dark:hover:text-primary-300
                               bg-primary-50 dark:bg-primary-500/10
                               hover:bg-primary-100 dark:hover:bg-primary-500/15
                               rounded-lg transition-colors"
                        wire:loading.attr="disabled" wire:target="loadMore">
                        <span wire:loading.remove wire:target="loadMore">{{ __('common.load_more') }}</span>
                        <span wire:loading wire:target="loadMore">Loading...</span>
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
