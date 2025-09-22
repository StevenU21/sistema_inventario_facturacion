@extends('layouts.app')
@section('title', 'Ver Rol')

@section('content')
    <div class="container px-6 mx-auto grid">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Módulo de Seguridad
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Ver Rol</span>
                </li>
            </ol>
        </nav>

        <style>
            .animate-gradient {background-image: linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#c026d3);background-size:300% 100%;animation:gradientShift 8s linear infinite alternate;filter:saturate(1.2) contrast(1.05);will-change:background-position}
            @keyframes gradientShift {0%{background-position:100% 50%}100%{background-position:0% 50%}}
            @media (prefers-reduced-motion: reduce){.animate-gradient{animation:none}}
        </style>
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient mb-6">
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image:radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%),radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);"></div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-user-shield text-white/90 mr-3"></i>
                            Ver Rol
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Detalles y permisos asignados al rol.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('roles.index') }}" class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-white/10 hover:bg-white/15 rounded-lg backdrop-blur">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <div class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
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
