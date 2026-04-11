<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title . ' — Coffee Shop Reviews' : 'Coffee Shop Reviews' }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white border-b border-gray-200">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="border-t border-gray-200 bg-white mt-12">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">☕</span>
                            <span class="font-display font-bold text-gray-900 text-sm tracking-tight">
                                Coffee Shop Reviews
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">
                            Finding the best coffee, one cup at a time.
                        </p>
                        <div class="flex gap-4 text-xs text-gray-400">
                            <a href="{{ route('venues.index') }}" class="hover:text-gray-600">Coffee shops</a>
                            <a href="{{ route('venues.map') }}" class="hover:text-gray-600">Map</a>
                            @auth
                                <a href="{{ route('venues.create') }}" class="hover:text-gray-600">Add a venue</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
