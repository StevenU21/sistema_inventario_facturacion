@extends('layouts.app')
@section('title', 'Compras')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Compras</h2>
        <x-session-message />

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="flex flex-row flex-wrap items-center gap-x-1 gap-y-0.5 mb-2">
                <form method="GET" action="{{ route('purchases.search') }}"
                    class="flex flex-row gap-x-1 items-center flex-1 min-w-[280px]">
                    <div class="flex flex-col p-0">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                            placeholder="Referencia...">
                    </div>
                    <div class="flex flex-col p-0">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                            Buscar
                        </button>
                    </div>
                </form>

                <div class="flex items-center gap-0.5 ml-auto shrink-0">
                    <form method="GET" action="{{ route('purchases.export') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="payment_method_id" value="{{ request('payment_method_id') }}">
                        <input type="hidden" name="entity_id" value="{{ request('entity_id') }}">
                        <input type="hidden" name="warehouse_id" value="{{ request('warehouse_id') }}">
                        <input type="hidden" name="from" value="{{ request('from') }}">
                        <input type="hidden" name="to" value="{{ request('to') }}">
                        <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                        <button type="submit"
                            class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                            <span>Exportar Excel</span>
                            <i class="fas fa-file-excel ml-2"></i>
                        </button>
                    </form>
                    <a href="{{ route('purchases.create') }}"
                        class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                        <span>Nueva Compra</span>
                        <i class="fas fa-plus ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="flex flex-row flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
                <form method="GET" action="{{ route('purchases.search') }}"
                    class="flex flex-row flex-wrap gap-x-1 gap-y-1 items-end self-end">
                    <div class="flex flex-col p-0.5">
                        <select name="per_page"
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
                        <select name="payment_method_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los métodos</option>
                            @isset($methods)
                                @foreach ($methods as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ request('payment_method_id') == $id ? 'selected' : '' }}>{{ $name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="entity_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-48 text-sm font-medium"
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
                    <div class="flex flex-col p-0.5">
                        <select name="warehouse_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los almacenes</option>
                            @isset($warehouses)
                                @foreach ($warehouses as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ request('warehouse_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="product_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Desde</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium" />
                    </div>
                    <div class="flex flex-col p-0.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Hasta</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium" />
                    </div>
                    <div class="flex flex-col p-0.5">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                            Filtrar
                        </button>
                    </div>
                </form>

                <div class="w-full overflow-hidden rounded-lg shadow-xs">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full whitespace-no-wrap">
                            <thead>
                                <tr
                                    class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Producto</th>
                                    <th class="px-4 py-3">Proveedor</th>
                                    <th class="px-4 py-3">Almacén</th>
                                    <th class="px-4 py-3">Método</th>
                                    <th class="px-4 py-3 text-right">Cantidad</th>
                                    <th class="px-4 py-3 text-right">Precio Unitario</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                                @forelse($purchases as $purchase)
                                    <tr class="text-gray-700 dark:text-gray-400">
                                        <td class="px-4 py-3 text-xs">
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">{{ $purchase->id }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $firstProductName = optional(
                                                    $purchase->details->first()?->productVariant?->product,
                                                )->name;
                                            @endphp
                                            {{ $firstProductName ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $purchase->entity?->short_name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $purchase->warehouse?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $purchase->paymentMethod?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            @php
                                                $firstQty = optional($purchase->details->first())->quantity;
                                            @endphp
                                            {{ $firstQty ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right">C$
                                            @php
                                                $firstUnitPrice = optional($purchase->details->first())->unit_price;
                                            @endphp
                                            {{ number_format($firstUnitPrice ?? 0, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right">C$
                                            {{ number_format($purchase->total ?? 0, 2) }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-4 text-sm">
                                                <a href="{{ route('purchases.show', $purchase) }}"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('purchases.edit', $purchase) }}"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                    aria-label="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar esta compra?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-red-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                        aria-label="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">
                                            No hay compras registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
