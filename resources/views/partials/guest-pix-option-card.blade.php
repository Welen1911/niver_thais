<div class="bg-white rounded-xl shadow overflow-hidden flex flex-col max-w-sm w-full">

    {{-- IMAGEM --}}
    <div class="w-full aspect-square overflow-hidden bg-gray-100">
        @if ($pixOption->photo)
            <img src="{{ asset('storage/' . $pixOption->photo) }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                Sem imagem
            </div>
        @endif
    </div>

    {{-- CONTEÚDO --}}
    <div class="p-4 flex flex-col flex-1 justify-between">

        <div>
            <h3 class="text-lg font-semibold">{{ $pixOption->name }}</h3>
            <p class="text-sm text-gray-500 line-clamp-2">{{ $pixOption->description }}</p>
            <p class="text-xl font-bold text-green-600 mt-1">
                R$ {{ number_format($pixOption->value, 2, ',', '.') }}
            </p>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if ($available)
                <x-button wire:click="openModal({{ $pixOption->id }})" size="sm">
                    💸 Contribuir via Pix
                </x-button>
            @else
                <span class="text-red-500 font-semibold text-sm">Indisponível</span>
            @endif
        </div>

    </div>
</div>
