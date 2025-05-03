{{-- Success Messages --}}
@if (session()->has('message'))
    <div class="mb-4 mx-4 px-4 py-3 bg-green-900/50 border border-green-700 rounded-md text-green-200">
        {{ session('message') }}
    </div>
@endif

{{-- Error Messages --}}
@if (session()->has('error'))
    <div class="mb-4 mx-4 px-4 py-3 bg-red-900/50 border border-red-700 rounded-md text-red-200">
        {{ session('error') }}
    </div>
@endif

{{-- Detailed Dependency Information --}}
@if (session()->has('skippedDetails') && count(session('skippedDetails')) > 0)
    <div class="mb-4 mx-4 px-4 py-3 bg-amber-900/50 border border-amber-700 rounded-md">
        <h4 class="font-medium text-amber-200 mb-2">Clients Not Deleted Due to Dependencies:</h4>
        <ul class="pl-5 list-disc space-y-1">
            @foreach(session('skippedDetails') as $client)
                <li class="text-amber-100">
                    <span class="font-medium">{{ $client['name'] }}</span>
                    <ul class="pl-5 list-disc text-amber-200 text-sm">
                        @if($client['serviceClients'] > 0)
                            <li>{{ $client['serviceClients'] }} service(s) associated</li>
                        @endif
                        @if($client['invoices'] > 0)
                            <li>{{ $client['invoices'] }} invoice(s) associated</li>
                        @endif
                    </ul>
                </li>
            @endforeach
        </ul>
        <p class="mt-2 text-amber-200 text-sm">Please remove these dependencies before deleting these clients.</p>
    </div>
@endif