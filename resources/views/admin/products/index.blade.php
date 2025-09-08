@extends('layouts.app')
@section('title', 'Productos')

@section('content')
    <div class="container grid px-6 mx-auto" x-data="{
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
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Productos
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="flex flex-row flex-wrap items-center gap-x-1 gap-y-0.5 mb-0">
                <form method="GET" action="{{ route('products.search') }}"
                    class="flex flex-row gap-x-1 items-center flex-1 min-w-[280px]">
                    <div class="flex flex-col p-0">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                            placeholder="Nombre...">
                    </div>
                    <div class="flex flex-col p-0">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                            Buscar
                        </button>
                    </div>
                </form>

                <div class="flex items-center gap-0.5 ml-auto shrink-0">
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
                    <button type="button" @click="isModalOpen = true"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                        <span>Crear Producto</span>
                        <i class="fas fa-plus ml-2"></i>
                    </button>
                </div>
            </div>

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

            <div class="flex flex-row flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
                <form method="GET" action="{{ route('products.search') }}"
                    class="flex flex-row flex-wrap gap-x-1 gap-y-1 items-end self-end">
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
                        <select name="category_id" id="category_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todas las categorías</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}"
                                    {{ request('category_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="brand_id" id="brand_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todas las marcas</option>
                            @foreach ($brands as $id => $name)
                                <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="entity_id" id="entity_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-48 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los proveedores</option>
                            @foreach ($entities as $id => $name)
                                <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="tax_id" id="tax_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los impuestos</option>
                            @foreach ($taxes as $id => $name)
                                <option value="{{ $id }}" {{ request('tax_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="unit_measure_id" id="unit_measure_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todas las medidas</option>
                            @foreach ($units as $id => $name)
                                <option value="{{ $id }}"
                                    {{ request('unit_measure_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="status" id="status"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible
                            </option>
                            <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>
                                Descontinuado</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Sin
                                stock</option>
                            </option>
                        </select>
                    </div>
                </form>
                <div class="w-full overflow-hidden rounded-lg shadow-xs">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full whitespace-no-wrap">
                            <thead>
                                <tr
                                    class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                    <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                            route="products.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                                    <th class="px-4 py-3"><i class="fas fa-image mr-2"></i>Imagen</th>
                                    <th class="px-4 py-3"><x-table-sort-header field="name" label="Nombre"
                                            route="products.search" icon="<i class='fas fa-box mr-2'></i>" /></th>
                                    <th class="px-4 py-3"><x-table-sort-header field="category_id" label="Categoría"
                                            route="products.search" icon="<i class='fas fa-list-alt mr-2'></i>" /></th>
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
                                            <div class="flex items-center space-x-4 text-sm">
                                                @if (in_array($product->status, ['available', 'out_of_stock']))
                                                    <div x-data="{ isModalOpen: false, closeModal() { this.isModalOpen = false } }">
                                                        <button @click="isModalOpen = true"
                                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
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
                                                    <button type="button"
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
                                                        class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                        aria-label="Editar Modal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif
                                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                    onsubmit="return confirm('{{ $product->status === 'discontinued' ? '¿Seguro de rehabilitar este producto?' : '¿Seguro de descontinuar este producto?' }}\n\n¿Está seguro que desea realizar esta acción?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
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
