<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                @foreach([
                    ['Total venues',   $stats['total_venues']],
                    ['Total reviews',  $stats['total_reviews']],
                    ['Total users',    $stats['total_users']],
                    ['Unverified venues',  $stats['pending_venues']],
                    ['Unverified reviews', $stats['flagged_reviews']],
                ] as [$label, $value])
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $value }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Navigation --}}
            <div class="grid grid-cols-3 gap-4">
                @foreach([
                    ['Manage venues',  route('admin.venues'),  'Review and verify submitted venues'],
                    ['Manage reviews', route('admin.reviews'), 'Approve or remove reviews'],
                    ['Manage users',   route('admin.users'),   'View and ban users'],
                ] as [$title, $url, $desc])
                    <a href="{{ $url }}"
                       class="block bg-white rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all">
                        <h3 class="font-medium text-gray-900">{{ $title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $desc }}</p>
                    </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
