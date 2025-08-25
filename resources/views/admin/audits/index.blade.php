@extends('layouts.app')
@section('title', 'Auditoría')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Auditoría
        </h2>
        <form method="GET" action="{{ route('audits.export') }}" class="mb-4 flex items-center gap-2">
            <label for="range" class="mr-2 font-semibold">Exportar:</label>
            <select name="range" id="range" class="form-select rounded border-gray-300">
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mes</option>
                <option value="completo">Histórico</option>
            </select>
            <button type="submit" class="ml-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Exportar
                Excel</button>
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
                                <td class="px-4 py-3 text-xs">{{ $activity->old }}</td>
                                <td class="px-4 py-3 text-xs">{{ $activity->new }}</td>
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
