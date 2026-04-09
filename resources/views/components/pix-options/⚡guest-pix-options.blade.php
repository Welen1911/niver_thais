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

    public string $guestName = '';

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

    public function openModal(int $optionId)
    {
        $this->modalOptionId = $optionId;
        $this->refreshModal();
        $this->dispatch('open-pix-guest-modal');
    }

    public function closeModal()
    {
        $this->modalOptionId = null;
        $this->modalOption   = null;
        $this->guestName     = '';
        $this->resetValidation();
        $this->dispatch('close-pix-guest-modal');
    }

    public function refreshModal()
    {
        if ($this->modalOptionId) {
            $this->modalOption = PixOption::with('contributions')
                ->findOrFail($this->modalOptionId);
        }
    }

    public function createContribution()
    {
        $this->validate([
            'guestName' => 'required|string|min:2',
        ]);

        $guestName = $this->guestName;

        PixContribution::create([
            'pix_option_id' => $this->modalOptionId,
            'guest_name'    => $guestName,
            'confirmed'     => false,
        ]);

        $this->closeModal();
        $this->loadOptions();

        $this->dispatch('toast-success', [
            'heading' => "{$guestName}, obrigado!",
            'message' => 'Assim que o Pix for confirmado, sua contribuição estará registrada! 💸',
        ]);
    }
};
?>

<div class="w-full">
    <div class="flex flex-col gap-6">

        {{-- DISPONÍVEIS --}}
        <div>
            <h2 class="text-lg font-semibold mb-3">Disponíveis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($availableOptions as $pixOption)
                    @include('partials.guest-pix-option-card', ['pixOption' => $pixOption, 'available' => true])
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
                    @include('partials.guest-pix-option-card', ['pixOption' => $pixOption, 'available' => false])
                @empty
                    <p class="text-sm text-gray-500">Nenhuma opção indisponível.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ===================== MODAL ===================== --}}
    <div
        x-data="{ open: false }"
        x-on:open-pix-guest-modal.window="open = true"
        x-on:close-pix-guest-modal.window="open = false"
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
            class="relative z-10 bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            @if ($modalOption)

                {{-- Cabeçalho --}}
                <div class="flex items-start justify-between p-5 border-b">
                    <div class="flex items-center gap-3">
                        @if ($modalOption->photo)
                            <img src="{{ asset('storage/' . $modalOption->photo) }}"
                                 class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                Sem foto
                            </div>
                        @endif
                        <div>
                            <h2 class="text-lg font-bold">{{ $modalOption->name }}</h2>
                            <p class="text-sm font-semibold text-green-600">
                                R$ {{ number_format($modalOption->value, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <button
                        x-on:click="open = false; $wire.closeModal()"
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Descrição --}}
                    @if ($modalOption->description)
                        <p class="text-sm text-gray-500">{{ $modalOption->description }}</p>
                    @endif

                    {{-- Chave Pix --}}
                    <div class="rounded-xl bg-green-50 border border-green-200 p-4 space-y-3">
                        <p class="text-sm text-green-800 font-medium">Chave Pix para transferência:</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 text-sm bg-white border border-green-200 rounded-lg px-3 py-2 text-gray-700 select-all">
                                {{ config('app.pix_key') }}
                            </code>
                            <button
                                type="button"
                                x-data="{ copied: false }"
                                x-on:click="
                                    navigator.clipboard.writeText('{{ config('app.pix_key') }}');
                                    copied = true;
                                    setTimeout(() => copied = false, 2000)
                                "
                                class="px-3 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white text-xs font-medium transition"
                            >
                                <span x-show="!copied">Copiar</span>
                                <span x-show="copied">✓ Copiado</span>
                            </button>
                        </div>
                        <p class="text-xs text-green-700 opacity-80">
                            Realize o Pix de <strong>R$ {{ number_format($modalOption->value, 2, ',', '.') }}</strong>
                            e clique em confirmar abaixo. O admin irá validar o pagamento.
                        </p>
                    </div>

                    {{-- INPUT NOME --}}
                    <div>
                        <label class="text-sm font-medium">Seu nome</label>
                        <x-input
                            type="text"
                            wire:model="guestName"
                            class="w-full mt-1"
                            placeholder="Ex: João"
                        />
                        @error('guestName')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- BOTÃO --}}
                    <div class="pt-2">
                        <x-button
                            wire:click="createContribution"
                            class="w-full justify-center"
                        >
                            💸 Já fiz o Pix, confirmar!
                        </x-button>
                    </div>

                </div>

                <div class="p-4 border-t flex justify-between items-center">
                    <x-button
                        x-on:click="open = false; $wire.closeModal()"
                        size="sm"
                        variant="ghost"
                    >
                        Fechar
                    </x-button>
                </div>

            @endif
        </div>
    </div>
</div>
