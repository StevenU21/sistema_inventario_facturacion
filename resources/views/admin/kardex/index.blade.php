@extends('layouts.app')
@section('title', 'Kardex')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Generar Informe Kardex del Inventario
        </h2>


        <x-session-message />

        <div
            class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded text-sm text-gray-700 dark:bg-gray-900 dark:text-gray-200">
            <strong>¿Qué significa cada método?</strong>
            <ul class="list-disc pl-5 mt-2">
                <li><strong>Costo Promedio Ponderado (CPP):</strong> Cada salida se valora al costo promedio de todas las
                    existencias hasta ese momento.</li>
                <li><strong>PEPS (FIFO):</strong> Las salidas se valoran al costo de las primeras entradas (las más
                    antiguas).</li>
                <li><strong>UEPS (LIFO):</strong> Las salidas se valoran al costo de las últimas entradas (las más
                    recientes).</li>
            </ul>
        </div>

        <div class="flex flex-col gap-y-2 mb-4">
            <div class="flex w-full">
                <div class="flex-1"></div>
                @if ($kardexModel)
                    <div class="flex flex-col p-0.5 ml-auto">
                        <label class="invisible block text-sm font-medium">.</label>
                        <a href="{{ route('kardex.export', request()->all()) }}" target="_blank"
                            class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                            <span>Exportar PDF</span>
                            <i class="fas fa-file-pdf ml-2"></i>
                        </a>
                    </div>
                @endif
            </div>
            <form method="GET" action="{{ route('kardex.index') }}"
                class="flex flex-wrap gap-x-1 gap-y-1 items-end self-end">
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Producto</label>
                    <select name="product_id" required
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-60 text-sm font-medium">
                        <option value="">Seleccionar Producto</option>
                        @foreach ($products as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) $id === (string) ($productId ?? '') ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Color</label>
                    <select name="color_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium">
                        <option value="">Todos los colores</option>
                        @isset($colors)
                            @foreach ($colors as $id => $name)
                                <option value="{{ $id }}"
                                    {{ (string) $id === (string) ($colorId ?? '') ? 'selected' : '' }}>{{ $name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Talla</label>
                    <select name="size_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-40 text-sm font-medium">
                        <option value="">Todas las tallas</option>
                        @isset($sizes)
                            @foreach ($sizes as $id => $name)
                                <option value="{{ $id }}"
                                    {{ (string) $id === (string) ($sizeId ?? '') ? 'selected' : '' }}>{{ $name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Almacén</label>
                    <select name="warehouse_id"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-50 text-sm font-medium">
                        <option value="">Seleccionar Almacén</option>
                        @foreach ($warehouses as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string) $id === (string) ($warehouseId ?? '') ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Seleccionar Kardex</label>
                    <select name="metodo"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-36 text-sm font-medium">
                        <option value="cpp" {{ request('metodo', 'cpp') == 'cpp' ? 'selected' : '' }}>Costo Promedio
                        </option>
                        <option value="peps" {{ request('metodo') == 'peps' ? 'selected' : '' }}>PEPS (FIFO)</option>
                        <option value="ueps" {{ request('metodo') == 'ueps' ? 'selected' : '' }}>UEPS (LIFO)</option>
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Desde</label>
                    <input type="date" name="from" value="{{ $from }}"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-36 text-sm font-medium" required/>
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Hasta</label>
                    <input type="date" name="to" value="{{ $to }}"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-36 text-sm font-medium" />
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="invisible block text-sm font-medium">.</label>
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-30 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Generar
                    </button>
                </div>
            </form>
        </div>

        @if ($kardexModel)
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto bg-white dark:bg-gray-800 p-4">
                    <div class="mb-4 text-gray-700 dark:text-gray-200">
                        <p><strong>Producto:</strong> {{ $kardexModel->product->name }}</p>
                        <p><strong>Almacén:</strong> {{ $kardexModel->warehouse->name ?? 'Todos' }}</p>
                        <p><strong>Rango:</strong> {{ $kardexModel->date_from }} a {{ $kardexModel->date_to }}</p>
                        <p><strong>Método:</strong>
                            @if (request('metodo', 'cpp') == 'cpp')
                                Costo Promedio Ponderado
                            @elseif(request('metodo') == 'peps')
                                PEPS (FIFO)
                            @elseif(request('metodo') == 'ueps')
                                UEPS (LIFO)
                            @endif
                        </p>
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
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['unit_cost'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['avg_cost'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['debe'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['haber'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['saldo'], 2) }}</td>
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
                            C$ {{ number_format($kardexModel->final['unit_cost'], 2) }}
                            =
                            <strong>C$
                                {{ number_format($kardexModel->final['qty'] * $kardexModel->final['unit_cost'], 2) }}</strong>
                        </p>
                        <p>Saldo final reportado: <strong>C$ {{ number_format($kardexModel->final['total'], 2) }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
