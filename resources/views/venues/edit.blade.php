<x-app-layout>
    <x-slot name="title">Edit {{ $venue->name }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-2xl text-gray-900">
                Edit {{ $venue->name }}
            </h2>
            <a href="{{ route('venues.show', $venue) }}"
               class="text-sm text-indigo-600 hover:underline">
                ← Back to venue
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="rounded-2xl bg-green-50 border border-green-200 p-4 mb-6">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <form method="POST" action="{{ route('venues.update', $venue) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" name="name" value="{{ old('name', $venue->name) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                            <input type="text" name="address" value="{{ old('address', $venue->address) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="city" value="{{ old('city', $venue->city) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                                @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                                <input type="text" name="postcode" value="{{ old('postcode', $venue->postcode) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $venue->phone) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" name="website" value="{{ old('website', $venue->website) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                                @error('website') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Roaster</label>
                            <select name="roaster_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Unknown / not listed</option>
                                @foreach($roasters as $roaster)
                                    <option value="{{ $roaster->id }}"
                                        {{ old('roaster_id', $venue->roaster_id) == $roaster->id ? 'selected' : '' }}>
                                        {{ $roaster->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($venue->lat && $venue->lng)
                            <p class="text-xs text-gray-400">
                                Coordinates: {{ $venue->lat }}, {{ $venue->lng }}
                            </p>
                        @endif

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
