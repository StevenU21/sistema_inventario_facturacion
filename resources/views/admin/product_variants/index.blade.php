@extends('layouts.app')
@section('title', 'Variantes de Producto')

@section('content')
    <div class="container grid px-6 mx-auto" x-data="{
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
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Variantes de Producto</h2>

        <x-session-message />

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="flex flex-row flex-wrap items-center gap-x-1 gap-y-0.5 mb-2">
                <form method="GET" action="{{ route('product_variants.search') }}"
                    class="flex flex-row gap-x-1 items-center flex-1 min-w-[280px]">
                    <div class="flex flex-col p-0">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                            placeholder="SKU, código de barras o producto...">
                    </div>
                    <div class="flex flex-col p-0">
                        <button type="submit"
                            class="flex items-center justify-center px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                            Buscar
                        </button>
                    </div>
                </form>
                <div class="flex items-center gap-0.5 ml-auto shrink-0">
                    <form method="GET" action="{{ route('product_variants.export') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                        <input type="hidden" name="color_id" value="{{ request('color_id') }}">
                        <input type="hidden" name="size_id" value="{{ request('size_id') }}">
                        <button type="submit"
                            class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                            <span>Exportar Excel</span>
                            <i class="fas fa-file-excel ml-2"></i>
                        </button>
                    </form>
                    <button type="button" @click="isModalOpen = true"
                        class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                        <span>Crear Variante</span>
                        <i class="fas fa-plus ml-2"></i>
                    </button>
                </div>
            </div>

            <div class="flex flex-row flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
                <form method="GET" action="{{ route('product_variants.search') }}"
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
                        <select name="product_id" id="product_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-48 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los productos</option>
                            @foreach ($products as $id => $name)
                                <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="color_id" id="color_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todos los colores</option>
                            @foreach ($colors as $id => $name)
                                <option value="{{ $id }}" {{ request('color_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col p-0.5">
                        <select name="size_id" id="size_id"
                            class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium"
                            onchange="this.form.submit()">
                            <option value="">Todas las tallas</option>
                            @foreach ($sizes as $id => $name)
                                <option value="{{ $id }}" {{ request('size_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

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

            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3">Color</th>
                                <th class="px-4 py-3">Talla</th>
                                <th class="px-4 py-3">Creado</th>
                                <th class="px-4 py-3">Actualizado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($variants as $variant)
                                <tr class="text-gray-700 dark:text-gray-400">
                                    <td class="px-4 py-3 text-sm">{{ $variant->id }}</td>
                                    <td class="px-4 py-3 text-sm">{{ optional($variant->product)->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ optional($variant->color)->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ optional($variant->size)->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $variant->formatted_created_at }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $variant->formatted_updated_at }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-4 text-sm">
                                            <button type="button"
                                                @click="isShowModalOpen=true; showVariant={ id:'{{ $variant->id }}', sku:'{{ $variant->sku }}', barcode:'{{ $variant->barcode }}', product:'{{ optional($variant->product)->name }}', color:'{{ optional($variant->color)->name }}', size:'{{ optional($variant->size)->name }}', created_at:'{{ $variant->created_at?->format('d/m/Y H:i') }}', updated_at:'{{ $variant->updated_at?->format('d/m/Y H:i') }}' }"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button"
                                                @click="isEditModalOpen=true; editAction='{{ route('product_variants.update', $variant) }}'; editVariant={ id:'{{ $variant->id }}', sku:'{{ $variant->sku }}', barcode:'{{ $variant->barcode }}', product_id:'{{ $variant->product_id }}', color_id:'{{ $variant->color_id }}', size_id:'{{ $variant->size_id }}' }"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Editar Modal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('product_variants.destroy', $variant) }}"
                                                method="POST" onsubmit="return confirm('¿Eliminar variante?')">
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
