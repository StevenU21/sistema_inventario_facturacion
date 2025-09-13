@section('title', 'Producto #' . $product->id)

@section('content')
    <div class="container grid px-6 mx-auto">
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
                    <span class="text-gray-700 dark:text-gray-200">Ver Producto</span>
                </li>
            </ol>
        </nav>
        <x-session-message />

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
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient mb-6">
            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-box text-white/90 mr-3"></i>
                            {{ $product->name }}
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">{{ $product->description }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('products.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-white/10 hover:bg-white/15 backdrop-blur">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </a>
                        <a href="{{ route('products.edit', $product) }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium bg-yellow-500 hover:bg-yellow-600 text-white">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Meta info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5 text-sm">
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">ID</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $product->id }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Código</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $product->barcode }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Categoría</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ optional(optional($product->brand)->category)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Marca</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ optional($product->brand)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Medida</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ optional($product->unitMeasure)->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Proveedor</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $product->entity ? $product->entity->short_name : '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Impuesto</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ optional($product->tax)->name ?? '-' }}
                            @if (optional($product->tax)->percentage !== null)
                                ({{ optional($product->tax)->percentage }}%)
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Estado</div>
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
                            ];
                            $color = $statusColors[$product->status] ?? 'bg-gray-400';
                            $label = $statusLabels[$product->status] ?? $product->status;
                        @endphp
                        <span
                            class="px-2 py-1 font-semibold leading-tight text-white rounded-full {{ $color }}">{{ $label }}</span>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Registro</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $product->formatted_created_at ?? $product->created_at }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Actualización</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $product->formatted_updated_at ?? $product->updated_at }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variantes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Tabla de variantes -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Variantes</h3>
                </div>
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-y dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800/60">
                                <th class="px-5 py-3">Color</th>
                                <th class="px-5 py-3">Talla</th>
                                <th class="px-5 py-3 text-right">Stock</th>
                                <th class="px-5 py-3 text-right">Creado</th>
                                <th class="px-5 py-3 text-right">Actualizado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            @forelse($product->variants as $variant)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="px-5 py-3 text-sm">{{ $variant->color->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-sm">{{ $variant->size->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-sm text-right">{{ $variant->inventories->sum('stock') ?? 0 }}
                                    </td>
                                    <td class="px-5 py-3 text-sm text-right">
                                        {{ $variant->formatted_created_at ?? $variant->created_at }}</td>
                                    <td class="px-5 py-3 text-sm text-right">
                                        {{ $variant->formatted_updated_at ?? $variant->updated_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-gray-400 dark:text-gray-500">Sin
                                        variantes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Imagen -->
            <div class="flex justify-center items-start">
                <div class="rounded-xl overflow-hidden shadow-md border bg-white dark:bg-gray-800 flex items-center justify-center"
                    style="width:250px; height:250px;">
                    <img src="{{ $product->image_url ?? '/img/image03.png' }}" alt="Imagen del producto"
                        class="object-contain mx-auto" style="width:250px; height:auto; max-height:250px;">
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Detalle del Producto')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Detalle del Producto</h2>

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
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="ml-2"
                    onsubmit="return confirm('¿Seguro de eliminar?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-700 hover:bg-red-800 focus:outline-none focus:shadow-outline-red">
                        <i class="fas fa-trash mr-2"></i> Eliminar
                    </button>
                </form>
            @endcan
        </div>

        @include('admin.products.partials.show_card')
    </div>
@endsection
