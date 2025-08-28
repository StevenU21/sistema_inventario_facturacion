@extends('layouts.app')
@section('title', 'Productos')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Productos
        </h2>

        <!-- Mensajes de éxito -->
        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        <!-- Fin mensajes de éxito -->

        <div class="flex justify-end mb-4">
            @can('create', App\Models\Product::class)
                <a href="{{ route('products.create') }}"
                    class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    <span>Nuevo Producto</span>
                    <i class="fas fa-plus ml-2"></i>
                </a>
            @endcan
        </div>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3"><i class="fas fa-image mr-2"></i>Imagen</th>
                            <th class="px-4 py-3"><i class="fas fa-box mr-2"></i>Nombre</th>
                            <th class="px-4 py-3"><i class="fas fa-tags mr-2"></i>Marca</th>
                            <th class="px-4 py-3"><i class="fas fa-list-alt mr-2"></i>Categoría</th>
                            <th class="px-4 py-3"><i class="fas fa-align-left mr-2"></i>Descripción</th>
                            <th class="px-4 py-3"><i class="fas fa-dollar-sign mr-2"></i>Precio Compra</th>
                            <th class="px-4 py-3"><i class="fas fa-dollar-sign mr-2"></i>Precio Venta</th>
                            <th class="px-4 py-3"><i class="fas fa-cubes mr-2"></i>Stock</th>
                            <th class="px-4 py-3"><i class="fas fa-check mr-2"></i>Estado</th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($products as $product)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <img src="{{ $product->image_url }}" alt="Imagen" width="50" class="rounded">
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->brand->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->description }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->purchase_price }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->sale_price }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->stock }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($product->productStatus && $product->productStatus->name)
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">{{ $product->productStatus->name }}</span>
                                    @else
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">Sin estado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @can('view', $product)
                                            <a href="{{ route('products.show', $product) }}"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('update', $product)
                                            <a href="{{ route('products.edit', $product) }}"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $product)
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Seguro de eliminar este producto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
