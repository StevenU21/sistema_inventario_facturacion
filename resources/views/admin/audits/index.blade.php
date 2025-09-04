@extends('layouts.app')
@section('title', 'Auditoría')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Auditoría
        </h2>
        <div class="flex flex-wrap gap-x-8 gap-y-4 items-end justify-between mb-4">
            <form method="GET" action="{{ route('audits.search') }}"
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
                    <select name="causer_id" id="causer_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los usuarios</option>
                        @foreach ($allCausers as $causer)
                            <option value="{{ $causer->id }}" {{ request('causer_id') == $causer->id ? 'selected' : '' }}>
                                {{ $causer->first_name }} {{ $causer->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="event" id="event"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los eventos</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Creado</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Actualizado</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminado</option>
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="model" id="model"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los modelos</option>
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

                <div class="flex flex-col p-1">
                    <select name="range" id="range"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Rango de tiempo</option>
                        <option value="hoy" {{ request('range') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ request('range') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                        <option value="mes" {{ request('range') == 'mes' ? 'selected' : '' }}>Este mes</option>
                        <option value="historico" {{ request('range') == 'historico' ? 'selected' : '' }}>Histórico
                        </option>
                    </select>
                </div>
            </form>
            <form method="GET" action="{{ route('audits.export') }}"
                class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <input type="hidden" name="range" value="{{ request('range') }}">
                <button type="submit"
                    class="px-5 py-2 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Exportar Excel
                </button>
            </form>
        </div>
        <x-session-message />
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
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
                                <x-table-sort-header field="subject_type" label="Modelo" route="audits.search"
                                    :params="request()->except(['sort', 'direction'])" icon="<i class='fas fa-cube mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="subject_id" label="Nombre Modelo" route="audits.search"
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
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse ($activities as $activity)
                            <tr class="text-gray-700 dark:text-gray-400">
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
