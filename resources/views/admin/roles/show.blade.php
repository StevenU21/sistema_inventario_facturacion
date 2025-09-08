@extends('layouts.app')
@section('title', 'Detalles del Rol')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Detalles del Rol
        </h2>

        <div class="mb-4 flex justify-end">
            <a href="{{ route('roles.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <div
            class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
            <div class="p-4">
                <h3
                    class="text-lg font-semibold text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">
                    Información del Rol
                </h3>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                        <i class="fas fa-tag text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Nombre:</strong>
                        {{ $role->name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 flex items-center">
                        <i class="fas fa-align-left text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Descripción:</strong>
                        {{ $role->guard_name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Fecha de creación:</strong>
                        {{ $role->created_at ? $role->created_at->format('d-m-Y H:i:s') : '-' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 flex items-center">
                        <i class="fas fa-clock text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Última actualización:</strong>
                        {{ $role->updated_at ? $role->updated_at->format('d-m-Y H:i:s') : '-' }}
                    </p>
                </div>

                <!-- Permisos asignados -->
                <div class="mt-6">
                    <span class="text-gray-700 dark:text-gray-400 font-semibold">Permisos asignados</span>
                    @if ($permissions->count())
                        <div class="overflow-x-auto mt-2">
                            <table class="w-full table-fixed">
                                <tbody>
                                    @foreach ($permissions->chunk(4) as $row)
                                        <tr>
                                            @foreach ($row as $permission)
                                                <td class="px-2 py-2 align-middle">
                                                    <span
                                                        class="inline-block bg-purple-200 text-purple-900 dark:bg-purple-500 dark:text-white rounded px-2 py-1 text-xs font-semibold uppercase">
                                                        {{ $translatedPermissions[$permission->name] ?? $permission->name }}
                                                    </span>
                                                </td>
                                            @endforeach
                                            @for ($i = $row->count(); $i < 4; $i++)
                                                <td></td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-gray-400 dark:text-gray-500 italic mt-2">Sin permisos asignados</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
