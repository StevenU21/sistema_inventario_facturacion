@extends('layouts.app')
@section('title', 'Cuentas por Cobrar')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1 text-gray-400 dark:text-gray-500"></i> Módulo de Ventas
                    </a>
                </li>
                <li class="text-gray-400 dark:text-gray-500">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Cuentas por Cobrar</span>
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
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-user-clock text-white/90 mr-3"></i>
                            Cuentas por Cobrar
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Estado por cliente y cuenta, exportable.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('admin.accounts_receivable.export') }}">
                            @foreach (request()->all() as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $subValue)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-4">
            <x-session-message />
        </div>

        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.accounts_receivable.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                <div class="col-span-1 sm:col-span-3 lg:col-span-5 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <x-autocomplete name="search" :value="request('search')" url="{{ route('admin.accounts_receivable.autocomplete') }}"
                            placeholder="Nombre del cliente..." id="search" />
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
                                'entity_id',
                                'status',
                                'sale_id',
                                'from',
                                'to',
                                'min_balance',
                                'max_balance',
                            ]))
                            <a href="{{ route('admin.accounts_receivable.index') }}"
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
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>
                                {{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="entity_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Cliente</label>
                    <select name="entity_id" id="entity_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach ($entities as $id => $name)
                            <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                    <select name="status" id="status"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="sale_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Venta
                        ID</label>
                    <input type="number" min="1" name="sale_id" id="sale_id" value="{{ request('sale_id') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
                <div>
                    <label for="from"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()" />
                </div>
                <div>
                    <label for="to"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()" />
                </div>
                <div>
                    <label for="min_balance"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Saldo
                        mín.</label>
                    <input type="number" step="0.01" name="min_balance" id="min_balance"
                        value="{{ request('min_balance') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
                <div>
                    <label for="max_balance"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Saldo
                        máx.</label>
                    <input type="number" step="0.01" name="max_balance" id="max_balance"
                        value="{{ request('max_balance') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                </div>
            </form>
        </section>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Deuda</th>
                            <th class="px-4 py-3 text-right">Pagado</th>
                            <th class="px-4 py-3 text-right">Restante</th>
                            <th class="px-4 py-3 text-right">Fecha</th>
                            <th class="px-4 py-3">Acciones</th>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($accounts as $ar)
                            @php
                                $sale = $ar->sale;
                                $firstDetail = $sale?->saleDetails->first();
                                $firstProductName = optional($firstDetail?->productVariant?->product)->name;
                                $client =
                                    $ar->entity?->short_name ?:
                                    trim(($ar->entity->first_name ?? '') . ' ' . ($ar->entity->last_name ?? ''));
                                $totalQty = $sale?->saleDetails->sum('quantity');
                                $firstUnitPrice = $firstDetail?->unit_price;
                                $taxAmount = $sale?->tax_amount ?? 0;
                                $total = $sale?->total ?? 0;
                            @endphp
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs"><span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700">{{ $ar->id }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($firstDetail?->productVariant)
                                        <span
                                            class="font-semibold">{{ $firstDetail->productVariant->product->name ?? '-' }}</span><br>
                                        <span class="text-xs text-gray-500">
                                            {{ $colors[$firstDetail->productVariant->color_id] ?? '-' }}
                                            /
                                            {{ $sizes[$firstDetail->productVariant->size_id] ?? '-' }}
                                        </span>
                                    @else
                                        {{ $firstProductName ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $client ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $ar->translated_status }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($ar->amount_due ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($ar->amount_paid ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format(($ar->amount_due ?? 0) - ($ar->amount_paid ?? 0), 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right">{{ $ar->formatted_created_at }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 text-sm">
                                        <a href="{{ route('admin.accounts_receivable.show', $ar) }}" title="Ver detalle"
                                            class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.accounts_receivable.exportPdf', $ar) }}" title="PDF"
                                            class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    cuentas por cobrar registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @isset($accounts)
                <div class="mt-4">{{ $accounts->links() }}</div>
            @endisset
        </div>
    </div>
@endsection
