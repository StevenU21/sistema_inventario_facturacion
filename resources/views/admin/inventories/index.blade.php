@extends('layouts.app')
@section('title', 'Inventarios')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Inventarios
        </h2>
        <x-session-message />

        <div class="flex flex-wrap gap-x-8 gap-y-4 items-end justify-between mb-4">
            <form method="GET" action="{{ route('inventories.search') }}" class="flex flex-wrap gap-x-4 gap-y-4 items-end self-end">
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
                    <select name="product_id" id="product_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los productos</option>
                        @isset($products)
                            @foreach ($products as $id => $name)
                                <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="warehouse_id" id="warehouse_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los almacenes</option>
                        @isset($warehouses)
                            @foreach ($warehouses as $id => $name)
                                <option value="{{ $id }}" {{ request('warehouse_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </form>
            <div class="flex flex-row p-1 gap-x-4 items-end">
                <label class="invisible block text-sm font-medium">.</label>
                <form method="GET" action="{{ route('inventories.export') }}">
                    <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                    <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                        <span>Exportar Excel</span>
                        <i class="fas fa-file-excel ml-2"></i>
                    </button>
                </form>
                <a href="{{ route('inventories.create') }}"
                    class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                    <span>Nuevo Inventario</span>
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
                            <th class="px-4 py-3">
                                <x-table-sort-header field="id" label="ID" route="inventories.search" icon="<i class='fas fa-hashtag mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="product_id" label="Producto" route="inventories.search" icon="<i class='fas fa-box mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><i class="fas fa-image mr-2"></i>Imagen</th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="warehouse_id" label="Almacén" route="inventories.search" icon="<i class='fas fa-warehouse mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="stock" label="Stock" route="inventories.search" icon="<i class='fas fa-cubes mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="min_stock" label="Mínimo" route="inventories.search" icon="<i class='fas fa-exclamation-triangle mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="purchase_price" label="Compra" route="inventories.search" icon="<i class='fas fa-money-bill-wave mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="sale_price" label="Venta" route="inventories.search" icon="<i class='fas fa-dollar-sign mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($inventories as $inventory)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $inventory->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->product->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($inventory->product && $inventory->product->image_url)
                                        <img src="{{ $inventory->product->image_url }}" alt="Imagen del producto"
                                            class="w-12 h-12 object-cover rounded">
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">No disponible</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->warehouse->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->stock }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->min_stock }}</td>
                                <td class="px-4 py-3 text-sm">C$ {{ number_format($inventory->purchase_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm">C$ {{ number_format($inventory->sale_price, 2) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <a href="{{ route('inventories.edit', $inventory) }}"
                                            class="flex items-center px-2 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-green"
                                            aria-label="Realizar Movimiento">
                                            <i class="fas fa-exchange-alt mr-2"></i> Movimiento
                                        </a>
                                        <a href="{{ route('inventories.show', $inventory) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('inventories.destroy', $inventory) }}" method="POST"
                                            onsubmit="return confirm('¿Seguro de eliminar este inventario?');">
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
                                <td colspan="10" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    inventarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
@endsection
