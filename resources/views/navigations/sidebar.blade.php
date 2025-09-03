<style>
    aside::-webkit-scrollbar {
        width: 8px;
        background: #f1f1f1;
    }

    aside::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    aside::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    aside {
        scrollbar-width: thin;
        scrollbar-color: #c1c1c1 #f1f1f1;
    }
</style>

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
            <!-- Productos -->
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('products.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('products.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                    href="{{ route('products.index') }}">
                    <i class="fas fa-tags w-5 h-5"></i>
                    <span class="ml-4">Productos</span>
                </a>
            </li>

            <!-- Inventarios -->
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('inventories.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('inventories.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                    href="{{ route('inventories.index') }}">
                    <i class="fas fa-warehouse w-5 h-5"></i>
                    <span class="ml-4">Inventarios</span>
                </a>
            </li>

            <!-- Movimientos del Inventario -->
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('inventory_movements.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('inventory_movements.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                    href="{{ route('inventory_movements.index') }}">
                    <i class="fas fa-exchange-alt w-5 h-5"></i>
                    <span class="ml-4">Movimientos</span>
                </a>
            </li>

            <!-- Clientes & Proveedores -->
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('entities.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('entities.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                    href="{{ route('entities.index') }}">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span class="ml-4">Clientes & Proveedores</span>
                </a>
            </li>

            <!-- Almacenes -->
            <li class="relative px-6 py-3">
                <span
                    class="{{ Route::is('warehouses.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('warehouses.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                    href="{{ route('warehouses.index') }}">
                    <i class="fas fa-warehouse w-5 h-5"></i>
                    <span class="ml-4">Almacenes</span>
                </a>
            </li>

            <!-- Catálogo Dropdown -->
            <li class="relative px-6 py-3" x-data="dropdownMenu()" x-init="initDropdown()">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="toggleDropdown" aria-haspopup="true">
                    <span class="inline-flex items-center">
                        <i class="fas fa-sign-in-alt w-5 h-5"></i>
                        <span class="ml-4">Catálogo</span>
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
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('categories.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('categories.index') }}">
                            <i class="fas fa-th-list w-5 h-5"></i>
                            <span class="ml-4">Categorías</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('brands.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('brands.index') }}">
                            <i class="fas fa-tags w-5 h-5"></i>
                            <span class="ml-4">Marcas</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('unit_measures.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('unit_measures.index') }}">
                            <i class="fas fa-balance-scale w-5 h-5"></i>
                            <span class="ml-4">Unidades de Medida</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('payment_methods.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('payment_methods.index') }}">
                            <i class="fas fa-credit-card w-5 h-5"></i>
                            <span class="ml-4">Métodos de Pago</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('taxes.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('taxes.index') }}">
                            <i class="fas fa-percent w-5 h-5"></i>
                            <span class="ml-4">Impuestos</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Administración Dropdown -->
            <li class="relative px-6 py-3" x-data="adminDropdownMenu()" x-init="initAdminDropdown()">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="toggleAdminDropdown" aria-haspopup="true">
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
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('users.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="fas fa-users-cog w-5 h-5"></i>
                            <span class="ml-4">Usuarios</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('users.inactive') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('users.inactive') }}">
                            <i class="fas fa-user-slash w-5 h-5"></i>
                            <span class="ml-4">Usuarios Inactivos</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('roles.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('roles.index') }}">
                            <i class="fas fa-user-shield w-5 h-5"></i>
                            <span class="ml-4">Roles & Permisos</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('audits.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('audits.index') }}">
                            <i class="fas fa-clipboard-list w-5 h-5"></i>
                            <span class="ml-4">Auditoría</span>
                        </a>
                    </li>
                    <li class="px-6 py-2">
                        <a class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('backups.*') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                            href="{{ route('backups.index') }}">
                            <i class="fas fa-database w-5 h-5"></i>
                            <span class="ml-4">Respaldos</span>
                        </a>
                    </li>
                    <!-- Empresa -->
                    <li class="px-6 py-2">
                        @php $company = \App\Models\Company::first(); @endphp
                        <span
                            class="{{ Route::is('companies.*') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                            aria-hidden="true"></span>
                        <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 {{ Route::is('companies.*') ? 'text-gray-800 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}"
                            href="{{ $company ? route('companies.show', $company) : route('companies.create') }}">
                            <i class="fas fa-building w-5 h-5"></i>
                            <span class="ml-4">Empresa</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
<script>
    function dropdownMenu() {
        return {
            isOpen: localStorage.getItem('catalogDropdownOpen') === 'true',
            toggleDropdown() {
                this.isOpen = !this.isOpen;
                localStorage.setItem('catalogDropdownOpen', this.isOpen);
            },
            initDropdown() {
                this.isOpen = localStorage.getItem('catalogDropdownOpen') === 'true';
            }
        }
    }

    function usuariosDropdownMenu() {
        return {
            isOpen: localStorage.getItem('usuariosDropdownOpen') === 'true',
            toggleUsuariosDropdown() {
                this.isOpen = !this.isOpen;
                localStorage.setItem('usuariosDropdownOpen', this.isOpen);
            },
            initUsuariosDropdown() {
                this.isOpen = localStorage.getItem('usuariosDropdownOpen') === 'true';
            }
        }
    }

    function adminDropdownMenu() {
        return {
            isOpen: localStorage.getItem('adminDropdownOpen') === 'true',
            toggleAdminDropdown() {
                this.isOpen = !this.isOpen;
                localStorage.setItem('adminDropdownOpen', this.isOpen);
            },
            initAdminDropdown() {
                this.isOpen = localStorage.getItem('adminDropdownOpen') === 'true';
            }
        }
    }
</script>
