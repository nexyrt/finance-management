 <x-slide wire="slide" size="md" blur>
     <x-slot:title>
         <div class="flex items-center justify-between gap-4">
             <div class="flex items-center gap-3">
                 <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                     <x-icon name="bell" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                 </div>
                 <div>
                     <h3 class="text-lg font-bold text-dark-900 dark:text-dark-50">{{ __('common.notifications') }}</h3>
                     <p class="text-sm text-dark-500 dark:text-dark-400">{{ $this->total }} total</p>
                 </div>
             </div>
             @if ($this->unreadCount > 0)
                 <button wire:click="markAllAsRead"
                     class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 hover:underline">
                     {{ __('common.mark_all_as_read') }}
                 </button>
             @endif
         </div>
     </x-slot:title>

     {{-- Notification list --}}
     <div class="divide-y divide-gray-100 dark:divide-dark-700">
         @forelse ($this->notifications as $notification)
             <div wire:click="openNotification({{ $notification->id }})"
                 class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors
                        {{ $notification->read_at ? '' : 'bg-primary-50 dark:bg-primary-900/10' }}
                        hover:bg-gray-50 dark:hover:bg-dark-700">

                 {{-- Icon --}}
                 <div
                     class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $notification->icon_bg_color }}">
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
                         <span class="w-2 h-2 bg-primary-500 rounded-full block mt-1"></span>
                     </div>
                 @endif
             </div>
         @empty
             <div class="px-4 py-8 text-center">
                 <x-icon name="bell-slash" class="w-12 h-12 mx-auto text-dark-300 dark:text-dark-600" />
                 <p class="mt-2 text-sm text-dark-500 dark:text-dark-400">{{ __('common.no_notifications') }}</p>
             </div>
         @endforelse
     </div>

     {{-- Load More --}}
     @if ($this->notifications->count() < $this->total)
         <div class="px-4 py-4 text-center border-t border-gray-100 dark:border-dark-700">
             <x-button wire:click="loadMore" color="zinc" size="sm" loading="loadMore">
                 Muat Lebih Banyak
             </x-button>
         </div>
     @endif
 </x-slide>
