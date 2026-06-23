<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cargos
        </h2>
    </x-slot>

    <div x-data="crudCargos" x-init="cargar()">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex gap-4 mb-4 flex-wrap items-end">
                <button @click="abrirForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    + Nuevo Cargo
                </button>

                <div>
                    <x-input-label for="filtro_nombre_cargo" value="Buscar por nombre" />
                    <x-text-input id="filtro_nombre_cargo" x-model="filtro" @input.debounce.300ms="cargar()" class="mt-1 block" placeholder="Ej: Gerente" />
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Descripcion</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="c in lista" :key="c.id_cargo">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="c.id_cargo"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="c.nombre_cargo"></td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="c.descripcion"></td>
                                <td class="px-4 py-3 text-sm space-x-2">
                                    <button @click="editar(c)" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</button>
                                    <button @click="eliminar(c.id_cargo)" class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="lista.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay cargos registrados.</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Paginacion --}}
                <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700"
                    x-show="meta.last_page > 1"
                    x-cloak>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando <span x-text="meta.from"></span> a <span x-text="meta.to"></span> de <span x-text="meta.total"></span>
                    </div>
                    <div class="flex gap-1">
                        <button @click="cargar(1)" :disabled="meta.current_page === 1"
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">
                            &laquo;
                        </button>
                        <button @click="cargar(meta.current_page - 1)" :disabled="meta.current_page === 1"
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">
                            &lsaquo;
                        </button>

                        <template x-for="page in paginas" :key="page">
                            <button @click="cargar(page)" x-text="page"
                                :class="page === meta.current_page
                                    ? 'px-3 py-1 text-sm rounded bg-blue-600 text-white'
                                    : 'px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                x-show="page !== '...'"
                                :disabled="page === '...'"></button>
                            <span x-show="page === '...'" class="px-2 py-1 text-sm text-gray-400">...</span>
                        </template>

                        <button @click="cargar(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page"
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">
                            &rsaquo;
                        </button>
                        <button @click="cargar(meta.last_page)" :disabled="meta.current_page === meta.last_page"
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">
                            &raquo;
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('cargos.form')
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('crudCargos', () => ({
                lista: [],
                meta: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                filtro: '',
                editando: null,
                form: { nombre_cargo: '', descripcion: '' },

                get paginas() {
                    const current = this.meta.current_page;
                    const last = this.meta.last_page;
                    const pages = [];
                    for (let i = 1; i <= last; i++) {
                        if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                            pages.push(i);
                        } else if (pages[pages.length - 1] !== '...') {
                            pages.push('...');
                        }
                    }
                    return pages;
                },

                async cargar(page = 1) {
                    try {
                        const params = new URLSearchParams();
                        if (this.filtro) params.set('nombre', this.filtro);
                        if (page > 1) params.set('page', page);
                        const qs = params.toString() ? '?' + params.toString() : '';
                        const data = await window.api.listar('cargos', qs);
                        this.lista = data.data;
                        this.meta = data.meta;
                    } catch (e) {
                        console.error('Error al cargar cargos:', e);
                        alert('Error al cargar: ' + (e.message || 'No autenticado'));
                    }
                },

                abrirForm() {
                    this.editando = null;
                    this.form = { nombre_cargo: '', descripcion: '' };
                    this.$dispatch('open-modal');
                },

                editar(c) {
                    this.editando = c.id_cargo;
                    this.form = { nombre_cargo: c.nombre_cargo, descripcion: c.descripcion };
                    this.$dispatch('open-modal');
                },

                async guardar() {
                    try {
                        if (this.editando) {
                            await window.api.actualizar('cargos', this.editando, this.form);
                        } else {
                            await window.api.crear('cargos', this.form);
                        }
                        this.$dispatch('close-modal');
                        await this.cargar(this.meta.current_page);
                    } catch (e) {
                        alert('Error al guardar: ' + (e.message || 'Error desconocido'));
                    }
                },

                async eliminar(id) {
                    if (!confirm('Eliminar este cargo?')) return;
                    try {
                        await window.api.eliminar('cargos', id);
                        await this.cargar(this.meta.current_page);
                    } catch (e) {
                        alert('No se pudo eliminar: ' + (e.message || 'El cargo tiene empleados asignados'));
                    }
                },
            }));
        });
    </script>
</x-app-layout>
