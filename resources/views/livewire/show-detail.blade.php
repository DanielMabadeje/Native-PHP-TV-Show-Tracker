<div class="flex flex-col min-h-screen bg-gray-950 pb-10">

    {{-- Header --}}
    <div class="relative bg-gray-900 border-b border-gray-800">
        @if ($show->poster_path)
            <div class="absolute inset-0 overflow-hidden opacity-20">
                <img src="{{ $show->posterUrl() }}" class="w-full h-full object-cover blur-xl scale-110">
            </div>
        @endif

        <div class="relative px-4 pt-safe pb-4">
            <div class="flex items-center gap-2 pt-3 mb-4">
                <a href="{{ route('home') }}" class="text-blue-400 text-sm font-medium flex items-center gap-1">
                    <i class="fa-solid fa-chevron-left text-xs"></i> Back
                </a>
            </div>

            <div class="flex gap-4">
                <div class="w-20 h-28 rounded-2xl overflow-hidden flex-shrink-0 bg-gray-800 shadow-xl">
                    @if ($show->poster_path)
                        <img src="{{ $show->posterUrl() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                            <i class="fa-solid fa-tv text-3xl"></i>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0 pt-1">
                    <h1 class="text-xl font-bold text-white leading-tight">{{ $show->name }}</h1>
                    <p class="text-gray-400 text-sm mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-dot text-xs
                            {{ $show->status === 'Returning Series' ? 'text-green-500' : 'text-gray-600' }}"></i>
                        {{ $show->status }}
                    </p>
                    @if ($show->newEpisodes->count() > 0)
                        <span class="inline-flex items-center gap-1 mt-2 bg-blue-600 text-white
                                     text-xs font-bold px-3 py-1 rounded-full">
                            <i class="fa-solid fa-bell"></i>
                            {{ $show->newEpisodes->count() }} new
                        </span>
                    @endif
                </div>
            </div>

            @if ($show->overview)
                <p class="text-gray-400 text-sm mt-3 line-clamp-3">{{ $show->overview }}</p>
            @endif

            {{-- Status message --}}
            @if ($status)
                <p class="text-blue-400 text-sm mt-3 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> {{ $status }}
                </p>
            @endif

            {{-- Actions --}}
            <div class="flex gap-2 mt-4">
                <button wire:click="refresh"
                    class="flex-1 bg-gray-800 text-gray-300 text-sm font-semibold py-2.5 rounded-xl
                           active:scale-95 transition-transform flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
                <button wire:click="markAllSeen"
                    class="flex-1 bg-gray-800 text-gray-300 text-sm font-semibold py-2.5 rounded-xl
                           active:scale-95 transition-transform flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-double"></i> All Seen
                </button>
                <button wire:click="removeShow({{ $show->id }})"
                    wire:confirm="Remove {{ $show->name }} from tracking?"
                    class="bg-red-900/40 text-red-400 text-sm font-semibold px-4 py-2.5 rounded-xl
                           active:scale-95 transition-transform">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Episodes by season --}}
    <div class="px-4 mt-4 space-y-5">
        @forelse ($episodes as $season => $eps)
            <div>
                <h2 class="text-gray-400 text-xs font-semibold uppercase tracking-wide mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group"></i> Season {{ $season }}
                </h2>
                <div class="space-y-2">
                    @foreach ($eps as $ep)
                        <div class="flex items-center gap-3 bg-gray-900 rounded-2xl px-4 py-3 border
                                    {{ $ep->is_new && !$ep->is_seen ? 'border-blue-700' : 'border-gray-800' }}">

                            <div class="flex-shrink-0 w-12 text-center">
                                <span class="text-xs font-bold
                                    {{ $ep->is_new && !$ep->is_seen ? 'text-blue-400' : 'text-gray-600' }}">
                                    {{ $ep->label() }}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate
                                    {{ $ep->is_seen ? 'text-gray-500' : 'text-white' }}">
                                    {{ $ep->name }}
                                </p>
                                @if ($ep->air_date)
                                    <p class="text-xs text-gray-600 mt-0.5 flex items-center gap-1">
                                        <i class="fa-regular fa-calendar text-xs"></i>
                                        {{ $ep->air_date->format('d M Y') }}
                                    </p>
                                @endif
                            </div>

                            @if (!$ep->is_seen)
                                <button wire:click="markSeen({{ $ep->id }})"
                                    class="flex-shrink-0 text-xs text-gray-500 bg-gray-800
                                           px-3 py-1.5 rounded-lg active:scale-95 transition-transform
                                           flex items-center gap-1">
                                    <i class="fa-regular fa-eye"></i> Seen
                                </button>
                            @else
                                <i class="fa-solid fa-check text-green-600 flex-shrink-0"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-gray-600">
                <i class="fa-solid fa-film text-4xl mb-3 block"></i>
                <p class="text-sm">No episodes loaded yet.</p>
                <button wire:click="refresh"
                    class="mt-3 text-blue-400 text-sm font-medium flex items-center gap-1 mx-auto">
                    <i class="fa-solid fa-rotate"></i> Tap Refresh to load episodes
                </button>
            </div>
        @endforelse
    </div>

</div>