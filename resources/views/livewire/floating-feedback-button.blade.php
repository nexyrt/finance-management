<div x-data="{ collapsed: false }"
     class="fixed bottom-6 z-50 transition-all duration-300 ease-in-out"
     :class="collapsed ? '-right-14' : 'right-6'">

    <div class="relative flex items-center">
        {{-- Close button (visible when expanded) --}}
        <button x-show="!collapsed"
                @click.stop="collapsed = true"
                class="absolute -top-2 -left-2 w-5 h-5 bg-white dark:bg-dark-700 border border-zinc-200 dark:border-dark-600 rounded-full flex items-center justify-center shadow-sm hover:bg-zinc-100 dark:hover:bg-dark-600 transition-colors z-10">
            <x-icon name="x-mark" class="w-3 h-3 text-dark-500 dark:text-dark-400" />
        </button>

        {{-- Feedback button --}}
        <button @click="collapsed ? collapsed = false : $dispatch('open-feedback-form', { pageUrl: window.location.href })"
                class="group flex items-center gap-2 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <x-icon name="chat-bubble-left-right" class="w-5 h-5" />
            <span x-show="!collapsed"
                  class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap text-sm font-medium">
                {{ __('feedback.send_feedback') }}
            </span>
        </button>
    </div>
</div>
