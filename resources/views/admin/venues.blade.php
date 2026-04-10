<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Venues</h2>
            <a href="{{ route('admin.index') }}" class="text-sm text-indigo-600 hover:underline">
                ← Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="space-y-3">
                @forelse($venues as $venue)
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-medium text-gray-900">{{ $venue->name }}</h3>
                                    @if(!$venue->verified)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs">
                                            Unverified
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs">
                                            Verified
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $venue->address }}, {{ $venue->city }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Added {{ $venue->created_at->diffForHumans() }}
                                    @if($venue->suggestedBy) by {{ $venue->suggestedBy->name }} @endif
                                    · {{ $venue->review_count }} {{ Str::plural('review', $venue->review_count) }}
                                    · Score: {{ number_format($venue->coffee_score, 1) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('venues.show', $venue) }}"
                                   class="text-xs text-indigo-600 hover:underline">
                                    View
                                </a>
                                @if(!$venue->verified)
                                    <form method="POST" action="{{ route('admin.venues.verify', $venue) }}">
                                        @csrf
                                        <button class="px-3 py-1 !bg-green-600 !text-white text-xs rounded-md">
                                            Verify
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.venues.delete', $venue) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($venue->name) }} and all its reviews?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 !bg-red-600 !text-white text-xs rounded-md">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">No venues found.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $venues->links() }}</div>

        </div>
    </div>
</x-app-layout>
