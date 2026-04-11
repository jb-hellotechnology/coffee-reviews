<x-app-layout>
    <x-slot name="title">{{ $roaster->name }} — Coffee Roaster</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-bold text-2xl text-gray-900">{{ $roaster->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $roaster->city }}</p>
            </div>
            @if($roaster->website)
                <a href="{{ $roaster->website }}" target="_blank"
                   class="px-4 py-2 border border-gray-200 text-sm font-medium text-gray-600 rounded-lg hover:border-indigo-300 hover:text-indigo-600 transition-colors">
                    Visit website
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- About --}}
            @if($roaster->description)
                <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8">
                    <h2 class="font-display font-bold text-lg text-gray-900 mb-3">About</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $roaster->description }}</p>
                </div>
            @endif

            {{-- Venues --}}
            <div>
                <h2 class="font-display font-bold text-lg text-gray-900 mb-4">
                    Venues serving {{ $roaster->name }} coffee
                </h2>

                @if($roaster->venues->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">
                            No venues linked to this roaster yet.
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($roaster->venues as $venue)
                            <a href="{{ route('venues.show', $venue) }}"
                               class="group flex items-center justify-between bg-white rounded-2xl border border-gray-200 p-5
                                      hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                                <div>
                                    <h3 class="font-display font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $venue->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-0.5">
                                        {{ $venue->city }}
                                        @if($venue->address) · {{ $venue->address }} @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $venue->review_count }} {{ Str::plural('review', $venue->review_count) }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($venue->coffee_score > 0)
                                        <div class="text-2xl font-display font-bold text-indigo-600">
                                            {{ number_format($venue->coffee_score, 1) }}
                                        </div>
                                        <div class="text-xs text-gray-400">coffee score</div>
                                    @else
                                        <div class="text-xs text-gray-300">No score<br>yet</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
