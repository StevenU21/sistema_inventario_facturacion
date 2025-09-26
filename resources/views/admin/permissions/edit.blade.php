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
                @php
                    // Agrupar por prefijo antes del primer punto. Si no hay punto, va a "otros".
                    $groupedPermissions = $displayPermissions->groupBy(function ($perm) {
                        return str_contains($perm, '.') ? \Illuminate\Support\Str::before($perm, '.') : 'otros';
                    })->sortKeys();
                @endphp

                <div class="mb-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input id="permission-filter" type="text" placeholder="Filtrar permisos..."
                        class="w-full pl-10 pr-9 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600"
                    >
                    <button type="button" id="permission-filter-clear" aria-label="Limpiar filtro"
                        class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                        <i class="fas fa-times-circle text-sm"></i>
                    </button>
                </div>

                <div class="space-y-6" id="permission-groups-wrapper">
                    @foreach ($groupedPermissions as $group => $perms)
                        <fieldset data-permission-group class="border border-gray-200 dark:border-gray-700 rounded-md p-4">
                            <legend class="px-2 text-sm font-semibold text-purple-700 dark:text-purple-400 uppercase tracking-wide">
                                {{ __((string) \Illuminate\Support\Str::of($group)->replace('_', ' ')->title()) }}
                            </legend>
                            <div class="flex flex-wrap -mx-2">
                                @foreach ($perms->sort() as $perm)
                                    <div class="w-1/2 md:w-1/3 lg:w-1/4 px-2 py-1">
                                        <label data-permission-label="{{ \Illuminate\Support\Str::lower($translatedDisplayPermissions[$perm] ?? \App\Classes\PermissionTranslator::translate($perm)) }}"
                                            class="flex items-center space-x-2 text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" name="permission[]" value="{{ $perm }}" class="form-checkbox h-4 w-4 text-purple-600"
                                                @if ($directPermissions->contains($perm)) checked @endif>
                                            <span class="uppercase leading-snug">{{ $translatedDisplayPermissions[$perm] ?? \App\Classes\PermissionTranslator::translate($perm) }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
                        <i class="fas fa-paper-plane mr-2"></i> Guardar
                    </button>
                </div>

                <script>
                    (function () {
                        const input = document.getElementById('permission-filter');
                        const clearBtn = document.getElementById('permission-filter-clear');
                        if(!input) return;

                        function applyFilter() {
                            const term = input.value.trim().toLowerCase();
                            if(clearBtn) {
                                if(term.length) clearBtn.classList.remove('hidden'); else clearBtn.classList.add('hidden');
                            }
                            const groups = document.querySelectorAll('#permission-groups-wrapper [data-permission-group]');
                            groups.forEach(groupEl => {
                                let anyVisibleInGroup = false;
                                const items = groupEl.querySelectorAll('[data-permission-label]');
                                items.forEach(label => {
                                    const text = label.getAttribute('data-permission-label');
                                    const container = label.closest('div');
                                    if(!term || text.includes(term)) {
                                        container.classList.remove('hidden');
                                        anyVisibleInGroup = true;
                                    } else {
                                        container.classList.add('hidden');
                                    }
                                });
                                groupEl.classList.toggle('hidden', !anyVisibleInGroup);
                            });
                        }

                        input.addEventListener('input', applyFilter);
                        if(clearBtn) {
                            clearBtn.addEventListener('click', () => {
                                input.value = '';
                                applyFilter();
                                input.focus();
                            });
                        }
                    })();
                </script>
            @else
                <div class="text-gray-700 dark:text-gray-300 italic py-4">No hay permisos especiales disponibles para
                    asignar.</div>
            @endif
        </form>
    </div>
@endsection
    