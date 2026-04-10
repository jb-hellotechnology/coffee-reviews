<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-2xl text-gray-900">Coffee shops</h2>
            @auth
                <a href="{{ route('venues.create') }}"
                   class="px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg">
                    Add a venue
                </a>
            @endauth
        </div>
    </x-slot>

    @livewire('venue-search')
</x-app-layout>
