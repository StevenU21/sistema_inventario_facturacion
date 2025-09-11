@extends('layouts.app')
@section('title', 'Auditoría')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('dashboard.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Auditoría</span>
                </li>
            </ol>
        </nav>

        <!-- Page header card -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                 style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);"></div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-clipboard-list text-white/90 mr-3"></i>
                            Auditoría
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Historial de actividades y cambios en el sistema.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('audits.export') }}">
                            <input type="hidden" name="range" value="{{ request('range') }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Messages -->
        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros, búsqueda -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('audits.search') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div>
                    <label for="per_page" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Mostrar</label>
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
                <div class="sm:col-span-2">
                    <label for="causer_id" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Usuario</label>
                    <select name="causer_id" id="causer_id"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            onchange="this.form.submit()">
                        <option value="">Todos los usuarios</option>
                        @foreach ($allCausers as $causer)
                            <option value="{{ $causer->id }}" {{ request('causer_id') == $causer->id ? 'selected' : '' }}>
                                {{ $causer->first_name }} {{ $causer->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="event" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Evento</label>
                    <select name="event" id="event"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            onchange="this.form.submit()">
                        <option value="">Todos los eventos</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Creado</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Actualizado</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminado</option>
                    </select>
                </div>
                <div>
                    <label for="model" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Registro</label>
                    <select name="model" id="model"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            onchange="this.form.submit()">
                        <option value="">Todos los registros</option>
                        @foreach ($allModels as $modelType)
                            @if ($modelType)
                                @php
                                    $base = class_basename($modelType);
                                    $translation = $modelTranslations[$base] ?? $base;
                                @endphp
                                <option value="{{ $modelType }}" {{ request('model') == $modelType ? 'selected' : '' }}>
                                    {{ $translation }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="range" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Rango</label>
                    <select name="range" id="range"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            onchange="this.form.submit()">
                        <option value="">Rango de tiempo</option>
                        <option value="hoy" {{ request('range') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ request('range') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                        <option value="mes" {{ request('range') == 'mes' ? 'selected' : '' }}>Este mes</option>
                        <option value="historico" {{ request('range') == 'historico' ? 'selected' : '' }}>Histórico</option>
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-6 flex gap-2">
                    @if(request()->hasAny(['per_page','causer_id','event','model','range']))
                        <a href="{{ route('audits.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                            <i class="fas fa-undo"></i>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3">
                                <x-table-sort-header field="id" label="ID" route="audits.search" :params="request()->except(['sort', 'direction'])"
                                    icon="<i class='fas fa-hashtag mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="causer_id" label="Usuario" route="audits.search"
                                    :params="request()->except(['sort', 'direction'])" icon="<i class='fas fa-user mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="event" label="Evento" route="audits.search" :params="request()->except(['sort', 'direction'])"
                                    icon="<i class='fas fa-bolt mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="subject_type" label="Registro" route="audits.search"
                                    :params="request()->except(['sort', 'direction'])" icon="<i class='fas fa-cube mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="subject_id" label="Nombre Registro" route="audits.search"
                                    :params="request()->except(['sort', 'direction'])" icon="<i class='fas fa-id-card mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">Antes</th>
                            <th class="px-4 py-3">Después</th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="created_at" label="Fecha" route="audits.search"
                                    :params="request()->except(['sort', 'direction'])" icon="<i class='fas fa-calendar-alt mr-2'></i>" />
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($activities as $activity)
                            <tr class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">{{ $activity->id }}</td>

                                <td class="px-4 py-3 text-sm">
                                    @if ($activity->causer)
                                        {{ $activity->causer->first_name ?? '' }} {{ $activity->causer->last_name ?? '' }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $activity->evento_es ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $activity->modelo_es ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $activity->model_display ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ Str::limit($activity->old, 12) }}</td>
                                <td class="px-4 py-3 text-xs">{{ Str::limit($activity->new, 12) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    registros de auditoría.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection
