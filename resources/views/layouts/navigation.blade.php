<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-center gap-8">
                <a href="{{ route('venues.index') }}" class="flex items-center gap-2 shrink-0">
                    <span class="text-xl">☕</span>
                    <span class="font-display font-bold text-gray-900 tracking-tight">
                        Coffee Shop Reviews
                    </span>
                </a>

                <div class="hidden sm:flex items-center gap-1">
                    @foreach([
                        ['Coffee shops', 'venues.index'],
                        ['Map',          'venues.map'],
                    ] as [$label, $route])
                        <a href="{{ route($route) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors
                               {{ request()->routeIs($route)
                                   ? 'bg-gray-100 text-gray-900'
                                   : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                    @auth
                        <a href="{{ route('venues.create') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors
                               {{ request()->routeIs('venues.create')
                                   ? 'bg-gray-100 text-gray-900'
                                   : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                            Add a venue
                        </a>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('users.show', auth()->user()) }}">
                                My profile
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('my.reviews') }}">
                                My reviews
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('profile.edit') }}">
                                Settings
                            </x-dropdown-link>
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-gray-100 my-1"></div>
                                <x-dropdown-link href="{{ route('admin.index') }}">
                                    Admin
                                </x-dropdown-link>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Sign out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Sign in
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 !bg-indigo-600 !text-white text-sm font-medium rounded-lg hover:!bg-indigo-700 transition-colors">
                        Register
                    </a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100">
        <div class="px-4 py-3 space-y-1">
            <x-responsive-nav-link href="{{ route('venues.index') }}" :active="request()->routeIs('venues.index')">
                Coffee shops
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('venues.map') }}" :active="request()->routeIs('venues.map')">
                Map
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link href="{{ route('venues.create') }}" :active="request()->routeIs('venues.create')">
                    Add a venue
                </x-responsive-nav-link>
            @endauth
        </div>
        @auth
            <div class="border-t border-gray-100 px-4 py-3 space-y-1">
                <x-responsive-nav-link href="{{ route('users.show', auth()->user()) }}">My profile</x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('my.reviews') }}">My reviews</x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('profile.edit') }}">Settings</x-responsive-nav-link>
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link href="{{ route('admin.index') }}">Admin</x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Sign out
                    </x-responsive-nav-link>
                </form>
            </div>
        @else
            <div class="border-t border-gray-100 px-4 py-3 space-y-1">
                <x-responsive-nav-link href="{{ route('login') }}">Sign in</x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('register') }}">Register</x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>
