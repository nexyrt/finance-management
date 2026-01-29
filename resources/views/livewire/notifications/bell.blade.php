<div class="relative" x-data="{ open: @entangle('dropdown') }" @click.away="open = false">
    {{-- Bell Button --}}
    <button @click="open = !open" type="button"
        class="relative p-2 text-dark-500 hover:text-dark-700 dark:text-dark-400 dark:hover:text-dark-200 transition-colors">
        <x-icon name="bell" class="w-6 h-6" />

        {{-- Badge --}}
        @if ($this->unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-dark-800 rounded-xl shadow-xl border border-gray-200 dark:border-dark-700 z-50"
        style="display: none;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-dark-700">
            <h3 class="text-sm font-semibold text-dark-900 dark:text-white">Notifikasi</h3>
            @if ($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="max-h-96 overflow-y-auto">
            @forelse ($this->notifications as $notification)
                <div wire:click="openNotification({{ $notification->id }})"
                    class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors
                        {{ $notification->read_at ? 'bg-white dark:bg-dark-800' : 'bg-primary-50 dark:bg-primary-900/10' }}
                        hover:bg-gray-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 last:border-0">

                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $notification->icon_bg_color }}">
                        <x-icon name="{{ $notification->icon }}" class="w-4 h-4 {{ $notification->icon_color }}" />
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-dark-900 dark:text-white truncate">
                            {{ $notification->title }}
                        </p>
                        <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5 line-clamp-2">
                            {{ $notification->message }}
                        </p>
                        <p class="text-[10px] text-dark-400 dark:text-dark-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Unread indicator --}}
                    @if (!$notification->read_at)
                        <div class="flex-shrink-0">
                            <span class="w-2 h-2 bg-primary-500 rounded-full block"></span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <x-icon name="bell-slash" class="w-12 h-12 mx-auto text-dark-300 dark:text-dark-600" />
                    <p class="mt-2 text-sm text-dark-500 dark:text-dark-400">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if ($this->notifications->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 dark:border-dark-700 text-center">
                <a href="{{ route('feedbacks.index') }}" class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400">
                    Lihat semua notifikasi
                </a>
            </div>
        @endif
    </div>
</div>
