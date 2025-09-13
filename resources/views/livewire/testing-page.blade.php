<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Testing Custom Tab Buttons</h1>

    {{-- Tab Buttons --}}
    <div class="flex items-center gap-2 bg-zinc-100 dark:bg-gray-700 p-1 rounded-lg mb-6">
        <button 
            wire:click="switchTab('tab1')" 
            loading="switchTab('tab1')"
            class="px-4 py-2 text-sm font-medium rounded-md cursor-pointer transition-all 
            {{ $activeTab === 'tab1' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="flex items-center gap-2">
                <x-icon name="home" class="w-4 h-4" />
                Tab Pertama
            </div>
        </button>
        
        <button 
            wire:click="switchTab('tab2')" 
            loading="switchTab('tab2')"
            class="px-4 py-2 text-sm font-medium rounded-md cursor-pointer transition-all 
            {{ $activeTab === 'tab2' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="flex items-center gap-2">
                <x-icon name="chart-bar" class="w-4 h-4" />
                Tab Kedua
            </div>
        </button>
        
        <button 
            wire:click="switchTab('tab3')" 
            loading="switchTab('tab3')"
            class="px-4 py-2 text-sm font-medium rounded-md cursor-pointer transition-all 
            {{ $activeTab === 'tab3' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
            <div class="flex items-center gap-2">
                <x-icon name="cog-6-tooth" class="w-4 h-4" />
                Tab Ketiga
            </div>
        </button>
    </div>

    {{-- Tab Content --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
        @if($activeTab === 'tab1')
            <h2 class="text-lg font-semibold text-green-600">Konten Tab Pertama</h2>
            <p class="mt-2">Ini adalah konten dari tab pertama. Tab sedang aktif: <strong>{{ $activeTab }}</strong></p>
        @elseif($activeTab === 'tab2')
            <h2 class="text-lg font-semibold text-blue-600">Konten Tab Kedua</h2>
            <p class="mt-2">Ini adalah konten dari tab kedua. Tab sedang aktif: <strong>{{ $activeTab }}</strong></p>
        @elseif($activeTab === 'tab3')
            <h2 class="text-lg font-semibold text-purple-600">Konten Tab Ketiga</h2>
            <p class="mt-2">Ini adalah konten dari tab ketiga. Tab sedang aktif: <strong>{{ $activeTab }}</strong></p>
        @endif
    </div>
</div>