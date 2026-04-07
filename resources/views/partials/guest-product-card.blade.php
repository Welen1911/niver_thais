<div class="bg-white dark:bg-zinc-900 rounded-xl shadow overflow-hidden flex flex-col max-w-sm w-full">

    {{-- IMAGEM --}}
    <div class="w-full aspect-square overflow-hidden bg-gray-100 dark:bg-zinc-800">
        @if ($product->photo)
            <img src="{{ asset('storage/' . $product->photo) }}"
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
            <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
            <p class="text-sm text-gray-500 line-clamp-2">{{ $product->description }}</p>
        </div>
        @php
            $reserved  = $product->reservations->sum('quantity');
            $remaining = $product->stock - $reserved;
        @endphp
        <div class="flex items-center justify-between mt-4">
            <div class="text-sm flex flex-col">
                <span>Total: {{ $product->stock }}</span>
                <span>Reservado: {{ $reserved }}</span>
                <span class="{{ $remaining <= 0 ? 'text-red-500' : 'text-green-600' }}">
                    Restante: {{ $remaining }}
                </span>
            </div>

            @if($available)
                <div class="flex gap-2">
                    <x-button wire:click="openModal({{ $product->id }})" size="sm">
                        Reservar
                    </x-button>
                </div>
            @else
                <span class="text-red-500 font-semibold">Indisponível</span>
            @endif
        </div>

    </div>
</div>
