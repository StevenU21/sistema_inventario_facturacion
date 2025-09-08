@extends('layouts.app')
@section('title', 'Asignar Permisos a Usuario')

@section('content')
    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="flex justify-end mb-4">
            <a href="{{ route('users.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Asignar Permisos a: {{ $user->first_name }} {{ $user->last_name }}
        </h2>
        <x-session-message />

        <!-- Permisos heredados por roles -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Permisos heredados por
                roles</label>
            @if ($rolePermissions->count())
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <tbody>
                            @foreach ($rolePermissions->chunk(4) as $row)
                                <tr>
                                    @foreach ($row as $index => $perm)
                                        <td class="px-2 py-2 align-middle">
                                            <label
                                                class="flex items-center space-x-2 font-semibold text-gray-400 dark:text-gray-500 opacity-80 cursor-not-allowed">
                                                <input type="checkbox" checked disabled class="form-checkbox">
                                                <span
                                                    class="uppercase">{{ $translatedRolePermissions[$perm] ?? \App\Classes\PermissionTranslator::translate($perm) }}</span>
                                            </label>
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
                <span class="text-gray-400 dark:text-gray-500">Sin permisos heredados</span>
            @endif
        </div>

        <!-- Permisos especiales (directos) -->
        <form action="{{ route('users.permissions.assign', $user) }}" method="POST" class="w-full">
            @csrf
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-400 mb-2">Permisos especiales
                (directos)</label>
            {{-- La lógica de permisos ya viene procesada desde el controlador --}}
            @if ($displayPermissions->count())
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <tbody>
                            @foreach ($displayPermissions->chunk(4) as $row)
                                <tr>
                                    @foreach ($row as $index => $perm)
                                        <td class="px-2 py-2 align-middle">
                                            <label
                                                class="flex items-center space-x-2 font-semibold text-gray-700 dark:text-gray-300">
                                                <input type="checkbox" name="permission[]" value="{{ $perm }}"
                                                    class="form-checkbox" @if ($directPermissions->contains($perm)) checked @endif>
                                                <span
                                                    class="uppercase">{{ $translatedDisplayPermissions[$perm] ?? \App\Classes\PermissionTranslator::translate($perm) }}</span>
                                            </label>
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
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
                        <i class="fas fa-paper-plane mr-2"></i> Guardar
                    </button>
                </div>
            @else
                <div class="text-gray-700 dark:text-gray-300 italic py-4">No hay permisos especiales disponibles para
                    asignar.</div>
            @endif
        </form>
    </div>
@endsection
    