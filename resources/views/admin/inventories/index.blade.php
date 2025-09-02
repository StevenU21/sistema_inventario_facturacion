@extends('layouts.app')
@section('title', 'Inventarios')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Inventarios
        </h2>
        <x-session-message />
        <div class="flex justify-end mb-4">
            <a href="{{ route('inventories.create') }}"
                class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <span>Nuevo Inventario</span>
                <i class="fas fa-plus ml-2"></i>
            </a>
        </div>
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3"><i class="fas fa-hashtag mr-2"></i>ID</th>
                            <th class="px-4 py-3"><i class="fas fa-box mr-2"></i>Producto</th>
                            <th class="px-4 py-3"><i class="fas fa-warehouse mr-2"></i>Almacén</th>
                            <th class="px-4 py-3"><i class="fas fa-cubes mr-2"></i>Stock</th>
                            <th class="px-4 py-3"><i class="fas fa-exclamation-triangle mr-2"></i>Stock Mínimo</th>
                            <th class="px-4 py-3"><i class="fas fa-money-bill-wave mr-2"></i>Precio Compra</th>
                            <th class="px-4 py-3"><i class="fas fa-dollar-sign mr-2"></i>Precio Venta</th>
                            <th class="px-4 py-3"><i class="fas fa-calendar-alt mr-2"></i>Creado</th>
                            <th class="px-4 py-3"><i class="fas fa-calendar-alt mr-2"></i>Actualizado</th>
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
                                <td class="px-4 py-3 text-sm">{{ $inventory->product->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->warehouse->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->stock }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->min_stock }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->purchase_price }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->sale_price }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->formatted_created_at ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inventory->formatted_updated_at ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <a href="{{ route('inventories.show', $inventory) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventories.edit', $inventory) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
    </div>
@endsection
