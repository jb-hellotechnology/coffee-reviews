<x-app-layout>
    <x-slot name="title" :value="strip_tags($post['title']['rendered'])"></x-slot>
    <x-slot name="description">{{ strip_tags(Str::limit($post['excerpt']['rendered'], 160)) }}</x-slot>

    @push('styles')
        <style>
            .wp-content h2 { font-size: 1.5rem; font-weight: 700; margin: 2rem 0 1rem; color: #111827; }
            .wp-content h3 { font-size: 1.25rem; font-weight: 600; margin: 1.5rem 0 0.75rem; color: #111827; }
            .wp-content p  { margin-bottom: 1.25rem; color: #374151; line-height: 1.75; }
            .wp-content ul, .wp-content ol { margin: 1rem 0 1.25rem 1.5rem; color: #374151; }
            .wp-content ul { list-style-type: disc; }
            .wp-content ol { list-style-type: decimal; }
            .wp-content li { margin-bottom: 0.4rem; line-height: 1.75; }
            .wp-content a  { color: #4f46e5; text-decoration: underline; }
            .wp-content img { border-radius: 12px; margin: 1.5rem 0; max-width: 100%; }
            .wp-content blockquote { border-left: 3px solid #4f46e5; padding-left: 1rem; margin: 1.5rem 0; color: #6b7280; font-style: italic; }
            .wp-content figure { margin: 1.5rem 0; }
            .wp-content figcaption { text-align: center; font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem; }
        </style>
    @endpush

    @php
        $image      = $wordpress->getFeaturedImageUrl($post);
        $author     = $wordpress->getAuthorName($post);
        $categories = $wordpress->getCategoryNames($post);
    @endphp

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Back link --}}
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:underline mb-6">
                ← Back to blog
            </a>

            <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden">

                {{-- Featured image --}}
                @if($image)
                    <div class="aspect-video overflow-hidden">
                        <img src="{{ $image }}"
                             alt="{!! $post['title']['rendered'] !!}"
                             class="w-full h-full object-cover"/>
                    </div>
                @endif

                <div class="p-8">

                    {{-- Categories --}}
                    @if(!empty($categories))
                        <div class="flex gap-2 mb-4">
                            @foreach($categories as $cat)
                                <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                                    {{ $cat }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Title --}}
                    <h1 class="font-display font-bold text-3xl text-gray-900 leading-tight mb-4">
                        {!! $post['title']['rendered'] !!}
                    </h1>

                    {{-- Meta --}}
                    <div class="flex items-center gap-3 text-sm text-gray-400 mb-8 pb-8 border-b border-gray-100">
                        <span>By {{ $author }}</span>
                        <span>·</span>
                        <span>{{ \Carbon\Carbon::parse($post['date'])->format('d F Y') }}</span>
                        @if($post['modified'] !== $post['date'])
                            <span>·</span>
                            <span>Updated {{ \Carbon\Carbon::parse($post['modified'])->format('d F Y') }}</span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="wp-content">
                        {!! $post['content']['rendered'] !!}
                    </div>

                </div>
            </article>

            {{-- Related posts --}}
            @if(!empty($related))
                <div class="mt-10">
                    <h2 class="font-display font-bold text-xl text-gray-900 mb-4">More from the blog</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($related as $relatedPost)
                            @php $relatedImage = $wordpress->getFeaturedImageUrl($relatedPost); @endphp
                            <a href="{{ route('blog.show', $relatedPost['slug']) }}"
                               class="group bg-white rounded-2xl border border-gray-200 overflow-hidden
                                      hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                                @if($relatedImage)
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ $relatedImage }}"
                                             alt="{!! $relatedPost['title']['rendered'] !!}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-display font-bold text-gray-900 group-hover:text-indigo-600 transition-colors leading-tight">
                                        {!! $relatedPost['title']['rendered'] !!}
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($relatedPost['date'])->format('d M Y') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
