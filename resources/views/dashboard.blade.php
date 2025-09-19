@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 mx-auto">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('dashboard.index') }}"
                        class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Inicio
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Dashboard</span>
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

            /* Tamaños compactos para los gráficos */
            .chart-box {
                height: 170px;
            }

            @media (min-width: 640px) {
                .chart-box {
                    height: 190px;
                }
            }

            @media (min-width: 1024px) {
                .chart-box {
                    height: 200px;
                }
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
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 shadow-lg animate-gradient">
            <div class="absolute inset-0 opacity-10 bg-black"></div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-tachometer-alt text-white/90 mr-3"></i>
                            Panel de Control
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Resumen operacional y de ventas.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- End header card -->

        <!-- Reportes periódicos y KPIs financieros -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4 mt-6">
            <!-- Ventas Hoy -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4 flex items-start justify-between">
                <div class="flex flex-col">
                    <p class="text-[11px] font-medium tracking-wide text-gray-500 dark:text-gray-400">Ventas Hoy</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">C$
                        {{ number_format($todaySales, 2) }}</p>
                </div>
                <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500/10 to-indigo-500/5 text-indigo-400">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <!-- Ventas Mes -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4 flex items-start justify-between">
                <div class="flex flex-col">
                    <p class="text-[11px] font-medium tracking-wide text-gray-500 dark:text-gray-400">Ventas Mes</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">C$
                        {{ number_format($monthSales, 2) }}</p>
                </div>
                <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 text-emerald-400">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <!-- Crecimiento Mes -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4 flex items-start justify-between">
                <div class="flex flex-col">
                    <p class="text-[11px] font-medium tracking-wide text-gray-500 dark:text-gray-400">Crecimiento Mes</p>
                    <p
                        class="mt-2 text-2xl font-semibold flex items-center gap-2 {{ ($growthRate ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : (($growthRate ?? 0) < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-100') }}">
                        @if (!is_null($growthRate))
                            <span>{{ $growthRate > 0 ? '+' : '' }}{{ number_format($growthRate, 2) }}%</span>
                            @if ($growthRate > 0)
                                <i class="fas fa-arrow-trend-up"></i>
                            @elseif($growthRate < 0)
                                <i class="fas fa-arrow-trend-down"></i>
                            @else
                                <i class="fas fa-minus"></i>
                            @endif
                        @else
                            <span class="text-xs font-medium text-gray-400">Sin datos previos</span>
                        @endif
                    </p>
                </div>
                <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 text-emerald-400">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>

            <!-- Mejor Mes -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition xl:col-span-2 xl:order-first">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Mejor Mes</p>
                        <p class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-300">{{ $bestMonthLabel ?? '---' }}
                        </p>
                        <p class="mt-1 text-lg font-semibold text-gray-700 dark:text-gray-100">C$
                            {{ number_format($bestMonthAmount, 2) }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-fuchsia-500/10 to-fuchsia-500/5 text-fuchsia-400">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
            <!-- Ventas últimas 12M -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total 12 Meses</p>
                        <p class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">C$
                            {{ number_format($totalSales, 2) }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-amber-500/10 to-amber-500/5 text-amber-400">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4 mt-6">
            <!-- Clientes -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Clientes</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">{{ $entities }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500/10 to-indigo-500/5 text-indigo-400">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <!-- Productos -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Productos</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">{{ $products }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 text-emerald-400">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <!-- Inventario total -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Inventario Total</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">{{ $inventoryTotal }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-sky-500/10 to-sky-500/5 text-sky-400">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </div>
            <!-- Movimientos hoy -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Movimientos Hoy</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">{{ $movementsToday }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-teal-500/10 to-teal-500/5 text-teal-400">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>
            <!-- Crédito Cobrado -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4 flex items-start justify-between">
                <div class="flex flex-col">
                    <p class="text-[11px] font-medium tracking-wide text-gray-500 dark:text-gray-400">Crédito Cobrado</p>
                    <p class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">C$
                        {{ number_format($totalCreditPaid, 2) }}</p>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400 whitespace-nowrap">de C$
                        {{ number_format($totalCreditDue, 2) }}</p>
                </div>
                <div class="p-2 rounded-lg bg-gradient-to-br from-teal-500/10 to-teal-500/5 text-teal-400">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <!-- Crédito Pendiente -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4 flex items-start justify-between">
                <div class="flex flex-col">
                    <p class="text-[11px] font-medium tracking-wide text-gray-500 dark:text-gray-400">Crédito Pendiente</p>
                    <p class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">C$
                        {{ number_format($totalCreditPending, 2) }}</p>
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Saldo Clientes</p>
                </div>
                <div class="p-2 rounded-lg bg-gradient-to-br from-rose-500/10 to-rose-500/5 text-rose-400">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>


        <!-- Gráficos principales (componentes) -->
        <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
            <x-dashboard.monthly-sales-chart :labels="$monthsLabels" :totals="$monthsTotals" />
            <x-dashboard.hourly-sales-chart :labels="$hoursLabels" :totals="$hoursTotals" />
        </div>

        <!-- Gráfico diario y tablas (componentes) -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <x-dashboard.daily-sales-chart :labels="$daysLabels" :totals="$daysTotals" />
            <!-- Top productos -->
            <div
                class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow flex flex-col">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Top Productos (30d)
                    </h3>
                </div>
                <div class="overflow-x-auto -mx-3 flex-1">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr
                                class="text-left text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 px-3 font-medium">Producto</th>
                                <th class="py-2 px-3 font-medium">Color</th>
                                <th class="py-2 px-3 font-medium">Talla</th>
                                <th class="py-2 px-3 font-medium text-right">Cant</th>
                                <th class="py-2 px-3 font-medium text-right">Ingreso</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($topProducts as $p)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-2 px-3 text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $p->product_name }}</td>
                                    <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ $p->color_name ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ $p->size_name ?? '-' }}</td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">{{ $p->qty_total }}
                                    </td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">C$
                                        {{ number_format($p->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Sin
                                        datos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top clientes y Deudores -->
        <div class="mt-8 grid grid-cols-1 2xl:grid-cols-3 gap-6">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Top Clientes (30d)
                    </h3>
                </div>
                <div class="overflow-x-auto -mx-3">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr
                                class="text-left text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 px-3 font-medium">Cliente</th>
                                <th class="py-2 px-3 font-medium text-right">Ventas</th>
                                <th class="py-2 px-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($topClients as $c)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-2 px-3 text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $c->client_name }}</td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">
                                        {{ $c->sales_count }}</td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">C$
                                        {{ number_format($c->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Sin
                                        datos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Deuda Clientes -->
            <div
                class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow flex flex-col">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Top Deudores</h3>
                </div>
                <div class="overflow-x-auto -mx-3 flex-1">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr
                                class="text-left text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 px-3 font-medium">Cliente</th>
                                <th class="py-2 px-3 font-medium text-right">Deuda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($topDebtors as $d)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-2 px-3 text-gray-700 dark:text-gray-300 font-medium">{{ $d['name'] }}
                                    </td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">C$
                                        {{ number_format($d['debt'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Sin
                                        deudas</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td
                                    class="py-2 px-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                                    Total</td>
                                <td
                                    class="py-2 px-3 text-right text-[11px] font-semibold text-gray-700 dark:text-gray-200">
                                    C$ {{ number_format($totalClientsDebt, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Notas -->
            <div
                class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow hidden 2xl:block">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide mb-4">Notas / Acciones
                    Rápidas</h3>
                <ul class="space-y-3 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2"><span
                            class="mt-0.5 w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Revisar inventario bajo y
                        reabastecer.</li>
                    <li class="flex items-start gap-2"><span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        Analizar comportamiento horario de ventas.</li>
                    <li class="flex items-start gap-2"><span
                            class="mt-0.5 w-1.5 h-1.5 rounded-full bg-fuchsia-500"></span> Preparar campaña para mes con
                        menor desempeño.</li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Librería Chart.js (los gráficos se renderizan en componentes Blade separados) -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endpush
@endsection
