@extends('layouts.app')
@section('title', 'Movimientos de Inventario')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Movimientos de Inventario
        </h2>

        <x-session-message />

        <div class="flex flex-col gap-y-2 mb-4">
            <div class="flex w-full">
                <div class="flex-1"></div>
                <div class="flex flex-col p-0.5 ml-auto">
                    <label class="invisible block text-sm font-medium">.</label>
                    <form method="GET" action="{{ route('inventory_movements.export') }}">
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                        <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                        <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
                        <input type="hidden" name="color_id" value="{{ request('color_id') }}">
                        <input type="hidden" name="size_id" value="{{ request('size_id') }}">
                        <input type="hidden" name="type" value="{{ request('type') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="sort" value="{{ request('sort', 'id') }}">
                        <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                        <button type="submit"
                            class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                            <span>Exportar Excel</span>
                            <i class="fas fa-file-excel ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>
            <form method="GET" action="{{ route('inventory_movements.search') }}"
                class="flex flex-wrap gap-x-1 gap-y-1 items-end self-end">
                <div class="flex flex-col p-0.5">
                    <select name="per_page" id="per_page"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-20 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <select name="user_id" id="user_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
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
                <div class="flex flex-col p-0.5">
                    <select name="product_id" id="product_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-48 text-sm font-medium"
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
                <div class="flex flex-col p-0.5">
                    <select name="color_id" id="color_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
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
                <div class="flex flex-col p-0.5">
                    <select name="size_id" id="size_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-36 text-sm font-medium"
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
                <div class="flex flex-col p-0.5">
                    <select name="warehouse_id" id="warehouse_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-48 text-sm font-medium"
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
                <div class="flex flex-col p-0.5">
                    <select name="type" id="type"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-42 text-sm font-medium"
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
        </div>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
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
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($inventoryMovements as $movement)
                            <tr class="text-gray-700 dark:text-gray-400">
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
