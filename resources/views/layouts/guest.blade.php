<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-mesh-light dark:bg-mesh-dark">
            <div class="flex flex-col items-center">
                <a href="/" class="flex items-center gap-2.5">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-purple-600 text-white shadow-lg shadow-brand-600/30">
                        <x-application-logo class="h-6 w-6" />
                    </span>
                    <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6">
                <div class="rounded-2xl bg-white px-6 py-8 shadow-card ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800 sm:px-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
