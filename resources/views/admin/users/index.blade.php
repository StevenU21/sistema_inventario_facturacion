@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
    <div class="container grid px-6 mx-auto" x-data="{
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
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Usuarios
        </h2>

        <!-- Success Messages -->
        <x-session-message />
        <!-- End Success Messages -->

        <!-- Filtros, búsqueda -->
        <div class="flex flex-wrap gap-x-1 gap-y-1 items-end justify-between mb-4">
            <form method="GET" action="{{ route('users.search') }}"
                class="flex flex-wrap gap-x-1 gap-y-1 items-end self-end">
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
                <div class="flex flex-col p-1">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring w-56 text-sm font-medium"
                        placeholder="Nombre, apellido, cedula...">
                </div>
                <div class="flex flex-col p-1">
                    <label class="invisible block text-sm font-medium">.</label>
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-32 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white">
                        Buscar
                    </button>
                </div>
                <div class="flex flex-col p-1">
                    <select name="role" id="role"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ mb_strtoupper($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="status" id="status"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="flex flex-col p-1">
                    <select name="gender" id="gender"
                        class="px-2 py-2 border rounded-lg focus:outline-none focus:ring w-32 text-sm font-medium"
                        onchange="this.form.submit()">
                        <option value="">Todos los géneros</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </form>
            <div class="flex flex-row p-1 gap-x-4 items-end">
                <label class="invisible block text-sm font-medium">.</label>
                <form method="GET" action="{{ route('users.export') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="gender" value="{{ request('gender') }}">
                    <button type="submit"
                        class="flex items-center justify-between px-4 py-2 w-36 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-red bg-red-600 hover:bg-red-700 text-white border border-red-600 active:bg-red-600">
                        <span>Exportar Excel</span>
                        <i class="fas fa-file-excel ml-2"></i>
                    </button>
                </form>
                <button type="button" @click="isModalOpen = true"
                    class="flex items-center justify-between px-4 py-2 w-40 text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:shadow-outline-purple bg-purple-600 hover:bg-purple-700 text-white border border-transparent active:bg-purple-600 ml-2">
                    <span>Crear Usuario</span>
                    <i class="fas fa-user-plus ml-2"></i>
                </button>
            </div>
        </div>

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

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
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
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @foreach ($users as $user)
                            <tr class="text-gray-700 dark:text-gray-400">
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
                                    <div class="flex items-center space-x-4 text-sm">
                                        @if ($user->is_active)
                                            <a href="{{ route('users.permissions.edit', $user) }}"
                                                class="flex items-center px-2 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-green"
                                                aria-label="Asignar Permisos">
                                                <i class="fas fa-user-shield mr-2"></i> Permisos
                                            </a>
                                            <button type="button"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Ver"
                                                @click='
                                                    showUser = {
                                                        id: {{ $user->id }},
                                                        name: @json($user->short_name),
                                                        email: @json($user->email ?? "-"),
                                                        role: @json($user->roles->count() ? ($user->formatted_role_name ?? "-") : "Sin rol"),
                                                        status: @json($user->is_active ? "Activo" : "Inactivo"),
                                                        gender: @json($user->profile->gender ?? "-"),
                                                        phone: @json($user->profile->formatted_phone ?? "-"),
                                                        identity_card: @json($user->profile->formatted_identity_card ?? "-"),
                                                        formatted_created_at: @json(optional($user->created_at)->format("d/m/Y H:i") ?? ""),
                                                        formatted_updated_at: @json(optional($user->updated_at)->format("d/m/Y H:i") ?? ""),
                                                    };
                                                    isShowModalOpen = true;
                                                '>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button"
                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                aria-label="Editar"
                                                @click='
                                                    editUser = {
                                                        id: {{ $user->id }},
                                                        first_name: @json($user->first_name ?? ""),
                                                        last_name: @json($user->last_name ?? ""),
                                                        email: @json($user->email ?? ""),
                                                        role: @json(optional($user->roles->first())->name ?? ""),
                                                        gender: @json($user->profile->gender ?? ""),
                                                        phone: @json($user->profile->phone ?? ""),
                                                        identity_card: @json($user->profile->identity_card ?? ""),
                                                        address: @json($user->profile->address ?? ""),
                                                        avatar_url: @json(($user->profile && $user->profile->avatar) ? asset('storage/' . $user->profile->avatar) : ""),
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
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 rounded-lg focus:outline-none focus:shadow-outline-gray text-purple-600 dark:text-gray-400"
                                                    aria-label="Desactivar">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de reactivar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 rounded-lg focus:outline-none focus:shadow-outline-gray text-white bg-green-600 hover:bg-green-700 active:bg-green-600"
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
