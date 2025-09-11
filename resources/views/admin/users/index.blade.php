@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8" x-data="{
        isModalOpen: false,
        isEditModalOpen: false,
        isShowModalOpen: false,
        editAction: '',
        showUser: { id: '', name: '', email: '', role: '', status: '', gender: '', phone: '', identity_card: '', formatted_created_at: '', formatted_updated_at: '' },
        editUser: { id: '', first_name: '', last_name: '', email: '', role: '', gender: '', phone: '', identity_card: '', address: '', avatar_url: '' },
        closeModal() { this.isModalOpen = false },
        closeEditModal() { this.isEditModalOpen = false },
        closeShowModal() { this.isShowModalOpen = false }
    }">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-cogs w-5 h-5"></i> Modulo de Administración
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Usuarios</span>
                </li>
            </ol>
        </nav>

        <!-- Page header card -->
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
            <div class="absolute inset-0 opacity-20 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-users text-white/90 mr-3"></i>
                            Usuarios
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Administra cuentas, estados y permisos.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('users.export') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="role" value="{{ request('role') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="gender" value="{{ request('gender') }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </form>
                        <button type="button" @click="isModalOpen = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-purple-700 hover:bg-gray-100 text-sm font-semibold shadow">
                            <i class="fas fa-user-plus"></i>
                            Crear Usuario
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Messages -->
        <div class="mt-4">
            <x-session-message />
        </div>
        <!-- End Success Messages -->

        <!-- Filtros, búsqueda -->
        <section class="mt-4 rounded-xl bg-white dark:bg-gray-800 shadow-md p-4 sm:p-5">
            <form method="GET" action="{{ route('users.search') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div class="sm:col-span-6 flex flex-row gap-2 items-end">
                    <div class="flex-1">
                        <label for="search"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Nombre, apellido, cédula...">
                    </div>
                    <div class="flex flex-row gap-2 items-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-purple-600 hover:bg-purple-700 text-white shadow">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                        @if (request()->hasAny(['per_page', 'search', 'role', 'status', 'gender']))
                            <a href="{{ route('users.index') }}"
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
                    <label for="role"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Rol</label>
                    <select name="role" id="role"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ mb_strtoupper($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                    <select name="status" id="status"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div>
                    <label for="gender"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">Género</label>
                    <select name="gender" id="gender"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        onchange="this.form.submit()">
                        <option value="">Todos los géneros</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </form>
        </section>

        <!-- Modales: Editar, Crear, Ver -->
        <x-edit-modal :title="'Editar Usuario'" :description="'Modifica los datos del usuario seleccionado.'">
            <form :action="editAction" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" :value="editUser.id">
                @include('admin.users.form', ['alpine' => true])
            </form>
        </x-edit-modal>

        <x-modal :title="'Crear Usuario'" :description="'Agrega un nuevo usuario al sistema.'">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.users.form', ['alpine' => false])
            </form>
        </x-modal>

        <x-show-modal :title="'Detalle de Usuario'" :description="'Consulta los datos del usuario seleccionado.'">
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 space-y-4">
                    <div class="border-b pb-3">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-user text-purple-600 dark:text-purple-400"></i>
                            <span x-text="showUser.name"></span>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 text-sm" x-text="showUser.email"></p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-hashtag text-purple-600 dark:text-purple-400"></i>
                            <strong>ID:</strong> <span x-text="showUser.id"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-user-tag text-purple-600 dark:text-purple-400"></i>
                            <strong>Rol:</strong> <span x-text="showUser.role"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-toggle-on text-purple-600 dark:text-purple-400"></i>
                            <strong>Estado:</strong> <span x-text="showUser.status"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-venus-mars text-purple-600 dark:text-purple-400"></i>
                            <strong>Género:</strong> <span x-text="showUser.gender"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-phone text-purple-600 dark:text-purple-400"></i>
                            <strong>Teléfono:</strong> <span x-text="showUser.phone"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <i class="fas fa-id-card text-purple-600 dark:text-purple-400"></i>
                            <strong>Cédula:</strong> <span x-text="showUser.identity_card"></span>
                        </div>
                    </div>
                    <div
                        class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t pt-3 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-purple-500"></i>
                            <strong>Registro:</strong> <span x-text="showUser.formatted_created_at"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-purple-500"></i>
                            <strong>Actualización:</strong> <span x-text="showUser.formatted_updated_at"></span>
                        </div>
                    </div>
                </div>
            </div>
        </x-show-modal>

        <div class="mt-4 w-full overflow-hidden rounded-xl shadow-md bg-white dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr
                            class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-300 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3">
                                <x-table-sort-header field="id" label="ID" route="users.search"
                                    icon="<i class='fas fa-hashtag mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="first_name" label="Nombre" route="users.search"
                                    icon="<i class='fas fa-user mr-2'></i>" />
                            </th>

                            <th class="px-4 py-3">
                                <x-table-sort-header field="identity_card" label="Cédula" route="users.search"
                                    icon="<i class='fas fa-envelope mr-2'></i>" />
                            </th>

                            <th class="px-4 py-3">
                                <x-table-sort-header field="phone" label="Teléfono" route="users.search"
                                    icon="<i class='fas fa-envelope mr-2'></i>" />
                            </th>

                            <th class="px-4 py-3">
                                <x-table-sort-header field="email" label="Email" route="users.search"
                                    icon="<i class='fas fa-envelope mr-2'></i>" />
                            </th>

                            <th class="px-4 py-3">
                                <x-table-sort-header field="role" label="Rol" route="users.search"
                                    icon="<i class='fas fa-user-tag mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3">
                                <x-table-sort-header field="is_active" label="Estado" route="users.search"
                                    icon="<i class='fas fa-toggle-on mr-2'></i>" />
                            </th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @foreach ($users as $user)
                            <tr
                                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50/60 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $user->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $user->short_name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $user->profile->formatted_identity_card ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $user->profile->formatted_phone ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $user->email ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    @if ($user->roles->count())
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-white bg-blue-600 rounded-full dark:bg-blue-700 dark:text-white">
                                            {{ $user->formatted_role_name ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Sin rol</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($user->is_active === true)
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-white bg-green-600 rounded-full dark:bg-green-700 dark:text-white">Activo</span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-white bg-red-600 rounded-full dark:bg-red-700 dark:text-white">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 text-sm">
                                        @if ($user->is_active)
                                            <a href="{{ route('users.permissions.edit', $user) }}" title="Permisos"
                                                class="inline-flex items-center justify-center h-9 w-9 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                aria-label="Asignar Permisos">
                                                <i class="fas fa-user-shield"></i>
                                            </a>
                                            <button type="button" title="Ver"
                                                class="inline-flex items-center justify-center h-9 w-9 text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                aria-label="Ver"
                                                @click='
                                                    showUser = {
                                                        id: {{ $user->id }},
                                                        name: @json($user->short_name),
                                                        email: @json($user->email ?? '-'),
                                                        role: @json($user->roles->count() ? $user->formatted_role_name ?? '-' : 'Sin rol'),
                                                        status: @json($user->is_active ? 'Activo' : 'Inactivo'),
                                                        gender: @json($user->profile->gender ?? '-'),
                                                        phone: @json($user->profile->formatted_phone ?? '-'),
                                                        identity_card: @json($user->profile->formatted_identity_card ?? '-'),
                                                        formatted_created_at: @json(optional($user->created_at)->format('d/m/Y H:i') ?? ''),
                                                        formatted_updated_at: @json(optional($user->updated_at)->format('d/m/Y H:i') ?? ''),
                                                    };
                                                    isShowModalOpen = true;
                                                '>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" title="Editar"
                                                class="inline-flex items-center justify-center h-9 w-9 text-green-600 hover:bg-green-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                aria-label="Editar"
                                                @click='
                                                    editUser = {
                                                        id: {{ $user->id }},
                                                        first_name: @json($user->first_name ?? ''),
                                                        last_name: @json($user->last_name ?? ''),
                                                        email: @json($user->email ?? ''),
                                                        role: @json(optional($user->roles->first())->name ?? ''),
                                                        gender: @json($user->profile->gender ?? ''),
                                                        phone: @json($user->profile->phone ?? ''),
                                                        identity_card: @json($user->profile->identity_card ?? ''),
                                                        address: @json($user->profile->address ?? ''),
                                                        avatar_url: @json($user->profile && $user->profile->avatar ? asset('storage/' . $user->profile->avatar) : ''),
                                                    };
                                                    editAction = `{{ route('users.update', $user) }}`;
                                                    isEditModalOpen = true;
                                                '>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de desactivar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Desactivar"
                                                    class="inline-flex items-center justify-center h-9 w-9 text-red-600 hover:bg-red-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                    aria-label="Desactivar">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de reactivar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Reactivar"
                                                    class="inline-flex items-center justify-center h-9 w-9 text-green-600 hover:bg-green-50 dark:hover:bg-gray-700 rounded-lg focus:outline-none"
                                                    aria-label="Reactivar">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
