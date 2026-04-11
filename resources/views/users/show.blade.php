<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Profile header --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center gap-5">

                    {{-- Avatar --}}
                    <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-indigo-700">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-400 mt-0.5">
                            Member since {{ $user->created_at->format('F Y') }}
                        </p>
                    </div>

                    @auth
                        @if(auth()->id() === $user->id)
                            <a href="{{ route('profile.edit') }}"
                               class="text-sm text-indigo-600 hover:underline shrink-0">
                                Edit profile
                            </a>
                        @endif
                    @endauth

                </div>

                {{-- Stats row --}}
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4 pt-5 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-600">
                            {{ $stats['total_reviews'] }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ Str::plural('review', $stats['total_reviews']) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-600">
                            {{ $stats['venues_reviewed'] }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ Str::plural('venue', $stats['venues_reviewed']) }} visited
                        </p>
                    </div>
                    <div class="text-center col-span-2 sm:col-span-1">
                        <p class="text-2xl font-bold text-indigo-600">
                            {{ $stats['avg_espresso'] ? number_format($stats['avg_espresso'], 1) : '–' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">avg espresso score</p>
                    </div>
                </div>

                {{-- Top tags --}}
                @if($stats['top_tags']->isNotEmpty())
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-500 mb-2">Frequently mentions</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($stats['top_tags'] as $tag)
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs text-gray-600">
                                    {{ $tag->name }}
                                    <span class="text-gray-400">{{ $tag->reviews_count }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Reviews --}}
            <div>
                <h2 class="text-base font-medium text-gray-900 mb-3 mt-8">Reviews</h2>

                @if($reviews->isEmpty())
                    <div class="bg-white rounded-lg border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">No reviews yet.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            <div class="bg-white rounded-lg border border-gray-200 p-5">

                                {{-- Venue + date --}}
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <a href="{{ route('venues.show', $review->venue) }}"
                                           class="font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $review->venue->name }}
                                        </a>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $review->venue->city }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400 shrink-0">
                                        {{ $review->created_at->format('d M Y') }}
                                    </span>
                                </div>

                                {{-- Scores --}}
                                @if($review->scores)
                                    @php
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
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @foreach($dimensions as $field => $label)
                                            @if($review->scores->$field)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                                    {{ $label }}
                                                    <span class="font-medium">
                                                        {{ $review->scores->$field }}/5
                                                    </span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Body --}}
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $review->body }}
                                </p>

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

                                {{-- Footer --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                                    <a href="{{ route('venues.show', $review->venue) }}"
                                       class="text-xs text-indigo-600 hover:underline">
                                        View venue
                                    </a>
                                    @if($review->venue->coffee_score > 0)
                                        <span class="text-xs text-gray-400">
                                            Venue score
                                            <span class="font-medium text-indigo-600">
                                                {{ number_format($review->venue->coffee_score, 1) }}
                                            </span>
                                        </span>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
