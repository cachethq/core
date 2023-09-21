<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('cachet.title', 'Cachet') }}</title>

    <!-- Scripts -->
    @routes

    @vite('resources/js/cachet.js', 'vendor/cachethq/cachet')
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-950">
    @inertia
</body>
</html>
