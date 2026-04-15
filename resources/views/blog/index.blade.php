<x-app-layout>
    <x-slot name="title">Blog</x-slot>
    <x-slot name="description">Coffee news, guides and reviews from the Coffee Shop Reviews team.</x-slot>

    <x-slot name="header">
        <h1 class="font-display font-bold text-2xl text-gray-900">Blog</h1>
        <p class="text-sm text-gray-500 mt-0.5">Coffee news, guides and insights</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(empty($posts))
                <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                    <p class="text-3xl mb-3">☕</p>
                    <p class="font-display font-bold text-xl text-gray-900">No posts yet</p>
                    <p class="text-gray-500 text-sm mt-1">Check back soon.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        @php
                            $image      = $wordpress->getFeaturedImageUrl($post);
                            $author     = $wordpress->getAuthorName($post);
                            $categories = $wordpress->getCategoryNames($post);
                        @endphp
                        <a href="{{ route('blog.show', $post['slug']) }}"
                           class="group bg-white rounded-2xl border border-gray-200 overflow-hidden
                                  hover:border-indigo-300 hover:shadow-md transition-all duration-200">

                            @if($image)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ $image }}"
                                         alt="{{ $post['title']['rendered'] }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                                </div>
                            @else
                                <div class="aspect-video bg-indigo-50 flex items-center justify-center">
                                    <span class="text-4xl">☕</span>
                                </div>
                            @endif

                            <div class="p-5">
                                @if(!empty($categories))
                                    <div class="flex gap-2 mb-2">
                                        @foreach($categories as $cat)
                                            <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                                                {{ $cat }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <h2 class="font-display font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition-colors">
                                    {!! $post['title']['rendered'] !!}
                                </h2>

                                <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-3">
                                    {!! strip_tags($post['excerpt']['rendered']) !!}
                                </p>

                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-xs text-gray-400">{{ $author }}</span>
                                    <span class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($post['date'])->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($totalPages > 1)
                    <div class="mt-8 flex justify-center gap-2">
                        @if($currentPage > 1)
                            <a href="{{ route('blog.index', ['page' => $currentPage - 1]) }}"
                               class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:border-indigo-300">
                                ← Previous
                            </a>
                        @endif
                        @for($i = 1; $i <= $totalPages; $i++)
                            <a href="{{ route('blog.index', ['page' => $i]) }}"
                               class="px-4 py-2 border rounded-lg text-sm transition-colors
                                   {{ $i === $currentPage
                                       ? '!bg-indigo-600 !text-white border-indigo-600'
                                       : 'border-gray-200 text-gray-600 hover:border-indigo-300' }}">
                                {{ $i }}
                            </a>
                        @endfor
                        @if($currentPage < $totalPages)
                            <a href="{{ route('blog.index', ['page' => $currentPage + 1]) }}"
                               class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:border-indigo-300">
                                Next →
                            </a>
                        @endif
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
