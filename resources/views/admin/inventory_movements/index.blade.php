@extends('layouts.app')
@section('title', 'Movimientos de Inventario')

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
                    <span class="text-gray-700 dark:text-gray-200">Movimientos de Inventario</span>
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
                            <i class="fas fa-exchange-alt text-white/90 mr-3"></i>
                            Movimientos de Inventario
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Historial de entradas, salidas, ajustes y transferencias.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- <form method="GET" action="{{ route('inventory_movements.export') }}">
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                            <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                            <input type="hidden" name="entity_id" value="{{ request('entity_id') }}">
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                            <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                            <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
                            <input type="hidden" name="color_id" value="{{ request('color_id') }}">
                            <input type="hidden" name="size_id" value="{{ request('size_id') }}">
                            <input type="hidden" name="type" value="{{ request('type') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
                            <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form> --}}
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
            <form method="GET" action="{{ route('inventory_movements.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                <div class="col-span-1 sm:col-span-3 lg:col-span-5 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <x-autocomplete
                            name="search"
                            :value="request('search')"
                            url="{{ route('inventory_movements.autocomplete') }}"
                            placeholder="Buscar por nombre de producto..."
                            id="search"
                        />
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request()->hasAny(['search', 'per_page', 'user_id', 'entity_id', 'category_id', 'brand_id', 'warehouse_id', 'color_id', 'size_id', 'type']))
                            <a href="{{ route('inventory_movements.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                                <i class="fas fa-undo"></i>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
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
                    <label for="user_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Usuario</label>
                    <select name="user_id" id="user_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los usuarios</option>
                        @isset($users)
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="entity_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Proveedor</label>
                    <select name="entity_id" id="entity_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los proveedores</option>
                        @isset($entities)
                            @foreach ($entities as $id => $name)
                                <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="category_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Categoría</label>
                    <select name="category_id" id="category_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="document.getElementById('brand_id').selectedIndex = 0; this.form.submit()">
                        <option value="">Todas las categorías</option>
                        @isset($categories)
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="brand_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Marca</label>
                    <select name="brand_id" id="brand_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todas las marcas</option>
                        @isset($brands)
                            @foreach ($brands as $id => $name)
                                <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>
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
                <div>
                    <label for="type"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Tipo</label>
                    <select name="type" id="type"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los movimientos</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrada</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Salida</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transferencia
                        </option>
                        <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Devolución</option>
                    </select>
                </div>
            </form>
        </section>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                    route="inventory_movements.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="user_id" label="Usuario"
                                    route="inventory_movements.search" icon="<i class='fas fa-user mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="inventory_id" label="Producto"
                                    route="inventory_movements.search" icon="<i class='fas fa-box mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="type" label="Tipo"
                                    route="inventory_movements.search" icon="<i class='fas fa-exchange-alt mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">Notas</th>
                            <th class="px-4 py-3"><x-table-sort-header field="quantity" label="Cantidad"
                                    route="inventory_movements.search" icon="<i class='fas fa-cubes mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="unit_price" label="Precio Unitario"
                                    route="inventory_movements.search"
                                    icon="<i class='fas fa-money-bill-wave mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="total_price" label="Total"
                                    route="inventory_movements.search" icon="<i class='fas fa-dollar-sign mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><x-table-sort-header field="created_at" label="Fecha"
                                    route="inventory_movements.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" />
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($inventoryMovements as $movement)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $movement->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $movement->user->short_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($movement->inventory && $movement->inventory->productVariant)
                                        <span
                                            class="font-semibold">{{ $movement->inventory->productVariant->product->name ?? '-' }}</span>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            @php
                                                $variant = $movement->inventory->productVariant;
                                                $color = $variant->color->name ?? null;
                                                $size = $variant->size->name ?? null;
                                            @endphp
                                            @if ($color && $size)
                                                {{ $color }} / {{ $size }}
                                            @elseif($color)
                                                {{ $color }}
                                            @elseif($size)
                                                {{ $size }}
                                            @else
                                                {{ $variant->name ?? 'Variante' }}
                                            @endif
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $movement->movement_type }}</td>
                                <td class="px-4 py-3 text-sm">{{ $movement->reference }}</td>
                                <td class="px-4 py-3 text-sm">{{ $movement->quantity }}</td>
                                <td class="px-4 py-3 text-sm">C$ {{ number_format($movement->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm">C$ {{ number_format($movement->total_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $movement->formatted_created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">
                                    No hay movimientos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $inventoryMovements->links() }}
            </div>
        </div>
    </div>
@endsection
