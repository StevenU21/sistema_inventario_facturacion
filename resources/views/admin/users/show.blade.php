@extends('layouts.app')
@section('title', 'Detalles de Usuario')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200 flex items-center">
            <i class="fas fa-user-circle text-purple-600 dark:text-purple-400 mr-3 text-3xl"></i>
            Detalles de Usuario
        </h2>
        <div class="mb-4 flex justify-end">
            <a href="{{ route('users.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
        <div class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
            <div class="flex flex-col md:flex-row p-6 gap-6 items-start">
                <!-- Left: Avatar + basic contact info -->
                <div class="w-full md:w-1/3 flex-shrink-0 flex flex-col items-start">
                    <div class="flex items-start space-x-4 w-full">
                        <div class="flex-shrink-0">
                            @if ($user->profile && $user->profile->avatar)
                                <img src="{{ $user->profile->getAvatarUrlAttribute() }}" alt="Avatar"
                                    class="w-24 h-24 object-cover border-2 border-white dark:border-purple-500 shadow rounded-md">
                            @else
                                <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-2xl text-gray-400 border-2 border-white dark:border-purple-500 shadow rounded-md">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="text-left">
                                <span class="block text-2xl md:text-3xl font-bold text-gray-800 dark:text-white leading-tight">{{ $user->first_name }} {{ $user->last_name }}</span>

                                <!-- DATOS PERSONALES -->
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-4 mb-2">Datos personales</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-200">
                                    <div>
                                        <p><span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->email }}</span></p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200">Teléfono:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->profile->phone ?? '-' }}</span></p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200">Cédula:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->profile->identity_card ?? '-' }}</span></p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200">Dirección:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->profile->address ?? '-' }}</span></p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200">Género:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->profile->gender ?? '-' }}</span></p>
                                    </div>
                                </div>

                                <!-- DATOS DEL REGISTRO -->
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-6 mb-2">Datos del registro</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-200">
                                    <div>
                                        <p><span class="font-semibold text-gray-700 dark:text-gray-200">Rol:</span>
                                            <span class="ml-1 px-2 py-1 bg-blue-600 text-white rounded-full text-xs">{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</span></p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200">Fecha de registro:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->created_at->format('Y-m-d') }}</span></p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200">Última actualización:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $user->updated_at->format('Y-m-d') }}</span></p>
                                    </div>
                                </div>

                                <!-- PERMISOS DIRECTOS -->
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-6 mb-2 flex items-center">
                                    <i class="fas fa-key mr-2"></i> Permisos Directos
                                </h4>
                                @php $directPermissions = $user->getDirectPermissions()->pluck('name'); @endphp
                                @if ($directPermissions->count())
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @foreach ($directPermissions as $perm)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold dark:bg-green-900 dark:text-green-200 uppercase">{{ $perm }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-300 italic mb-2">Sin permisos directos</span>
                                @endif

                                <!-- PERMISOS HEREDADOS POR ROL -->
                                <h4 class="text-sm font-semibold text-purple-700 dark:text-purple-400 mt-4 mb-2 flex items-center">
                                    <i class="fas fa-users-cog mr-2"></i> Permisos Heredados por Rol
                                </h4>
                                @php $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->unique(); @endphp
                                @if ($rolePermissions->count())
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @foreach ($rolePermissions as $perm)
                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-semibold dark:bg-gray-700 dark:text-gray-200 uppercase">{{ $perm }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-300 italic mb-2">Sin permisos heredados</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: placeholder for spacing; main details/cards remain below -->
                <div class="flex-1 w-full md:pl-4">
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection
