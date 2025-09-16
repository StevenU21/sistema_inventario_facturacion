@extends('layouts.app')
@section('title', 'Cuenta por Cobrar #' . $ar->id)

@section('content')
    <div class="container grid px-6 mx-auto">
        <x-session-message />

        @php
            $clientFullName = trim(($ar->entity->first_name ?? '') . ' ' . ($ar->entity->last_name ?? ''));
            $clientLabel = $clientFullName !== '' ? $clientFullName : $ar->entity->short_name ?? '-';
            $balance = round(($ar->amount_due ?? 0) - ($ar->amount_paid ?? 0), 2);
        @endphp

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
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient mb-6">
            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-user-clock text-white/90 mr-3"></i>
                            Cuenta por Cobrar #{{ $ar->id }}
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Cliente: {{ $clientLabel }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.accounts_receivable.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-white/10 hover:bg-white/15 backdrop-blur">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </a>
                        <a href="{{ route('admin.accounts_receivable.exportPdf', $ar) }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white">
                            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Meta info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5 text-sm">
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Cliente</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $clientLabel }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Fecha Venta</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $ar->sale?->sale_date ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Método de pago</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $ar->sale?->paymentMethod?->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Usuario</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $ar->sale?->user?->short_name ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Monto total</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">C$
                            {{ number_format($ar->amount_due, 2) }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Saldo</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                            <span class="{{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">C$
                                {{ number_format($balance, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="shrink-0 h-9 w-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Estado</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $ar->translated_status }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details and payments -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Details table -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detalles de Venta</h3>
                </div>
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-y dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800/60">
                                <th class="px-5 py-3">Producto</th>
                                <th class="px-5 py-3">Variante</th>
                                <th class="px-5 py-3 text-right">Cant.</th>
                                <th class="px-5 py-3 text-right">P. Unit</th>
                                <th class="px-5 py-3 text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($ar->sale?->saleDetails ?? [] as $d)
                                @php
                                    $color = $d->productVariant?->color?->name ?? null;
                                    $size = $d->productVariant?->size?->name ?? null;
                                    $variant =
                                        $color || $size
                                            ? trim(($color ?: '') . ($color && $size ? ' / ' : '') . ($size ?: ''))
                                            : 'Simple';
                                    $amount = (float) $d->quantity * (float) $d->unit_price;
                                @endphp
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="px-5 py-3 text-sm">{{ $d->productVariant?->product?->name }}
                                        {{ $d->productVariant?->sku ? '(' . $d->productVariant->sku . ')' : '' }}</td>
                                    <td class="px-5 py-3 text-sm">{{ $variant }}</td>
                                    <td class="px-5 py-3 text-sm text-right">{{ $d->quantity }}</td>
                                    <td class="px-5 py-3 text-sm text-right">C$ {{ number_format($d->unit_price, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-right">C$ {{ number_format($amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-gray-400 dark:text-gray-500">Sin
                                        detalles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5 h-max">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Pagos</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left">
                            <th class="py-2">Fecha</th>
                            <th class="py-2">Método</th>
                            <th class="py-2">Monto</th>
                            <th class="py-2">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($ar->payments as $p)
                            <tr>
                                <td class="py-2">
                                    {{ \Illuminate\Support\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
                                <td class="py-2">{{ $p->paymentMethod?->name ?? '-' }}</td>
                                <td class="py-2">C$ {{ number_format($p->amount, 2) }}</td>
                                <td class="py-2">{{ $p->user?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">Sin pagos aún</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
