<div class="flex flex-col min-h-screen bg-gray-950 pb-24">

    {{-- Header --}}
    <div class="px-4 pt-safe pb-4 bg-gray-900 border-b border-gray-800">
        <h1 class="text-2xl font-bold text-white pt-3">Search Shows</h1>

        <div class="mt-3 flex gap-2">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                <input
                    wire:model="query"
                    wire:keydown.enter="search"
                    type="text"
                    placeholder="Search for a TV show..."
                    class="w-full bg-gray-800 text-white rounded-2xl pl-10 pr-4 py-3 text-sm
                           outline-none placeholder-gray-500 border border-gray-700">
            </div>
            <button wire:click="search"
                class="bg-blue-600 text-white font-semibold px-5 py-3 rounded-2xl text-sm
                       active:scale-95 transition-transform">
                @if ($searching)
                    <i class="fa-solid fa-spinner animate-spin"></i>
                @else
                    Go
                @endif
            </button>
        </div>
    </div>

    {{-- Status --}}
    @if ($status)
        <div class="mx-4 mt-3 bg-blue-900/40 border border-blue-700 text-blue-300 rounded-2xl px-4 py-2.5 text-sm">
            <i class="fa-solid fa-circle-info mr-1"></i> {{ $status }}
        </div>
    @endif

    {{-- Results --}}
    <div class="px-4 mt-4 space-y-3">
        @foreach ($results as $result)
            @php $isTracked = in_array($result['id'], $tracked); @endphp
            <div class="flex items-center gap-3 bg-gray-900 rounded-2xl p-3 border border-gray-800">

                {{-- Poster --}}
                <div class="w-12 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-800">
                    @if (!empty($result['poster_path']))
                        <img src="https://image.tmdb.org/t/p/w200{{ $result['poster_path'] }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                            <i class="fa-solid fa-tv"></i>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">{{ $result['name'] }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">
                        @if (!empty($result['first_air_date']))
                            <i class="fa-regular fa-calendar mr-1"></i>{{ substr($result['first_air_date'], 0, 4) }}
                        @endif
                    </p>
                    <p class="text-gray-600 text-xs mt-1 line-clamp-2">{{ $result['overview'] ?? '' }}</p>
                </div>

                {{-- Add button --}}
                <button
                    wire:click="addShow({{ $result['id'] }})"
                    @disabled($isTracked)
                    class="flex-shrink-0 text-xs font-semibold px-3 py-2 rounded-xl transition-colors
                           {{ $isTracked
                               ? 'bg-gray-800 text-gray-600 cursor-default'
                               : 'bg-blue-600 text-white active:scale-95' }}">
                    @if ($isTracked)
                        <i class="fa-solid fa-check"></i>
                    @else
                        <i class="fa-solid fa-plus"></i>
                    @endif
                </button>

            </div>
        @endforeach

        @if ($searching)
            <div class="text-center py-10">
                <i class="fa-solid fa-spinner animate-spin text-gray-500 text-2xl"></i>
            </div>
        @endif

        @if (!$searching && empty($results) && strlen($query) >= 2)
            <div class="text-center py-16 text-gray-600">
                <i class="fa-solid fa-film text-4xl mb-3 block"></i>
                <p class="text-sm">No results found for "{{ $query }}"</p>
            </div>
        @endif
    </div>

    {{-- Bottom nav --}}
    <div class="fixed bottom-0 left-0 right-0 bg-gray-900 border-t border-gray-800 px-6 py-3 pb-safe flex">
        <a href="{{ route('home') }}"
            class="flex-1 flex flex-col items-center gap-1 text-gray-500">
            <i class="fa-solid fa-tv text-xl"></i>
            <span class="text-xs font-medium">My Shows</span>
        </a>
        <a href="{{ route('search') }}"
            class="flex-1 flex flex-col items-center gap-1 text-blue-400">
            <i class="fa-solid fa-magnifying-glass text-xl"></i>
            <span class="text-xs font-medium">Search</span>
        </a>
    </div>

</div>