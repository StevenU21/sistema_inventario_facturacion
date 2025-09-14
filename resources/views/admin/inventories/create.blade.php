@extends('layouts.app')
@section('title', 'Nuevo Inventario')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="mt-4 mb-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home mr-1"></i> Modulo de Inventario
                    </a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <a href="{{ route('inventories.index') }}"
                        class="hover:text-gray-700 dark:hover:text-gray-200">Inventarios</a>
                </li>
                <li class="text-gray-400">/</li>
                <li>
                    <span class="text-gray-700 dark:text-gray-200">Crear</span>
                </li>
            </ol>
        </nav>

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

        <!-- Header card inspired by purchases -->
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient">
            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-plus-circle text-white/90 mr-3"></i>
                            Nuevo Inventario
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Registra existencias iniciales por variante y almacén.</p>
                    </div>
                    <a href="{{ route('inventories.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-sm font-medium backdrop-blur transition">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </section>

        <form action="{{ route('inventories.store') }}" method="POST" class="mt-4">
            @csrf
            @include('admin.inventories.form_create')
        </form>
    </div>
@endsection
