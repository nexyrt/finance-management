<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? env('APP_NAME') }}</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:600,700,800" rel="stylesheet" />

<!-- Favicons -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">

<!-- Apple Touch Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('apple-touch-icon-152x152.png') }}">

<!-- Android Chrome Icons -->
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">

<!-- Web App Manifest (PWA) -->
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Microsoft Tiles -->
<meta name="msapplication-TileColor" content="#2563eb">
<meta name="msapplication-TileImage" content="{{ asset('mstile-150x150.png') }}">
<meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">

<!-- Theme Color -->
<meta name="theme-color" content="#2563eb">

<link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

<style>
    .client-type-radio {
        flex: 1;
        min-width: 0;
    }

    .client-type-container {
        display: flex;
        gap: 1rem;
        width: 100%;
    }

    @media (max-width: 640px) {
        .client-type-container {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>


{{-- Initialize theme BEFORE anything else --}}
<script>
    // Apply theme immediately on page load
    (function() {
        const theme = localStorage.getItem('tallstackui.theme');
        if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

@livewireStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])

<tallstackui:script />
