<?php

use Livewire\Component;
use App\Models\PixOption;
use App\Models\PixContribution;

new class extends Component
{
    public $availableOptions;
    public $unavailableOptions;

    // Modal state
    public ?int $modalOptionId = null;
    public $modalOption = null;

    // Edit state
    public ?int $editingContributionId = null;

    public string $editGuestName = '';

    public function mount()
    {
        $this->loadOptions();
    }

    public function loadOptions()
    {
        $this->availableOptions = PixOption::with('contributions')
            ->where('is_available', true)
            ->get();

        $this->unavailableOptions = PixOption::with('contributions')
            ->where('is_available', false)
            ->get();
    }

    public function toggleAvailability(int $id)
    {
        $option = PixOption::findOrFail($id);
        $option->is_available = !$option->is_available;
        $option->save();
        $this->loadOptions();

        if ($this->modalOptionId === $option->id) {
            $this->refreshModal();
        }
    }

    public function openModal(int $optionId)
    {
        $this->modalOptionId = $optionId;
        $this->cancelEdit();
        $this->refreshModal();
        $this->dispatch('open-pix-modal');
    }

    public function closeModal()
    {
        $this->modalOptionId = null;
        $this->modalOption   = null;
        $this->cancelEdit();
        $this->dispatch('close-pix-modal');
    }

    public function refreshModal()
    {
        if ($this->modalOptionId) {
            $this->modalOption = PixOption::with('contributions')
                ->findOrFail($this->modalOptionId);
        }
    }

    public function toggleConfirmed(int $contributionId)
    {
        $contribution = PixContribution::findOrFail($contributionId);
        $contribution->confirmed = !$contribution->confirmed;
        $contribution->save();
        $this->refreshModal();
        $this->loadOptions();
    }

    public function startEdit(int $contributionId)
    {
        $contribution = PixContribution::findOrFail($contributionId);
        $this->editingContributionId = $contributionId;
        $this->editGuestName = $contribution->guest_name;
    }

    public function cancelEdit()
    {
        $this->editingContributionId = null;
        $this->editGuestName = '';
        $this->resetValidation();
    }

    public function updateContribution()
    {
        $this->validate([
            'editGuestName' => 'required|string|min:2',
        ]);

        PixContribution::findOrFail($this->editingContributionId)
            ->update(['guest_name' => $this->editGuestName]);

        $this->cancelEdit();
        $this->refreshModal();
    }

    public function deleteContribution(int $contributionId)
    {
        PixContribution::findOrFail($contributionId)->delete();
        $this->refreshModal();
        $this->loadOptions();
    }
};
?>

<div class="w-full">
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Opções Pix</h1>
            <x-button href="{{ route('pixs.create') }}" variant="primary">
                + Nova opção Pix
            </x-button>
        </div>

        {{-- DISPONÍVEIS --}}
        <div>
            <h2 class="text-lg font-semibold mb-3">Disponíveis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($availableOptions as $pixOption)
                    @include('partials.pix-option-card', ['pixOption' => $pixOption, 'available' => true])
                @empty
                    <p class="text-sm text-gray-500">Nenhuma opção disponível.</p>
                @endforelse
            </div>
        </div>

        {{-- INDISPONÍVEIS --}}
        <div>
            <h2 class="text-lg font-semibold mb-3">Indisponíveis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($unavailableOptions as $pixOption)
                    @include('partials.pix-option-card', ['pixOption' => $pixOption, 'available' => false])
                @empty
                    <p class="text-sm text-gray-500">Nenhuma opção indisponível.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ===================== MODAL ===================== --}}
    <div
        x-data="{ open: false }"
        x-on:open-pix-modal.window="open = true"
        x-on:close-pix-modal.window="open = false"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/50"
            x-on:click="open = false; $wire.closeModal()"
        ></div>

        {{-- Painel --}}
        <div
            class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            @if ($modalOption)

                {{-- Cabeçalho --}}
                <div class="flex items-start justify-between p-5 border-b dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        @if ($modalOption->photo)
                            <img src="{{ asset('storage/' . $modalOption->photo) }}"
                                 class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-zinc-800 flex items-center justify-center text-gray-400 text-xs">
                                Sem foto
                            </div>
                        @endif
                        <div>
                            <h2 class="text-lg font-bold">{{ $modalOption->name }}</h2>
                            <p class="text-sm font-semibold text-green-600">
                                R$ {{ number_format($modalOption->value, 2, ',', '.') }}
                            </p>
                            @php
                                $confirmed = $modalOption->contributions->where('confirmed', true)->count();
                                $pending   = $modalOption->contributions->where('confirmed', false)->count();
                            @endphp
                            <p class="text-xs text-gray-500">
                                <span class="text-green-600">{{ $confirmed }} confirmado(s)</span>
                                &nbsp;·&nbsp;
                                <span class="text-yellow-500">{{ $pending }} pendente(s)</span>
                            </p>
                        </div>
                    </div>
                    <button
                        x-on:click="open = false; $wire.closeModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Lista de contribuições --}}
                <div class="overflow-y-auto flex-1 p-5 space-y-3">

                    @forelse ($modalOption->contributions as $contribution)
                        <div class="rounded-xl border dark:border-zinc-700 overflow-hidden">

                            @if ($editingContributionId !== $contribution->id)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <div>
                                        <p class="font-medium text-sm">{{ $contribution->guest_name }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $contribution->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">

                                        {{-- Toggle confirmado --}}
                                        <button
                                            wire:click="toggleConfirmed({{ $contribution->id }})"
                                            title="{{ $contribution->confirmed ? 'Marcar como pendente' : 'Confirmar pagamento' }}"
                                            class="text-xs px-2 py-1 rounded-lg font-medium transition
                                                {{ $contribution->confirmed
                                                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                    : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}"
                                        >
                                            {{ $contribution->confirmed ? '✓ Confirmado' : 'Pendente' }}
                                        </button>

                                        {{-- Editar --}}
                                        <button
                                            wire:click="startEdit({{ $contribution->id }})"
                                            class="text-blue-500 hover:text-blue-700 transition"
                                            title="Editar"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z"/>
                                            </svg>
                                        </button>

                                        {{-- Excluir --}}
                                        <button
                                            wire:click="deleteContribution({{ $contribution->id }})"
                                            wire:confirm="Tem certeza que deseja excluir a contribuição de '{{ $contribution->guest_name }}'?"
                                            class="text-red-500 hover:text-red-700 transition"
                                            title="Excluir"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                            @else
                                <div class="px-4 py-3 space-y-3 bg-blue-50 dark:bg-zinc-800">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                                            Nome do convidado
                                        </label>
                                        <input
                                            type="text"
                                            wire:model="editGuestName"
                                            class="mt-1 w-full rounded-lg border dark:border-zinc-600 bg-white dark:bg-zinc-900 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                        @error('editGuestName')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex gap-2">
                                        <x-button wire:click="updateContribution" variant="primary" size="sm">
                                            Salvar
                                        </x-button>
                                        <x-button wire:click="cancelEdit" size="sm">
                                            Cancelar
                                        </x-button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Nenhuma contribuição registrada.
                        </div>
                    @endforelse

                </div>

                {{-- Rodapé --}}
                <div class="p-4 border-t dark:border-zinc-700 flex justify-end">
                    <x-button x-on:click="open = false; $wire.closeModal()" size="sm">
                        Fechar
                    </x-button>
                </div>

            @endif
        </div>
    </div>
</div>
