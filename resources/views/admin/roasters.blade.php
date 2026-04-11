<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roasters</h2>
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
                @forelse($roasters as $roaster)
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $roaster->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $roaster->city }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $roaster->venues_count }} {{ Str::plural('venue', $roaster->venues_count) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('roasters.show', $roaster) }}"
                                   class="text-xs text-indigo-600 hover:underline">
                                    View
                                </a>
                                <a href="{{ route('roasters.edit', $roaster) }}"
                                   class="px-3 py-1 !bg-indigo-600 !text-white text-xs rounded-md">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">No roasters found.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $roasters->links() }}</div>

        </div>
    </div>
</x-app-layout>
