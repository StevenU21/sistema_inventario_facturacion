@extends('layouts.app')
@section('title', 'Colores')

@section('content')
    <div class="container grid px-6 mx-auto" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editId: null,
        editAction: '',
        showColor: { id: '', name: '', hex_code: '', formatted_created_at: '', formatted_updated_at: '' },
        editColor: { id: '', name: '', hex_code: '' },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Colores
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <!-- Filtros, búsqueda -->
        <div class="flex flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
            <form method="GET" action="{{ route('colors.search') }}"
                class="flex flex-wrap gap-x-1 gap-y-1 items-end self-end">
                <div class="flex flex-col p-0.5">
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
                <div class="flex flex-col p-0.5">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                        placeholder="Nombre o código hex...">
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="invisible block text-sm font-medium">.</label>
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Buscar
                    </button>
                </div>
            </form>
            <div class="flex flex-col p-0.5">
                <label class="invisible block text-sm font-medium">.</label>
                @can('create colors')
                    <button @click="isModalOpen = true" type="button"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600">
                        <span>Crear Color</span>
                        <i class="fas fa-plus ml-2"></i>
                    </button>
                @endcan
            </div>
        </div>

        <!-- Edit Modal Trigger and Component -->
        <x-edit-modal :title="'Editar Color'" :description="'Modifica los datos del color seleccionado.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editColor.id">
                @include('admin.colors.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Color'" :description="'Agrega un nuevo color al sistema.'">
            <form action="{{ route('colors.store') }}" method="POST">
                @csrf
                @include('admin.colors.form', ['alpine' => false])
            </form>
        </x-modal>

        <!-- Show Modal Trigger and Component -->
        <x-show-modal :title="'Detalle de Color'" :description="'Consulta los datos del color seleccionado.'">
            <div class="mt-4">
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                    <i class="fas fa-hashtag text-purple-600 dark:text-purple-400"></i>
                    <strong>ID:</strong> <span x-text="showColor.id"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-tag text-purple-600 dark:text-purple-400"></i>
                    <strong>Nombre:</strong> <span x-text="showColor.name"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-align-left text-purple-600 dark:text-purple-400"></i>
                    <strong>Código Hex:</strong> <span x-text="showColor.hex_code"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400"></i>
                    <strong>Fecha de Registro:</strong> <span x-text="showColor.formatted_created_at"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                    <strong>Fecha de Actualización:</strong> <span x-text="showColor.formatted_updated_at"></span>
                </p>
            </div>
        </x-show-modal>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID" route="colors.search"
                                    icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="name" label="Nombre" route="colors.search"
                                    icon="<i class='fas fa-tag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="hex_code" label="Código Hex"
                                    route="colors.search" icon="<i class='fas fa-align-left mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="created_at" label="Fecha de Registro"
                                    route="colors.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="updated_at" label="Fecha de Actualización"
                                    route="colors.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3">
                                <i class="fas fa-tools mr-2"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($colors as $color)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $color->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $color->name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $color->hex_code ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $color->formatted_created_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $color->formatted_updated_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @can('read colors')
                                            <button type="button"
                                                @click="showColor = { id: {{ $color->id }}, name: '{{ $color->name }}', hex_code: '{{ $color->hex_code }}', formatted_created_at: '{{ $color->formatted_created_at }}', formatted_updated_at: '{{ $color->formatted_updated_at }}' }; isShowModalOpen = true;"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver Modal">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endcan
                                        @can('update colors')
                                            <button type="button"
                                                @click="editColor = { id: {{ $color->id }}, name: '{{ addslashes($color->name) }}', hex_code: '{{ addslashes($color->hex_code) }}' }; editAction = '{{ route('colors.update', $color) }}'; isEditModalOpen = true;"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Editar Modal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('destroy colors')
                                            <form action="{{ route('colors.destroy', $color) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de eliminar este color?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No se
                                    encontraron colores.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $colors->links() }}
            </div>
        </div>
    </div>
@endsection
