<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My reviews
        </h2>
        <div class="mb-6">
            <a href="{{ route('users.show', auth()->user()) }}"
               class="text-sm text-indigo-600 hover:underline">
                View my public profile
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if($reviews->isEmpty())
                <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">You haven't reviewed any coffee shops yet.</p>
                    <a href="{{ route('venues.index') }}"
                       class="mt-4 inline-block text-indigo-600 text-sm underline">
                        Find a coffee shop to review
                    </a>
                </div>

            @else
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-lg border border-gray-200 p-5">

                            {{-- Venue name + date --}}
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

                            @if($review->photo && $review->photoUrl())
                                <div class="mb-3 rounded-lg overflow-hidden">
                                    <img src="{{ $review->photoUrl() }}"
                                         alt="{{ $review->photo_alt ?? 'Review photo' }}"
                                         class="w-full max-h-48 object-cover rounded-lg"/>
                                    @if(!$review->photo_analysed)
                                        <p class="text-xs text-gray-400 mt-1">Photo pending moderation.</p>
                                    @endif
                                </div>
                            @endif

                            {{-- Review body --}}
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

                            {{-- Footer: link to venue --}}
                            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                                <a href="{{ route('venues.show', $review->venue) }}"
                                   class="text-xs text-indigo-600 hover:underline">
                                    View venue
                                </a>
                                <div class="flex items-center gap-3">
                                    @if($review->venue->coffee_score > 0)
                                        <span class="text-xs text-gray-400">
                                            Venue score
                                            <span class="font-medium text-indigo-600">
                                                {{ number_format($review->venue->coffee_score, 1) }}
                                            </span>
                                        </span>
                                    @endif
                                    <a href="{{ route('reviews.edit', $review) }}"
                                       class="text-xs text-indigo-600 hover:underline">
                                        Edit
                                    </a>
                                </div>
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
</x-app-layout>
