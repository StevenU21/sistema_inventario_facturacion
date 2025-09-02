@extends('layouts.app')
@section('title', 'Detalle del Almacén')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Detalle del Almacén
        </h2>
        <div class="mb-4 flex justify-end">
            <a href="{{ route('warehouses.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            @can('update', $warehouse)
                <a href="{{ route('warehouses.edit', $warehouse) }}"
                    class="ml-2 flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-yellow-500 border border-transparent rounded-lg active:bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:shadow-outline-yellow">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
            @endcan
            @can('delete', $warehouse)
                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="ml-2" onsubmit="return confirm('¿Seguro de eliminar?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-700 hover:bg-red-800 focus:outline-none focus:shadow-outline-red">
                        <i class="fas fa-trash mr-2"></i> Eliminar
                    </button>
                </form>
            @endcan
        </div>
        <div class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150 mb-4">
                    Información del Almacén
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                        <i class="fas fa-warehouse text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Nombre:</strong> {{ $warehouse->name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                        <i class="fas fa-map-marker-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Dirección:</strong> {{ $warehouse->address }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center md:col-span-2">
                        <i class="fas fa-align-left text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Descripción:</strong> {{ $warehouse->description }}
                    </p>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Creado:</strong> {{ $warehouse->formatted_created_at }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                        <i class="fas fa-clock text-purple-600 dark:text-purple-400 mr-2"></i>
                        <strong class="text-gray-700 dark:text-gray-200 mr-1">Actualizado:</strong> {{ $warehouse->formatted_updated_at }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
