<x-app-layout>
    <x-slot name="title">About Coffee Shop Reviews - Find the Best Coffee Near You</x-slot>
    <x-slot name="description">The story behind Coffee Shop Reviews — a specialist coffee review platform built by coffee fans, for coffee fans.</x-slot>

    <x-slot name="header">
        <h1 class="font-display font-bold text-2xl text-gray-900">About Coffee Shop Reviews</h1>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Founder story --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <h2 class="font-display font-bold text-2xl text-gray-900 mb-4">
                    Built by a coffee fan, for coffee fans
                </h2>
                <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed space-y-4">
                    <p>
                        If you've ever searched for "best coffee in [town]" and ended up with a list of
                        chain cafés ranked by people who mostly commented on the WiFi speed, you'll
                        understand why Coffee Shop Reviews exists.
                    </p>
                    <p>
                        General review platforms weren't built with coffee in mind. A single star rating
                        tells you nothing about whether the espresso was over-extracted, whether the
                        barista could name the farm their beans came from, or whether the grinder is
                        worth the price on the menu. TripAdvisor doesn't care about natural process
                        Ethiopians. Google Reviews doesn't ask about latte art.
                    </p>
                    <p>
                        We do.
                    </p>
                    <p>
                        Coffee Shop Reviews is a crowd-sourced platform built specifically for people
                        who think seriously about coffee. Every review scores the things that actually
                        matter — espresso quality, bean sourcing, equipment, milk work, barista
                        knowledge, filter options and value. The result is a Coffee Score you can
                        actually trust, because it's built by people who care about the same things
                        you do.
                    </p>
                </div>
            </div>

            {{-- How it works --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <h2 class="font-display font-bold text-xl text-gray-900 mb-5">How it works</h2>
                <div class="space-y-5">
                    @foreach([
                        ['🔍', 'Find',    'Search for coffee shops by town, city or tag. Filter by score, browse the map, or explore our city guides.'],
                        ['⭐', 'Review',  'Leave a detailed review scoring the coffee across eight specialist dimensions. Add a photo of your cup.'],
                        ['🤖', 'Analyse', 'Our AI analyses every review to extract coffee-specific tags — Ethiopian, AeroPress, La Marzocca — and adds them to the venue profile.'],
                        ['📊', 'Score',   'The Coffee Score is a weighted average across all dimensions, prioritising the things coffee fans care about most.'],
                        ['🫘', 'Discover','Find roasters supplying your favourite venues. Explore the roaster directory and see where their beans end up.'],
                    ] as [$emoji, $title, $desc])
                        <div class="flex gap-4">
                            <div class="text-2xl shrink-0 w-10 text-center">{{ $emoji }}</div>
                            <div>
                                <h3 class="font-display font-bold text-gray-900">{{ $title }}</h3>
                                <p class="text-sm text-gray-500 mt-0.5 leading-relaxed">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- The Coffee Score --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <h2 class="font-display font-bold text-xl text-gray-900 mb-3">The Coffee Score</h2>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Not all dimensions are weighted equally. Espresso quality and bean sourcing
                    carry the most weight — because they're the most reliable indicators of a
                    café that takes coffee seriously. The full weighting:
                </p>
                <div class="space-y-2">
                    @foreach([
                        ['Espresso quality',    '2.0'],
                        ['Bean sourcing',       '1.8'],
                        ['Filter options',      '1.5'],
                        ['Milk work',           '1.2'],
                        ['Barista knowledge',   '1.2'],
                        ['Equipment',           '1.0'],
                        ['Value',               '0.8'],
                        ['Decaf availability',  '0.5'],
                    ] as [$label, $weight])
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 w-44 shrink-0">{{ $label }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full"
                                     style="width: {{ ($weight / 2.0) * 100 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-6 text-right">{{ $weight }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Community --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <h2 class="font-display font-bold text-xl text-gray-900 mb-3">Join the community</h2>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Every review you leave helps other coffee lovers find exceptional coffee.
                    Add venues that aren't listed yet, score the dimensions you experienced,
                    write about what made the coffee stand out — or fall flat.
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    The more specific your review, the more useful it is. Did they use an EK43?
                    Was it a natural process Ethiopian? Did the barista explain the extraction?
                    These details matter to people who care about coffee — and that's exactly
                    who reads reviews here.
                </p>
                @guest
                    <div class="flex gap-3">
                        <a href="{{ route('register') }}"
                           class="px-5 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg hover:!bg-indigo-700 transition-colors">
                            Create an account
                        </a>
                        <a href="{{ route('venues.index') }}"
                           class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:border-gray-300 transition-colors">
                            Browse coffee shops
                        </a>
                    </div>
                @else
                    <a href="{{ route('venues.index') }}"
                       class="inline-block px-5 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg hover:!bg-indigo-700 transition-colors">
                        Browse coffee shops
                    </a>
                @endguest
            </div>

        </div>
    </div>
</x-app-layout>
