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
