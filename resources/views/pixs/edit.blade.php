<x-layouts::app :title="__('Editar produto')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        @livewire('pix-options.pix-form', ['pixOption' => $pixOption])

    </div>
</x-layouts::app>
