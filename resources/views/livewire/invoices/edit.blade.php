<div class="space-y-6" x-data="invoiceEditForm()" @click.away="closeAllDropdowns()">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('invoice.edit_invoice') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">{{ __('pages.update_invoice_details') }}</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-1">{{ __('pages.please_fix') }}:</p>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex gap-3">
            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
        </div>
    @endif

    {{-- 2-Column Sticky Layout --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

        {{-- LEFT COLUMN — Main Form (2/3 width) --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- ── Section 1: Invoice Details ── --}}
            <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">
                {{-- Section Header --}}
                <div class="px-6 py-4 border-b border-dark-100 dark:border-dark-700 flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('invoice.invoice_details') }}</h2>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.basic_invoice_info') }}</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Row 1: Invoice Number + Client --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Invoice Number (readonly on edit) --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('invoice.invoice_number') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="invoice.invoice_number" readonly
                                class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-50 dark:bg-dark-700/50 text-dark-500 dark:text-dark-400 cursor-not-allowed">
                        </div>

                        {{-- Client --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('invoice.bill_to') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative" @click.away="selectOpen = false">
                                <div x-show="!invoice.client_id"
                                    x-ref="clientTrigger"
                                    @click="
                                        const rect = $refs.clientTrigger.getBoundingClientRect();
                                        const el = document.getElementById('main-client-dd');
                                        if (el) { el.style.left = rect.left + 'px'; el.style.width = rect.width + 'px'; el.style.top = (rect.bottom + 4) + 'px'; setTimeout(() => { const ddH = el.offsetHeight; const spaceBelow = window.innerHeight - rect.bottom; const spaceAbove = rect.top; el.style.top = (spaceBelow >= ddH || spaceBelow >= spaceAbove ? rect.bottom + 4 : rect.top - ddH - 4) + 'px'; }, 0); }
                                        selectOpen = !selectOpen;
                                        const sh = (e) => {
                                            const dd = document.getElementById('main-client-dd');
                                            if (dd && dd.contains(e.target)) return;
                                            selectOpen = false;
                                            window.removeEventListener('scroll', sh, true);
                                        };
                                        if (selectOpen) window.addEventListener('scroll', sh, true);"
                                    class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-400 dark:text-dark-500 cursor-pointer hover:border-primary-400 dark:hover:border-primary-500 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ __('pages.select_client') }}
                                </div>
                                <div x-show="invoice.client_id"
                                    class="w-full px-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shrink-0"
                                        x-text="invoice.client_name ? invoice.client_name.charAt(0).toUpperCase() : ''"></div>
                                    <span class="flex-1 text-dark-900 dark:text-dark-50 font-medium truncate" x-text="invoice.client_name"></span>
                                    <button @click="clearClient()" type="button"
                                        class="shrink-0 text-dark-300 hover:text-red-500 dark:text-dark-500 dark:hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                {{-- Dropdown via teleport --}}
                                <template x-teleport="body">
                                    <div x-show="selectOpen"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        id="main-client-dd"
                                        @click.away="selectOpen = false"
                                        class="fixed z-9999 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-xl overflow-hidden">
                                        <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                            <input type="text" x-model="selectSearch" @click.stop
                                                placeholder="{{ __('common.search') }} {{ strtolower(__('common.clients')) }}..."
                                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                        <div class="overflow-y-auto max-h-56">
                                            <template x-for="client in filteredClients" :key="client.id">
                                                <div @click="selectClient(client)"
                                                    class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition flex items-center gap-3 border-b border-dark-50 dark:border-dark-700 last:border-0">
                                                    <div class="w-8 h-8 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shrink-0"
                                                        x-text="client.name.charAt(0).toUpperCase()"></div>
                                                    <div class="min-w-0">
                                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate" x-text="client.name"></div>
                                                        <div class="text-xs text-dark-400 dark:text-dark-500 truncate" x-text="client.email || '-'"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredClients.length === 0" class="px-4 py-8 text-center">
                                                <p class="text-sm text-dark-400 dark:text-dark-500">{{ __('pages.no_clients_found') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Issue Date + Due Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('invoice.invoice_date') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-400 dark:text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" x-model="invoice.issue_date"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('invoice.due_date') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-400 dark:text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" x-model="invoice.due_date"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:w-full [&::-webkit-calendar-picker-indicator]:cursor-pointer">
                            </div>
                        </div>
                    </div>

                    {{-- Faktur Upload --}}
                    <div class="space-y-1.5" x-data="{ isDragging: false }">
                        <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                            {{ __('invoice.faktur_upload') }}
                            <span class="ml-1 font-normal text-dark-400 normal-case">{{ __('common.optional') }}</span>
                        </label>

                        {{-- Existing faktur indicator --}}
                        @if ($invoice->faktur && !$faktur)
                            <div class="flex items-center justify-between p-3 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/40 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm text-primary-800 dark:text-primary-200 font-medium truncate">{{ basename($invoice->faktur) }}</span>
                                </div>
                                <a href="{{ Storage::url($invoice->faktur) }}" target="_blank"
                                    class="shrink-0 text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium underline ml-3">
                                    {{ __('common.view_file') }}
                                </a>
                            </div>
                        @endif

                        <div @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                             :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : 'border-dark-200 dark:border-dark-600 hover:border-dark-300 dark:hover:border-dark-500'"
                             class="relative border-2 border-dashed rounded-xl p-5 transition-all cursor-pointer">
                            <input type="file" wire:model="faktur" accept=".pdf,.jpg,.jpeg,.png"
                                x-ref="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            @if ($faktur)
                                <div class="flex items-center gap-4 pointer-events-none">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-green-700 dark:text-green-400">{{ $faktur->getClientOriginalName() }}</p>
                                        <p class="text-xs text-dark-400 dark:text-dark-500">{{ number_format($faktur->getSize() / 1024, 1) }} KB</p>
                                    </div>
                                    <button type="button" wire:click="$set('faktur', null)"
                                        class="pointer-events-auto shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors">
                                        {{ __('common.delete_file') }}
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center gap-4 pointer-events-none">
                                    <div class="w-10 h-10 bg-dark-100 dark:bg-dark-700 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-dark-700 dark:text-dark-300">{{ __('invoice.upload_instructions') }}</p>
                                        <p class="text-xs text-dark-400 dark:text-dark-500 mt-0.5">
                                            @if ($invoice->faktur)
                                                {{ __('invoice.new_file_replace_note') }}
                                            @else
                                                {{ __('invoice.faktur_file_types') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('faktur')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        @if ($faktur)
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-dark-600 dark:text-dark-400">{{ __('invoice.filename_optional') }}</label>
                                <input type="text" wire:model="fakturName"
                                    placeholder="{{ __('invoice.filename_example') }}{{ $invoice->invoice_number }}"
                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <p class="text-xs text-dark-400 dark:text-dark-500">{{ __('invoice.filename_note') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Invoice Items ── --}}
            <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">
                {{-- Section Header --}}
                <div class="px-6 py-4 border-b border-dark-100 dark:border-dark-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.invoice_items') }}</h2>
                            <p class="text-xs text-dark-500 dark:text-dark-400">
                                <span x-text="items.length"></span> {{ __('pages.items_added') }}
                            </p>
                        </div>
                    </div>
                    {{-- Add Item Control --}}
                    <div class="flex items-center gap-2">
                        <div class="flex items-center border border-dark-200 dark:border-dark-600 rounded-lg overflow-hidden">
                            <button @click="bulkCount = Math.max(1, bulkCount - 1)" type="button"
                                class="px-2.5 py-2 text-dark-500 hover:text-dark-900 dark:hover:text-dark-50 hover:bg-dark-50 dark:hover:bg-dark-700 transition-colors text-sm font-medium">−</button>
                            <input type="number" x-model="bulkCount" min="1" max="50"
                                class="w-12 py-2 text-sm text-center bg-white dark:bg-dark-800 border-x border-dark-200 dark:border-dark-600 text-dark-900 dark:text-dark-50 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button @click="bulkCount = Math.min(50, bulkCount + 1)" type="button"
                                class="px-2.5 py-2 text-dark-500 hover:text-dark-900 dark:hover:text-dark-50 hover:bg-dark-50 dark:hover:bg-dark-700 transition-colors text-sm font-medium">+</button>
                        </div>
                        <button @click="bulkAddItems()" type="button"
                            class="flex items-center gap-1.5 px-3.5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('pages.add_item') }}
                        </button>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="items.length === 0" class="px-6 py-16 text-center">
                    <div class="w-14 h-14 bg-dark-50 dark:bg-dark-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-dark-300 dark:text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-dark-500 dark:text-dark-400">{{ __('pages.no_items_yet') }}</p>
                    <p class="text-xs text-dark-400 dark:text-dark-500 mt-1">{{ __('pages.click_add_item_to_start') }}</p>
                </div>

                {{-- Desktop Table --}}
                <div x-show="items.length > 0" class="hidden md:block overflow-x-auto">
                    <div class="min-w-[960px]">
                        {{-- Table Header --}}
                        <div class="grid grid-cols-24 gap-2 px-6 py-2.5 bg-dark-50 dark:bg-dark-900/60 border-b border-dark-100 dark:border-dark-700">
                            <div class="col-span-1 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-center">#</div>
                            <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider">{{ __('invoice.client') }}</div>
                            <div class="col-span-5 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider">{{ __('common.services') }}</div>
                            <div class="col-span-2 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-center">{{ __('invoice.qty') }}</div>
                            <div class="col-span-2 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider">{{ __('invoice.unit') }}</div>
                            <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-right">{{ __('invoice.unit_price') }}</div>
                            <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-right">{{ __('invoice.amount') }}</div>
                            <div class="col-span-3 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-right">{{ __('pages.cogs') }}</div>
                            <div class="col-span-1 text-[10px] font-bold text-dark-500 dark:text-dark-400 uppercase tracking-wider text-center">{{ __('common.tax') }}</div>
                            <div class="col-span-1"></div>
                        </div>

                        {{-- Rows --}}
                        <div class="divide-y divide-dark-100 dark:divide-dark-700">
                            <template x-for="(item, index) in items" :key="'d-' + item.id">
                                <div class="grid grid-cols-24 gap-2 px-6 py-3 items-center hover:bg-dark-50/50 dark:hover:bg-dark-900/30 transition-colors group">
                                    {{-- # --}}
                                    <div class="col-span-1 text-center">
                                        <span class="text-xs font-bold text-dark-400 dark:text-dark-500" x-text="index + 1"></span>
                                    </div>

                                    {{-- Client --}}
                                    <div class="col-span-3" @click.away="itemSelectOpen[item.id] = false">
                                        <div x-show="!item.client_id"
                                            :id="`clientInput-${item.id}`"
                                            @click="
                                                const trigger = $event.target;
                                                const rect = trigger.getBoundingClientRect();
                                                const el = document.getElementById('client-dd-' + item.id);
                                                if (el) { el.style.left = rect.left + 'px'; el.style.top = (rect.bottom + 4) + 'px'; requestAnimationFrame(() => requestAnimationFrame(() => { const ddH = el.offsetHeight; const spaceBelow = window.innerHeight - rect.bottom; const spaceAbove = rect.top; el.style.top = (spaceBelow >= ddH || spaceBelow >= spaceAbove ? rect.bottom + 4 : rect.top - ddH - 4) + 'px'; })); }
                                                itemSelectOpen[item.id] = true;
                                                const sh = (e) => {
                                                    const dd = document.getElementById('client-dd-' + item.id);
                                                    if (dd && dd.contains(e.target)) return;
                                                    itemSelectOpen[item.id] = false;
                                                    window.removeEventListener('scroll', sh, true);
                                                };
                                                window.addEventListener('scroll', sh, true);"
                                            class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-400 dark:text-dark-500 cursor-pointer hover:border-primary-400 transition-colors truncate">
                                            {{ __('common.select') }}
                                        </div>
                                        <div x-show="item.client_id"
                                            class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center gap-1">
                                            <span class="flex-1 text-dark-900 dark:text-dark-50 font-medium truncate" x-text="item.client_name"></span>
                                            <button @click="clearItemClient(item)" type="button" class="shrink-0 text-dark-300 hover:text-red-500 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <template x-teleport="body">
                                            <div x-show="itemSelectOpen[item.id]" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                                :id="`client-dd-${item.id}`"
                                                @click.away="itemSelectOpen[item.id] = false"
                                                class="fixed z-9999 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-xl overflow-hidden"
                                                style="width: 240px;">
                                                <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                                    <input type="text" x-model="itemSelectSearch[item.id]" @click.stop
                                                        placeholder="{{ __('common.search') }}..."
                                                        class="w-full px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                </div>
                                                <div class="overflow-y-auto max-h-48">
                                                    <template x-for="c in filteredItemClients(item.id)" :key="c.id">
                                                        <div @click="selectItemClient(item, c)"
                                                            class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer flex items-center gap-2 border-b border-dark-50 dark:border-dark-700 last:border-0">
                                                            <div class="w-6 h-6 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0" x-text="c.name.charAt(0).toUpperCase()"></div>
                                                            <div class="min-w-0">
                                                                <div class="text-xs font-medium text-dark-900 dark:text-dark-50 truncate" x-text="c.name"></div>
                                                                <div class="text-[10px] text-dark-400 truncate" x-text="c.email || '-'"></div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredItemClients(item.id).length === 0" class="py-6 text-center text-xs text-dark-400">{{ __('pages.no_clients_found') }}</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Service --}}
                                    <div class="col-span-5" @click.away="serviceSelectOpen[item.id] = false">
                                        <input type="text" x-model="item.service_name"
                                            :id="`serviceInput-${item.id}`"
                                            class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            :placeholder="'{{ __('pages.type_service_name') }}...'"
                                            @click="
                                                const rect = $event.target.getBoundingClientRect();
                                                const el = document.getElementById('service-dd-' + item.id);
                                                if (el) { el.style.left = rect.left + 'px'; el.style.top = (rect.bottom + 4) + 'px'; requestAnimationFrame(() => requestAnimationFrame(() => { const ddH = el.offsetHeight; const spaceBelow = window.innerHeight - rect.bottom; const spaceAbove = rect.top; el.style.top = (spaceBelow >= ddH || spaceBelow >= spaceAbove ? rect.bottom + 4 : rect.top - ddH - 4) + 'px'; })); }
                                                serviceSelectOpen[item.id] = true;
                                                const sh = (e) => {
                                                    const dd = document.getElementById('service-dd-' + item.id);
                                                    if (dd && dd.contains(e.target)) return;
                                                    serviceSelectOpen[item.id] = false;
                                                    window.removeEventListener('scroll', sh, true);
                                                };
                                                window.addEventListener('scroll', sh, true);">
                                        <template x-teleport="body">
                                            <div x-show="serviceSelectOpen[item.id]" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                                :id="`service-dd-${item.id}`"
                                                @click.away="serviceSelectOpen[item.id] = false"
                                                class="fixed z-9999 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-xl overflow-hidden"
                                                style="width: 280px;">
                                                <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                                    <input type="text" x-model="serviceSelectSearch[item.id]" @click.stop
                                                        placeholder="{{ __('common.search') }} {{ strtolower(__('common.services')) }}..."
                                                        class="w-full px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                </div>
                                                <div class="overflow-y-auto max-h-48">
                                                    <template x-for="svc in filteredItemServices(item.id)" :key="svc.id">
                                                        <div @click="selectService(item, svc)"
                                                            class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer border-b border-dark-50 dark:border-dark-700 last:border-0">
                                                            <div class="text-xs font-semibold text-dark-900 dark:text-dark-50" x-text="svc.name"></div>
                                                            <div class="flex items-center justify-between mt-0.5">
                                                                <span class="text-[10px] text-primary-600 dark:text-primary-400" x-text="svc.type"></span>
                                                                <span class="text-[10px] font-semibold text-dark-600 dark:text-dark-300" x-text="svc.formatted_price"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="filteredItemServices(item.id).length === 0" class="py-6 text-center text-xs text-dark-400">{{ __('pages.no_services_found') }}</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Qty --}}
                                    <div class="col-span-2">
                                        <input type="text" x-model="item.quantity" @input="calculateItem(item)"
                                            class="w-full px-2 py-1.5 text-xs text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="1">
                                    </div>

                                    {{-- Unit --}}
                                    <div class="col-span-2">
                                        <select x-model="item.unit"
                                            class="w-full px-2 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <option value="pcs">pcs</option>
                                            <option value="m³">m³</option>
                                        </select>
                                    </div>

                                    {{-- Unit Price --}}
                                    <div class="col-span-3">
                                        <input type="text" x-model="item.unit_price"
                                            @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                                            class="w-full px-2 py-1.5 text-xs text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>

                                    {{-- Amount --}}
                                    <div class="col-span-3">
                                        <div class="px-2 py-1.5 text-xs text-right rounded-lg bg-dark-50 dark:bg-dark-900/60 text-dark-700 dark:text-dark-200 font-semibold"
                                            x-text="formatCurrency(item.amount)"></div>
                                    </div>

                                    {{-- COGS --}}
                                    <div class="col-span-3">
                                        <input type="text" x-model="item.cogs_amount"
                                            @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                                            class="w-full px-2 py-1.5 text-xs text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>

                                    {{-- Tax --}}
                                    <div class="col-span-1 flex justify-center">
                                        <label class="cursor-pointer">
                                            <input type="checkbox" x-model="item.is_tax_deposit"
                                                class="rounded border-dark-300 dark:border-dark-600 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                        </label>
                                    </div>

                                    {{-- Delete --}}
                                    <div class="col-span-1 flex justify-center">
                                        <button @click="removeItem(index)" type="button"
                                            class="p-1.5 text-dark-300 dark:text-dark-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Mobile Card Layout --}}
                <div x-show="items.length > 0" class="block md:hidden divide-y divide-dark-100 dark:divide-dark-700">
                    <template x-for="(item, index) in items" :key="'m-' + item.id">
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 bg-dark-100 dark:bg-dark-700 rounded-lg flex items-center justify-center text-xs font-bold text-dark-600 dark:text-dark-300" x-text="index + 1"></span>
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" x-model="item.is_tax_deposit"
                                            class="rounded border-dark-300 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                        <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('invoice.tax_deposit') }}</span>
                                    </label>
                                </div>
                                <button @click="removeItem(index)" type="button"
                                    class="p-1.5 text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Client --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('invoice.client') }}</label>
                                <div class="relative">
                                    <div x-show="!item.client_id" @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-400 cursor-pointer hover:border-primary-400 transition-colors">
                                        {{ __('pages.select_client') }}
                                    </div>
                                    <div x-show="item.client_id"
                                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center gap-2">
                                        <span class="flex-1 text-dark-900 dark:text-dark-50 font-medium truncate" x-text="item.client_name"></span>
                                        <button @click="clearItemClient(item)" type="button" class="text-dark-300 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div x-show="itemSelectOpen[item.id]" x-transition class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-lg overflow-hidden">
                                        <div class="p-2 border-b border-dark-100 dark:border-dark-700">
                                            <input type="text" x-model="itemSelectSearch[item.id]" @click.stop placeholder="{{ __('common.search') }}..."
                                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                        <div class="overflow-y-auto max-h-48">
                                            <template x-for="c in filteredItemClients(item.id)" :key="c.id">
                                                <div @click="selectItemClient(item, c)" class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer flex items-center gap-3 border-b border-dark-50 last:border-0">
                                                    <div class="w-7 h-7 rounded-full bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold shrink-0" x-text="c.name.charAt(0).toUpperCase()"></div>
                                                    <div class="min-w-0">
                                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate" x-text="c.name"></div>
                                                        <div class="text-xs text-dark-400 truncate" x-text="c.email || '-'"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredItemClients(item.id).length === 0" class="py-6 text-center text-sm text-dark-400">{{ __('pages.no_clients_found') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Service --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.service_name') }}</label>
                                <div class="relative flex gap-2" @click.away="serviceSelectOpen[item.id] = false">
                                    <input type="text" x-model="item.service_name"
                                        class="flex-1 px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        :placeholder="'{{ __('pages.type_service_name') }}...'">
                                    <button @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]" type="button"
                                        class="px-2.5 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 hover:bg-dark-50 transition shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="serviceSelectOpen[item.id]" x-transition class="absolute z-50 top-full mt-1 left-0 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-xl shadow-lg overflow-hidden">
                                        <div class="overflow-y-auto max-h-48">
                                            <template x-for="svc in filteredItemServices(item.id)" :key="svc.id">
                                                <div @click="selectService(item, svc)" class="px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer border-b border-dark-50 last:border-0">
                                                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50" x-text="svc.name"></div>
                                                    <div class="flex items-center justify-between mt-0.5">
                                                        <span class="text-xs text-primary-600 dark:text-primary-400" x-text="svc.type"></span>
                                                        <span class="text-xs font-semibold text-dark-600 dark:text-dark-300" x-text="svc.formatted_price"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredItemServices(item.id).length === 0" class="py-6 text-center text-sm text-dark-400">{{ __('pages.no_services_found') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Qty, Unit, Price --}}
                            <div class="grid grid-cols-3 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('invoice.qty') }}</label>
                                    <input type="text" x-model="item.quantity" @input="calculateItem(item)"
                                        class="w-full px-2 py-2 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="1">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('invoice.unit') }}</label>
                                    <select x-model="item.unit" class="w-full px-2 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="pcs">pcs</option>
                                        <option value="m³">m³</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('invoice.unit_price') }}</label>
                                    <input type="text" x-model="item.unit_price"
                                        @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                                        class="w-full px-2 py-2 text-sm text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="0">
                                </div>
                            </div>

                            {{-- Amount + COGS --}}
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('invoice.amount') }}</label>
                                    <div class="px-3 py-2 text-sm text-right rounded-lg bg-dark-50 dark:bg-dark-900/60 text-dark-700 dark:text-dark-200 font-semibold"
                                        x-text="formatCurrency(item.amount)"></div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-dark-500 uppercase tracking-wide">{{ __('pages.cogs') }}</label>
                                    <input type="text" x-model="item.cogs_amount"
                                        @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                                        class="w-full px-3 py-2 text-sm text-right border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN — Sticky Summary (1/3 width) --}}
        <div class="xl:col-span-1 xl:self-start xl:sticky xl:top-6">
            <div class="bg-white dark:bg-dark-800 rounded-xl border border-dark-200 dark:border-dark-600 overflow-hidden">

                {{-- Rows: label + value --}}
                <div class="divide-y divide-dark-100 dark:divide-dark-700">

                    {{-- Subtotal --}}
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('invoice.subtotal') }}</span>
                        <span class="text-xs font-semibold text-dark-900 dark:text-dark-50 tabular-nums" x-text="formatCurrency(subtotal)"></span>
                    </div>

                    {{-- Tax Deposit (conditional) --}}
                    <div class="flex items-center justify-between px-4 py-3" x-show="taxDeposits > 0">
                        <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('invoice.tax_deposit') }}</span>
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 tabular-nums" x-text="formatCurrency(taxDeposits)"></span>
                    </div>

                    {{-- Discount (conditional) --}}
                    <div class="flex items-center justify-between px-4 py-3" x-show="discountAmount > 0">
                        <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('invoice.discount') }}</span>
                        <span class="text-xs font-semibold text-red-500 tabular-nums" x-text="'− ' + formatCurrency(discountAmount)"></span>
                    </div>

                    {{-- Total --}}
                    <div class="px-4 py-4 bg-dark-50 dark:bg-dark-900/40">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide">{{ __('invoice.total_amount') }}</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400 tabular-nums" x-text="formatCurrency(totalAmount)"></span>
                        </div>
                    </div>

                    {{-- Profit + Tax pills --}}
                    <div class="grid grid-cols-2 divide-x divide-dark-100 dark:divide-dark-700">
                        <div class="px-4 py-3">
                            <p class="text-[10px] text-dark-400 dark:text-dark-500 mb-0.5">{{ __('pages.net_profit') }}</p>
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 tabular-nums" x-text="formatCurrency(netProfit)"></p>
                        </div>
                        <div class="px-4 py-3">
                            <p class="text-[10px] text-dark-400 dark:text-dark-500 mb-0.5">{{ __('invoice.tax_deposit') }}</p>
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 tabular-nums" x-text="formatCurrency(taxDeposits)"></p>
                        </div>
                    </div>
                </div>

                {{-- Discount inputs (collapsible section) --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between px-4 py-3 border-t border-dark-100 dark:border-dark-700 text-xs text-dark-500 dark:text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 hover:bg-dark-50 dark:hover:bg-dark-700/40 transition-colors">
                        <span class="font-medium">{{ __('invoice.discount') }}</span>
                        <div class="flex items-center gap-1.5">
                            <span x-show="discountAmount > 0" class="text-[10px] font-semibold text-red-500" x-text="'− ' + formatCurrency(discountAmount)"></span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="border-t border-dark-100 dark:divide-dark-700 px-4 pb-4 pt-3 space-y-2">
                        <div class="flex gap-2">
                            <select x-model="discount.type"
                                class="flex-1 px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="fixed">{{ __('pages.fixed_amount') }}</option>
                                <option value="percentage">%</option>
                            </select>
                            <input type="text" x-model="discount.value"
                                @input="discount.value = formatInput($event.target.value)"
                                x-show="discount.type === 'fixed'"
                                placeholder="0"
                                class="flex-1 px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                            <input type="number" x-model="discount.value"
                                x-show="discount.type === 'percentage'"
                                placeholder="0" min="0" max="100"
                                class="flex-1 px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                        </div>
                        <input type="text" x-model="discount.reason"
                            placeholder="{{ __('pages.reason_optional') }}"
                            class="w-full px-2.5 py-1.5 text-xs border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>

                {{-- Update Button --}}
                <div class="p-4 border-t border-dark-100 dark:border-dark-700">
                    <button @click="syncAndUpdate()" type="button"
                        :disabled="saving || items.length === 0"
                        :class="saving || items.length === 0
                            ? 'opacity-50 cursor-not-allowed'
                            : 'hover:bg-primary-700 shadow-md shadow-primary-500/20'"
                        class="w-full py-3 bg-primary-600 text-white text-sm font-semibold rounded-lg transition-all duration-150 flex items-center justify-center gap-2">
                        <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span x-text="saving ? '{{ __('common.updating') }}...' : '{{ __('pages.update_invoice') }}'"></span>
                    </button>
                    <p x-show="items.length === 0" class="text-center text-[10px] text-dark-400 dark:text-dark-500 mt-2">
                        {{ __('pages.add_items_to_enable_save') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function invoiceEditForm() {
        return {
            invoice: {
                invoice_number: '',
                client_id: null,
                client_name: '',
                issue_date: null,
                due_date: null,
            },
            items: [],
            discount: {
                type: 'fixed',
                value: 0,
                reason: ''
            },
            clients: @js($this->clients),
            services: @js($this->services),
            existingInvoice: @js($this->existingInvoiceData),
            existingItems: @js($this->existingItems),
            existingDiscount: @js($this->existingDiscount),
            nextId: 1,
            selectOpen: false,
            selectSearch: '',
            itemSelectOpen: {},
            itemSelectSearch: {},
            serviceSelectOpen: {},
            serviceSelectSearch: {},
            saving: false,
            bulkCount: 1,

            init() {
                this.invoice = { ...this.existingInvoice };

                this.items = this.existingItems.map(item => ({
                    ...item,
                    id: this.nextId++
                }));

                this.items.forEach(item => {
                    this.itemSelectOpen[item.id] = false;
                    this.itemSelectSearch[item.id] = '';
                    this.serviceSelectOpen[item.id] = false;
                    this.serviceSelectSearch[item.id] = '';
                    // Format existing values for display
                    if (item.unit_price) item.unit_price = this.formatInput(item.unit_price.toString());
                    if (item.cogs_amount) item.cogs_amount = this.formatInput(item.cogs_amount.toString());
                    this.calculateItem(item);
                });

                this.discount = { ...this.existingDiscount };
                // Format existing discount for display
                if (this.discount.type === 'fixed' && this.discount.value) {
                    this.discount.value = this.formatInput(this.discount.value.toString());
                }
            },

            get filteredClients() {
                return this.filter(this.clients, this.selectSearch, ['name', 'email']);
            },
            filteredItemClients(id) {
                return this.filter(this.clients, this.itemSelectSearch[id], ['name', 'email']);
            },
            filteredItemServices(id) {
                return this.filter(this.services, this.serviceSelectSearch[id], ['name', 'type']);
            },
            get subtotal() {
                return this.items.filter(i => !i.is_tax_deposit).reduce((s, i) => s + (i.amount || 0), 0);
            },
            get totalProfit() {
                return this.items.filter(i => !i.is_tax_deposit).reduce((s, i) => s + (i.profit || 0), 0);
            },
            get netProfit() {
                return Math.max(0, this.totalProfit - this.discountAmount);
            },
            get taxDeposits() {
                return this.items.filter(i => i.is_tax_deposit).reduce((s, i) => s + (i.amount || 0), 0);
            },
            get discountAmount() {
                if (this.discount.type === 'fixed') return this.parse(this.discount.value);
                return Math.round((this.subtotal * (this.discount.value / 100)));
            },
            get totalAmount() {
                return Math.max(0, this.subtotal - this.discountAmount);
            },

            filter(arr, search, keys) {
                if (!search) return arr;
                const s = search.toLowerCase();
                return arr.filter(item => keys.some(k => item[k]?.toLowerCase().includes(s)));
            },

            selectClient(c) {
                this.invoice.client_id = c.id;
                this.invoice.client_name = c.name;
                this.selectOpen = false;
                this.selectSearch = '';
                this.items.forEach(i => {
                    if (!i.client_id) {
                        i.client_id = c.id;
                        i.client_name = c.name;
                    }
                });
            },
            clearClient() {
                this.invoice.client_id = null;
                this.invoice.client_name = '';
            },

            selectItemClient(item, c) {
                item.client_id = c.id;
                item.client_name = c.name;
                this.itemSelectOpen[item.id] = false;
            },
            clearItemClient(item) {
                item.client_id = null;
                item.client_name = '';
            },

            selectService(item, s) {
                item.service_name = s.name;
                item.unit_price = s.formatted_price.replace('Rp ', '');
                this.serviceSelectOpen[item.id] = false;
                this.calculateItem(item);
            },

            addItem() {
                const item = {
                    id: this.nextId++,
                    client_id: this.invoice.client_id || null,
                    client_name: this.invoice.client_name || '',
                    service_name: '',
                    quantity: 1,
                    unit: 'pcs',
                    unit_price: '',
                    amount: 0,
                    cogs_amount: '',
                    profit: 0,
                    is_tax_deposit: false
                };
                this.items.push(item);
                this.itemSelectOpen[item.id] = false;
                this.itemSelectSearch[item.id] = '';
                this.serviceSelectOpen[item.id] = false;
                this.serviceSelectSearch[item.id] = '';
            },

            bulkAddItems() {
                const count = parseInt(this.bulkCount) || 1;
                for (let i = 0; i < count; i++) {
                    this.addItem();
                }
                this.bulkCount = 1;
            },

            removeItem(index) {
                const id = this.items[index].id;
                ['itemSelectOpen', 'itemSelectSearch', 'serviceSelectOpen', 'serviceSelectSearch'].forEach(o =>
                    delete this[o][id]);
                this.items.splice(index, 1);
            },

            calculateItem(item) {
                const qty = parseInt(item.quantity) || 1,
                    price = this.parse(item.unit_price),
                    cogs = this.parse(item.cogs_amount);
                item.amount = qty * price;
                item.profit = item.amount - cogs;
            },

            parse(val) {
                return parseInt((val || '').toString().replace(/[^0-9]/g, '')) || 0;
            },
            formatInput(val) {
                const num = (val || '').toString().replace(/[^0-9]/g, '');
                if (!num) return '';
                return parseInt(num).toLocaleString('id-ID');
            },
            formatCurrency(val) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
            },
            closeAllDropdowns() {
                this.selectOpen = false;
                Object.keys(this.itemSelectOpen).forEach(k => this.itemSelectOpen[k] = false);
                Object.keys(this.serviceSelectOpen).forEach(k => this.serviceSelectOpen[k] = false);
            },

            async syncAndUpdate() {
                this.saving = true;
                try {
                    this.$wire.invoiceData = this.invoice;
                    this.$wire.items = this.items;

                    let discountValue = 0;
                    if (this.discount.type === 'percentage') {
                        discountValue = parseFloat(this.discount.value) || 0;
                    } else {
                        discountValue = this.parse(this.discount.value);
                    }

                    this.$wire.discount = {
                        type: this.discount.type,
                        value: discountValue,
                        reason: this.discount.reason || ''
                    };

                    await this.$wire.update();
                } catch (error) {
                    console.error(error);
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
