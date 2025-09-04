@extends('layouts.app')
@section('title', 'Roles')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Roles
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <!-- Filtros, búsqueda -->
        <div class="flex flex-wrap gap-x-8 gap-y-4 items-end justify-between mb-4">
            <form method="GET" action="{{ route('roles.index') }}"
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
                        placeholder="Nombre...">
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
                <a href="{{ route('roles.create') }}"
                    class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600">
                    <span>Crear Rol</span>
                    <i class="fas fa-plus ml-2"></i>
                </a>
            </div>
        </div>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">
                                <i class="fas fa-tag mr-2"></i>Nombre
                            </th>
                            <th class="px-4 py-3">
                                <i class="fas fa-calendar-alt mr-2"></i>Fecha de Registro
                            </th>
                            <th class="px-4 py-3">
                                <i class="fas fa-calendar-alt mr-2"></i>Fecha de Actualización
                            </th>
                            <th class="px-4 py-3">
                                <i class="fas fa-tools mr-2"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($roles as $role)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-sm">
                                    {{ $role->name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $role->created_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $role->updated_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <a href="{{ route('roles.show', $role) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $role) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este rol?');">
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
                                    encontraron roles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
