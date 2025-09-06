
@extends('layouts.app')
@section('title', 'Detalle del Producto')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Detalle del Producto</h2>

        <div class="mb-4 flex justify-end">
            <a href="{{ route('products.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            @can('update', $product)
                <a href="{{ route('products.edit', $product) }}"
                    class="ml-2 flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-yellow-500 border border-transparent rounded-lg active:bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:shadow-outline-yellow">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
            @endcan
            @can('delete', $product)
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="ml-2" onsubmit="return confirm('¿Seguro de eliminar?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-700 hover:bg-red-800 focus:outline-none focus:shadow-outline-red">
                        <i class="fas fa-trash mr-2"></i> Eliminar
                    </button>
                </form>
            @endcan
        </div>

        @include('admin.products.partials.show_card')
    </div>
@endsection
