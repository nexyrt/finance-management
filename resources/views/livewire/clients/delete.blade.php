{{-- resources/views/livewire/clients/delete.blade.php --}}

<x-modal wire="showDeleteModal" title="Delete Client" center>
    @if($client)
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
                <x-icon name="exclamation-triangle" class="h-6 w-6 text-red-600 dark:text-red-400" />
            </div>
            
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Delete Client
            </h3>
            
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Are you sure you want to delete <strong>{{ $client->name }}</strong>?
                This action cannot be undone and will also delete all related invoices and data.
            </p>

            <!-- Invoice List -->
            @if($client->invoices->count() > 0)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4 max-h-64 overflow-y-auto">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        Invoices that will be deleted:
                    </h4>
                    
                    <div class="space-y-2">
                        @foreach($client->invoices as $invoice)
                            <div class="flex justify-between items-center p-2 bg-white dark:bg-gray-700 rounded border">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $invoice->invoice_number }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $invoice->issue_date->format('M d, Y') }}
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </div>
                                    <x-badge text="{{ ucfirst($invoice->status) }}" 
                                             color="{{ $invoice->status === 'paid' ? 'green' : ($invoice->status === 'overdue' ? 'red' : 'yellow') }}" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-gray-600 dark:text-gray-300">Total Amount:</span>
                            <span class="text-red-600 dark:text-red-400">
                                Rp {{ number_format($client->invoices->sum('total_amount'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <x-slot:footer>
        <x-button wire:click="close" color="secondary">
            Cancel
        </x-button>
        <x-button wire:click="confirm" color="red" spinner="confirm">
            Delete Client
        </x-button>
    </x-slot:footer>
</x-modal>