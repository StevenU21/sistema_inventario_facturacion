@extends('layouts.app')
@section('title', 'Categorías')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Categorías
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <div class="flex justify-end mb-4">
            <a href="{{ route('categories.create') }}"
                class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <span>Crear Categoría</span>
                <i class="fas fa-plus ml-2"></i>
            </a>
        </div>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">
                                <i class="fas fa-hashtag mr-2"></i>ID
                            </th>
                            <th class="px-4 py-3">
                                <i class="fas fa-tag mr-2"></i>Nombre
                            </th>
                            <th class="px-4 py-3">
                                <i class="fas fa-align-left mr-2"></i>Descripción
                            </th>
                            <th class="px-4 py-3">
                                <i class="fas fa-calendar-alt mr-2"></i>Fecha de Registro
                            </th>
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
                                    {{ $category->description }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $category->formatted_created_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <a href="{{ route('categories.show', $category) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
