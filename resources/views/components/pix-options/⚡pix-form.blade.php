<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PixOption;

new class extends Component
{
    use WithFileUploads;

    public ?PixOption $pixOption = null;

    public $photo;
    public string $name = '';
    public ?string $description = null;
    public float $value = 0;
    public bool $is_available = true;

    public function mount(?PixOption $pixOption = null)
    {
        if ($pixOption) {
            $this->pixOption    = $pixOption;
            $this->name         = $pixOption->name;
            $this->description  = $pixOption->description;
            $this->value        = $pixOption->value;
            $this->is_available = $pixOption->is_available;
        }
    }

    public function save()
    {
        $this->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'value'       => 'required|numeric|min:0.01',
            'photo'       => $this->pixOption ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $data = [
            'name'         => $this->name,
            'description'  => $this->description,
            'value'        => $this->value,
            'is_available' => $this->is_available,
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo->store('pix-options', 'public');
        }

        if ($this->pixOption) {
            $this->pixOption->update($data);
        } else {
            PixOption::create($data);
        }

        return redirect()->route('pixs.index')
            ->with('success', 'Opção Pix salva com sucesso!');
    }
};
?>

<div>
    <form wire:submit.prevent="save" class="max-w-2xl mx-auto flex flex-col gap-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold">
                {{ $pixOption ? 'Editar opção Pix' : 'Nova opção Pix' }}
            </h1>
            <p class="text-sm text-gray-500">
                Preencha as informações da contribuição via Pix
            </p>
        </div>

        {{-- Nome --}}
        <x-field>
            <x-label>Nome</x-label>
            <x-input wire:model="name" placeholder="Ex: Contribuição especial" />
            <x-error name="name" />
        </x-field>

        {{-- Descrição --}}
        <x-field>
            <x-label>Descrição</x-label>
            <x-textarea wire:model="description" placeholder="Detalhes da contribuição..." />
            <x-error name="description" />
        </x-field>

        {{-- Valor --}}
        <x-field>
            <x-label>Valor (R$)</x-label>
            <x-input
                type="number"
                wire:model="value"
                min="0.01"
                step="0.01"
                placeholder="Ex: 150.00"
            />
            <x-error name="value" />
        </x-field>

        {{-- Foto --}}
        <x-field>
            <x-label>Foto</x-label>
            <x-input type="file" wire:model="photo" />

            <div wire:loading wire:target="photo" class="text-xs text-gray-500">
                Carregando imagem...
            </div>

            @if ($photo)
                <div class="mt-3 max-w-md">
                    <p class="text-xs text-gray-500 mb-1">Preview</p>
                    <div class="w-full h-56 rounded-xl overflow-hidden border bg-gray-100">
                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                    </div>
                </div>
            @elseif ($pixOption && $pixOption->photo)
                <div class="mt-3">
                    <p class="text-xs text-gray-500 mb-1">Imagem atual</p>
                    <div class="w-full h-48 rounded-xl overflow-hidden border bg-gray-100">
                        <img src="{{ asset('storage/' . $pixOption->photo) }}" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            <x-error name="photo" />
        </x-field>

        {{-- Disponível --}}
        <x-field>
            <div class="flex items-center justify-between">
                <div>
                    <x-label>Disponível</x-label>
                    <p class="text-xs text-gray-500">
                        Define se esta opção aparece para os convidados
                    </p>
                </div>
                <x-switch wire:model="is_available" />
            </div>
        </x-field>

        {{-- Ações --}}
        <div class="flex justify-end gap-2 pt-4">
            <x-button href="{{ route('pixs.index') }}" variant="ghost">
                Cancelar
            </x-button>
            <x-button type="submit" variant="primary">
                Salvar
            </x-button>
        </div>

    </form>
</div>
