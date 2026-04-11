<div class="flex flex-col min-h-screen bg-gray-950 pb-24">

    {{-- Header --}}
    <div class="px-4 pt-safe pb-4 bg-gray-900 border-b border-gray-800">
        <div class="flex items-center justify-between pt-3">
            <div>
                <h1 class="text-2xl font-bold text-white">TV Tracker</h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    @if ($checking)
                        Checking for new episodes...
                    @elseif ($newCount > 0)
                        {{ $newCount }} new {{ $newCount === 1 ? 'episode' : 'episodes' }} found!
                    @else
                        Your shows, up to date
                    @endif
                </p>
            </div>


            {{-- <button wire:click="checkForNewEpisodes"
                class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center
                       text-gray-400 hover:text-white transition-colors">
                <i class="fa-solid fa-rotate {{ $checking ? 'animate-spin' : '' }}"></i>
            </button> --}}

            <div class="flex items-center gap-2">
                {{-- Test notification button (remove before production) --}}
                <button wire:click="testNotification"
                    class="w-10 h-10 rounded-full bg-yellow-600/30 flex items-center justify-center text-yellow-400">
                    <i class="fa-solid fa-bell text-sm"></i>
                </button>

                <button wire:click="checkForNewEpisodes"
                    class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center
               text-gray-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-rotate {{ $checking ? 'animate-spin' : '' }}"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="px-4 mt-4 space-y-3">

        @forelse ($shows as $show)
            @php $newEps = $show->episodes->count(); @endphp
            <a href="{{ route('show', $show) }}"
                class="flex items-center gap-4 bg-gray-900 rounded-2xl p-3 border border-gray-800
                       active:scale-95 transition-transform block">

                {{-- Poster --}}
                <div class="w-14 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-gray-800">
                    @if ($show->poster_path)
                        <img src="{{ $show->posterUrl() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                            <i class="fa-solid fa-tv text-2xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-white font-semibold truncate">{{ $show->name }}</p>
                        @if ($newEps > 0)
                            <span
                                class="flex-shrink-0 bg-blue-600 text-white text-xs font-bold
                                         px-2 py-0.5 rounded-full">
                                {{ $newEps }} new
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $show->status }}</p>
                    @if ($show->latestEpisode())
                        <p class="text-gray-400 text-xs mt-1">
                            Latest: {{ $show->latestEpisode()->label() }}
                            · {{ $show->latestEpisode()->name }}
                        </p>
                    @endif
                </div>

                <i class="fa-solid fa-chevron-right text-gray-600 flex-shrink-0 text-sm"></i>
            </a>
        @empty
            <div class="text-center py-24">
                <i class="fa-solid fa-tv text-5xl text-gray-700 mb-4 block"></i>
                <p class="text-lg font-medium text-gray-400">No shows tracked yet</p>
                <p class="text-sm text-gray-600 mt-1">Tap Search to add your favourites</p>
            </div>
        @endforelse

    </div>

    {{-- Bottom nav --}}
    <div class="fixed bottom-0 left-0 right-0 bg-gray-900 border-t border-gray-800 px-6 py-3 pb-safe flex">
        <a href="{{ route('home') }}" class="flex-1 flex flex-col items-center gap-1 text-blue-400">
            <i class="fa-solid fa-tv text-xl"></i>
            <span class="text-xs font-medium">My Shows</span>
        </a>
        <a href="{{ route('search') }}" class="flex-1 flex flex-col items-center gap-1 text-gray-500">
            <i class="fa-solid fa-magnifying-glass text-xl"></i>
            <span class="text-xs font-medium">Search</span>
        </a>
    </div>

</div>
