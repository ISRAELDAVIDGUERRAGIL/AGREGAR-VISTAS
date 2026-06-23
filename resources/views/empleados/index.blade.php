<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Empleados
        </h2>
    </x-slot>

    <div x-data="crudEmpleados" x-init="cargar()">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex gap-4 mb-4 flex-wrap items-end">
                <button @click="abrirForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    + Nuevo Empleado
                </button>

                <div>
                    <x-input-label for="filtro_nombre" value="Buscar" />
                    <x-text-input id="filtro_nombre" x-model="filtros.nombre" @input.debounce.300ms="cargar()" class="mt-1 block" />
                </div>
                <div>
                    <x-input-label for="filtro_estado" value="Estado" />
                    <select id="filtro_estado" x-model="filtros.estado" @change="cargar()"
                        class="mt-1 block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="filtro_cargo" value="Cargo" />
                    <select id="filtro_cargo" x-model="filtros.cargo" @change="cargar()"
                        class="mt-1 block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option value="">Todos</option>
                        <template x-for="c in cargosLista" :key="c.id_cargo">
                            <option :value="c.id_cargo" x-text="c.nombre_cargo"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nombres</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Apellidos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cargo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Salario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="e in lista" :key="e.id_empleado">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="e.id_empleado"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="e.nombres"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="e.apellidos"></td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="e.cargo?.nombre_cargo"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="'$' + Number(e.salario).toLocaleString()"></td>
                                <td class="px-4 py-3 text-sm">
                                    <span x-text="e.estado"
                                        :class="e.estado === 'activo' ? 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-xs' : 'text-red-600 bg-red-100 dark:bg-red-900 dark:text-red-300 px-2 py-1 rounded-full text-xs'"></span>
                                </td>
                                <td class="px-4 py-3 text-sm space-x-2">
                                    <button @click="editar(e)" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</button>
                                    <button @click="eliminar(e.id_empleado)" class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="lista.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay empleados registrados.</td>
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
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">&laquo;</button>
                        <button @click="cargar(meta.current_page - 1)" :disabled="meta.current_page === 1"
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">&lsaquo;</button>

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
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">&rsaquo;</button>
                        <button @click="cargar(meta.last_page)" :disabled="meta.current_page === meta.last_page"
                            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 disabled:opacity-50">&raquo;</button>
                    </div>
                </div>
            </div>
        </div>

        @include('empleados.form')
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('crudEmpleados', () => ({
                lista: [],
                cargosLista: [],
                meta: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
                editando: null,
                filtros: { nombre: '', estado: '', cargo: '' },
                form: { id_cargo: '', nombres: '', apellidos: '', fecha_nacimiento: '', fecha_ingreso: '', salario: '', estado: 'activo' },

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
                        if (this.filtros.nombre) params.set('nombre', this.filtros.nombre);
                        if (this.filtros.estado) params.set('estado', this.filtros.estado);
                        if (this.filtros.cargo) params.set('cargo', this.filtros.cargo);
                        if (page > 1) params.set('page', page);
                        const qs = params.toString() ? '?' + params.toString() : '';
                        const [empData, cargData] = await Promise.all([
                            window.api.listar('empleados', qs),
                            window.api.listar('cargos'),
                        ]);
                        this.lista = empData.data;
                        this.meta = empData.meta;
                        this.cargosLista = cargData.data;
                    } catch (e) {
                        console.error('Error al cargar empleados:', e);
                        alert('Error al cargar: ' + (e.message || 'No autenticado'));
                    }
                },

                abrirForm() {
                    this.editando = null;
                    this.form = { id_cargo: '', nombres: '', apellidos: '', fecha_nacimiento: '', fecha_ingreso: '', salario: '', estado: 'activo' };
                    this.$dispatch('open-modal');
                },

                editar(e) {
                    this.editando = e.id_empleado;
                    this.form = {
                        id_cargo: e.id_cargo,
                        nombres: e.nombres,
                        apellidos: e.apellidos,
                        fecha_nacimiento: e.fecha_nacimiento,
                        fecha_ingreso: e.fecha_ingreso,
                        salario: e.salario,
                        estado: e.estado,
                    };
                    this.$dispatch('open-modal');
                },

                async guardar() {
                    try {
                        if (this.editando) {
                            await window.api.actualizar('empleados', this.editando, this.form);
                        } else {
                            await window.api.crear('empleados', this.form);
                        }
                        this.$dispatch('close-modal');
                        await this.cargar(this.meta.current_page);
                    } catch (e) {
                        alert('Error al guardar: ' + (e.message || 'Error desconocido'));
                    }
                },

                async eliminar(id) {
                    if (!confirm('Eliminar este empleado?')) return;
                    try {
                        await window.api.eliminar('empleados', id);
                        await this.cargar(this.meta.current_page);
                    } catch (e) {
                        alert('No se pudo eliminar: ' + (e.message || 'Error desconocido'));
                    }
                },
            }));
        });
    </script>
</x-app-layout>
