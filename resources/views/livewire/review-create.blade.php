<div class="max-w-2xl mx-auto">

    @if($saved)
        <div class="rounded-lg bg-green-50 border border-green-200 p-6 text-center">
            <p class="text-green-800 font-medium text-lg">Review submitted!</p>
            <p class="text-green-600 mt-1 text-sm">Thanks for helping the coffee community.</p>
            <a href="{{ route('venues.show', $venue) }}"
               class="mt-4 inline-block text-sm text-green-700 underline">
                Back to {{ $venue->name }}
            </a>
        </div>

    @else

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h2 class="text-lg font-medium text-gray-900">
                Reviewing <span class="text-indigo-600">{{ $venue->name }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ $venue->address }}</p>
        </div>

        {{-- Score dimensions --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h3 class="text-base font-medium text-gray-900 mb-1">Rate the coffee</h3>
            <p class="text-sm text-gray-500 mb-5">
                Score only what you tried — leave anything you didn't have blank.
            </p>

            <div class="space-y-4">
                @foreach([
                    'espresso'          => ['Espresso',          'Balance, extraction, crema'],
                    'milk_work'         => ['Milk work',         'Texture, temperature, latte art'],
                    'filter_options'    => ['Filter / batch',    'Pour-over, AeroPress, batch brew'],
                    'bean_sourcing'     => ['Bean sourcing',     'Single origin, roaster named'],
                    'barista_knowledge' => ['Barista knowledge', 'Can they talk about the coffee?'],
                    'equipment'         => ['Equipment',         'Grinder and espresso machine quality'],
                    'decaf_available'   => ['Decaf',             'Quality decaf option available'],
                    'value'             => ['Value',             'Price relative to quality'],
                ] as $field => [$label, $hint])

                <div class="flex items-center justify-between gap-4">
                    <div class="w-44 shrink-0">
                        <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
                        <p class="text-xs text-gray-400">{{ $hint }}</p>
                    </div>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button
                                wire:click="setScore('{{ $field }}', {{ $i }})"
                                title="{{ $i }}"
                                class="w-8 h-8 rounded text-lg leading-none transition-colors
                                    {{ $this->$field >= $i
                                        ? '!bg-amber-400 !text-white'
                                        : '!bg-gray-100 !text-gray-300 hover:!bg-amber-100' }}"
                            >★</button>
                        @endfor
                        @if($this->$field)
                            <button
                                wire:click="setScore('{{ $field }}', {{ $this->$field }})"
                                class="ml-1 text-xs !text-gray-400 hover:!text-gray-600 self-center"
                                title="Clear">✕</button>
                        @endif
                    </div>
                </div>

                @endforeach
            </div>
        </div>

        {{-- Written review --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h3 class="text-base font-medium text-gray-900 mb-1">Your review</h3>
            <p class="text-sm text-gray-500 mb-3">
                Focus on the coffee — what did you have, what stood out, would you go back?
            </p>
            <textarea
                wire:model="body"
                rows="6"
                placeholder="I had a single origin Ethiopian pour-over. The barista explained it was a natural process bean from..."
                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
            ></textarea>
            <div class="flex justify-between items-center mt-1">
                @error('body')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-400">Minimum 30 characters</p>
                @enderror
                <p class="text-xs text-gray-400">{{ strlen($body) }} / 2000</p>
            </div>
        </div>

        {{-- Photo upload --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h3 class="text-base font-medium text-gray-900 mb-1">Add a photo</h3>
            <p class="text-sm text-gray-500 mb-3">
                Optional — share a photo of your coffee or the venue. Max 5MB.
            </p>

            <input type="file" wire:model="photo" accept="image/*"
                   class="text-sm text-gray-600"/>

            @error('photo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror

            @if($photo)
                <div class="mt-3">
                    <img src="{{ $photo->temporaryUrl() }}"
                         alt="Preview"
                         class="rounded-lg max-h-48 object-cover"/>
                </div>
            @endif
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="px-6 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-md hover:!bg-indigo-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">Submit review</span>
                <span wire:loading wire:target="save">Submitting...</span>
            </button>
        </div>

    @endif

</div>

