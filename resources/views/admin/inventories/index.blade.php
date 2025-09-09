@extends('layouts.app')
@section('title', 'Inventarios')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Inventarios
        </h2>
        <x-session-message />

        <div class="flex flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
            <form method="GET" action="{{ route('inventories.search') }}"
                class="flex flex-wrap gap-x-1 gap-y-1 items-end self-end">
                <div class="flex flex-col p-0.5">
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
                <div class="flex flex-col p-0.5">
                    <select name="product_id" id="product_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-44 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los productos</option>
                        @isset($products)
                            @foreach ($products as $id => $name)
                                <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <select name="color_id" id="color_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-36 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los colores</option>
                        @isset($colors)
                            @foreach ($colors as $id => $name)
                                <option value="{{ $id }}" {{ request('color_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                <option value="{{ $id }}" {{ request('size_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <select name="warehouse_id" id="warehouse_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-46 text-sm font-medium"
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
            <div class="flex flex-row p-0.5 gap-x-1 items-end">
                <label class="invisible block text-sm font-medium">.</label>
                <form method="GET" action="{{ route('inventories.export') }}">
                    <input type="hidden" name="product_variant_id" value="{{ request('product_variant_id') }}">
                    <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                        <span>Exportar Excel</span>
                        <i class="fas fa-file-excel ml-2"></i>
                    </button>
                </form>
                <!-- Create Modal Trigger + Modal -->
                <div x-data="{ isModalOpen: false, closeModal() { this.isModalOpen = false } }" class="ml-2">
                    <button @click="isModalOpen = true"
                        class="flex items-center justify-center px-4 py-2 w-48 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600">
                        <span>Nuevo Inventario</span>
                        <i class="fas fa-plus ml-2"></i>
                    </button>
                    <x-modal maxWidth="md">
                        <x-slot name="title">Nuevo Inventario</x-slot>
                        <x-slot name="description"></x-slot>
                        <form action="{{ route('inventories.store') }}" method="POST">
                            @csrf
                            @include('admin.inventories.form_create')
                        </form>
                    </x-modal>
                </div>
            </div>
        </div>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
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
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($inventories as $inventory)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $inventory->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($inventory->productVariant)
                                        <span class="font-semibold">{{ $inventory->productVariant->product->name ?? '-' }}</span>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            {{ $inventory->productVariant->color->name ?? '-' }}
                                            /
                                            {{ $inventory->productVariant->size->name ?? '-' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($inventory->productVariant && $inventory->productVariant->product && $inventory->productVariant->product->image_url)
                                        <img src="{{ $inventory->productVariant->product->image_url }}" alt="Imagen del producto"
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
                                        <!-- Edit/Movement Modal per row -->
                                        <div x-data="{ isModalOpen: false, closeModal() { this.isModalOpen = false } }">
                                            <button @click="isModalOpen = true"
                                                class="flex items-center px-2 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-green"
                                                aria-label="Realizar Movimiento">
                                                <i class="fas fa-exchange-alt mr-2"></i> Movimiento
                                            </button>
                                            <x-modal maxWidth="lg">
                                                <x-slot name="title">Registrar Movimiento</x-slot>
                                                <x-slot name="description"></x-slot>
                                                <form action="{{ route('inventories.update', $inventory) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    @include('admin.inventories.form_edit', [
                                                        'inventory' => $inventory,
                                                        'warehouses' => $warehouses,
                                                    ])
                                                </form>
                                            </x-modal>
                                        </div>
                                        <!-- Show Modal per row -->
                                        <div x-data="{ isModalOpen: false, closeModal() { this.isModalOpen = false } }">
                                            <button @click="isModalOpen = true"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <x-modal maxWidth="md">
                                                <x-slot name="title">Detalle de Inventario
                                                    #{{ $inventory->id }}</x-slot>
                                                <x-slot name="description"></x-slot>
                                                @include('admin.inventories.partials.show_card', [
                                                    'inventory' => $inventory,
                                                ])
                                            </x-modal>
                                        </div>
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

        <!-- Per-row modals defined above with their triggers -->
    </div>
@endsection
