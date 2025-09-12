@extends('layouts.app')
@section('title', 'Nueva Compra')

@section('content')
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
                <span class="text-gray-700 dark:text-gray-200">Registrar Compra</span>
            </li>
        </ol>
    </nav>
    <div class="container px-6 mx-auto grid">
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
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg animate-gradient mb-6">
            <div class="absolute inset-0 opacity-10 pointer-events-none"
                style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
            </div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                            <i class="fas fa-shopping-cart text-white/90 mr-3"></i>
                            Crear Compra
                        </h1>
                        <p class="mt-1 text-white/80 text-sm">Registra una nueva compra en el sistema.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('purchases.index') }}"
                            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-white/10 hover:bg-white/15 rounded-lg backdrop-blur">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <form method="POST" action="{{ route('purchases.store') }}">
            @csrf
            @include('admin.purchases.form')
        </form>
    </div>
@endsection
