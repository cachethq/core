@props([
    'title',
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-background-light text-base-light dark:bg-background-dark dark:text-base-dark">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="shortcut icon" href="{{ asset('vendor/cachethq/cachet/favicon.ico') }}" />
        <link rel="apple-touch-icon" href="{{ asset('vendor/cachethq/cachet/apple-touch-icon.png') }}" />

        <title>{{ $title ?? config('cachet.title', 'Cachet') }}</title>

        @vite(['resources/css/cachet.css', 'resources/js/cachet.js'], 'vendor/cachethq/cachet/build')
        @filamentStyles

        <!-- Custom Cachet Header -->
        {!! $cachet_header !!}

        <style type="text/css">
            /* Cachet custom styles */
            :root {
                @foreach (\Cachet\Cachet::cssVariables() as $key => $value)
                    --{{ $key }}-light: {{ $value[0] }};
                    --{{ $key }}-dark: {{ $value[1] }};
                @endforeach
            }
        </style>
    </head>
    <body class="flex min-h-screen flex-col items-stretch antialiased">
        {{ $slot }}

        <!-- Custom Cachet Footer -->
        {!! $cachet_footer !!}
    </body>
</html>
