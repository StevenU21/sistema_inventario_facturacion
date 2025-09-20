@extends('layouts.app')
@section('title', 'Pagos a Cuentas por Cobrar')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1 text-gray-400 dark:text-gray-500"></i> Módulo de Ventas
                    </a>
                </li>
                <li class="text-gray-400 dark:text-gray-500">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Pagos</span>
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
                            <i class="fas fa-money-bill-wave text-white/90 mr-3"></i>
                            Historial de Pagos a Cuentas por Cobrar
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Busca, filtra y exporta tus pagos.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- <form method="GET" action="{{ route('admin.payments.export') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="payment_method_id" value="{{ request('payment_method_id') }}">
                            <input type="hidden" name="entity_id" value="{{ request('entity_id') }}">
                            <input type="hidden" name="sale_id" value="{{ request('sale_id') }}">
                            <input type="hidden" name="from" value="{{ request('from') }}">
                            <input type="hidden" name="to" value="{{ request('to') }}">
                            <input type="hidden" name="min_amount" value="{{ request('min_amount') }}">
                            <input type="hidden" name="max_amount" value="{{ request('max_amount') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form> --}}
                        <form method="GET" action="{{ route('admin.payments.exportPdf') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="payment_method_id" value="{{ request('payment_method_id') }}">
                            <input type="hidden" name="entity_id" value="{{ request('entity_id') }}">
                            <input type="hidden" name="sale_id" value="{{ request('sale_id') }}">
                            <input type="hidden" name="from" value="{{ request('from') }}">
                            <input type="hidden" name="to" value="{{ request('to') }}">
                            <input type="hidden" name="min_amount" value="{{ request('min_amount') }}">
                            <input type="hidden" name="max_amount" value="{{ request('max_amount') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-pdf"></i>
                                Exportar PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.payments.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                <div class="col-span-1 sm:col-span-3 lg:col-span-5 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <x-autocomplete name="search" :value="request('search')" url="{{ route('admin.payments.autocomplete') }}"
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
                                'payment_method_id',
                                'entity_id',
                                'sale_id',
                                'from',
                                'to',
                                'min_amount',
                                'max_amount',
                            ]))
                            <a href="{{ route('admin.payments.index') }}"
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
                    <label for="entity_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Cliente</label>
                    <select name="entity_id" id="entity_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los clientes</option>
                        @isset($entities)
                            @foreach ($entities as $id => $name)
                                <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="payment_method_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Método</label>
                    <select name="payment_method_id" id="payment_method_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los métodos</option>
                        @isset($methods)
                            @foreach ($methods as $id => $name)
                                <option value="{{ $id }}"
                                    {{ request('payment_method_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
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
            </form>
        </section>

        <!-- Tabla -->
        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Método de pago</th>
                            <th class="px-4 py-3 text-right">Monto</th>
                            <th class="px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($payments as $p)
                            @php
                                $client =
                                    $p->entity?->short_name ?:
                                    trim(($p->entity->first_name ?? '') . ' ' . ($p->entity->last_name ?? ''));
                                $saleId = $p->accountReceivable?->sale_id ?? $p->accountReceivable?->sale?->id;
                                $fechaPago = $p->payment_date
                                    ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y')
                                    : $p->formatted_created_at ?? null;
                            @endphp
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700">{{ $p->id }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $p->user->short_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $client ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $p->accountReceivable?->sale?->saleDetails?->first()?->productVariant?->product?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $p->paymentMethod->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-right">C$ {{ number_format($p->amount ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $p->formatted_created_at ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    pagos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @isset($payments)
                <div class="mt-4">{{ $payments->links() }}</div>
            @endisset
        </div>
    </div>
@endsection
