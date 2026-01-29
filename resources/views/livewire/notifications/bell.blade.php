<div x-data="notificationDropdown" @click.away="close" x-init="init">
    {{-- Bell Button --}}
    <button @click="toggle"
        x-ref="button"
        type="button"
        class="relative p-2 text-dark-500 hover:text-dark-700 dark:text-dark-400 dark:hover:text-dark-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-dark-700">
        <x-icon name="bell" class="w-6 h-6" />

        {{-- Badge --}}
        @if ($this->unreadCount > 0)
            <span class="absolute top-1 right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Backdrop Overlay --}}
    <template x-teleport="body">
        <div x-show="isOpen"
            x-cloak
            @click="close"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/10 dark:bg-black/30"
            style="z-index: 999998 !important;">
        </div>

        {{-- Dropdown --}}
        <div x-show="isOpen"
            x-cloak
            x-ref="dropdown"
            @click.stop
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed w-80 sm:w-96 bg-white dark:bg-dark-800 rounded-xl shadow-2xl border border-gray-200 dark:border-dark-700"
            :style="dropdownStyle"
            style="z-index: 999999 !important;">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-dark-700">
                <h3 class="text-sm font-semibold text-dark-900 dark:text-white">Notifikasi</h3>
                @if ($this->unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 hover:underline">
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
                    <a href="{{ route('feedbacks.index') }}" class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 hover:underline">
                        Lihat semua notifikasi
                    </a>
                </div>
            @endif
        </div>
    </template>
</div>

@script
<script>
    Alpine.data('notificationDropdown', () => ({
        isOpen: @entangle('dropdown'),
        dropdownStyle: '',

        init() {
            // Update position on scroll and resize
            window.addEventListener('scroll', () => this.updatePosition(), { passive: true });
            window.addEventListener('resize', () => this.updatePosition());

            // Watch for isOpen changes to update position
            this.$watch('isOpen', (value) => {
                if (value) {
                    this.$nextTick(() => this.updatePosition());
                }
            });
        },

        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
        },

        updatePosition() {
            if (!this.isOpen || !this.$refs.button) return;

            const button = this.$refs.button;
            const rect = button.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            const dropdownWidth = 384; // w-96 = 24rem = 384px
            const dropdownMaxHeight = 500; // approximate max height

            let top = 0;
            let left = 0;

            // Detect if we're on mobile or desktop based on screen width
            const isMobile = viewportWidth < 1024; // lg breakpoint

            if (isMobile) {
                // Mobile: dropdown below button, right-aligned
                top = rect.bottom + 8;
                left = Math.max(16, rect.right - dropdownWidth);

                // Ensure dropdown doesn't go off right edge
                if (left + dropdownWidth > viewportWidth - 16) {
                    left = viewportWidth - dropdownWidth - 16;
                }

                // If dropdown goes off bottom, position above button
                if (top + dropdownMaxHeight > viewportHeight - 16) {
                    top = Math.max(16, rect.top - dropdownMaxHeight - 8);
                }
            } else {
                // Desktop: dropdown to the right of button, vertically centered
                const dropdownLeft = rect.right + 16; // Space from button
                const dropdownTop = rect.top + (rect.height / 2) - (dropdownMaxHeight / 2);

                // Check if dropdown fits to the right
                if (dropdownLeft + dropdownWidth < viewportWidth - 16) {
                    // Position to the right
                    left = dropdownLeft;
                } else {
                    // Position to the left of button
                    left = Math.max(16, rect.left - dropdownWidth - 16);
                }

                // Adjust vertical position to keep dropdown in viewport
                if (dropdownTop < 16) {
                    top = 16; // Top of viewport
                } else if (dropdownTop + dropdownMaxHeight > viewportHeight - 16) {
                    top = viewportHeight - dropdownMaxHeight - 16; // Bottom of viewport
                } else {
                    top = dropdownTop; // Centered
                }
            }

            this.dropdownStyle = `top: ${Math.round(top)}px; left: ${Math.round(left)}px;`;
        }
    }));
</script>
@endscript
