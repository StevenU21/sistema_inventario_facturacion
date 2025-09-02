
@extends('layouts.app')
@section('title', 'Detalle del Producto')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Detalle del Producto
        </h2>

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

        <div class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center justify-center bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <img src="{{ $product->image_url }}" alt="Imagen del producto" class="object-cover rounded shadow-lg" style="max-width: 220px; max-height: 220px;">
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150 mb-4">
                        Información del Producto
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-box text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Nombre:</strong> {{ $product->name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-align-left text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Descripción:</strong> {{ $product->description }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Precio Compra:</strong> {{ $product->purchase_price }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-dollar-sign text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Precio Venta:</strong> {{ $product->sale_price }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-cubes text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Stock:</strong> {{ $product->stock }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-exclamation-triangle text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Stock Mínimo:</strong> {{ $product->min_stock }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-tags text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Marca:</strong> {{ $product->brand->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-list-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Categoría:</strong> {{ $product->category->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-percentage text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Impuesto:</strong> {{ $product->tax->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-balance-scale text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Unidad de Medida:</strong> {{ $product->unitMeasure->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-user-tie text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Entidad:</strong> {{ $product->entity->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-toggle-on text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Estado:</strong> {{ $product->productStatus->name ?? '-' }}
                        </p>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Creado:</strong> {{ $product->formatted_created_at }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="fas fa-clock text-purple-600 dark:text-purple-400 mr-2"></i>
                            <strong class="text-gray-700 dark:text-gray-200 mr-1">Actualizado:</strong> {{ $product->formatted_updated_at }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
