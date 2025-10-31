<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
                Reimbursements
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                Manage expense reimbursement requests
            </p>
        </div>

        @if ($this->canCreate())
            <livewire:reimbursements.create @created="$refresh" />
        @endif
    </div>

    {{-- Tab Container --}}
    <x-tab selected="my-requests">
        {{-- My Requests Tab --}}
        <x-tab.items tab="my-requests">
            <x-slot:left>
                <x-icon name="user" class="w-5 h-5" />
            </x-slot:left>
            {{-- My Requests Component --}}
            <div class="mt-3">
                <livewire:reimbursements.my-requests />
            </div>
        </x-tab.items>

        {{-- All Requests Tab (Finance Only) --}}
        @if ($this->canViewAllRequests())
            <x-tab.items tab="all-requests">
                <x-slot:left>
                    <x-icon name="users" class="w-5 h-5" />
                </x-slot:left>
                {{-- All Requests Component --}}
                <div class="mt-3">
                    <livewire:reimbursements.all-requests />
                </div>
            </x-tab.items>
        @endif
    </x-tab>

    {{-- Child Components (Shared) --}}
    <livewire:reimbursements.show />
    <livewire:reimbursements.update />
    <livewire:reimbursements.review />
    <livewire:reimbursements.payment />
</div>
