<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reviews</h2>
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
                @forelse($reviews as $review)
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <a href="{{ route('venues.show', $review->venue) }}"
                                       class="font-medium text-gray-900 hover:text-indigo-600">
                                        {{ $review->venue->name }}
                                    </a>
                                    @if(!$review->verified)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs">
                                            Unverified
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs">
                                            Verified
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mb-2">
                                    by {{ $review->user->name }}
                                    · {{ $review->created_at->diffForHumans() }}
                                </p>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ Str::limit($review->body, 200) }}
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 shrink-0">
                                @if(!$review->verified)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        <button class="w-full px-3 py-1 !bg-green-600 !text-white text-xs rounded-md">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.delete', $review) }}"
                                      onsubmit="return confirm('Delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full px-3 py-1 !bg-red-600 !text-white text-xs rounded-md">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">No reviews found.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $reviews->links() }}</div>

        </div>
    </div>
</x-app-layout>
