<div>
    {{-- Hero --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h1 class="font-display font-bold text-5xl sm:text-6xl text-gray-900 leading-tight tracking-tight">
                Find the best<br>
                <span class="text-indigo-600">coffee</span> near you.
            </h1>
            <p class="mt-4 text-lg text-gray-500 max-w-xl">
                Crowd-sourced reviews from coffee fans — scored on espresso, bean sourcing,
                equipment and more. Not just another café directory.
            </p>

            {{-- Search --}}
            <div class="mt-8 flex gap-3 flex-col sm:flex-row max-w-2xl">
                <div class="flex-1 relative">
                    <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="query"
                        placeholder="Search by name, town or tag…"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border-gray-200 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>
                <select
                    wire:model.live="city"
                    class="rounded-lg border-gray-200 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 py-3">
                    <option value="">All cities</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <select
                    wire:model.live="sortBy"
                    class="rounded-lg border-gray-200 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 py-3">
                    <option value="score">Highest score</option>
                    <option value="recent">Most recent</option>
                    <option value="most">Most reviewed</option>
                </select>
            </div>

            @if($query)
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-sm text-gray-500">
                        Results for <span class="font-semibold text-gray-900">"{{ $query }}"</span>
                    </span>
                    <button wire:click="$set('query', '')"
                            class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                        ✕ Clear
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Results --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display font-bold text-2xl text-gray-900">
                @if($query)
                    Results for "{{ $query }}"
                @elseif($city)
                    Coffee shops in {{ $city }}
                @else
                    Top rated coffee shops
                @endif
            </h2>
            @if($query || $city)
                <button wire:click="$set('query', ''); $set('city', '')"
                        class="text-sm text-indigo-600 hover:underline">
                    Clear filters
                </button>
            @endif
        </div>

        <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity duration-150">

            @if($venues->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                    <p class="text-3xl mb-3">☕</p>
                    <p class="font-display font-bold text-xl text-gray-900">No venues found</p>
                    <p class="text-gray-500 text-sm mt-1">
                        @if($query) for "{{ $query }}" @endif
                        @if($city) in {{ $city }} @endif
                    </p>
                    @if($query || $city)
                        <button wire:click="$set('query', ''); $set('city', '')"
                                class="mt-4 text-sm text-indigo-600 hover:underline">
                            Clear filters
                        </button>
                    @endif
                </div>

            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($venues as $venue)
                        <a href="{{ route('venues.show', $venue) }}"
                           class="group bg-white rounded-2xl border border-gray-200 p-6
                                  hover:border-indigo-300 hover:shadow-md transition-all duration-200">

                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="min-w-0">
                                    <h3 class="font-display font-bold text-lg text-gray-900 leading-tight
                                               group-hover:text-indigo-600 transition-colors truncate">
                                        {{ $venue->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-0.5">
                                        {{ $venue->city }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if($venue->coffee_score > 0)
                                        <div class="text-3xl font-display font-bold text-indigo-600 leading-none">
                                            {{ number_format($venue->coffee_score, 1) }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">/ 5.0</div>
                                    @else
                                        <div class="text-xs text-gray-300 font-medium">No score<br>yet</div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">
                                    {{ $venue->review_count }}
                                    {{ Str::plural('review', $venue->review_count) }}
                                </span>
                                @if($venue->coffee_score > 0)
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="w-5 h-1.5 rounded-full {{ $venue->coffee_score >= $i ? 'bg-indigo-500' : ($venue->coffee_score >= $i - 0.5 ? 'bg-indigo-200' : 'bg-gray-100') }}"></div>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $venues->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- How it works --}}
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="font-display font-bold text-2xl text-gray-900 mb-8">
                Reviews built for coffee lovers
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['☕', 'Espresso quality',    'Scored on extraction, crema, balance and consistency — not just whether it tastes nice.'],
                    ['🌱', 'Bean sourcing',        'Does the café name their roaster? Single origin? Seasonal rotation? Coffee fans want to know.'],
                    ['🥛', 'Milk work',            'Texture, temperature and latte art — because a flat white lives or dies by its milk.'],
                    ['🔧', 'Equipment',            'The grinder matters as much as the machine. Our reviewers notice the difference.'],
                    ['🫗', 'Filter options',       'Pour-over, AeroPress, batch brew — we track which cafés take filter seriously.'],
                    ['💬', 'Barista knowledge',    'Can they explain the coffee? The best baristas are as passionate as you are.'],
                    ['🌿', 'Decaf availability',   'Quality decaf is rare. We help you find the cafés that take it seriously.'],
                    ['💷', 'Value',                'Great coffee at a fair price. We score value relative to quality, not just cost.'],
                ] as [$icon, $title, $desc])
                    <div class="bg-white rounded-2xl border border-gray-200 p-5">
                        <div class="text-2xl mb-3">{{ $icon }}</div>
                        <h3 class="font-display font-bold text-gray-900 mb-1">{{ $title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SEO footer content --}}
    <div class="border-t border-gray-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                <div>
                    <h2 class="font-display font-bold text-2xl text-gray-900 mb-4">
                        Coffee reviews for coffee fans
                    </h2>
                    <p class="text-gray-500 leading-relaxed mb-4">
                        Most review platforms treat a coffee shop the same as a burger joint.
                        A single star rating tells you nothing about whether the espresso was
                        over-extracted, whether the barista could name the farm their beans
                        came from, or whether the grinder is worth the price on the menu.
                    </p>
                    <p class="text-gray-500 leading-relaxed">
                        Coffee Shop Reviews is built by coffee fans, for coffee fans. Every review
                        scores the things that actually matter — from bean sourcing and
                        equipment to milk texture and filter options. The result is a
                        Coffee Score you can actually trust.
                    </p>
                </div>

                <div>
                    <h2 class="font-display font-bold text-2xl text-gray-900 mb-4">
                        Join the community
                    </h2>
                    <p class="text-gray-500 leading-relaxed mb-4">
                        Every review you leave helps other coffee lovers find exceptional
                        coffee in their town. Add venues that aren't listed yet, score the
                        dimensions you experienced, and write about what made the coffee
                        stand out — or fall flat.
                    </p>
                    <p class="text-gray-500 leading-relaxed mb-6">
                        The more specific your review, the more useful it is. Did they use
                        an EK43? Was it a natural process Ethiopian? Did the barista explain
                        the extraction? These details matter to people who care about coffee.
                    </p>
                    @guest
                        <div class="flex gap-3">
                            <a href="{{ route('register') }}"
                               class="px-5 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg hover:!bg-indigo-700 transition-colors">
                                Create an account
                            </a>
                            <a href="{{ route('login') }}"
                               class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:border-gray-300 transition-colors">
                                Sign in
                            </a>
                        </div>
                    @else
                        <a href="{{ route('venues.create') }}"
                           class="px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                            Add a coffee shop
                        </a>
                    @endguest
                </div>

            </div>
        </div>
    </div>

</div>
