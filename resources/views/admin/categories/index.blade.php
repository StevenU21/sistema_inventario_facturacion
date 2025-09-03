@extends('layouts.app')
@section('title', 'Categorías')

@section('content')
    <div class="container grid px-6 mx-auto" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editId: null,
        editAction: '',
        showCategory: { id: '', name: '', description: '', formatted_created_at: '', formatted_updated_at: '' },
        editCategory: { id: '', name: '', description: '' },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Categorías
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <!-- Filtros, búsqueda -->
        <div class="flex flex-wrap gap-x-8 gap-y-4 items-end justify-between mb-4">
            <form method="GET" action="{{ route('categories.search') }}"
                class="flex flex-wrap gap-x-4 gap-y-4 items-end self-end">
                <div class="flex flex-col p-1">
                    <select name="per_page" id="per_page"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-16 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                        placeholder="Nombre o descripción...">
                </div>
                <div class="flex flex-col p-1">
                    <label class="invisible block text-sm font-medium">.</label>
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Buscar
                    </button>
                </div>
            </form>
            <div class="flex flex-col p-1">
                <label class="invisible block text-sm font-medium">.</label>
                <button @click="isModalOpen = true" type="button"
                    class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600">
                    <span>Crear Categoría</span>
                    <i class="fas fa-plus ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Edit Modal Trigger and Component -->
        <x-edit-modal :title="'Editar Categoría'" :description="'Modifica los datos de la categoría seleccionada.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editCategory.id">
                @include('admin.categories.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Categoría'" :description="'Agrega una nueva categoría al sistema.'">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                @include('admin.categories.form', ['alpine' => false])
            </form>
        </x-modal>

        <!-- Show Modal Trigger and Component -->
        <x-show-modal :title="'Detalle de Categoría'" :description="'Consulta los datos de la categoría seleccionada.'">
            <div class="mt-4">
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                    <i class="fas fa-hashtag text-purple-600 dark:text-purple-400"></i>
                    <strong>ID:</strong> <span x-text="showCategory.id"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-tag text-purple-600 dark:text-purple-400"></i>
                    <strong>Nombre:</strong> <span x-text="showCategory.name"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-align-left text-purple-600 dark:text-purple-400"></i>
                    <strong>Descripción:</strong> <span x-text="showCategory.description"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400"></i>
                    <strong>Fecha de Registro:</strong> <span x-text="showCategory.formatted_created_at"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                    <strong>Fecha de Actualización:</strong> <span x-text="showCategory.formatted_updated_at"></span>
                </p>
            </div>
        </x-show-modal>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                    route="categories.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="name" label="Nombre"
                                    route="categories.search" icon="<i class='fas fa-tag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="description" label="Descripción"
                                    route="categories.search" icon="<i class='fas fa-align-left mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="created_at" label="Fecha de Registro"
                                    route="categories.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="updated_at" label="Fecha de Actualización"
                                    route="categories.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3">
                                <i class="fas fa-tools mr-2"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($categories as $category)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $category->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $category->name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $category->formatted_created_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $category->formatted_updated_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <button type="button"
                                            @click="showCategory = { id: {{ $category->id }}, name: '{{ $category->name }}', description: '{{ $category->description }}', formatted_created_at: '{{ $category->formatted_created_at }}', formatted_updated_at: '{{ $category->formatted_updated_at }}' }; isShowModalOpen = true;"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver Modal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button"
                                            @click="editCategory = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description) }}' }; editAction = '{{ route('categories.update', $category) }}'; isEditModalOpen = true;"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Editar Modal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No se
                                    encontraron categorías.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
