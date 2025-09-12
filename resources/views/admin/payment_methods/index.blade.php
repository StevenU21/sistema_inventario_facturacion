@extends('layouts.app')
@section('title', 'Métodos de Pago')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editId: null,
        editAction: '',
        showPaymentMethod: { id: '', name: '', description: '', formatted_created_at: '', formatted_updated_at: '' },
        editPaymentMethod: { id: '', name: '', description: '' },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">

        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Modulo de Catálogos
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Métodos de Pago</span>
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
                            <i class="fas fa-credit-card text-white/90 mr-3"></i>
                            Métodos de Pago
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Administra las formas de pago disponibles.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @can('create payment_methods')
                            <button @click="isModalOpen = true" type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                                <i class="fas fa-plus"></i>
                                Crear Método de Pago
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-4">
            <x-session-message />
        </div>

        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('payment_methods.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div class="sm:col-span-3 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Nombre o descripción...">
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request('search') || request('per_page'))
                            <a href="{{ route('payment_methods.index') }}"
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
            </form>
        </section>

        <!-- Edit Modal Trigger and Component -->
        <x-edit-modal :title="'Editar Método de Pago'" :description="'Modifica los datos del método de pago seleccionado.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editPaymentMethod.id">
                @include('admin.payment_methods.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Método de Pago'" :description="'Agrega un nuevo método de pago al sistema.'">
            <form action="{{ route('payment_methods.store') }}" method="POST">
                @csrf
                @include('admin.payment_methods.form', ['alpine' => false])
            </form>
        </x-modal>

        <!-- Show Modal Trigger and Component -->
        <x-show-modal :title="'Detalle de Método de Pago'" :description="'Consulta los datos del método de pago seleccionado.'">
            <div class="mt-4">
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200"><i
                        class="fas fa-hashtag text-purple-600 dark:text-purple-400"></i><strong>ID:</strong> <span
                        x-text="showPaymentMethod.id"></span></p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2"><i
                        class="fas fa-tag text-purple-600 dark:text-purple-400"></i><strong>Nombre:</strong> <span
                        x-text="showPaymentMethod.name"></span></p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2"><i
                        class="fas fa-align-left text-purple-600 dark:text-purple-400"></i><strong>Descripción:</strong>
                    <span x-text="showPaymentMethod.description"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2"><i
                        class="fas fa-calendar-alt text-purple-600 dark:text-purple-400"></i><strong>Fecha de
                        Registro:</strong> <span x-text="showPaymentMethod.formatted_created_at"></span></p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2"><i
                        class="fas fa-clock text-purple-600 dark:text-purple-400"></i><strong>Fecha de
                        Actualización:</strong> <span x-text="showPaymentMethod.formatted_updated_at"></span></p>
            </div>
        </x-show-modal>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                    route="payment_methods.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="name" label="Nombre"
                                    route="payment_methods.search" icon="<i class='fas fa-tag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="description" label="Descripción"
                                    route="payment_methods.search" icon="<i class='fas fa-align-left mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="created_at" label="Fecha de Registro"
                                    route="payment_methods.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="updated_at" label="Fecha de Actualización"
                                    route="payment_methods.search" icon="<i class='fas fa-calendar-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($paymentMethods as $paymentMethod)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs"><span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700">{{ $paymentMethod->id }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $paymentMethod->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $paymentMethod->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $paymentMethod->formatted_created_at ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $paymentMethod->formatted_updated_at ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @can('read payment_methods')
                                            <button type="button" title="Ver"
                                                @click="showPaymentMethod = { id: {{ $paymentMethod->id }}, name: '{{ addslashes($paymentMethod->name) }}', description: '{{ addslashes($paymentMethod->description) }}', formatted_created_at: '{{ addslashes($paymentMethod->formatted_created_at) }}', formatted_updated_at: '{{ addslashes($paymentMethod->formatted_updated_at) }}' }; isShowModalOpen = true;"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endcan
                                        @can('update payment_methods')
                                            <button type="button" title="Editar"
                                                @click="editPaymentMethod = { id: {{ $paymentMethod->id }}, name: '{{ addslashes($paymentMethod->name) }}', description: '{{ addslashes($paymentMethod->description) }}' }; editAction = '{{ route('payment_methods.update', $paymentMethod) }}'; isEditModalOpen = true;"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('destroy payment_methods')
                                            <form action="{{ route('payment_methods.destroy', $paymentMethod) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('¿Estás seguro de eliminar este método de pago?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Eliminar"
                                                    class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No se
                                    encontraron métodos de pago.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $paymentMethods->links() }}
            </div>
        </div>
    </div>
@endsection
