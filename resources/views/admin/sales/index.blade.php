@extends('layouts.app')
@section('title', 'Ventas')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1 text-gray-400 dark:text-gray-500"></i> Módulo de Ventas
                    </a>
                </li>
                <li class="text-gray-400 dark:text-gray-500">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Ventas</span>
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
                            <i class="fas fa-cash-register text-white/90 mr-3"></i>
                            Ventas
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Busca, filtra y exporta tus ventas.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.sales.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                            <i class="fas fa-plus"></i>
                            Registrar venta
                        </a>
                        <form method="GET" action="{{ route('admin.sales.export') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="payment_method_id" value="{{ request('payment_method_id') }}">
                            <input type="hidden" name="entity_id" value="{{ request('entity_id') }}">
                            <input type="hidden" name="from" value="{{ request('from') }}">
                            <input type="hidden" name="to" value="{{ request('to') }}">
                            <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                            <input type="hidden" name="color_id" value="{{ request('color_id') }}">
                            <input type="hidden" name="size_id" value="{{ request('size_id') }}">
                            <input type="hidden" name="is_credit" value="{{ request('is_credit') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.sales.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                <div class="col-span-1 sm:col-span-3 lg:col-span-5 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <x-autocomplete name="search" :value="request('search')" url="{{ route('admin.sales.autocomplete') }}"
                            placeholder="Nombre producto..." id="search" />
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-rose-600 hover:bg-rose-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request()->hasAny([
                                'search',
                                'per_page',
                                'payment_method_id',
                                'entity_id',
                                'from',
                                'to',
                                'brand_id',
                                'color_id',
                                'size_id',
                                'is_credit',
                            ]))
                            <a href="{{ route('admin.sales.index') }}"
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
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div>
                    <label for="brand_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Marca</label>
                    <select name="brand_id" id="brand_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
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
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
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
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
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
                    <label for="entity_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Cliente</label>
                    <select name="entity_id" id="entity_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los clientes</option>
                        @isset($entities)
                            @foreach ($entities as $id => $name)
                                <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="payment_method_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Método</label>
                    <select name="payment_method_id" id="payment_method_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los métodos</option>
                        @isset($methods)
                            @foreach ($methods as $id => $name)
                                <option value="{{ $id }}"
                                    {{ request('payment_method_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="is_credit"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Tipo</label>
                    <select name="is_credit" id="is_credit"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        onchange="this.form.submit()">
                        <option value="">Todas</option>
                        <option value="1" {{ request('is_credit') === '1' ? 'selected' : '' }}>Crédito</option>
                        <option value="0" {{ request('is_credit') === '0' ? 'selected' : '' }}>Contado</option>
                    </select>
                </div>
                <div>
                    <label for="from"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        onchange="this.form.submit()" />
                </div>
                <div>
                    <label for="to"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        onchange="this.form.submit()" />
                </div>
            </form>
        </section>

        <!-- Tabla -->
        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Método de pago</th>
                            <th class="px-4 py-3">Tipo de pago</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3 text-right">Precio Unitario</th>
                            <th class="px-4 py-3 text-right">Impuesto</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @isset($sales)
                            @forelse($sales as $sale)
                                <tr
                                    class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-xs"><span
                                            class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700">{{ $sale->id }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $firstDetail = $sale->saleDetails->first();
                                            $variant = $firstDetail?->productVariant;
                                            $productName = $variant?->product?->name ?? '-';
                                            $colorName = isset($variant) ? $colors[$variant->color_id] ?? '-' : '-';
                                            $sizeName = isset($variant) ? $sizes[$variant->size_id] ?? '-' : '-';
                                        @endphp
                                        @if ($variant)
                                            <span class="font-semibold">{{ $productName }}</span><br>
                                            <span class="text-xs text-gray-500">{{ $colorName }} /
                                                {{ $sizeName }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ trim(($sale->entity?->first_name ?? '') . ' ' . ($sale->entity?->last_name ?? '')) ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $sale->paymentMethod?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $sale->is_credit ? 'Crédito' : 'Contado' }}</td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        @php $totalQty = $sale->saleDetails->sum('quantity'); @endphp
                                        {{ $totalQty > 0 ? $totalQty : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">C$
                                        @php $firstUnitPrice = optional($sale->saleDetails->first())->unit_price; @endphp
                                        {{ number_format($firstUnitPrice ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($sale->tax_amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($sale->total ?? 0, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2 text-sm">
                                            <a href="{{ route('admin.sales.pdf', $sale) }}" title="PDF"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('admin.sales.exportDetails', $sale) }}"
                                                title="Exportar detalle"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                                <i class="fas fa-file-excel"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                        ventas registradas.</td>
                                </tr>
                            @endforelse
                        @endisset
                    </tbody>
                </table>
            </div>
            @isset($sales)
                <div class="mt-4">{{ $sales->links() }}</div>
            @endisset
        </div>
    </div>
@endsection
