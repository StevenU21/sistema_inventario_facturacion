@extends('layouts.app')
@section('title', 'Detalles de la Empresa')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-bold text-gray-700 dark:text-gray-200 flex items-center">
            <i class="fas fa-building text-purple-600 dark:text-purple-400 mr-3 text-3xl"></i>
            Datos de la Empresa
        </h2>
        <div class="mb-4 flex justify-end">
            <a href="{{ route('companies.edit', $company) }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-edit mr-2"></i> Actualizar
            </a>
        </div>
        <div class="w-full overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 hover:shadow-lg transition-shadow duration-150">
            <div class="flex flex-col md:flex-row p-6 gap-6 items-start">
                <!-- Left: Logo + basic info -->
                <div class="w-full md:w-1/3 flex-shrink-0 flex flex-col items-start">
                    <div class="flex items-start space-x-4 w-full">
                        <div class="flex-shrink-0">
                            @if ($company->logo)
                                <img src="{{ $company->avatar_url }}" alt="Logo"
                                    class="w-full max-w-xs md:max-w-sm lg:max-w-md aspect-square object-cover border-2 border-white dark:border-purple-500 shadow rounded-md mx-auto"
                                    style="min-width:180px;min-height:180px;max-width:350px;max-height:350px;object-fit:cover;">
                            @else
                                <div class="w-full max-w-xs md:max-w-sm lg:max-w-md aspect-square bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-6xl text-gray-400 border-2 border-white dark:border-purple-500 shadow rounded-md mx-auto"
                                    style="min-width:180px;min-height:180px;max-width:350px;max-height:350px;">
                                    <i class="fas fa-building"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="grid grid-cols-1 gap-8">
                                <div class="text-left">
                                    <span class="block text-3xl md:text-4xl font-extrabold text-gray-800 dark:text-white leading-tight mb-4 uppercase">{{ $company->name }}</span>
                                    <h4 class="text-base font-bold text-purple-700 dark:text-purple-400 mt-4 mb-2 flex items-center">
                                        <i class="fas fa-id-card mr-2"></i> Datos de la empresa
                                    </h4>
                                    <div class="text-base text-gray-600 dark:text-gray-200">
                                        <p><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-building mr-1"></i> RUC:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->ruc }}</span>
                                        </p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-envelope mr-1"></i> Email:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->email }}</span>
                                        </p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-phone mr-1"></i> Teléfono:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->phone }}</span>
                                        </p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-map-marker-alt mr-1"></i> Dirección:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->address }}</span>
                                        </p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-align-left mr-1"></i> Descripción:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->description }}</span>
                                        </p>
                                    </div>
                                    <h4 class="text-base font-bold text-purple-700 dark:text-purple-400 mt-6 mb-2 flex items-center">
                                        <i class="fas fa-calendar-alt mr-2"></i> Fechas
                                    </h4>
                                    <div class="text-base text-gray-600 dark:text-gray-200">
                                        <p><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-calendar-plus mr-1"></i> Fecha de registro:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->formatted_created_at ?? '-' }}</span>
                                        </p>
                                        <p class="mt-1"><span class="font-semibold text-gray-700 dark:text-gray-200"><i class="fas fa-history mr-1"></i> Última actualización:</span>
                                            <span class="text-gray-800 dark:text-white ml-1">{{ $company->formatted_updated_at ?? '-' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 w-full md:pl-4"></div>
        </div>
    </div>
    </div>
@endsection
