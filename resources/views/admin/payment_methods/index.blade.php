@extends('layouts.app')
@section('title', 'Métodos de Pago')

@section('content')

    <div class="container grid px-6 mx-auto" x-data="{
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
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Métodos de Pago
        </h2>

        <!-- Mensajes de éxito -->
        <x-session-message />
        <!-- Fin mensajes de éxito -->

        <!-- Filtros, búsqueda -->
        <div class="flex flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
            <form method="GET" action="{{ route('payment_methods.search') }}"
                class="flex flex-wrap gap-x-1 gap-y-1 items-end self-end">
                <div class="flex flex-col p-0.5">
                    <select name="per_page" id="per_page"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-16 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="flex flex-col p-0.5">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                        placeholder="Nombre o descripción...">
                </div>
                <div class="flex flex-col p-0.5">
                    <label class="invisible block text-sm font-medium">.</label>
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Buscar
                    </button>
                </div>
            </form>
            @can('create payment_methods')
            <div class="flex flex-col p-0.5">
                <label class="invisible block text-sm font-medium">.</label>
                <button @click="isModalOpen = true" type="button"
                    class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600">
                    <span>Crear Método de Pago</span>
                    <i class="fas fa-plus ml-2"></i>
                </button>
            </div>
            @endcan
        </div>

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
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                    <i class="fas fa-hashtag text-purple-600 dark:text-purple-400"></i>
                    <strong>ID:</strong> <span x-text="showPaymentMethod.id"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-tag text-purple-600 dark:text-purple-400"></i>
                    <strong>Nombre:</strong> <span x-text="showPaymentMethod.name"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-align-left text-purple-600 dark:text-purple-400"></i>
                    <strong>Descripción:</strong> <span x-text="showPaymentMethod.description"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400"></i>
                    <strong>Fecha de Registro:</strong> <span x-text="showPaymentMethod.formatted_created_at"></span>
                </p>
                <p class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 mt-2">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                    <strong>Fecha de Actualización:</strong> <span x-text="showPaymentMethod.formatted_updated_at"></span>
                </p>
            </div>
        </x-show-modal>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
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
                            <th class="px-4 py-3">
                                <i class="fas fa-tools mr-2"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($paymentMethods as $paymentMethod)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $paymentMethod->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $paymentMethod->name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $paymentMethod->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $paymentMethod->formatted_created_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $paymentMethod->formatted_updated_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        @can('read payment_methods')
                                        <button type="button"
                                            @click="showPaymentMethod = { id: {{ $paymentMethod->id }}, name: '{{ $paymentMethod->name }}', description: '{{ $paymentMethod->description }}', formatted_created_at: '{{ $paymentMethod->formatted_created_at }}', formatted_updated_at: '{{ $paymentMethod->formatted_updated_at }}' }; isShowModalOpen = true;"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver Modal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endcan
                                        @can('update payment_methods')
                                        <button type="button"
                                            @click="editPaymentMethod = { id: {{ $paymentMethod->id }}, name: '{{ addslashes($paymentMethod->name) }}', description: '{{ addslashes($paymentMethod->description) }}' }; editAction = '{{ route('payment_methods.update', $paymentMethod) }}'; isEditModalOpen = true;"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Editar Modal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endcan
                                        @can('destroy payment_methods')
                                        <form action="{{ route('payment_methods.destroy', $paymentMethod) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este método de pago?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No se
                                    encontraron métodos de pago.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $paymentMethods->links() }}
            </div>
        </div>
    </div>
@endsection
