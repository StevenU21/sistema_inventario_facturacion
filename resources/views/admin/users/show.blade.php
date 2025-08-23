@extends('layouts.app')
@section('title', 'Detalles de Usuario')

@section('content')
	<div class="container px-6 mx-auto grid">
		<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
			Detalles de Usuario
		</h2>

		<div class="mb-4 flex justify-end">
			<a href="{{ route('users.index') }}"
				class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
				<i class="fas fa-arrow-left mr-2"></i> Volver
			</a>
		</div>

		<div class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
			<div class="p-4">
				<h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">
					Información del Usuario
				</h3>
				<div class="mt-4">
					<p class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 flex items-center">
						<i class="fas fa-hashtag text-purple-600 dark:text-purple-400 mr-2"></i>
						<strong class="text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">ID:</strong> {{ $user->id }}
					</p>
					<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 flex items-center">
						<i class="fas fa-user text-purple-600 dark:text-purple-400 mr-2"></i>
						<strong class="text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">Nombre:</strong> {{ $user->first_name }} {{ $user->last_name }}
					</p>
					<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 flex items-center">
						<i class="fas fa-envelope text-purple-600 dark:text-purple-400 mr-2"></i>
						<strong class="text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">Email:</strong> {{ $user->email }}
					</p>
					<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 flex items-center">
						<i class="fas fa-user-tag text-purple-600 dark:text-purple-400 mr-2"></i>
						<strong class="text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">Rol:</strong>
						@if($user->roles->count())
							<span class="ml-1 px-2 py-1 font-semibold leading-tight text-white bg-blue-600 rounded-full dark:bg-blue-700 dark:text-white">
								{{ $user->roles->pluck('name')->join(', ') }}
							</span>
						@else
							<span class="ml-1 text-gray-400">Sin rol</span>
						@endif
					</p>
					<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 flex items-center">
						<i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i>
						<strong class="text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">Fecha de registro:</strong> {{ $user->created_at->format('Y-m-d') }}
					</p>
					<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-150 flex items-center">
						<i class="fas fa-clock text-purple-600 dark:text-purple-400 mr-2"></i>
						<strong class="text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-150">Última actualización:</strong> {{ $user->updated_at->format('Y-m-d') }}
					</p>
				</div>
			</div>
		</div>
	</div>
@endsection
