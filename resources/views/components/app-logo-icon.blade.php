@if(isset($companyProfile) && $companyProfile && $companyProfile->logo_path)
    <img src="{{ Storage::url($companyProfile->logo_path) }}" alt="{{ $companyProfile->name ?? 'Company Logo' }}" class="w-full h-full object-contain">
@else
    <img src="{{ asset('images/kisantra.png') }}" alt="KISANTRA" class="w-full h-full object-contain">
@endif