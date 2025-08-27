@extends('layouts.app')
@section('title', 'Crear Unidad de Medida')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Crear Unidad de Medida
        </h2>

        <div class="mb-4 flex justify-end">
            <a href="{{ route('unit_measures.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <form action="{{ route('unit_measures.store') }}" method="POST">
            @csrf
            @include('admin.unit_measures.form')
        </form>
    </div>
@endsection
