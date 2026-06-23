<div x-data="{ open: false }"
    @open-modal.window="open = true"
    @close-modal.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4" @click.outside="open = false">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4"
                x-text="editando ? 'Editar Cargo' : 'Nuevo Cargo'"></h3>

            <div class="space-y-4">
                <div>
                    <x-input-label for="nombre_cargo" value="Nombre del Cargo" />
                    <x-text-input id="nombre_cargo" x-model="form.nombre_cargo" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="descripcion" value="Descripcion" />
                    <textarea id="descripcion" x-model="form.descripcion" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button @click="open = false">Cancelar</x-secondary-button>
                <x-primary-button @click="guardar()">Guardar</x-primary-button>
            </div>
        </div>
    </div>
</div>
