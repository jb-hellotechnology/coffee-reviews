<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Profile information</h2>
        <p class="mt-1 text-sm text-gray-600">Update your profile details and public presence.</p>
    </header>

    <form method="POST" action="{{ route('profile.update') }}"
          enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Avatar --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile photo</label>
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-indigo-100 flex items-center justify-center shrink-0">
                    @if(auth()->user()->avatarUrl())
                        <img src="{{ auth()->user()->avatarUrl() }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover"/>
                    @else
                        <span class="text-2xl font-bold text-indigo-700">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div>
                    <input type="file" name="avatar" accept="image/*"
                           class="text-sm text-gray-600"/>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG or GIF. Max 2MB.</p>
                </div>
            </div>
            @error('avatar')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input id="name" name="name" type="text"
                          class="mt-1 block w-full" :value="old('name', $user->name)"
                          required autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" name="email" type="email"
                          class="mt-1 block w-full" :value="old('email', $user->email)"
                          required autocomplete="username"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>
        </div>

        {{-- Bio --}}
        <div>
            <x-input-label for="bio" value="About you"/>
            <textarea id="bio" name="bio" rows="3"
                      placeholder="Tell the coffee community a bit about yourself..."
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('bio', $user->bio) }}</textarea>
            <p class="mt-1 text-xs text-gray-400">Max 500 characters.</p>
            <x-input-error class="mt-2" :messages="$errors->get('bio')"/>
        </div>

        {{-- Website --}}
        <div>
            <x-input-label for="website" value="Website or social link"/>
            <x-text-input id="website" name="website" type="url"
                          class="mt-1 block w-full" :value="old('website', $user->website)"
                          placeholder="https://"/>
            <x-input-error class="mt-2" :messages="$errors->get('website')"/>
        </div>

        {{-- Expertise level --}}
        <div>
            <x-input-label value="Your coffee expertise"/>
            <p class="text-xs text-gray-400 mb-3">Be honest — or not. We won't judge.</p>
            <div class="space-y-2">
                @foreach(App\Models\User::expertiseLevels() as $key => $level)
                    <label class="flex items-center gap-3 p-3 mb-3 rounded-lg border cursor-pointer transition-colors
                        {{ old('expertise_level', auth()->user()->expertise_level) === $key
                            ? 'border-indigo-400 bg-indigo-50'
                            : 'border-gray-200 hover:border-indigo-200' }}">
                        <input type="radio" name="expertise_level" value="{{ $key }}"
                               class="text-indigo-600 focus:ring-indigo-500"
                               {{ old('expertise_level', auth()->user()->expertise_level) === $key ? 'checked' : '' }}>
                        <span class="text-xl">{{ $level['emoji'] }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $level['label'] }}</p>
                            <p class="text-xs text-gray-400">{{ $level['description'] }}</p>
                        </div>
                    </label>
                @endforeach
                <label class="flex items-center gap-3 p-3 mb-5 rounded-lg border cursor-pointer transition-colors
                    {{ old('expertise_level', auth()->user()->expertise_level) === null
                        ? 'border-indigo-400 bg-indigo-50'
                        : 'border-gray-200 hover:border-indigo-200' }}">
                    <input type="radio" name="expertise_level" value=""
                           class="text-indigo-600 focus:ring-indigo-500"
                           {{ old('expertise_level', auth()->user()->expertise_level) === null ? 'checked' : '' }}>
                    <span class="text-xl">🤷</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Prefer not to say</p>
                        <p class="text-xs text-gray-400">No label shown on your reviews</p>
                    </div>
                </label>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('expertise_level')"/>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="rounded-lg bg-green-50 border border-green-200 p-3">
                <p class="text-sm text-green-800">Profile updated successfully.</p>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>Save</x-primary-button>
        </div>

    </form>
</section>
