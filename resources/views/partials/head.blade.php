<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? env('APP_NAME') }}</title>

<!-- Include Jodit CSS Styling -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link rel="icon" href="favicon.ico" type="image/x-icon">

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


<wireui:scripts />
<tallstackui:script />

@livewireStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
