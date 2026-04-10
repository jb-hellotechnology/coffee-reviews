<x-app-layout>
    <x-slot name="title">{{ $venue->name }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $venue->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $venue->address }}</p>
                <p class="text-sm text-gray-500 mt-0.5"><a href="{{ $venue->website }}" class="font-medium text-fg-brand underline hover:no-underline">{{ $venue->website }}</a></p>
            </div>
            @auth
                @if(App\Models\Review::userCanReviewVenue(auth()->user(), $venue))
                    <a href="{{ route('reviews.create', $venue) }}"
                       class="px-4 py-2 !bg-indigo-600 !text-white text-sm rounded-md">
                        Write a review
                    </a>
                @else
                    @php $nextDate = App\Models\Review::userNextReviewDate(auth()->user(), $venue); @endphp
                    <span class="text-sm text-gray-400">
                        You can review again after {{ $nextDate->format('j F Y') }}
                    </span>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Score summary card --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-indigo-600">
                            {{ number_format($venue->coffee_score, 1) }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">coffee score</div>
                        <div class="text-xs text-gray-400">
                            {{ $venue->review_count }}
                            {{ Str::plural('review', $venue->review_count) }}
                        </div>
                    </div>

                    {{-- Per-dimension averages --}}
                    @php
                        $allScores = $venue->reviews->map->scores->filter();
                        $dimensions = [
                            'espresso'          => 'Espresso',
                            'milk_work'         => 'Milk work',
                            'filter_options'    => 'Filter',
                            'bean_sourcing'     => 'Bean sourcing',
                            'barista_knowledge' => 'Barista knowledge',
                            'equipment'         => 'Equipment',
                            'decaf_available'   => 'Decaf',
                            'value'             => 'Value',
                        ];
                    @endphp

                    @if($allScores->isNotEmpty())
                        <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-2">
                            @foreach($dimensions as $field => $label)
                                @php
                                    $avg = $allScores->whereNotNull($field)->avg($field);
                                @endphp
                                @if($avg)
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs text-gray-500">{{ $label }}</span>
                                        <div class="flex items-center gap-1">
                                            <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                                <div class="bg-amber-400 h-1.5 rounded-full"
                                                     style="width: {{ ($avg / 5) * 100 }}%"></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-700 w-5">
                                                {{ number_format($avg, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if(session('review_blocked'))
                <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 mb-6">
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                        <p class="text-sm text-amber-800">{{ session('review_blocked') }}</p>
                    </div>
                </div>
            @endif

            {{-- Reviews --}}
            <div>
                <h3 class="text-base font-medium text-gray-900 mb-3">Reviews</h3>

                @forelse($venue->reviews->sortByDesc('created_at') as $review)
                    <div class="bg-white rounded-lg border border-gray-200 p-5 mb-3">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-indigo-700">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <a href="{{ route('users.show', $review->user) }}"
                                   class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                                    {{ $review->user->name }}
                                </a>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ $review->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Scores for this review --}}
                        @if($review->scores)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($dimensions as $field => $label)
                                    @if($review->scores->$field)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                            {{ $label }}
                                            <span class="font-medium">{{ $review->scores->$field }}/5</span>
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <p class="text-sm text-gray-700 leading-relaxed">{{ $review->body }}</p>

                        {{-- AI tags --}}
                        @if($review->ai_tags && count($review->ai_tags) > 0)
                            <div class="flex flex-wrap gap-1 mt-3">
                                @foreach($review->ai_tags as $tag)
                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs text-gray-500">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                        <p class="text-gray-500 text-sm">No reviews yet.</p>
                        @auth
                            <a href="{{ route('reviews.create', $venue) }}"
                               class="mt-2 inline-block text-indigo-600 text-sm underline">
                                Be the first to review
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
