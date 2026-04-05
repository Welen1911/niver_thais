<x-layouts::app :title="__('Editar produto')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        @livewire('products.product-form', ['product' => $product])

    </div>
</x-layouts::app>
