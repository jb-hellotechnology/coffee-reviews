<div class="max-w-2xl mx-auto">

    @if($saved)
        <div class="rounded-2xl bg-green-50 border border-green-200 p-8 text-center">
            <p class="font-display font-bold text-lg text-green-800">Roaster added!</p>
            <p class="text-green-600 text-sm mt-1">Thank you for contributing to the directory.</p>
            <button wire:click="$set('saved', false)"
                    class="mt-4 text-sm text-green-700 underline">
                Add another roaster
            </button>
        </div>

    @else
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-display font-bold text-lg text-gray-900 mb-5">Roaster details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" wire:model="name"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" wire:model="city"
                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                        @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" wire:model="website"
                               placeholder="https://"
                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"/>
                        @error('website') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">About the roaster</label>
                    <textarea wire:model="description" rows="4"
                              placeholder="Tell us about this roastery — their style, origins, what makes them special..."
                              class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 !bg-indigo-600 !text-white text-sm font-medium rounded-lg hover:!bg-indigo-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">Add roaster</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    @endif

</div>
