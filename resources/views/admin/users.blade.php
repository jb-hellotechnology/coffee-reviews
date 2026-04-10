<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
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
            @if(session('error'))
                <div class="rounded-lg bg-red-50 border border-red-200 p-4 mb-6">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <div class="space-y-3">
                @forelse($users as $user)
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('users.show', $user) }}"
                                       class="font-medium text-gray-900 hover:text-indigo-600">
                                        {{ $user->name }}
                                    </a>
                                    @if($user->isAdmin())
                                        <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs">
                                            Admin
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Joined {{ $user->created_at->diffForHumans() }}
                                    · {{ $user->reviews_count }} {{ Str::plural('review', $user->reviews_count) }}
                                </p>
                            </div>
                            @if(!$user->isAdmin())
                                <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                                      onsubmit="return confirm('Ban {{ addslashes($user->name) }} and delete all their reviews?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 !bg-red-600 !text-white text-xs rounded-md">
                                        Ban user
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">No users found.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $users->links() }}</div>

        </div>
    </div>
</x-app-layout>
