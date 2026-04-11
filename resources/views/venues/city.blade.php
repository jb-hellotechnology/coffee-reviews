<x-app-layout>
    <x-slot name="title">Best Coffee in {{ $cityName }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-bold text-2xl text-gray-900">
                    Best coffee in {{ $cityName }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $venues->count() }} {{ Str::plural('venue', $venues->count()) }} ·
                    ranked by Coffee Score
                </p>
            </div>
            @auth
                <a href="{{ route('venues.create') }}"
                   class="px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                    Add a venue
                </a>
            @endauth
        </div>
    </x-slot>

    {{-- SEO meta description --}}
    @push('styles')
        <meta name="description" content="Find the best coffee shops in {{ $cityName }}, ranked by coffee quality. Real reviews from coffee fans scoring espresso, bean sourcing, equipment and more.">
    @endpush

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Intro paragraph for SEO --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
                <p class="text-gray-600 leading-relaxed">
                    Looking for the best coffee in {{ $cityName }}? You've come to the right place.
                    Every coffee shop below has been reviewed by coffee fans and scored on what
                    actually matters — espresso quality, bean sourcing, equipment, milk work and more.
                    The Coffee Score is a weighted average across all these dimensions, so you can
                    trust it reflects the quality of the coffee, not just the atmosphere.
                </p>
            </div>

            @if($venues->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                    <p class="text-3xl mb-3">☕</p>
                    <p class="font-display font-bold text-xl text-gray-900">
                        No venues in {{ $cityName }} yet
                    </p>
                    <p class="text-gray-500 text-sm mt-2">
                        Know a great coffee shop here? Be the first to add it.
                    </p>
                    @auth
                        <a href="{{ route('venues.create') }}"
                           class="mt-4 inline-block px-5 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                            Add a venue
                        </a>
                    @endauth
                </div>

            @else
                <div class="space-y-4">
                    @foreach($venues as $index => $venue)
                        <a href="{{ route('venues.show', $venue) }}"
                           class="group flex items-start gap-5 bg-white rounded-2xl border border-gray-200 p-5
                                  hover:border-indigo-300 hover:shadow-md transition-all duration-200">

                            {{-- Rank --}}
                            <div class="shrink-0 w-8 text-center">
                                <span class="font-display font-bold text-xl
                                    {{ $index === 0 ? 'text-amber-500' : ($index === 1 ? 'text-gray-400' : ($index === 2 ? 'text-amber-700' : 'text-gray-300')) }}">
                                    {{ $index + 1 }}
                                </span>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <h2 class="font-display font-bold text-lg text-gray-900
                                           group-hover:text-indigo-600 transition-colors">
                                    {{ $venue->name }}
                                </h2>
                                <p class="text-sm text-gray-400 mt-0.5">{{ $venue->address }}</p>

                                {{-- Dimension bars --}}
                                @if($venue->reviews->isNotEmpty())
                                    @php
                                        $allScores = $venue->reviews->map->scores->filter();
                                    @endphp
                                    @if($allScores->isNotEmpty())
                                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-1.5">
                                            @foreach($dimensions as $field => $label)
                                                @php $avg = $allScores->whereNotNull($field)->avg($field); @endphp
                                                @if($avg)
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-gray-400 w-16 shrink-0">{{ $label }}</span>
                                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                                            <div class="bg-indigo-500 h-1.5 rounded-full"
                                                                 style="width: {{ ($avg / 5) * 100 }}%"></div>
                                                        </div>
                                                        <span class="text-xs text-gray-500 w-4">{{ number_format($avg, 1) }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                <p class="text-xs text-gray-400 mt-3">
                                    {{ $venue->review_count }} {{ Str::plural('review', $venue->review_count) }}
                                </p>
                            </div>

                            {{-- Score --}}
                            <div class="shrink-0 text-right">
                                @if($venue->coffee_score > 0)
                                    <div class="text-3xl font-display font-bold text-indigo-600 leading-none">
                                        {{ number_format($venue->coffee_score, 1) }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">/ 5.0</div>
                                @else
                                    <div class="text-xs text-gray-300">No score<br>yet</div>
                                @endif
                            </div>

                        </a>
                    @endforeach
                </div>

                {{-- Bottom SEO content --}}
                <div class="mt-10 bg-white rounded-2xl border border-gray-200 p-6 mt-8">
                    <h2 class="font-display font-bold text-lg text-gray-900 mb-3">
                        About our {{ $cityName }} coffee rankings
                    </h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Our rankings for coffee shops in {{ $cityName }} are based entirely on
                        crowd-sourced reviews from coffee enthusiasts. Unlike general review platforms,
                        every review on Coffee Shop Reviews scores specific aspects of the coffee
                        experience — from the quality of the espresso extraction to whether the venue
                        names its bean origins and roaster. The Coffee Score you see next to each venue
                        is a weighted average that prioritises the things coffee fans care about most.
                    </p>
                    <p class="text-sm text-gray-500 leading-relaxed mt-3">
                        Know a coffee shop in {{ $cityName }} that isn't listed? Add it and leave
                        the first review.
                    </p>
                    @auth
                        <a href="{{ route('venues.create') }}"
                           class="mt-4 inline-block px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                            Add a coffee shop in {{ $cityName }}
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="mt-4 inline-block px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                            Join to add a venue
                        </a>
                    @endauth
                </div>

            @endif
        </div>
    </div>
</x-app-layout>
