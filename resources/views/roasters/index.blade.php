<x-app-layout>
    <x-slot name="title">Coffee Roasters Directory</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-bold text-2xl text-gray-900">Coffee roasters</h1>
                <p class="text-sm text-gray-500 mt-0.5">Roasteries whose coffee you can find in our reviewed venues</p>
            </div>
            @auth
                <a href="{{ route('roasters.create') }}"
                   class="px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                    Add a roaster
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            @if($roasters->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                    <p class="text-3xl mb-3">🫘</p>
                    <p class="font-display font-bold text-xl text-gray-900">No roasters yet</p>
                    <p class="text-gray-500 text-sm mt-1">Add the first roaster to the directory.</p>
                    @auth
                        <a href="{{ route('roasters.create') }}"
                           class="mt-4 inline-block px-5 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                            Add a roaster
                        </a>
                    @endauth
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($roasters as $roaster)
                        <a href="{{ route('roasters.show', $roaster) }}"
                           class="group bg-white rounded-2xl border border-gray-200 p-5
                                  hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="font-display font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">
                                        {{ $roaster->name }}
                                    </h2>
                                    <p class="text-sm text-gray-400 mt-0.5">{{ $roaster->city }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <div class="text-xl font-display font-bold text-indigo-600">
                                        {{ $roaster->venues_count }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ Str::plural('venue', $roaster->venues_count) }}
                                    </div>
                                </div>
                            </div>
                            @if($roaster->description)
                                <p class="text-sm text-gray-500 mt-3 leading-relaxed line-clamp-2">
                                    {{ $roaster->description }}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">{{ $roasters->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
