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
            <!-- Mejor Mes -->
            <div
                class="group relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 p-4 shadow hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Mejor Mes</p>
                        <p class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-300">{{ $bestMonthLabel ?? '---' }}
                        </p>
                        <p class="mt-1 text-lg font-semibold text-gray-700 dark:text-gray-100">$
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
                        <p class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-100">$
                            {{ number_format($totalSales, 2) }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-gradient-to-br from-amber-500/10 to-amber-500/5 text-amber-400">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes periódicos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4">
                <p class="text-xs font-medium tracking-wide text-gray-500 dark:text-gray-400">Ventas Hoy</p>
                <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">$
                    {{ number_format($todaySales, 2) }}
                </p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4">
                <p class="text-xs font-medium tracking-wide text-gray-500 dark:text-gray-400">Ventas Mes</p>
                <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">$
                    {{ number_format($monthSales, 2) }}
                </p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white/60 dark:bg-gray-800/70 p-4">
                <p class="text-xs font-medium tracking-wide text-gray-500 dark:text-gray-400">Ventas Año</p>
                <p class="mt-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">$
                    {{ number_format($yearSales, 2) }}
                </p>
            </div>
        </div>

        <!-- Gráficos principales -->
        <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Ventas por mes -->
            <div
                class="xl:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Ventas por Mes</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Últimos 12 meses</p>
                    </div>
                </div>
                <canvas id="chartMonthly" height="120"></canvas>
            </div>
            <!-- Ventas por hora -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Ventas por Hora
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Hoy</p>
                    </div>
                </div>
                <canvas id="chartHourly" height="120"></canvas>
            </div>
        </div>

        <!-- Gráfico diario y tablas -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div
                class="lg:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 tracking-wide">Ventas por Día</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Últimos 14 días</p>
                    </div>
                </div>
                <canvas id="chartDaily" height="120"></canvas>
            </div>
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
                                    <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ $p->color_name ?? '-' }}</td>
                                    <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ $p->size_name ?? '-' }}</td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">{{ $p->qty_total }}
                                    </td>
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">$
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

        <!-- Top clientes -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                    <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">$
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
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow">
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            const monthlyCtx = document.getElementById('chartMonthly').getContext('2d');
            const hourlyCtx = document.getElementById('chartHourly').getContext('2d');
            const dailyCtx = document.getElementById('chartDaily').getContext('2d');

            const baseGrid = 'rgba(148,163,184,0.15)';
            const baseTicks = '#94a3b8';
            const fontFamily = 'Inter, system-ui, sans-serif';
            const moneyFmt = v => '$ ' + new Intl.NumberFormat('es-NI', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(v);

            // Data desde backend
            const monthsLabels = @json($monthsLabels);
            const monthsTotals = @json($monthsTotals);
            const hoursLabels = @json($hoursLabels);
            const hoursTotals = @json($hoursTotals);
            const daysLabels = @json($daysLabels);
            const daysTotals = @json($daysTotals);

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30,41,59,0.9)',
                        titleColor: '#f1f5f9',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: ctx => moneyFmt(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: baseGrid
                        },
                        ticks: {
                            color: baseTicks,
                            font: {
                                family: fontFamily
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: baseGrid
                        },
                        ticks: {
                            color: baseTicks,
                            font: {
                                family: fontFamily
                            },
                            callback: v => '$' + v
                        }
                    }
                }
            };

            // Monthly chart (bar + line overlay optional later)
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthsLabels,
                    datasets: [{
                        label: 'Ventas',
                        data: monthsTotals,
                        borderRadius: 6,
                        backgroundColor: monthsTotals.map(v =>
                            'linear-gradient(180deg, rgba(99,102,241,0.9), rgba(79,70,229,0.4))'),
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            beginAtZero: true
                        }
                    }
                }
            });

            // Hourly chart (line)
            new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: hoursLabels,
                    datasets: [{
                        label: 'Ventas',
                        data: hoursTotals,
                        fill: true,
                        tension: .35,
                        borderColor: 'rgba(16,185,129,0.9)',
                        backgroundColor: 'rgba(16,185,129,0.15)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(16,185,129,1)',
                        pointBorderWidth: 0
                    }]
                },
                options: commonOptions
            });

            // Daily chart (area)
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: daysLabels,
                    datasets: [{
                        label: 'Ventas',
                        data: daysTotals,
                        fill: true,
                        tension: .25,
                        borderColor: 'rgba(236,72,153,0.9)',
                        backgroundColor: 'rgba(236,72,153,0.15)',
                        pointRadius: 0,
                        borderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    elements: {
                        line: {
                            borderJoinStyle: 'round'
                        }
                    },
                }
            });
        </script>
    @endpush
@endsection
