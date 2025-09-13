@extends('layouts.app')
@section('title', 'Inventarios')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Modulo de Inventario
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Inventarios</span>
                </li>
            </ol>
        </nav>

        <style>
            .animate-gradient {
                background-image: linear-gradient(90deg, #c026d3, #7c3aed, #4f46e5, #c026d3);
                background-size: 300% 100%;
                animation: gradientShift 8s linear infinite alternate;
                filter: saturate(1.2) contrast(1.05);
                will-change: background-position;
            }

            @keyframes gradientShift {
                0% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .animate-gradient {
                    animation: none;
                }
            }
        </style>

        <!-- Page header card -->
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-warehouse text-white/90 mr-3"></i>
                            Inventarios
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Administra existencias por variante y almacén.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('inventories.export') }}">
                            <input type="hidden" name="product_variant_id" value="{{ request('product_variant_id') }}">
                            <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                        <a href="{{ route('inventories.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                            <i class="fas fa-plus"></i>
                            Nuevo Inventario
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mensajes de éxito -->
        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('inventories.search') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label for="per_page"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Mostrar</label>
                    <select name="per_page" id="per_page"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div>
                    <label for="product_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Producto</label>
                    <select name="product_id" id="product_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
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
                <div>
                    <label for="color_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Color</label>
                    <select name="color_id" id="color_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los colores</option>
                        @isset($colors)
                            @foreach ($colors as $id => $name)
                                <option value="{{ $id }}" {{ request('color_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="size_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Talla</label>
                    <select name="size_id" id="size_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todas las tallas</option>
                        @isset($sizes)
                            @foreach ($sizes as $id => $name)
                                <option value="{{ $id }}" {{ request('size_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="warehouse_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Almacén</label>
                    <select name="warehouse_id" id="warehouse_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
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
                <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
                    @if (request()->hasAny(['per_page', 'product_id', 'color_id', 'size_id', 'warehouse_id']))
                        <a href="{{ route('inventories.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                            <i class="fas fa-undo"></i>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3">
                                <x-table-sort-header field="id" label="ID" route="inventories.search"
                                    icon="<i class='fas fa-hashtag mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="product_variant_id" label="Variante" route="inventories.search"
                                    icon="<i class='fas fa-box mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><i class="fas fa-image mr-2"></i>Imagen</th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="warehouse_id" label="Almacén" route="inventories.search"
                                    icon="<i class='fas fa-warehouse mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="stock" label="Stock" route="inventories.search"
                                    icon="<i class='fas fa-cubes mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="min_stock" label="Mínimo" route="inventories.search"
                                    icon="<i class='fas fa-exclamation-triangle mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="purchase_price" label="Compra" route="inventories.search"
                                    icon="<i class='fas fa-money-bill-wave mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="sale_price" label="Venta" route="inventories.search"
                                    icon="<i class='fas fa-dollar-sign mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($inventories as $inventory)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $inventory->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($inventory->productVariant)
                                        <span
                                            class="font-semibold">{{ $inventory->productVariant->product->name ?? '-' }}</span>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            {{ $colors[$inventory->productVariant->color_id] ?? '-' }}
                                            /
                                            {{ $sizes[$inventory->productVariant->size_id] ?? '-' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($inventory->productVariant && $inventory->productVariant->product && $inventory->productVariant->product->image_url)
                                        <img src="{{ $inventory->productVariant->product->image_url }}"
                                            alt="Imagen del producto" class="w-12 h-12 object-cover rounded">
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
                                    <div class="flex items-center gap-2 text-sm">
                                        <!-- Movement/Edit Page link -->
                                        <a href="{{ route('inventories.edit', $inventory) }}" title="Movimiento"
                                            class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                            aria-label="Realizar Movimiento">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                        <!-- Show Page link -->
                                        <a href="{{ route('inventories.show', $inventory) }}" title="Ver"
                                            class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                            aria-label="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('inventories.destroy', $inventory) }}" method="POST"
                                            onsubmit="return confirm('¿Seguro de eliminar este inventario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
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

        <!-- Per-row modals defined above with their triggers -->
    </div>
@endsection
