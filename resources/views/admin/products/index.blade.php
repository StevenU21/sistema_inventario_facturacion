@extends('layouts.app')
@section('title', 'Productos')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editAction: '',
        showProduct: { id: '', name: '', description: '', barcode: '', category: '', brand: '', unit: '', provider: '', tax: '', status: '', formatted_created_at: '', formatted_updated_at: '' },
        editProduct: { id: '', name: '', description: '', barcode: '', category_id: null, brand_id: null, unit_measure_id: null, entity_id: null, tax_id: null, status: '', image_url: '' },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">
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
                    <span class="text-gray-700 dark:text-gray-200">Productos</span>
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
                            <i class="fas fa-boxes text-white/90 mr-3"></i>
                            Productos
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Busca, filtra y gestiona tus productos.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('products.export') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                            {{-- <input type="hidden" name="category_id" value="{{ request('category_id') }}"> --}}
                            <input type="hidden" name="unit_measure_id" value="{{ request('unit_measure_id') }}">
                            <input type="hidden" name="tax_id" value="{{ request('tax_id') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                        <button type="button" @click="isModalOpen = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                            <i class="fas fa-plus"></i>
                            Crear producto
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mensajes de éxito -->
        <div class="mt-4">
            <x-session-message />
        </div>
        <!-- Fin mensajes de éxito -->

        <!-- Filtros, búsqueda -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('products.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                <div class="col-span-1 sm:col-span-3 lg:col-span-5 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <x-autocomplete
                            name="search"
                            :value="request('search')"
                            url="{{ route('products.autocomplete') }}"
                            placeholder="Nombre..."
                            id="search"
                        />
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request()->hasAny([
                                'search',
                                'per_page',
                                'category_id',
                                'brand_id',
                                'entity_id',
                                'tax_id',
                                'unit_measure_id',
                                'status',
                            ]))
                            <a href="{{ route('products.index') }}"
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
                    <label for="category_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Categoría</label>
                    <select name="category_id" id="category_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todas las categorías</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="brand_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Marca</label>
                    <select name="brand_id" id="brand_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todas las marcas</option>
                        @foreach ($brands as $id => $name)
                            <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="entity_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Proveedor</label>
                    <select name="entity_id" id="entity_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los proveedores</option>
                        @foreach ($entities as $id => $name)
                            <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tax_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Impuesto</label>
                    <select name="tax_id" id="tax_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los impuestos</option>
                        @foreach ($taxes as $id => $name)
                            <option value="{{ $id }}" {{ request('tax_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="unit_measure_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Medida</label>
                    <select name="unit_measure_id" id="unit_measure_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todas las medidas</option>
                        @foreach ($units as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('unit_measure_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                    <select name="status" id="status"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible
                        </option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>
                            Descontinuado</option>
                    </select>
                </div>
            </form>
        </section>


        <!-- Modales: Editar, Crear, Ver -->
        <x-edit-modal :title="'Editar Producto'" :description="'Modifica los datos del producto seleccionado.'">
            <form :action="editAction" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editProduct.id">
                @include('admin.products.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Producto'" :description="'Agrega un nuevo producto al sistema.'">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.products.form', ['alpine' => false])
            </form>
        </x-modal>


        <x-show-modal :title="'Detalle de Producto'" :description="'Consulta los datos del producto seleccionado.'">
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Imagen del producto -->
                <div class="flex justify-center items-start">
                    <div class="rounded-xl overflow-hidden shadow-md border bg-white dark:bg-gray-800 flex items-center justify-center"
                        style="width:200px; height:200px;">
                        <img :src="showProduct.image_url ? showProduct.image_url : '/img/image03.png'"
                            alt="Imagen del producto" class="object-contain mx-auto"
                            style="width:200px; height:auto; max-height:200px;">
                    </div>
                </div>

                <!-- Información principal -->
                <div class="md:col-span-2 space-y-4">
                    <div class="border-b pb-3">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-box text-purple-600 dark:text-purple-400"></i>
                            <span x-text="showProduct.name"></span>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 text-sm" x-text="showProduct.description"></p>
                    </div>

                    <!-- Datos en 2 columnas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-hashtag text-purple-600 dark:text-purple-400"></i>
                            <strong>ID:</strong> <span x-text="showProduct.id"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-barcode text-purple-600 dark:text-purple-400"></i>
                            <strong>Código:</strong> <span x-text="showProduct.barcode"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-list-alt text-purple-600 dark:text-purple-400"></i>
                            <strong>Categoría:</strong> <span x-text="showProduct.category"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-tags text-purple-600 dark:text-purple-400"></i>
                            <strong>Marca:</strong> <span x-text="showProduct.brand"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-balance-scale text-purple-600 dark:text-purple-400"></i>
                            <strong>Medida:</strong> <span x-text="showProduct.unit"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-user-tie text-purple-600 dark:text-purple-400"></i>
                            <strong>Proveedor:</strong> <span x-text="showProduct.provider"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-percent text-purple-600 dark:text-purple-400"></i>
                            <strong>Impuesto:</strong>
                            <span x-text="showProduct.tax"></span>
                            <template
                                x-if="showProduct.tax_percentage !== undefined && showProduct.tax_percentage !== null && showProduct.tax_percentage !== ''">
                                <span>(<span x-text="showProduct.tax_percentage"></span>%)</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400"></i>
                            <strong>Estado:</strong>
                            <template x-if="showProduct.status === 'available'">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-white rounded-full bg-green-600 dark:bg-green-700">Disponible</span>
                            </template>
                            <template x-if="showProduct.status === 'discontinued'">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-white rounded-full bg-gray-500 dark:bg-gray-600">Descontinuado</span>
                            </template>
                            <template x-if="showProduct.status === 'out_of_stock'">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-white rounded-full bg-red-600 dark:bg-red-700">Sin
                                    stock</span>
                            </template>
                            <template x-if="showProduct.status === 'reserved'">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight text-white rounded-full bg-yellow-500 dark:bg-yellow-600">Reservado</span>
                            </template>
                            <template
                                x-if="!['available','discontinued','out_of_stock','reserved'].includes(showProduct.status)">
                                <span class="px-2 py-1 font-semibold leading-tight text-white rounded-full bg-gray-400"
                                    x-text="showProduct.status"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div
                        class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t pt-3 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-purple-500"></i>
                            <strong>Registro:</strong> <span x-text="showProduct.formatted_created_at"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-purple-500"></i>
                            <strong>Actualización:</strong> <span x-text="showProduct.formatted_updated_at"></span>
                        </div>
                    </div>
                </div>
            </div>
        </x-show-modal>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                    route="products.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><i class="fas fa-image mr-2"></i>Imagen</th>
                            <th class="px-4 py-3"><x-table-sort-header field="name" label="Nombre"
                                    route="products.search" icon="<i class='fas fa-box mr-2'></i>" /></th>
                <th class="px-4 py-3"><span class="inline-flex items-center"><i class="fas fa-list-alt mr-2"></i>Categoría</span></th>
                            <th class="px-4 py-3"><x-table-sort-header field="brand_id" label="Marca"
                                    route="products.search" icon="<i class='fas fa-tags mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="unit_measure_id" label="Medida"
                                    route="products.search" icon="<i class='fas fa-balance-scale mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><x-table-sort-header field="entity_id" label="Proveedor"
                                    route="products.search" icon="<i class='fas fa-user-tie mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="status" label="Estado"
                                    route="products.search" icon="<i class='fas fa-money-bill-wave mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($products as $product)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $product->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <img src="{{ $product->image_url }}" alt="Imagen" width="50" class="rounded">
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->brand->category->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->brand->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->unitMeasure->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    {{ $product->entity ? $product->entity->short_name : '-' }}
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
                                    <div class="flex items-center gap-2 text-sm">
                                        @if (in_array($product->status, ['available', 'out_of_stock']))
                                            <div x-data="{ isModalOpen: false, closeModal() { this.isModalOpen = false } }">
                                                <button @click="isModalOpen = true" title="Ver"
                                                    class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                    aria-label="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <x-modal maxWidth="md">
                                                    <x-slot name="title">Detalle del Producto
                                                        #{{ $product->id }}</x-slot>
                                                    <x-slot name="description"></x-slot>
                                                    @include('admin.products.partials.show_card', [
                                                        'product' => $product,
                                                    ])
                                                </x-modal>
                                            </div>
                                            <button type="button" title="Editar"
                                                @click="
                                                            editProduct = {
                                                                id: {{ $product->id }},
                                                                name: '{{ addslashes($product->name) }}',
                                                                description: '{{ addslashes($product->description) }}',
                                                                barcode: '{{ addslashes($product->barcode) }}',
                                                                category_id: {{ $product->category_id ?? 'null' }},
                                                                brand_id: {{ $product->brand_id ?? 'null' }},
                                                                unit_measure_id: {{ $product->unit_measure_id ?? 'null' }},
                                                                entity_id: {{ $product->entity_id ?? 'null' }},
                                                                tax_id: {{ $product->tax_id ?? 'null' }},
                                                                status: '{{ addslashes($product->status) }}',
                                                                image_url: '{{ addslashes($product->image_url) }}'
                                                            };
                                                            editAction = '{{ route('products.update', $product) }}';
                                                            isEditModalOpen = true;
                                                        "
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                aria-label="Editar Modal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                                            onsubmit="return confirm('{{ $product->status === 'discontinued' ? '¿Seguro de rehabilitar este producto?' : '¿Seguro de descontinuar este producto?' }}\n\n¿Está seguro que desea realizar esta acción?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                title="{{ $product->status === 'discontinued' ? 'Rehabilitar' : 'Descontinuar' }}"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                aria-label="{{ $product->status === 'discontinued' ? 'Rehabilitar' : 'Descontinuar' }}">
                                                @if ($product->status === 'discontinued')
                                                    <i class="fas fa-undo"></i>
                                                @else
                                                    <i class="fas fa-ban"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">
                                    No hay productos registrados.</td>
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
