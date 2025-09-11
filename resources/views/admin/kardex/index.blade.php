@extends('layouts.app')
@section('title', 'Kardex')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('dashboard.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Kardex</span>
                </li>
            </ol>
        </nav>

        <!-- Page header card -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                 style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);"></div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-clipboard-list text-white/90 mr-3"></i>
                            Kardex de Inventario
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Genera y exporta el informe por producto, rango y método.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($kardexModel)
                            <a href="{{ route('kardex.export', request()->all()) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-pdf"></i>
                                Exportar PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Mensajes de éxito -->
        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Info helper card -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">¿Qué significa cada método?</h3>
            <ul class="list-disc pl-5 mt-2 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                <li><strong>Costo Promedio Ponderado (CPP):</strong> Cada salida se valora al costo promedio de todas las existencias hasta ese momento.</li>
                <li><strong>PEPS (FIFO):</strong> Las salidas se valoran al costo de las primeras entradas (las más antiguas).</li>
                <li><strong>UEPS (LIFO):</strong> Las salidas se valoran al costo de las últimas entradas (las más recientes).</li>
            </ul>
        </section>

        <!-- Filtros -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('kardex.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Producto</label>
                    <select name="product_id" required
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Seleccionar Producto</option>
                        @foreach ($products as $id => $name)
                            <option value="{{ $id }}" {{ (string) $id === (string) ($productId ?? '') ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Color</label>
                    <select name="color_id"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Todos los colores</option>
                        @isset($colors)
                            @foreach ($colors as $id => $name)
                                <option value="{{ $id }}" {{ (string) $id === (string) ($colorId ?? '') ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Talla</label>
                    <select name="size_id"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Todas las tallas</option>
                        @isset($sizes)
                            @foreach ($sizes as $id => $name)
                                <option value="{{ $id }}" {{ (string) $id === (string) ($sizeId ?? '') ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Almacén</label>
                    <select name="warehouse_id"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Seleccionar Almacén</option>
                        @foreach ($warehouses as $id => $name)
                            <option value="{{ $id }}" {{ (string) $id === (string) ($warehouseId ?? '') ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Seleccionar Kardex</label>
                    <select name="metodo"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="cpp" {{ request('metodo', 'cpp') == 'cpp' ? 'selected' : '' }}>Costo Promedio</option>
                        <option value="peps" {{ request('metodo') == 'peps' ? 'selected' : '' }}>PEPS (FIFO)</option>
                        <option value="ueps" {{ request('metodo') == 'ueps' ? 'selected' : '' }}>UEPS (LIFO)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                    <input type="date" name="from" value="{{ $from }}" required
                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                    <input type="date" name="to" value="{{ $to }}"
                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
                <div class="sm:col-span-2 lg:col-span-6 flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                        <i class="fas fa-cogs"></i>
                        Generar
                    </button>
                    @if(request()->hasAny(['product_id','color_id','size_id','warehouse_id','metodo','from','to']))
                        <a href="{{ route('kardex.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 w-full sm:w-auto text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                            <i class="fas fa-undo"></i>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </section>

        @if ($kardexModel)
            <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
                <div class="w-full overflow-x-auto p-4">
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
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
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
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse ($kardexModel->rows as $r)
                                <tr class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm">{{ $r['date'] }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $r['concept'] ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $r['warehouse'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['entry_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['exit_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">{{ $r['balance_qty'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($r['unit_cost'], 2) }}</td>
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
                            <strong>C$ {{ number_format($kardexModel->final['qty'] * $kardexModel->final['unit_cost'], 2) }}</strong>
                        </p>
                        <p>Saldo final reportado: <strong>C$ {{ number_format($kardexModel->final['total'], 2) }}</strong></p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
