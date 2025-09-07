@extends('layouts.app')
@section('title', 'Kardex')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Kardex (Costo Promedio Ponderado)
        </h2>

        <x-session-message />

        <div class="flex flex-wrap gap-x-8 gap-y-4 items-end justify-between mb-4">
            <form method="GET" action="{{ route('kardex.index') }}"
                class="flex flex-wrap gap-x-4 gap-y-4 items-end self-end">
                <div class="flex flex-col p-1">
                    <label class="block text-sm font-medium">Producto</label>
                    <select name="product_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-64 text-sm font-medium">
                        <option value="">-- Seleccione --</option>
                        @foreach ($products as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) $id === (string) ($productId ?? '') ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <label class="block text-sm font-medium">Almacén</label>
                    <select name="warehouse_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium">
                        <option value="">Todos</option>
                        @foreach ($warehouses as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) $id === (string) ($warehouseId ?? '') ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <label class="block text-sm font-medium">Desde</label>
                    <input type="date" name="from" value="{{ $from }}"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-44 text-sm font-medium" />
                </div>
                <div class="flex flex-col p-1">
                    <label class="block text-sm font-medium">Hasta</label>
                    <input type="date" name="to" value="{{ $to }}"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-44 text-sm font-medium" />
                </div>
                <div class="flex flex-col p-1">
                    <label class="invisible block text-sm font-medium">.</label>
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Generar
                    </button>
                </div>
            </form>

            @if ($kardexModel)
                <div class="flex flex-row p-1 gap-x-4 items-end">
                    <label class="invisible block text-sm font-medium">.</label>
                    <a href="{{ route('kardex.export', request()->all()) }}" target="_blank"
                        class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                        <span>Exportar PDF</span>
                        <i class="fas fa-file-pdf ml-2"></i>
                    </a>
                </div>
            @endif
        </div>

        @if ($kardexModel)
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto bg-white dark:bg-gray-800 p-4">
                    <div class="mb-4 text-gray-700 dark:text-gray-200">
                        <p><strong>Producto:</strong> {{ $kardexModel->product->name }}</p>
                        <p><strong>Almacén:</strong> {{ $kardexModel->warehouse->name ?? 'Todos' }}</p>
                        <p><strong>Rango:</strong> {{ $kardexModel->date_from }} a {{ $kardexModel->date_to }}</p>
                    </div>
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                <th class="px-4 py-3">Fecha y hora</th>
                                <th class="px-4 py-3">Concepto</th>
                                <th class="px-4 py-3">Almacén</th>
                                <th class="px-4 py-3 text-right">Entrada (Cant.)</th>
                                <th class="px-4 py-3 text-right">Salida (Cant.)</th>
                                <th class="px-4 py-3 text-right">Existencias</th>
                                <th class="px-4 py-3 text-right">Costo unitario</th>
                                <th class="px-4 py-3 text-right">Costo promedio</th>
                                <th class="px-4 py-3 text-right">Debe</th>
                                <th class="px-4 py-3 text-right">Haber</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($kardexModel->rows as $r)
                                <tr class="text-gray-700 dark:text-gray-400">
                                    <td class="px-4 py-3 text-sm">{{ $r['date'] }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $r['concept'] ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $r['warehouse'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['entry_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['exit_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['balance_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ number_format($r['unit_cost'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ number_format($r['avg_cost'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ number_format($r['debe'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ number_format($r['haber'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ number_format($r['saldo'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3" colspan="11">Sin movimientos en el rango.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4 text-gray-700 dark:text-gray-200">
                        <p><strong>Determinación final del inventario:</strong>
                            Unidades finales {{ $kardexModel->final['qty'] }} × Costo promedio
                            {{ number_format($kardexModel->final['unit_cost'], 2) }}
                            =
                            <strong>{{ number_format($kardexModel->final['qty'] * $kardexModel->final['unit_cost'], 2) }}</strong>
                        </p>
                        <p>Saldo final reportado: <strong>{{ number_format($kardexModel->final['total'], 2) }}</strong></p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
