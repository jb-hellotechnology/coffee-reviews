<x-app-layout>
    <x-slot name="title">Edit review</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-2xl text-gray-900">Edit review</h2>
            <a href="{{ route('venues.show', $review->venue) }}"
               class="text-sm text-indigo-600 hover:underline">
                ← Back to {{ $review->venue->name }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-gray-200 p-6">

                <div class="mb-5 pb-5 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-700">
                        Review of
                        <a href="{{ route('venues.show', $review->venue) }}"
                           class="text-indigo-600 hover:underline">
                            {{ $review->venue->name }}
                        </a>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Originally submitted {{ $review->created_at->format('d M Y') }}
                    </p>
                </div>

                {{-- Show existing scores read-only --}}
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
                    <div class="mb-5">
                        <p class="text-xs font-medium text-gray-500 mb-2">
                            Scores (not editable)
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($dimensions as $field => $label)
                                @if($review->scores->$field)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                        {{ $label }}
                                        <span class="font-medium">{{ $review->scores->$field }}/5</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('reviews.update', $review) }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Review text
                        </label>
                        <textarea name="body" rows="8"
                                  class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('body', $review->body) }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            @error('body')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-xs text-gray-400">Minimum 30 characters</p>
                            @enderror
                            <p class="text-xs text-gray-400">{{ strlen(old('body', $review->body)) }} / 2000</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                class="px-6 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg hover:!bg-indigo-700">
                            Save changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
