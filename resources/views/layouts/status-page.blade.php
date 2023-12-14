<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-background-light text-base-light dark:bg-background-dark dark:text-base-dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('cachet.title', 'Cachet') }}</title>

    <!-- Scripts -->
    @routes

    @vite('resources/css/cachet.css', 'vendor/cachethq/cachet')

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
<body class="antialiased">
    @yield('content')
</body>
</html>
