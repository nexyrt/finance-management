<div class="max-w-full mx-auto p-6" x-data="invoiceForm()" @click.away="closeAllDropdowns()">
    {{-- Header --}}
    <div class="mb-6 space-y-1">
        <h1
            class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
            {{ __('invoice.create_invoice') }}
        </h1>
        <p class="text-dark-600 dark:text-dark-400 text-lg">{{ __('pages.multi_client_invoice') }}</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div
            class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <h3 class="text-sm font-semibold text-red-900 dark:text-red-200 mb-2">{{ __('pages.please_fix') }}:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Invoice Info - 1 ROW Grid (4 cols equal) --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
            <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-4">{{ __('invoice.invoice_details') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- Invoice Number (3 cols) --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.invoice_number') }}
                        *</label>
                    <div class="relative">
                        <input type="text" x-model="invoice.invoice_number" :readonly="invoice.number_locked"
                            :class="invoice.number_locked ? 'bg-dark-100 dark:bg-dark-700 cursor-not-allowed' :
                                'bg-white dark:bg-dark-800'"
                            class="w-full pl-3 pr-10 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="{{ __('pages.auto_generate_on_save') }}">
                        <button @click="invoice.number_locked = !invoice.number_locked" type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-dark-400 hover:text-primary-600 transition">
                            <svg x-show="invoice.number_locked" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <svg x-show="!invoice.number_locked" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Client (3 cols) --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.bill_to') }} ({{ __('pages.owner') }})
                        *</label>
                    <div class="relative">
                        <div x-show="!invoice.client_id" @click="selectOpen = !selectOpen"
                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition">
                            {{ __('pages.select_client') }}
                        </div>
                        <div x-show="invoice.client_id"
                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between">
                            <span class="text-dark-900 dark:text-dark-50" x-text="invoice.client_name"></span>
                            <button @click="clearClient()" type="button"
                                class="text-dark-400 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div x-show="selectOpen" x-transition
                            class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                            <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                <input type="text" x-model="selectSearch" @click.stop :placeholder="'{{ __('common.search') }} {{ strtolower(__('common.clients')) }}...'"
                                    class="w-full px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div class="overflow-y-auto max-h-64">
                                <template x-for="client in filteredClients" :key="client.id">
                                    <div @click="selectClient(client)"
                                        class="px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                                <span x-text="client.name.charAt(0).toUpperCase()"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-dark-900 dark:text-dark-50 truncate"
                                                    x-text="client.name"></div>
                                                <div class="text-sm text-dark-500 dark:text-dark-400 truncate"
                                                    x-text="client.email || '-'"></div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="filteredClients.length === 0"
                                    class="px-4 py-8 text-center text-dark-500 dark:text-dark-400">
                                    <p class="text-sm">{{ __('pages.no_clients_found') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Issue Date (3 cols) --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.invoice_date') }} *</label>
                    <input type="date" x-model="invoice.issue_date"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                {{-- Due Date (3 cols) --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">{{ __('invoice.due_date') }} *</label>
                    <input type="date" x-model="invoice.due_date"
                        class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>

            {{-- Faktur Upload --}}
            <div class="mt-6 pt-6 border-t border-dark-200 dark:border-dark-600" x-data="{ isDragging: false }">
                <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-2">
                    Faktur (PDF/Gambar)
                </label>
                <div class="space-y-3">
                    {{-- Drag and Drop Zone --}}
                    <div @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                         :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-dark-300 dark:border-dark-600'"
                         class="relative border-2 border-dashed rounded-xl p-6 transition-all duration-200">

                        <input type="file"
                               wire:model="faktur"
                               accept=".pdf,.jpg,.jpeg,.png"
                               x-ref="fileInput"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="text-center pointer-events-none">
                            @if ($faktur)
                                {{-- File Selected State --}}
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">File berhasil dipilih</p>
                                        <p class="text-xs text-dark-600 dark:text-dark-400 mt-1">{{ $faktur->getClientOriginalName() }}</p>
                                        <p class="text-xs text-dark-500 dark:text-dark-500 mt-1">{{ number_format($faktur->getSize() / 1024, 2) }} KB</p>
                                    </div>
                                    <button type="button"
                                            wire:click="$set('faktur', null)"
                                            class="pointer-events-auto text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                        Hapus File
                                    </button>
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            <span class="text-primary-600 dark:text-primary-400">Klik untuk upload</span>
                                            atau drag & drop
                                        </p>
                                        <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">PDF, JPG, JPEG, PNG (Maks. 5MB)</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @error('faktur')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    @if ($faktur)
                        <div>
                            <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">
                                Nama File (Opsional)
                            </label>
                            <input type="text" wire:model="fakturName" placeholder="Contoh: Faktur-{{ $invoice['invoice_number'] ?? 'INV001' }}"
                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            @error('fakturName')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">Kosongkan untuk menggunakan nama file asli. Ekstensi file akan ditambahkan otomatis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invoice Items - Full Width --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.invoice_items') }}</h2>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="bulkCount" min="1" max="50"
                        class="w-16 px-2 py-2 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <button @click="bulkAddItems()" type="button"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('pages.add_item') }}
                    </button>
                </div>
            </div>

            {{-- Table Header --}}
            <div class="hidden lg:grid lg:grid-cols-24 gap-3 px-4 py-3 bg-dark-100 dark:bg-dark-900 rounded-lg mb-2">
                <div class="col-span-1 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">#
                </div>
                <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">{{ __('invoice.client') }}</div>
                <div class="col-span-4 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">{{ __('common.services') }}</div>
                <div class="col-span-2 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
                    {{ __('invoice.qty') }}</div>
                <div class="col-span-2 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
                    {{ __('invoice.unit') }}</div>
                <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">{{ __('invoice.unit_price') }}
                </div>
                <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">{{ __('invoice.amount') }}</div>
                <div class="col-span-3 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase">{{ __('pages.cogs') }}</div>
                <div class="col-span-2 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
                    {{ __('common.tax') }}</div>
                <div class="col-span-1 text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase text-center">
                </div>
            </div>

            <div class="space-y-2">
                <template x-for="(item, index) in items" :key="item.id">
                    <div>
                        {{-- Desktop: Grid Row --}}
                        <div
                            class="hidden lg:grid lg:grid-cols-24 gap-3 p-4 bg-dark-50 dark:bg-dark-900/50 rounded-lg border border-dark-200 dark:border-dark-700 items-center">
                            {{-- No (col-span-1) --}}
                            <div class="col-span-1 text-center">
                                <span class="text-sm font-semibold text-dark-700 dark:text-dark-300"
                                    x-text="index + 1"></span>
                            </div>

                            {{-- Client (col-span-3) --}}
                            <div class="col-span-3">
                                <div class="relative">
                                    <div x-show="!item.client_id"
                                        @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                                        class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition truncate">
                                        {{ __('common.select') }}
                                    </div>
                                    <div x-show="item.client_id"
                                        class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between gap-1">
                                        <span class="text-dark-900 dark:text-dark-50 truncate text-xs"
                                            x-text="item.client_name"></span>
                                        <button @click="clearItemClient(item)" type="button"
                                            class="text-dark-400 hover:text-red-500 transition flex-shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="itemSelectOpen[item.id]" x-transition
                                        class="absolute z-50 mt-1 w-64 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                        <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                            <input type="text" x-model="itemSelectSearch[item.id]" @click.stop
                                                :placeholder="'{{ __('common.search') }}...'"
                                                class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                        <div class="overflow-y-auto max-h-64">
                                            <template x-for="client in filteredItemClients(item.id)"
                                                :key="client.id">
                                                <div @click="selectItemClient(item, client)"
                                                    class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                                            <span x-text="client.name.charAt(0).toUpperCase()"></span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate"
                                                                x-text="client.name"></div>
                                                            <div class="text-xs text-dark-500 dark:text-dark-400 truncate"
                                                                x-text="client.email || '-'"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredItemClients(item.id).length === 0"
                                                class="px-3 py-6 text-center text-dark-500 dark:text-dark-400">
                                                <p class="text-xs">{{ __('pages.no_clients_found') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Service (col-span-4) --}}
                            <div class="col-span-4">
                                <div class="relative flex gap-1" @click.away="serviceSelectOpen[item.id] = false">
                                    <input type="text" x-model="item.service_name"
                                        class="flex-1 px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        :placeholder="'{{ __('pages.type_service_name') }}...'" >
                                    <button @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]"
                                        type="button"
                                        class="px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-600 dark:text-dark-400 hover:bg-dark-50 dark:hover:bg-dark-700 transition flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="serviceSelectOpen[item.id]" x-transition
                                        class="absolute z-50 mt-1 w-96 bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                        <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                            <input type="text" x-model="serviceSelectSearch[item.id]" @click.stop
                                                :placeholder="'{{ __('common.search') }} {{ strtolower(__('common.services')) }}...'"
                                                class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                        <div class="overflow-y-auto max-h-64">
                                            <template x-for="service in filteredItemServices(item.id)"
                                                :key="service.id">
                                                <div @click="selectService(item, service)"
                                                    class="px-3 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                    <div class="font-medium text-sm text-dark-900 dark:text-dark-50"
                                                        x-text="service.name"></div>
                                                    <div class="flex items-center justify-between mt-0.5">
                                                        <span class="text-xs text-primary-600 dark:text-primary-400"
                                                            x-text="service.type"></span>
                                                        <span
                                                            class="text-xs font-semibold text-dark-700 dark:text-dark-300"
                                                            x-text="service.formatted_price"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="filteredItemServices(item.id).length === 0"
                                                class="px-3 py-6 text-center text-dark-500 dark:text-dark-400">
                                                <p class="text-xs">{{ __('pages.no_services_found') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Qty (col-span-2) --}}
                            <div class="col-span-2">
                                <input type="text" x-model="item.quantity" @input="calculateItem(item)"
                                    class="w-full px-2 py-1.5 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="1">
                            </div>

                            {{-- Unit (col-span-2) --}}
                            <div class="col-span-2">
                                <select x-model="item.unit"
                                    class="w-full px-2 py-1.5 text-sm text-center border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="pcs">pcs</option>
                                    <option value="m³">m³</option>
                                </select>
                            </div>

                            {{-- Unit Price (col-span-3) --}}
                            <div class="col-span-3">
                                <input type="text" x-model="item.unit_price"
                                    @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                                    class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="0">
                            </div>

                            {{-- Amount (col-span-3) --}}
                            <div class="col-span-3">
                                <div class="px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-100 dark:bg-dark-900 text-dark-900 dark:text-dark-50 font-semibold text-right"
                                    x-text="formatCurrency(item.amount)"></div>
                            </div>

                            {{-- COGS (col-span-3) --}}
                            <div class="col-span-3">
                                <input type="text" x-model="item.cogs_amount"
                                    @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                                    class="w-full px-2 py-1.5 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="0">
                            </div>

                            {{-- Tax Checkbox (col-span-2) --}}
                            <div class="col-span-2 flex justify-center">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" x-model="item.is_tax_deposit"
                                        class="rounded border-dark-300 dark:border-dark-600 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                    <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('common.tax') }}</span>
                                </label>
                            </div>

                            {{-- Remove Button (col-span-1) --}}
                            <div class="col-span-1 flex justify-center">
                                <button @click="removeItem(index)" type="button"
                                    class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Mobile: Card Layout --}}
                        <div
                            class="block lg:hidden p-4 bg-dark-50 dark:bg-dark-900/50 rounded-lg border border-dark-200 dark:border-dark-700 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-dark-900 dark:text-dark-50"
                                        x-text="'#' + (index + 1)"></span>
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" x-model="item.is_tax_deposit"
                                            class="rounded border-dark-300 dark:border-dark-600 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                        <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('invoice.tax_deposit') }}</span>
                                    </label>
                                </div>
                                <button @click="removeItem(index)" type="button"
                                    class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-1 rounded transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-2">
                                {{-- Client --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('invoice.client') }}</label>
                                    <div class="relative">
                                        <div x-show="!item.client_id"
                                            @click="itemSelectOpen[item.id] = !itemSelectOpen[item.id]"
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 cursor-pointer hover:border-primary-400 transition">
                                            {{ __('pages.select_client') }}
                                        </div>
                                        <div x-show="item.client_id"
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 flex items-center justify-between">
                                            <span class="text-dark-900 dark:text-dark-50"
                                                x-text="item.client_name"></span>
                                            <button @click="clearItemClient(item)" type="button"
                                                class="text-dark-400 hover:text-red-500 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div x-show="itemSelectOpen[item.id]" x-transition
                                            class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                            <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                                <input type="text" x-model="itemSelectSearch[item.id]" @click.stop
                                                    :placeholder="'{{ __('common.search') }} {{ strtolower(__('common.clients')) }}...'"
                                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            </div>
                                            <div class="overflow-y-auto max-h-64">
                                                <template x-for="client in filteredItemClients(item.id)"
                                                    :key="client.id">
                                                    <div @click="selectItemClient(item, client)"
                                                        class="px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                                                <span
                                                                    x-text="client.name.charAt(0).toUpperCase()"></span>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <div class="font-medium text-dark-900 dark:text-dark-50 truncate"
                                                                    x-text="client.name"></div>
                                                                <div class="text-sm text-dark-500 dark:text-dark-400 truncate"
                                                                    x-text="client.email || '-'"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="filteredItemClients(item.id).length === 0"
                                                    class="px-4 py-8 text-center text-dark-500 dark:text-dark-400">
                                                    <p class="text-sm">{{ __('pages.no_clients_found') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Service --}}
                                <div>
                                    <label
                                        class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.service_name') }}</label>
                                    <div class="relative flex gap-2" @click.away="serviceSelectOpen[item.id] = false">
                                        <input type="text" x-model="item.service_name"
                                            class="flex-1 px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            :placeholder="'{{ __('pages.type_service_name') }}...'" >
                                        <button @click="serviceSelectOpen[item.id] = !serviceSelectOpen[item.id]"
                                            type="button"
                                            class="px-3 py-2 border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-600 dark:text-dark-400 hover:bg-dark-50 dark:hover:bg-dark-700 transition flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="serviceSelectOpen[item.id]" x-transition
                                            class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg shadow-lg max-h-80 overflow-hidden">
                                            <div class="p-2 border-b border-dark-200 dark:border-dark-700">
                                                <input type="text" x-model="serviceSelectSearch[item.id]"
                                                    @click.stop :placeholder="'{{ __('common.search') }} {{ strtolower(__('common.services')) }}...'"
                                                    class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 placeholder-dark-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            </div>
                                            <div class="overflow-y-auto max-h-64">
                                                <template x-for="service in filteredItemServices(item.id)"
                                                    :key="service.id">
                                                    <div @click="selectService(item, service)"
                                                        class="px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition border-b border-dark-100 dark:border-dark-700 last:border-0">
                                                        <div class="font-medium text-dark-900 dark:text-dark-50"
                                                            x-text="service.name"></div>
                                                        <div class="flex items-center justify-between mt-1">
                                                            <span
                                                                class="text-xs text-primary-600 dark:text-primary-400"
                                                                x-text="service.type"></span>
                                                            <span
                                                                class="text-sm font-semibold text-dark-700 dark:text-dark-300"
                                                                x-text="service.formatted_price"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="filteredItemServices(item.id).length === 0"
                                                    class="px-4 py-8 text-center text-dark-500 dark:text-dark-400">
                                                    <p class="text-sm">{{ __('pages.no_services_found') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Qty, Unit & Unit Price --}}
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('invoice.quantity') }}</label>
                                        <input type="text" x-model="item.quantity" @input="calculateItem(item)"
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="1">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('invoice.unit') }}</label>
                                        <select x-model="item.unit"
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <option value="pcs">pcs</option>
                                            <option value="m³">m³</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('invoice.unit_price') }}</label>
                                        <input type="text" x-model="item.unit_price"
                                            @input="item.unit_price = formatInput($event.target.value); calculateItem(item)"
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>

                                {{-- Amount & COGS --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('invoice.amount') }}</label>
                                        <div class="px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-dark-100 dark:bg-dark-900 text-dark-900 dark:text-dark-50 font-semibold"
                                            x-text="formatCurrency(item.amount)"></div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">{{ __('pages.cogs') }}</label>
                                        <input type="text" x-model="item.cogs_amount"
                                            @input="item.cogs_amount = formatInput($event.target.value); calculateItem(item)"
                                            class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                </template>

                <div x-show="items.length === 0"
                    class="p-12 text-center border-2 border-dashed border-dark-300 dark:border-dark-600 rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-dark-400 dark:text-dark-500 mb-3" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-dark-600 dark:text-dark-400 text-sm">{{ __('pages.no_items_yet') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Invoice Summary Grid - Half Width Right --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Empty left column on desktop --}}
            <div class="hidden lg:block"></div>

            {{-- Summary right column --}}
            <div
                class="bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-dark-200 dark:border-dark-600 p-6">
                <h2 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-6">{{ __('pages.invoice_summary') }}</h2>

                <div class="space-y-4">
                    {{-- Subtotal --}}
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('invoice.subtotal') }}</span>
                        <span class="text-lg font-semibold text-dark-900 dark:text-dark-50"
                            x-text="formatCurrency(subtotal)"></span>
                    </div>

                    {{-- Discount Section --}}
                    <div class="pt-4 pb-4 border-t border-b border-dark-200 dark:border-dark-700 space-y-3">
                        <div class="flex flex-col gap-2">
                            <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('invoice.discount') }}</span>
                            <select x-model="discount.type"
                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem] bg-[right_0.5rem_center] bg-no-repeat pr-10">
                                <option value="fixed">{{ __('pages.fixed_amount') }}</option>
                                <option value="percentage">{{ __('pages.percentage') }} (%)</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <input type="text" x-model="discount.value"
                                @input="discount.value = formatInput($event.target.value)"
                                x-show="discount.type === 'fixed'" :placeholder="'{{ __('pages.enter_amount') }}'"
                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <input type="number" x-model="discount.value" x-show="discount.type === 'percentage'"
                                :placeholder="'{{ __('pages.enter_percentage') }}'" min="0" max="100"
                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <input type="text" x-model="discount.reason" :placeholder="'{{ __('pages.reason_optional') }}'"
                                class="w-full px-3 py-2 text-sm border border-dark-200 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <div class="flex justify-between items-center" x-show="discountAmount > 0">
                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.discount_applied') }}</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400"
                                x-text="'- ' + formatCurrency(discountAmount)"></span>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="py-4 px-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-semibold text-dark-900 dark:text-dark-50">{{ __('invoice.total_amount') }}</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400"
                                x-text="formatCurrency(totalAmount)"></span>
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="grid grid-cols-2 gap-3 pt-3">
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-xs text-green-700 dark:text-green-400 block mb-1">{{ __('pages.net_profit') }}</span>
                            <span class="text-base font-semibold text-green-600 dark:text-green-400"
                                x-text="formatCurrency(netProfit)"></span>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <span class="text-xs text-blue-700 dark:text-blue-400 block mb-1">{{ __('invoice.tax_deposit') }}</span>
                            <span class="text-base font-semibold text-blue-600 dark:text-blue-400"
                                x-text="formatCurrency(taxDeposits)"></span>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <button @click="syncAndSave()" type="button" :disabled="saving || items.length === 0"
                        :class="saving || items.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700'"
                        class="w-full mt-4 px-6 py-3 bg-primary-600 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="saving ? '{{ __('common.saving') }}...' : '{{ __('pages.save_invoice') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function invoiceForm() {
        return {
            invoice: {
                invoice_number: '',
                client_id: null,
                client_name: '',
                issue_date: null,
                due_date: null,
                number_locked: true
            },
            items: [],
            discount: {
                type: 'fixed',
                value: 0,
                reason: ''
            },
            clients: @js($this->clients),
            services: @js($this->services),
            maxInvoiceSequence: @js($this->maxInvoiceSequence),
            companyInitials: @js($this->companyInitials),
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
                const t = new Date();
                this.invoice.issue_date = t.toISOString().split('T')[0];
                this.invoice.due_date = new Date(t.getTime() + 5 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

                // Invoice number will be generated after client is selected
                this.invoice.invoice_number = '';
            },

            getRomanMonth(month) {
                const romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                return romans[month - 1] || 'I';
            },

            getInitials(name) {
                if (!name) return 'XXX';
                return name.split(/\s+/)
                    .filter(word => word.length > 0)
                    .map(word => word[0].toUpperCase())
                    .join('');
            },

            generateInvoiceNumber() {
                if (!this.invoice.client_id) {
                    return '';
                }

                const client = this.clients.find(c => c.id === this.invoice.client_id);
                if (!client) return '';

                const t = new Date();
                const year = t.getFullYear();
                const romanMonth = this.getRomanMonth(t.getMonth() + 1);
                const nextSequence = this.maxInvoiceSequence + 1;

                // Use company initials from backend
                const clientInitials = this.getInitials(client.name);

                // Format: 001/INV/SPI-SAB/I/2026
                return `${String(nextSequence).padStart(3, '0')}/INV/${this.companyInitials}-${clientInitials}/${romanMonth}/${year}`;
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

                // Auto-generate invoice number when client is selected
                if (!this.invoice.number_locked || !this.invoice.invoice_number) {
                    this.invoice.invoice_number = this.generateInvoiceNumber();
                }

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

            parseQuantity(value) {
                if (!value) return 0;
                // Convert Indonesian format (2.828,93) to standard float (2828.93)
                const str = value.toString().trim();
                // Remove all dots (thousand separators), then replace comma with dot (decimal)
                const cleaned = str.replace(/\./g, '').replace(/,/g, '.');
                const result = parseFloat(cleaned) || 0;
                return result;
            },

            calculateItem(item) {
                const qty = this.parseQuantity(item.quantity),
                    price = this.parse(item.unit_price),
                    cogs = this.parse(item.cogs_amount);
                item.amount = Math.round(qty * price);
                item.profit = item.amount - cogs;
            },

            parse(val) {
                return parseInt((val || '').toString().replace(/[^0-9]/g, '')) || 0;
            },
            formatInput(val) {
                // Remove non-digits
                const num = (val || '').toString().replace(/[^0-9]/g, '');
                if (!num) return '';
                // Format with thousand separator
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

            async syncAndSave() {
                this.saving = true;
                try {
                    this.$wire.invoice = this.invoice;
                    this.$wire.items = this.items;

                    // Parse discount value properly
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

                    await this.$wire.save();
                } catch (error) {
                    console.error(error);
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
