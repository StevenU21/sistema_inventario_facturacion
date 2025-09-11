@extends('layouts.app')
@section('title', 'Almacenes')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editId: null,
        editAction: '',
        showWarehouse: { id: '', name: '', address: '', description: '', is_active: true, formatted_created_at: '', formatted_updated_at: '' },
        editWarehouse: { id: '', name: '', address: '', description: '', is_active: true },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('dashboard.index') }}"
                        class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Almacenes</span>
                </li>
            </ol>
        </nav>

        <!-- Page header card -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-warehouse text-white/90 mr-3"></i>
                            Almacenes
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Gestiona tus almacenes y su estado.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('warehouses.export') }}" class="mr-0">
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                            <input type="hidden" name="is_active" value="{{ request('is_active') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
                            <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                        @can('create warehouses')
                            <button @click="isModalOpen = true" type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                                <i class="fas fa-plus"></i>
                                Nuevo Almacén
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </section>

        <!-- Mensajes de éxito -->
        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros, búsqueda -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('warehouses.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                <div class="sm:col-span-5 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Nombre, dirección o descripción...">
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request()->hasAny(['per_page', 'search', 'is_active']))
                            <a href="{{ route('warehouses.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                                <i class="fas fa-undo"></i>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
                <div>
                    <label for="per_page"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Mostrar</label>
                    <select name="per_page" id="per_page"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div>
                    <label for="is_active"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                    <select name="is_active" id="is_active"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
            </form>
        </section>

        <!-- Edit Modal and Create Modal -->
        <x-edit-modal :title="'Editar Almacén'" :description="'Modifica los datos del almacén seleccionado.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editWarehouse.id">
                @include('admin.warehouses.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Almacén'" :description="'Agrega un nuevo almacén al sistema.'">
            <form action="{{ route('warehouses.store') }}" method="POST">
                @csrf
                @include('admin.warehouses.form', ['alpine' => false])
            </form>
        </x-modal>
        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                    route="warehouses.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="name" label="Nombre"
                                    route="warehouses.search" icon="<i class='fas fa-warehouse mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="address" label="Dirección"
                                    route="warehouses.search" icon="<i class='fas fa-map-marker-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="description" label="Descripción"
                                    route="warehouses.search" icon="<i class='fas fa-align-left mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="is_active" label="Estado"
                                    route="warehouses.search" icon="<i class='fas fa-toggle-on mr-2'></i>" /></th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($warehouses as $warehouse)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $warehouse->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $warehouse->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $warehouse->address }}</td>
                                <td class="px-4 py-3 text-sm">{{ $warehouse->description }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($warehouse->is_active)
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 text-sm">
                                        @if ($warehouse->is_active)
                                            @can('read warehouses')
                                                <button type="button" title="Ver"
                                                    @click="showWarehouse = { id: {{ $warehouse->id }}, name: '{{ addslashes($warehouse->name) }}', address: '{{ addslashes($warehouse->address) }}', description: '{{ addslashes($warehouse->description) }}', is_active: {{ $warehouse->is_active ? 'true' : 'false' }}, formatted_created_at: '{{ $warehouse->formatted_created_at }}', formatted_updated_at: '{{ $warehouse->formatted_updated_at }}' }; isShowModalOpen = true;"
                                                    class="inline-flex items-center justify-center h-9 w-9 text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                    aria-label="Ver Modal">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endcan
                                            @can('update warehouses')
                                                <button type="button" title="Editar"
                                                    @click="editWarehouse = { id: {{ $warehouse->id }}, name: '{{ addslashes($warehouse->name) }}', address: '{{ addslashes($warehouse->address) }}', description: '{{ addslashes($warehouse->description) }}', is_active: {{ $warehouse->is_active ? 'true' : 'false' }} }; editAction = '{{ route('warehouses.update', $warehouse) }}'; isEditModalOpen = true;"
                                                    class="inline-flex items-center justify-center h-9 w-9 text-green-600 hover:bg-green-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                    aria-label="Editar Modal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endcan
                                            @can('destroy warehouses')
                                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                                                    onsubmit="return confirm('¿Seguro de desactivar este almacén?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Desactivar"
                                                        class="inline-flex items-center justify-center h-9 w-9 text-red-600 hover:bg-red-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                        aria-label="Desactivar">
                                                        <i class="fas fa-toggle-off"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('destroy warehouses')
                                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                                                    onsubmit="return confirm('¿Seguro de activar este almacén?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Activar"
                                                        class="inline-flex items-center justify-center h-9 w-9 text-green-600 hover:bg-green-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                        aria-label="Activar">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    almacenes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $warehouses->links() }}
            </div>
        </div>

        <!-- Show Modal -->
        <x-show-modal :title="'Detalle de Almacén'" :description="'Consulta los datos del almacén seleccionado.'">
            <div class="mt-4 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                <p class="flex items-center gap-2"><i class="fas fa-hashtag text-purple-600"></i><strong>ID:</strong>
                    <span x-text="showWarehouse.id"></span>
                </p>
                <p class="flex items-center gap-2"><i
                        class="fas fa-warehouse text-purple-600"></i><strong>Nombre:</strong> <span
                        x-text="showWarehouse.name"></span></p>
                <p class="flex items-center gap-2"><i
                        class="fas fa-map-marker-alt text-purple-600"></i><strong>Dirección:</strong> <span
                        x-text="showWarehouse.address"></span></p>
                <p class="flex items-center gap-2"><i
                        class="fas fa-align-left text-purple-600"></i><strong>Descripción:</strong> <span
                        x-text="showWarehouse.description"></span></p>
                <p class="flex items-center gap-2"><i
                        class="fas fa-toggle-on text-purple-600"></i><strong>Estado:</strong> <span
                        x-text="showWarehouse.is_active ? 'Activo' : 'Inactivo'"></span></p>
                <p class="flex items-center gap-2"><i class="fas fa-calendar-alt text-purple-600"></i><strong>Fecha de
                        Registro:</strong> <span x-text="showWarehouse.formatted_created_at"></span></p>
                <p class="flex items-center gap-2"><i class="fas fa-clock text-purple-600"></i><strong>Fecha de
                        Actualización:</strong> <span x-text="showWarehouse.formatted_updated_at"></span></p>
            </div>
        </x-show-modal>
    </div>
@endsection
