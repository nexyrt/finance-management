<div>
    <x-modal :title="$reimbursement ? 'Detail Pengajuan' : 'Detail'" wire size="2xl">
        @if ($reimbursement)
            <div class="space-y-6">
                {{-- Status Badge --}}
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reimbursement->title }}</h3>
                    <x-badge :color="$reimbursement->status_badge_color" :text="$reimbursement->status_label" />
                </div>

                {{-- Requestor Info --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Pengaju</h4>
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ $reimbursement->user->initials() }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $reimbursement->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reimbursement->user->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</label>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $reimbursement->formatted_amount }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $reimbursement->category_label }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal
                            Pengeluaran</label>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $reimbursement->expense_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal
                            Dibuat</label>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $reimbursement->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                {{-- Description --}}
                @if ($reimbursement->description)
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deskripsi</label>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $reimbursement->description }}</p>
                    </div>
                @endif

                {{-- Attachment --}}
                @if ($reimbursement->hasAttachment())
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2 block">Bukti
                            Pengeluaran</label>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            @if ($reimbursement->isImageAttachment())
                                <a href="{{ $reimbursement->attachment_url }}" target="_blank">
                                    <img src="{{ $reimbursement->attachment_url }}" alt="Attachment"
                                        class="max-h-64 rounded-lg mx-auto">
                                </a>
                            @else
                                <a href="{{ $reimbursement->attachment_url }}" target="_blank"
                                    class="flex items-center space-x-3 text-blue-600 hover:text-blue-700">
                                    <x-icon name="document" class="w-8 h-8" />
                                    <span class="text-sm font-medium">{{ $reimbursement->attachment_name }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Review Info --}}
                @if ($reimbursement->reviewed_at)
                    <div
                        class="bg-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-50 dark:bg-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-900/20 border border-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-200 dark:border-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-700 rounded-lg p-4">
                        <h4
                            class="text-sm font-semibold text-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-800 dark:text-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-200 mb-2">
                            {{ $reimbursement->isApproved() ? 'Disetujui' : 'Ditolak' }}
                        </h4>
                        <div
                            class="text-sm text-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-700 dark:text-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-300">
                            <p>Oleh: <strong>{{ $reimbursement->reviewer->name }}</strong></p>
                            <p>Tanggal: {{ $reimbursement->reviewed_at->format('d M Y H:i') }}</p>
                            @if ($reimbursement->review_notes)
                                <p class="mt-2">Catatan: {{ $reimbursement->review_notes }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Payment Info --}}
                @if ($reimbursement->isPaid())
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Pembayaran</h4>
                        <div class="text-sm text-blue-700 dark:text-blue-300">
                            <p>Dibayar oleh: <strong>{{ $reimbursement->payer->name }}</strong></p>
                            <p>Tanggal: {{ $reimbursement->paid_at->format('d M Y H:i') }}</p>
                            @if ($reimbursement->bankTransaction)
                                <p>Referensi: {{ $reimbursement->bankTransaction->reference_number ?? '-' }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Timeline --}}
                <div>
                    <label
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-3 block">Timeline</label>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Dibuat</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $reimbursement->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @if ($reimbursement->reviewed_at)
                            <div class="flex items-start space-x-3">
                                <div
                                    class="w-2 h-2 bg-{{ $reimbursement->isApproved() ? 'green' : 'red' }}-500 rounded-full mt-1.5">
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $reimbursement->isApproved() ? 'Disetujui' : 'Ditolak' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $reimbursement->reviewed_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($reimbursement->paid_at)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-1.5"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Dibayar</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $reimbursement->paid_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end w-full">
                    <x-button color="gray" wire:click="$set('modal', false)">Tutup</x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
