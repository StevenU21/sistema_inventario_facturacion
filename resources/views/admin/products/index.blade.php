@extends('layouts.app')
@section('title', 'Productos')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Productos
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="flex flex-row flex-wrap gap-x-4 gap-y-4 items-end justify-between mb-2">
                <form method="GET" action="{{ route('products.search') }}" class="flex flex-row gap-x-4 items-end w-full">
                    <div class="flex flex-col p-1 flex-1">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-full text-sm font-medium"
                            placeholder="Nombre, descripción, código de barras...">
                    </div>
                    <div class="flex flex-col p-1">
                        <label class="invisible block text-sm font-medium">.</label>
                        <button type="submit"
                            class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
            <div class="flex flex-row flex-wrap gap-x-4 gap-y-4 items-end justify-between mb-4">
                <form method="GET" action="{{ route('products.search') }}"
                    class="flex flex-row flex-wrap gap-x-4 gap-y-4 items-end self-end">
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
                        <select name="category_id" id="category_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todas las categorías</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-1">
                        <select name="unit_measure_id" id="unit_measure_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todas las unidades</option>
                            @foreach ($units as $id => $name)
                                <option value="{{ $id }}"
                                    {{ request('unit_measure_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-1">
                        <select name="status" id="status"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible
                            </option>
                            <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>
                                Descontinuado</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Sin
                                stock</option>
                            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reservado
                            </option>
                        </select>
                    </div>
                </form>
                <div class="flex flex-row gap-x-2 items-end">
                    <form method="GET" action="{{ route('products.export') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        <input type="hidden" name="unit_measure_id" value="{{ request('unit_measure_id') }}">
                        <input type="hidden" name="tax_id" value="{{ request('tax_id') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button type="submit"
                            class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                            <span>Exportar Excel</span>
                            <i class="fas fa-file-excel ml-2"></i>
                        </button>
                    </form>
                    <a href="{{ route('products.create') }}"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                        <span>Crear Producto</span>
                        <i class="fas fa-plus ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                <th class="px-4 py-3"><i class="fas fa-hashtag mr-2"></i>ID</th>
                                <th class="px-4 py-3"><i class="fas fa-image mr-2"></i>Imagen</th>
                                <th class="px-4 py-3"><i class="fas fa-box mr-2"></i>Nombre</th>
                                <th class="px-4 py-3"><i class="fas fa-list-alt mr-2"></i>Categoría</th>
                                <th class="px-4 py-3"><i class="fas fa-tags mr-2"></i>Marca</th>
                                <th class="px-4 py-3"><i class="fas fa-balance-scale mr-2"></i>Medida</th>
                                <th class="px-4 py-3"><i class="fas fa-user-tie mr-2"></i>Proveedor</th>
                                <th class="px-4 py-3"><i class="fas fa-money-bill-wave mr-2"></i>Estado</th>
                                <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            @forelse($products as $product)
                                <tr class="text-gray-700 dark:text-gray-400">
                                    <td class="px-4 py-3 text-xs">
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                            {{ $product->id }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        <img src="{{ $product->image_url }}" alt="Imagen" width="50"
                                            class="rounded">
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $product->category->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $product->brand->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $product->unitMeasure->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $product->entity ? $product->entity->first_name . ' ' . $product->entity->last_name : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $statusColors = [
                                                'available' => 'bg-green-600 dark:bg-green-700',
                                                'discontinued' => 'bg-gray-500 dark:bg-gray-600',
                                                'out_of_stock' => 'bg-red-600 dark:bg-red-700',
                                                'reserved' => 'bg-yellow-500 dark:bg-yellow-600',
                                            ];
                                            $statusLabels = [
                                                'available' => 'Disponible',
                                                'discontinued' => 'Descontinuado',
                                                'out_of_stock' => 'Sin stock',
                                                'reserved' => 'Reservado',
                                            ];
                                            $color = $statusColors[$product->status] ?? 'bg-gray-400';
                                            $label = $statusLabels[$product->status] ?? $product->status;
                                        @endphp
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-white rounded-full {{ $color }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-4 text-sm">
                                            <a href="{{ route('products.show', $product) }}"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                onsubmit="return confirm('¿Seguro de eliminar este producto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No
                                        hay productos registrados.</td>
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
    </div>
@endsection
