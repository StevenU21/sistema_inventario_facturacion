@extends('layouts.app')
@section('title', 'Usuarios Inactivos')

@section('content')
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Usuarios Inactivos
        </h2>

        <!-- Success Messages -->
        <x-session-message />
        <!-- End Success Messages -->

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3"><i class="fas fa-hashtag mr-2"></i>ID</th>
                            <th class="px-4 py-3"><i class="fas fa-user mr-2"></i>Nombre</th>
                            <th class="px-4 py-3"><i class="fas fa-envelope mr-2"></i>Email</th>
                            <th class="px-4 py-3"><i class="fas fa-user-tag mr-2"></i>Rol</th>
                            <th class="px-4 py-3"><i class="fas fa-toggle-on mr-2"></i>Estado</th>
                            <th class="px-4 py-3"><i class="fas fa-calendar-alt mr-2"></i>Fecha de registro</th>
                            <th class="px-4 py-3"><i class="fas fa-tools mr-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse ($users as $user)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-xs">
                                    <span class="px-2 py-1 font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-700 dark:text-white">
                                        {{ $user->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($user->roles->count())
                                        @php
                                            $roleColor = 'bg-blue-600 dark:bg-blue-700';
                                            if ($user->roles->pluck('name')->first() === 'admin') {
                                                $roleColor = 'bg-purple-700 dark:bg-purple-800';
                                            } elseif ($user->roles->pluck('name')->first() === 'cashier') {
                                                $roleColor = 'bg-orange-500 dark:bg-orange-600';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 font-semibold leading-tight text-white rounded-full {{ $roleColor }}">
                                            {{ $user->formatted_role_name ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Sin rol</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 font-semibold leading-tight text-white bg-red-600 rounded-full dark:bg-red-700 dark:text-white">Inactivo</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $user->formatted_created_at ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('inactive-users.reactivate', $user->id) }}" method="POST" onsubmit="return confirm('¿Reactivar este usuario?');">
                                        @csrf
                                        <button type="submit" class="flex items-center px-2 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-green">
                                            <i class="fas fa-user-check mr-2"></i> Reactivar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No hay usuarios inactivos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
