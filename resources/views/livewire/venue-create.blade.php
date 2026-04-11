<div class="max-w-2xl mx-auto">

    @if($saved)
        <div class="rounded-lg bg-green-50 p-6 text-center">
            <p class="text-green-800 font-medium text-lg">Venue added successfully!</p>
            <p class="text-green-600 mt-1 text-sm">Thank you for contributing to the platform.</p>
            <button wire:click="$set('saved', false)" class="mt-4 text-sm text-green-700 underline">
                Add another venue
            </button>
        </div>

    @else

        {{-- Step 1: Google Maps URL --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h2 class="text-lg font-medium text-gray-900 mb-1">Find on Google Maps</h2>
            <p class="text-sm text-gray-500 mb-4">
                Paste a Google Maps link to auto-fill the venue details, or
                <button wire:click="skipLookup" class="text-indigo-600 underline">fill in manually</button>.
            </p>

            <div class="flex gap-2">
                <input
                    type="url"
                    wire:model="mapsUrl"
                    placeholder="https://maps.google.com/..."
                    class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                <button
                    wire:click="lookupFromUrl"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 !bg-indigo-600 !text-white text-sm rounded-md hover:!bg-indigo-700 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="lookupFromUrl">Look up</span>
                    <span wire:loading wire:target="lookupFromUrl">Looking up...</span>
                </button>
            </div>

            @if($lookupError)
                <p class="mt-2 text-sm text-red-600">{{ $lookupError }}</p>
            @endif
        </div>

        {{-- Step 2: Venue form (shown after lookup or manual skip) --}}
        @if($formVisible)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Venue details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" wire:model="name"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                    <input type="text" wire:model="address"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" wire:model="city"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                        @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                        <input type="text" wire:model="postcode"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" wire:model="phone"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" wire:model="website"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                        @error('website') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Hidden lat/lng for debugging during development --}}
                @if($lat && $lng)
                    <p class="text-xs text-gray-400">Coordinates: {{ $lat }}, {{ $lng }}</p>
                @endif

            </div>

            @if($formVisible)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Roaster (optional)</label>
                    <select wire:model="roasterId"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Unknown / not listed</option>
                        @foreach($roasters as $roaster)
                            <option value="{{ $roaster->id }}">{{ $roaster->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">
                        Can't find the roaster?
                        <a href="{{ route('roasters.create') }}" class="text-indigo-600 hover:underline">Add them first</a>
                        then come back.
                    </p>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-md hover:!bg-indigo-700 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">Add venue</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
        @endif

    @endif
</div>
