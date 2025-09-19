<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        @yield('title') - {{ config('app.name') }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tailwind CSS via CDN (used for the loading screen utilities) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/tailwind.output.css') }}" />

    <style>
        /* Hide Alpine elements until it initializes */
        [x-cloak] { display: none !important; }
    </style>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <script src="{{ asset('js/init-alpine.js') }}"></script>

    {{-- Chart.js se carga de forma diferida sólo en las vistas que lo necesitan mediante @push('scripts') --}}
</head>

<body>
    <!-- Loading Screen -->
    <div id="app-loader"
        class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900 transition-opacity duration-300">
        <div class="text-center">
            <div class="h-12 w-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto">
            </div>
            <p class="mt-4 text-sm font-medium text-gray-600 dark:text-gray-300">Cargando...</p>
        </div>
    </div>

    <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <!-- Desktop sidebar -->
        @include('navigations.sidebar')
        <!-- Mobile sidebar -->
        <!-- Backdrop -->
        @include('navigations.mobile-sidebar')

        <div class="flex flex-col flex-1 w-full">
            @include('navigations.header')
            <main class="h-full overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        // Hide loading screen when the page has fully loaded
        window.addEventListener('load', function() {
            var el = document.getElementById('app-loader');
            if (!el) return;
            el.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(function() {
                el.style.display = 'none';
            }, 300);
        });
        // Failsafe: auto-hide after 7s in case 'load' doesn't fire
        setTimeout(function() {
            var el = document.getElementById('app-loader');
            if (el && getComputedStyle(el).display !== 'none') {
                el.style.display = 'none';
            }
        }, 7000);
    </script>
    {{-- Stack for page-specific scripts (e.g., dashboard charts) --}}
    @stack('scripts')
</body>

</html>
