@extends('layouts.app')
@section('title', 'Movimientos de Inventario')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Movimientos de Inventario
        </h2>

        <x-session-message />

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Notas</th>
                            <th class="px-4 py-3">Cantidad</th>
                            <th class="px-4 py-3">Precio Unitario</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Fecha</th>
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
                                    @if ($movement->inventory)
                                        {{ $movement->inventory->product->name ?? '' }}
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
