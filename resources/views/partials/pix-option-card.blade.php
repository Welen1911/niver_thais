<div class="bg-white dark:bg-zinc-900 rounded-xl shadow overflow-hidden flex flex-col max-w-sm w-full">

    {{-- IMAGEM --}}
    <div class="w-full aspect-square overflow-hidden bg-gray-100 dark:bg-zinc-800">
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
            <p class="text-lg font-bold text-green-600 mt-1">
                R$ {{ number_format($pixOption->value, 2, ',', '.') }}
            </p>
        </div>

        {{-- Contribuições --}}
        @if ($pixOption->contributions->count())
            <button
                wire:click="openModal({{ $pixOption->id }})"
                class="mt-3 w-full text-left text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-lg p-2 transition border border-dashed border-gray-200 dark:border-zinc-700"
            >
                <p class="font-semibold mb-1">
                    Contribuições
                    <span class="ml-1 text-blue-500 underline underline-offset-2">(ver detalhes)</span>
                </p>
                <ul class="space-y-1">
                    @foreach ($pixOption->contributions->take(3) as $contribution)
                        <li class="flex justify-between items-center">
                            <span>{{ $contribution->guest_name }}</span>
                            @if ($contribution->confirmed)
                                <span class="text-green-500 font-medium">✓ Confirmado</span>
                            @else
                                <span class="text-yellow-500 font-medium">Pendente</span>
                            @endif
                        </li>
                    @endforeach
                    @if ($pixOption->contributions->count() > 3)
                        <li class="text-gray-400 italic">
                            + {{ $pixOption->contributions->count() - 3 }} outros...
                        </li>
                    @endif
                </ul>
            </button>
        @endif

        <div class="flex items-center justify-between mt-4">
            <div class="text-sm flex flex-col gap-0.5">
                @php
                    $confirmed = $pixOption->contributions->where('confirmed', true)->count();
                    $pending   = $pixOption->contributions->where('confirmed', false)->count();
                @endphp
                <span>Confirmados: <span class="text-green-600 font-medium">{{ $confirmed }}</span></span>
                <span>Pendentes: <span class="text-yellow-500 font-medium">{{ $pending }}</span></span>
            </div>

            <div class="flex gap-2">
                <x-button href="{{ route('pixs.edit', $pixOption->id) }}" size="sm">
                    Editar
                </x-button>

                @if ($available)
                    <x-button wire:click="toggleAvailability({{ $pixOption->id }})" variant="danger" size="sm">
                        Indisponibilizar
                    </x-button>
                @else
                    <x-button wire:click="toggleAvailability({{ $pixOption->id }})" variant="primary" size="sm">
                        Disponibilizar
                    </x-button>
                @endif
            </div>
        </div>

    </div>
</div>
