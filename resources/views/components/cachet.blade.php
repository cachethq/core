@use('Cachet\Cachet')
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="bg-background-light text-base-light dark:bg-background-dark dark:text-base-dark"
    x-data="{ darkMode: localStorage.getItem('darkMode') || localStorage.setItem('darkMode', '{{ Cachet::darkMode() }}')}"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    x-bind:class="{'dark': darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)}"
>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="shortcut icon" href="{{ asset('vendor/cachethq/cachet/favicon.ico') }}" />
        <link rel="apple-touch-icon" href="{{ asset('vendor/cachethq/cachet/apple-touch-icon.png') }}" />

        <title>{{ $title ?: config('cachet.title', 'Cachet') }}</title>
        <meta name="title" content="{{ $title ?: config('cachet.title', 'Cachet') }}" />
        <meta name="description" content="Status page for {{ $site_name }}." />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url(Cachet::path()) }}" />
        <meta property="og:title" content="{{ $title ?: config('cachet.title', 'Cachet') }}" />
        <meta property="og:description" content="Status page for {{ $site_name }}." />

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:url" content="{{ url(Cachet::path()) }}" />
        <meta property="twitter:title" content="{{ $title ?: config('cachet.title', 'Cachet') }}" />
        <meta property="twitter:description" content="Status page for {{ $site_name }}." />


        @vite(['resources/css/cachet.css', 'resources/js/cachet.js'], 'vendor/cachethq/cachet/build')

        @if($refresh_rate)
        <meta http-equiv="refresh" content="{{ $refresh_rate }}">
        @endif

        <!-- Custom Cachet Header -->
        {!! $cachet_header !!}

        <style type="text/css">
            /* Cachet custom styles */
            :root {
                @foreach (\Cachet\Cachet::cssVariables() as $key => $value)
                    --{{ $key }}: {{ $value }};
                @endforeach
            }

            {!! $cachet_css !!}
        </style>
    </head>
    <body class="flex min-h-screen flex-col items-stretch antialiased">
        {{ $slot }}

        <!-- Custom Cachet Footer -->
        {!! $cachet_footer !!}
    <script>
        localStorage.setItem('darkMode', '{{ Cachet::darkMode() }}')
    </script>
    </body>
</html>
