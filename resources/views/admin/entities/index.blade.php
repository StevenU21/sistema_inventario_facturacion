@extends('layouts.app')
@section('title', 'Clientes & Proveedores')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8" x-data="{
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
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Modulo de Compras
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Clientes & Proveedores</span>
                </li>
            </ol>
        </nav>

        <!-- Animación de gradiente y Page header card -->
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
                            <i class="fas fa-users text-white/90 mr-3"></i>
                            Clientes & Proveedores
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Gestiona, busca y organiza tus entidades.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('entities.export') }}">
                            @if (request()->filled('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if (request()->has('is_client') && request('is_client') !== '')
                                <input type="hidden" name="is_client" value="{{ request('is_client') }}">
                            @endif
                            @if (request()->has('is_supplier') && request('is_supplier') !== '')
                                <input type="hidden" name="is_supplier" value="{{ request('is_supplier') }}">
                            @endif
                            @if (request()->has('is_active') && request('is_active') !== '')
                                <input type="hidden" name="is_active" value="{{ request('is_active') }}">
                            @endif
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                        <button type="button" @click="isModalOpen = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                            <i class="fas fa-plus"></i>
                            Crear registro
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-4">
            <x-session-message />
        </div>

        <!-- Filtros, búsqueda -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('entities.search') }}"
                class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-3 lg:col-span-4 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Nombre, cédula, correo...">
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request()->hasAny(['search', 'per_page', 'is_client', 'is_supplier', 'is_active']))
                            <a href="{{ route('entities.index') }}"
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
                    <label for="is_client"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Cliente</label>
                    <select name="is_client" id="is_client"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Cliente?</option>
                        <option value="1" {{ request('is_client') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('is_client') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <label for="is_supplier"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Proveedor</label>
                    <select name="is_supplier" id="is_supplier"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Proveedor?</option>
                        <option value="1" {{ request('is_supplier') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('is_supplier') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <label for="is_active"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Activo</label>
                    <select name="is_active" id="is_active"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Activo?</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </form>
        </section>

        <!-- Modales: Editar, Crear, Ver -->
        <x-edit-modal :title="'Editar Entidad'" :description="'Modifica los datos de la entidad seleccionada.'">
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editEntity.id">
                @include('admin.entities.form', ['entity' => null, 'useAlpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Entidad'" :description="'Agrega una nueva entidad al sistema.'">
            <form action="{{ route('entities.store') }}" method="POST">
                @csrf
                @include('admin.entities.form', ['useAlpine' => false])
            </form>
        </x-modal>

        <x-show-modal :title="'Detalle de Entidad'" :description="'Consulta los datos de la entidad seleccionada.'">
            @include('admin.entities.partials.show_card', ['entity' => null])
        </x-show-modal>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3"><x-table-sort-header field="id" label="ID"
                                    route="entities.search" icon="<i class='fas fa-hashtag mr-2'></i>" /></th>
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
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($entities as $entity)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
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
                                        class="px-2 py-1 font-semibold leading-tight r  ounded-full {{ $entity->is_active ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : 'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' }}">{{ $entity->is_active ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 text-sm">
                                        <button type="button" title="Ver"
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
                                            class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                            aria-label="Ver Modal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" title="Editar"
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
                                                    department_id: {{ optional($entity->municipality)->department_id ?? 'null' }},
                                                    is_client: {{ $entity->is_client ? 'true' : 'false' }},
                                                    is_supplier: {{ $entity->is_supplier ? 'true' : 'false' }},
                                                    is_active: {{ $entity->is_active ? 'true' : 'false' }},
                                                };
                                                editAction = '{{ route('entities.update', $entity) }}';
                                                isEditModalOpen = true;
                                            "
                                            class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                            aria-label="Editar Modal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('entities.destroy', $entity) }}" method="POST"
                                            onsubmit="return confirm('{{ $entity->is_active ? '¿Estás seguro de desactivar esta entidad?' : '¿Estás seguro de activar esta entidad?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                title="{{ $entity->is_active ? 'Desactivar' : 'Activar' }}"
                                                class="inline-flex items-center justify-center h-9 w-9 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
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
