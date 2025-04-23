<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">
    @foreach ($items as $item)
        <div class="rounded-2xl shadow-lg p-6 text-white {{ $item['color'] }} flex flex-col justify-between">
            <div class="text-lg font-semibold mb-4">{{ $item['label'] }}</div>

            @if (!is_null($item['count']))
                <div class="text-5xl font-bold text-center mb-4">{{ $item['count'] }}</div>
            @else
                <div class="h-16 mb-4"></div>
            @endif

            <a href="{{ $item['route'] }}"
                class="mt-auto text-center bg-white text-black rounded-full px-4 py-2 text-sm font-semibold hover:bg-gray-100 transition">
                Lihat Detail
            </a>
        </div>
    @endforeach
</div>
