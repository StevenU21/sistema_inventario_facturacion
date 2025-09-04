@extends('layouts.app')
@section('title', 'Auditoría')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Auditoría
        </h2>
        <form method="GET" action="{{ route('audits.export') }}"
            class="mb-6 flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <label for="range" class="font-semibold text-gray-700 dark:text-gray-200 mr-2">Exportar:</label>
            <select name="range" id="range"
                class="form-select rounded-lg border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition w-40 text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-800">
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mes</option>
                <option value="completo">Histórico</option>
            </select>
            <button type="submit"
                class="ml-2 px-5 py-2 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Exportar Excel
            </button>
        </form>
        <x-session-message />
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Evento</th>
                            <th class="px-4 py-3">Modelo</th>
                            <th class="px-4 py-3">ID Modelo</th>
                            <th class="px-4 py-3">Antes</th>
                            <th class="px-4 py-3">Después</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse ($activities as $activity)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">{{ $activity->id }}</td>
                                <td class="px-4 py-3 text-sm">{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($activity->causer)
                                        {{ $activity->causer->first_name ?? '' }} {{ $activity->causer->last_name ?? '' }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $activity->evento_es ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $activity->modelo_es ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $activity->subject_id ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ Str::limit($activity->old, 12) }}</td>
                                <td class="px-4 py-3 text-xs">{{ Str::limit($activity->new, 12) }}</td>
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
