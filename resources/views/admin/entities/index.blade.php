@extends('layouts.app')
@section('title', 'Clientes & Proveedores')

@section('content')
    <div class="container grid px-6 mx-auto" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editAction: '',
        showEntity: { id: '', first_name: '', last_name: '', identity_card: '', ruc: '', email: '', phone: '', address: '', description: '', municipality: '', is_client: false, is_supplier: false, is_active: true, formatted_created_at: '', formatted_updated_at: '' },
        editEntity: { id: '', first_name: '', last_name: '', identity_card: '', ruc: '', email: '', phone: '', address: '', description: '', municipality_id: null, is_client: false, is_supplier: false, is_active: true },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false },
    }">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">Clientes & Proveedores</h2>

        <x-session-message />

        <div class="flex flex-row flex-wrap items-center gap-x-4 gap-y-4 mb-2">
            <form method="GET" action="{{ route('entities.search') }}"
                class="flex flex-row gap-x-4 items-center flex-1 min-w-[280px]">
                <div class="flex flex-col p-1">
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
                <div class="flex flex-col p-1 flex-1">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-full text-sm font-medium"
                        placeholder="Nombre, cédula, correo...">
                </div>
                <div class="flex flex-col p-1">
                    <button type="submit"
                        class="flex items-center justify-center px-4 py-2 w-28 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Buscar
                    </button>
                </div>
                <div class="flex flex-col p-1">
                    <select name="is_client"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-28 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Cliente?</option>
                        <option value="1" {{ request('is_client') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('is_client') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="is_supplier"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Proveedor?</option>
                        <option value="1" {{ request('is_supplier') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('is_supplier') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="is_active"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-28 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Activo?</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </form>

            <div class="flex items-center gap-2 ml-auto shrink-0">
                <form method="GET" action="{{ route('entities.export') }}">
                    @if(request()->filled('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request()->has('is_client') && request('is_client') !== '')
                        <input type="hidden" name="is_client" value="{{ request('is_client') }}">
                    @endif
                    @if(request()->has('is_supplier') && request('is_supplier') !== '')
                        <input type="hidden" name="is_supplier" value="{{ request('is_supplier') }}">
                    @endif
                    @if(request()->has('is_active') && request('is_active') !== '')
                        <input type="hidden" name="is_active" value="{{ request('is_active') }}">
                    @endif
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                        <span>Exportar Excel</span>
                        <i class="fas fa-file-excel ml-2"></i>
                    </button>
                </form>
                <button type="button" @click="isModalOpen = true"
                    class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                    <span>Crear Entidad</span>
                    <i class="fas fa-plus ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Modales: Editar, Crear, Ver -->
        <x-edit-modal :title="'Editar Entidad'" :description="'Modifica los datos de la entidad seleccionada.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editEntity.id">
                @include('admin.entities.form', ['entity' => null])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Entidad'" :description="'Agrega una nueva entidad al sistema.'">
            <form action="{{ route('entities.store') }}" method="POST">
                @csrf
                @include('admin.entities.form')
            </form>
        </x-modal>

        <x-show-modal :title="'Detalle de Entidad'" :description="'Consulta los datos de la entidad seleccionada.'">
            @include('admin.entities.partials.show_card', ['entity' => null])
        </x-show-modal>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID" route="entities.search"
                                    icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="first_name" label="Nombres"
                                    route="entities.search" icon="<i class='fas fa-user mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="identity_card" label="Cédula"
                                    route="entities.search" icon="<i class='fas fa-id-card mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="phone" label="Teléfono"
                                    route="entities.search" icon="<i class='fas fa-phone mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="municipality_id" label="Municipio"
                                    route="entities.search" icon="<i class='fas fa-map-marked-alt mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="is_client" label="Cliente"
                                    route="entities.search" icon="<i class='fas fa-user-check mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="is_supplier" label="Proveedor"
                                    route="entities.search" icon="<i class='fas fa-truck mr-2'></i>" /></th>
                            <th class="px-4 py-3"><x-table-sort-header field="is_active" label="Activo"
                                    route="entities.search" icon="<i class='fas fa-check mr-2'></i>" /></th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($entities as $entity)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">{{ $entity->id }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $entity->full_name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $entity->formatted_identity_card }}</td>
                                <td class="px-4 py-3 text-sm">{{ $entity->formatted_phone }}</td>
                                <td class="px-4 py-3 text-sm">{{ $entity->municipality->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight rounded-full {{ $entity->is_client ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : 'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' }}">{{ $entity->is_client ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight rounded-full {{ $entity->is_supplier ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : 'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' }}">{{ $entity->is_supplier ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight rounded-full {{ $entity->is_active ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : 'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' }}">{{ $entity->is_active ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <button type="button"
                                            @click="
                                                showEntity = {
                                                    id: {{ $entity->id }},
                                                    first_name: '{{ addslashes($entity->first_name) }}',
                                                    last_name: '{{ addslashes($entity->last_name) }}',
                                                    identity_card: '{{ addslashes($entity->formatted_identity_card ?? $entity->identity_card) }}',
                                                    ruc: '{{ addslashes($entity->ruc) }}',
                                                    email: '{{ addslashes($entity->email) }}',
                                                    phone: '{{ addslashes($entity->formatted_phone ?? $entity->phone) }}',
                                                    address: '{{ addslashes($entity->address) }}',
                                                    description: '{{ addslashes($entity->description) }}',
                                                    municipality: '{{ addslashes(optional($entity->municipality)->name) }}',
                                                    is_client: {{ $entity->is_client ? 'true' : 'false' }},
                                                    is_supplier: {{ $entity->is_supplier ? 'true' : 'false' }},
                                                    is_active: {{ $entity->is_active ? 'true' : 'false' }},
                                                    formatted_created_at: '{{ addslashes($entity->formatted_created_at ?? '-') }}',
                                                    formatted_updated_at: '{{ addslashes($entity->formatted_updated_at ?? '-') }}',
                                                };
                                                isShowModalOpen = true;
                                            "
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Ver Modal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button"
                                            @click="
                                                editEntity = {
                                                    id: {{ $entity->id }},
                                                    first_name: '{{ addslashes($entity->first_name) }}',
                                                    last_name: '{{ addslashes($entity->last_name) }}',
                                                    identity_card: '{{ addslashes($entity->identity_card) }}',
                                                    ruc: '{{ addslashes($entity->ruc) }}',
                                                    email: '{{ addslashes($entity->email) }}',
                                                    phone: '{{ addslashes($entity->phone) }}',
                                                    address: '{{ addslashes($entity->address) }}',
                                                    description: '{{ addslashes($entity->description) }}',
                                                    municipality_id: {{ $entity->municipality_id ?? 'null' }},
                                                    is_client: {{ $entity->is_client ? 'true' : 'false' }},
                                                    is_supplier: {{ $entity->is_supplier ? 'true' : 'false' }},
                                                    is_active: {{ $entity->is_active ? 'true' : 'false' }},
                                                };
                                                editAction = '{{ route('entities.update', $entity) }}';
                                                isEditModalOpen = true;
                                            "
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Editar Modal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('entities.destroy', $entity) }}" method="POST"
                                            onsubmit="return confirm('{{ $entity->is_active ? '¿Estás seguro de desactivar esta entidad?' : '¿Estás seguro de activar esta entidad?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 {{ $entity->is_active ? 'text-purple-600' : 'text-green-600' }} rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Toggle Active">
                                                @if ($entity->is_active)
                                                    <i class="fas fa-user-slash"></i>
                                                @else
                                                    <i class="fas fa-user-check"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No se
                                    encontraron entidades.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $entities->links() }}</div>
        </div>
    </div>
@endsection
