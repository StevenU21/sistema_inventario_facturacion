@extends('layouts.app')
@section('title', 'Variantes de Producto')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editAction: '',
        editVariant: { id: '', sku: '', barcode: '', product_id: null, color_id: null, size_id: null },
        showVariant: { id: '', sku: '', barcode: '', product: '', color: '', size: '', created_at: '', updated_at: '' },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#"
                        class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Modulo de Inventario
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Variantes de Productos</span>
                </li>
            </ol>
        </nav>

        <!-- Page header card -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-layer-group text-white/90 mr-3"></i>
                            Variantes de Producto
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Gestiona las variantes por producto, color y talla.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('product_variants.export') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                            <input type="hidden" name="color_id" value="{{ request('color_id') }}">
                            <input type="hidden" name="size_id" value="{{ request('size_id') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                        <button type="button" @click="isModalOpen = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                            <i class="fas fa-plus"></i>
                            Crear variante
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mensajes de éxito -->
        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros, búsqueda -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('product_variants.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 items-end">
                    <div class="sm:col-span-3 lg:col-span-4 flex flex-row gap-2 items-end">
                        <div class="flex-1">
                            <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="SKU, código de barras o producto...">
                        </div>
                        <div class="flex flex-row gap-2 items-end">
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                            @if(request()->hasAny(['search','per_page','product_id','color_id','size_id']))
                                <a href="{{ route('product_variants.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
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
                    <label for="product_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Producto</label>
                    <select name="product_id" id="product_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los productos</option>
                        @foreach ($products as $id => $name)
                            <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="color_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Color</label>
                    <select name="color_id" id="color_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los colores</option>
                        @foreach ($colors as $id => $name)
                            <option value="{{ $id }}" {{ request('color_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="size_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Talla</label>
                    <select name="size_id" id="size_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todas las tallas</option>
                        @foreach ($sizes as $id => $name)
                            <option value="{{ $id }}" {{ request('size_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </section>

        <!-- Modales -->
        <x-modal :title="'Crear Variante'" :description="'Agrega una nueva variante.'">
            <form action="{{ route('product_variants.store') }}" method="POST">
                @csrf
                @include('admin.product_variants.partials.form', ['alpine' => false])
            </form>
        </x-modal>

        <x-edit-modal :title="'Editar Variante'" :description="'Modifica la variante seleccionada.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editVariant.id">
                @include('admin.product_variants.partials.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-show-modal :title="'Detalle de Variante'" :description="'Consulta la información de la variante.'">
            @include('admin.product_variants.partials.show_card')
        </x-show-modal>
    </div>

    <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr
                        class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Color</th>
                        <th class="px-4 py-3">Talla</th>
                        <th class="px-4 py-3">Creado</th>
                        <th class="px-4 py-3">Actualizado</th>
                        <th class="px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse ($variants as $variant)
                        <tr
                            class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 text-sm">{{ $variant->id }}</td>
                            <td class="px-4 py-3 text-sm">{{ optional($variant->product)->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ optional($variant->color)->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ optional($variant->size)->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $variant->formatted_created_at }}</td>
                            <td class="px-4 py-3 text-sm">{{ $variant->formatted_updated_at }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <button type="button" title="Ver"
                                        @click="isShowModalOpen=true; showVariant={ id:'{{ $variant->id }}', sku:'{{ $variant->sku }}', barcode:'{{ $variant->barcode }}', product:'{{ optional($variant->product)->name }}', color:'{{ optional($variant->color)->name }}', size:'{{ optional($variant->size)->name }}', created_at:'{{ $variant->created_at?->format('d/m/Y H:i') }}', updated_at:'{{ $variant->updated_at?->format('d/m/Y H:i') }}' }"
                                        class="inline-flex items-center justify-center h-9 w-9 text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                        aria-label="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" title="Editar"
                                        @click="isEditModalOpen=true; editAction='{{ route('product_variants.update', $variant) }}'; editVariant={ id:'{{ $variant->id }}', sku:'{{ $variant->sku }}', barcode:'{{ $variant->barcode }}', product_id:'{{ $variant->product_id }}', color_id:'{{ $variant->color_id }}', size_id:'{{ $variant->size_id }}' }"
                                        class="inline-flex items-center justify-center h-9 w-9 text-green-600 hover:bg-green-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                        aria-label="Editar Modal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('product_variants.destroy', $variant) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar variante?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar"
                                            class="inline-flex items-center justify-center h-9 w-9 text-red-600 hover:bg-red-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                            aria-label="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-center text-gray-500">No hay variantes
                                registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $variants->links() }}
        </div>
    </div>
    </div>
    </div>
@endsection
