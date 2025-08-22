<aside class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0">
    <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="#">
            Blessa Boutique
        </a>
        <ul class="mt-6">
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('dashboard') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('dashboard') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="fas fa-home w-5 h-5"></i>
                    <span class="ml-4">Inicio</span>
                </a>
            </li>
        </ul>
        <ul>
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('categories.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('categories.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                    href="{{ route('categories.index') }}">
                    <i class="fas fa-tags w-5 h-5"></i>
                    <span class="ml-4">Categorías</span>
                </a>
            </li>

            <!-- Dropdown menu -->
            <li class="relative px-6 py-3" x-data="{ isOpen: {{ Route::is('forms', 'cards', 'charts', 'buttons', 'modals', 'tables') ? 'true' : 'false' }} }">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="isOpen = !isOpen" aria-haspopup="true">
                    <span class="inline-flex items-center">
                        <i class="fas fa-sign-in-alt w-5 h-5"></i>
                        <span class="ml-4">Entradas</span>
                    </span>
                    <i class="fas" :class="{ 'fa-chevron-down': !isOpen, 'fa-chevron-up': isOpen }"></i>
                </button>
                <ul x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('forms') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('forms') }}">
                            <i class="fas fa-file-alt w-5 h-5"></i>
                            <span class="ml-4">Formularios</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('cards') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('cards') }}">
                            <i class="fas fa-id-card w-5 h-5"></i>
                            <span class="ml-4">Tarjetas</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('charts') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('charts') }}">
                            <i class="fas fa-chart-bar w-5 h-5"></i>
                            <span class="ml-4">Gráficas</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('buttons') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('buttons') }}">
                            <i class="fas fa-mouse-pointer w-5 h-5"></i>
                            <span class="ml-4">Botones</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('modals') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('modals') }}">
                            <i class="fas fa-window-restore w-5 h-5"></i>
                            <span class="ml-4">Modales</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('tables') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('tables') }}">
                            <i class="fas fa-table w-5 h-5"></i>
                            <span class="ml-4">Tablas</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative px-6 py-3" x-data="{ isOpen: {{ Route::is('forms', 'cards', 'charts', 'buttons', 'modals', 'tables') ? 'true' : 'false' }} }">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="isOpen = !isOpen" aria-haspopup="true">
                    <span class="inline-flex items-center">
                        <i class="fas fa-cogs w-5 h-5"></i>
                        <span class="ml-4">Administración</span>
                    </span>
                    <i class="fas" :class="{ 'fa-chevron-down': !isOpen, 'fa-chevron-up': isOpen }"></i>
                </button>
                <ul x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 dark:text-gray-400">
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('users') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="fas fa-users-cog w-5 h-5"></i>
                            <span class="ml-4">Usuarios</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('cards') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('cards') }}">
                            <i class="fas fa-id-card w-5 h-5"></i>
                            <span class="ml-4">Tarjetas</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('charts') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('charts') }}">
                            <i class="fas fa-chart-bar w-5 h-5"></i>
                            <span class="ml-4">Gráficas</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('buttons') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('buttons') }}">
                            <i class="fas fa-mouse-pointer w-5 h-5"></i>
                            <span class="ml-4">Botones</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('modals') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('modals') }}">
                            <i class="fas fa-window-restore w-5 h-5"></i>
                            <span class="ml-4">Modales</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('tables') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('tables') }}">
                            <i class="fas fa-table w-5 h-5"></i>
                            <span class="ml-4">Tablas</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
