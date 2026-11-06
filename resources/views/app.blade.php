<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Mongkol') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,400..700;1,400..700&family=Moul&family=Plus+Jakarta+Sans:ital,wght@0,400..700;1,400..700&display=swap">
        @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
        @inertiaHead
    </head>
    <body class="bg-[#FAF7F2] text-stone-900 antialiased font-sans min-h-screen selection:bg-amber-100 selection:text-amber-900">
        @inertia
    </body>
</html>