@extends('layouts.app')
@section('title', 'Clientes & Proveedores')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Clientes & Proveedores
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <div class="flex justify-end mb-4">
            <a href="{{ route('entities.create') }}"
                class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <span>Crear Cliente & Proveedor</span>
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
                            <th class="px-4 py-3"><i class="fas fa-user mr-2"></i>Nombres</th>
                            <th class="px-4 py-3"><i class="fas fa-id-card mr-2"></i>Cédula</th>
                            <th class="px-4 py-3"><i class="fas fa-phone mr-2"></i>Teléfono</th>
                            <th class="px-4 py-3"><i class="fas fa-map-marked-alt mr-2"></i>Municipio</th>
                            <th class="px-4 py-3"><i class="fas fa-user-check mr-2"></i></th>
                            <th class="px-4 py-3"><i class="fas fa-truck mr-2"></i></th>
                            <th class="px-4 py-3"><i class="fas fa-check mr-2"></i></th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($entities as $entity)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $entity->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $entity->first_name . ' ' . $entity->last_name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $entity->formatted_identity_card }}</td>
                                <td class="px-4 py-3 text-sm">{{ $entity->formatted_phone }}</td>
                                <td class="px-4 py-3 text-sm">{{ $entity->municipality->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($entity->is_client)
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">Sí</span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($entity->is_supplier)
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">Sí</span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($entity->is_active)
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">Sí</span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @if ($entity->is_active)
                                            <a href="{{ route('entities.show', $entity) }}"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('entities.edit', $entity) }}"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('entities.destroy', $entity) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de desactivar esta entidad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Desactivar">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('entities.destroy', $entity) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de activar esta entidad?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Activar">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No se
                                    encontraron entidades.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $entities->links() }}
            </div>
        </div>
    </div>
@endsection
