<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{!! isset($title) ? e(html_entity_decode($title, ENT_QUOTES, 'UTF-8')) . ' — Coffee Shop Reviews' : 'Coffee Shop Reviews - Find the Best Coffee Near You' !!}</title>
        <meta name="description" content="{{ $description ?? 'Find the best coffee shops near you. Crowd-sourced reviews from coffee fans scored on espresso, bean sourcing, equipment and more.' }}">
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

            {{-- Newsletter signup --}}
            <div class="border-t border-gray-200 bg-indigo-600">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div>
                            <h3 class="font-display font-bold text-white text-lg">
                                Stay in the loop ☕
                            </h3>
                            <p class="text-indigo-200 text-sm mt-1">
                                New venues, coffee guides and platform updates — straight to your inbox.
                            </p>
                        </div>

                        @if(session('newsletter_success'))
                            <p class="text-white font-medium text-sm">
                                ✓ {{ session('newsletter_success') }}
                            </p>
                        @elseif(session('newsletter_error'))
                            <p class="text-red-200 text-sm">{{ session('newsletter_error') }}</p>
                        @else
                            <form method="POST" action="{{ route('newsletter.subscribe') }}"
                                  class="flex gap-2 w-full sm:w-auto">
                                @csrf
                                <input type="email" name="email" placeholder="your@email.com" required
                                       class="flex-1 sm:w-64 rounded-lg border-0 text-sm px-4 py-2.5
                                              focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600"/>
                                <button type="submit"
                                        class="px-4 py-2.5 bg-white !text-indigo-600 text-sm font-medium
                                               rounded-lg hover:bg-indigo-50 transition-colors shrink-0">
                                    Subscribe
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Existing footer --}}
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
                            <a href="{{ route('about') }}" class="hover:text-gray-600">About</a>
                            <a href="{{ route('blog.index') }}" class="hover:text-gray-600">Blog</a>
                            <a href="{{ route('privacy') }}" class="hover:text-gray-600">Privacy policy</a>
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

        <!-- Fathom - beautiful, simple website analytics -->
        <script src="https://cdn.usefathom.com/script.js" data-site="OSJCOSOY" defer></script>
        <!-- / Fathom -->
    </body>
</html>
