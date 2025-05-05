@if (session()->has('message'))
    <div 
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-4 right-4 z-50"
    >
        <div class="bg-green-900/90 border border-green-800 text-green-100 px-6 py-4 rounded-lg shadow-lg backdrop-blur-sm">
            {{ session('message') }}
        </div>
    </div>
@endif

@if (session()->has('error'))
    <div 
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-4 right-4 z-50"
    >
        <div class="bg-red-900/90 border border-red-800 text-red-100 px-6 py-4 rounded-lg shadow-lg backdrop-blur-sm">
            {{ session('error') }}
        </div>
    </div>
@endif