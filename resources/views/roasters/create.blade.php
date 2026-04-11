<x-app-layout>
    <x-slot name="title">Add a roaster</x-slot>

    <x-slot name="header">
        <h2 class="font-display font-bold text-2xl text-gray-900">Add a roaster</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @livewire('roaster-create')
        </div>
    </div>
</x-app-layout>
