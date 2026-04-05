<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;

new class extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public $photo;
    public $name;
    public $description;
    public $stock = 1;
    public $is_available = true;

    public function mount(?Product $product = null)
    {
        if ($product) {
            $this->product = $product;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->stock = $product->stock;
            $this->is_available = $product->is_available;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
            'photo' => $this->product ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'stock' => $this->stock,
            'is_available' => $this->is_available,
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo->store('products', 'public');
        }

        if ($this->product) {
            $this->product->update($data);
        } else {
            Product::create($data);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto salvo com sucesso!');
    }
};
?>

<div>
    <form wire:submit.prevent="save" class="max-w-2xl mx-auto flex flex-col gap-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold">
                {{ $product ? 'Editar Produto' : 'Novo Produto' }}
            </h1>
            <p class="text-sm text-gray-500">
                Preencha as informações do produto
            </p>
        </div>

        {{-- Nome --}}
        <x-field>
            <x-label>Nome</x-label>

            <x-input wire:model="name" placeholder="Ex: Air Fryer" />

            <x-error name="name" />
        </x-field>

        {{-- Descrição --}}
        <x-field>
            <x-label>Descrição</x-label>

            <x-textarea wire:model="description" placeholder="Detalhes do produto..." />

            <x-error name="description" />
        </x-field>

        {{-- Estoque --}}
        <x-field>
            <x-label>Quantidade desejada</x-label>

            <x-input type="number" wire:model="stock" min="1" />

            <x-error name="stock" />
        </x-field>

        {{-- Foto --}}
        <x-field>
            <x-label>Foto</x-label>

            <x-input type="file" wire:model="photo" />

            {{-- Loading --}}
            <div wire:loading wire:target="photo" class="text-xs text-gray-500">
                Carregando imagem...
            </div>

            {{-- Preview --}}
            @if ($photo)
                <div class="mt-3 max-w-md">
                    <p class="text-xs text-gray-500 mb-1">Preview</p>

                    <div class="w-full h-56 rounded-xl overflow-hidden border bg-gray-100">
                        <img src="{{ $photo ? $photo->temporaryUrl() : asset('storage/' . $product->photo) }}"
                            class="w-full h-full object-cover">
                    </div>
                </div>

            @elseif ($product && $product->photo)
                <div class="mt-3">
                    <p class="text-xs text-gray-500 mb-1">Imagem atual</p>

                    <div class="w-full h-48 rounded-xl overflow-hidden border bg-gray-100">
                        <img src="{{ asset('storage/' . $product->photo) }}"
                            class="w-full h-full object-cover">
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
                        Define se o produto pode ser escolhido
                    </p>
                </div>

                <x-switch wire:model="is_available" />
            </div>
        </x-field>

        {{-- Ações --}}
        <div class="flex justify-end gap-2 pt-4">

            <x-button href="{{ route('products.index') }}" variant="ghost">
                Cancelar
            </x-button>

            <x-button type="submit" variant="primary">
                Salvar
            </x-button>

        </div>

    </form>
</div>
