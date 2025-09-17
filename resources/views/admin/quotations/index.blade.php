@extends('layouts.app')
@section('title', 'Cotizaciones')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1 text-gray-400 dark:text-gray-500"></i> Módulo de Cotizaciones
                    </a>
                </li>
                <li class="text-gray-400 dark:text-gray-500">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Cotizaciones</span>
                </li>
            </ol>
        </nav>

        <!-- Page header card -->
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
                            <i class="fas fa-file-invoice-dollar text-white/90 mr-3"></i>
                            Cotizaciones
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Lista y exporta tus cotizaciones.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.quotations.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/90 hover:bg-white text-purple-700 text-sm font-medium transition">
                            <i class="fas fa-plus"></i>
                            Nueva cotización
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.quotations.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                <div class="col-span-1 sm:col-span-3 lg:col-span-4">
                    <label for="search"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                    <x-autocomplete name="search" :value="request('search')" url="{{ route('admin.quotations.autocomplete') }}"
                        placeholder="Cliente..." id="search" />
                </div>
                <div>
                    <label for="entity_id"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Cliente</label>
                    <select name="entity_id" id="entity_id"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @isset($entities)
                            @foreach ($entities as $id => $name)
                                <option value="{{ $id }}" {{ request('entity_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label for="per_page"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Mostrar</label>
                    <select name="per_page" id="per_page"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div>
                    <label for="from"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Desde</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        onchange="this.form.submit()" />
                </div>
                <div>
                    <label for="to"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        onchange="this.form.submit()" />
                </div>
                <div>
                    <label for="status"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                    <select name="status" id="status"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('status', $status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Aceptada</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelada</option>
                    </select>
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
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($quotations as $quotation)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold text-white bg-green-600 rounded-full dark:bg-green-700">{{ $quotation->id }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ trim(($quotation->entity?->first_name ?? '') . ' ' . ($quotation->entity?->last_name ?? '')) ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $quotation->user?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    C$ {{ number_format($quotation->total ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ ucfirst($quotation->status) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $quotation->formatted_created_at }}
                                </td>
                                <td class="px-4 py-3 text-sm space-x-2">
                                    <!-- Download Proforma PDF -->
                                    <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank"
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="Descargar Proforma">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @if($quotation->status === 'pending')
                                        <!-- Accept -->
                                        <form action="{{ route('admin.quotations.accept', $quotation) }}" method="POST"
                                              class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-800"
                                                    title="Aceptar Proforma">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <!-- Cancel -->
                                        <form action="{{ route('admin.quotations.cancel', $quotation) }}" method="POST"
                                              class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-800"
                                                    title="Cancelar Proforma">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay
                                    cotizaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $quotations->links() }}</div>
        </div>
    </div>
@endsection
