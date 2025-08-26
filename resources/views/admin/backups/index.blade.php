@extends('layouts.app')
@section('title', 'Backups')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Backups de Base de Datos
        </h2>
        <div class="mb-4 flex gap-2">
            <a href="{{ route('backups.index') }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ empty($filter) ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-purple-100 dark:hover:bg-purple-800' }}">Todos</a>
            <a href="{{ route('backups.index', ['type' => 'full']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $filter === 'full' ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-purple-100 dark:hover:bg-purple-800' }}">Full
                (.bak)</a>
            <a href="{{ route('backups.index', ['type' => 'diff']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $filter === 'diff' ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-purple-100 dark:hover:bg-purple-800' }}">Diferencial
                (.diff)</a>
            <a href="{{ route('backups.index', ['type' => 'log']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $filter === 'log' ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-purple-100 dark:hover:bg-purple-800' }}">Log
                (.trn)</a>
        </div>
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Tamaño</th>
                            <th class="px-4 py-3">Última modificación</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse ($files as $file)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-sm">{{ $file->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ number_format($file->size / 1048576, 2) }} MB</td>
                                <td class="px-4 py-3 text-sm">{{ $file->last_modified }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <form action="{{ route('backups.restore') }}" method="POST"
                                        onsubmit="return confirm('¿Está seguro que desea restaurar la base de datos desde este backup?\n\nEsta acción es irreversible y reemplazará todos los datos actuales.');">
                                        @csrf
                                        <input type="hidden" name="file" value="{{ $file->name }}">
                                        <button type="submit"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray border border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-800 transition">
                                            <i class="fas fa-database mr-2"></i> Restaurar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    backups disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $files->links() }}
            </div>
        </div>
    </div>
@endsection
