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
                    class="{{ Route::is('dashboard.index') ? 'absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg' : '' }}"
                    aria-hidden="true"></span>
                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ Route::is('dashboard.index') ? 'text-gray-800 dark:text-gray-100' : '' }}"
                    href="{{ route('dashboard.index') }}">
                    <i class="fas fa-home w-5 h-5"></i>
                    <span class="ml-4">Inicio</span>
                </a>
            </li>

            <!-- Dropdown Gestión de Ventas -->
            <li class="relative px-6 py-3" x-data="salesDropdownMenu()" x-init="initSalesDropdown()">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="toggleSalesDropdown" aria-haspopup="true">
                    <span class="inline-flex items-center">
                        <i class="fas fa-cash-register w-5 h-5"></i>
                        <span class="ml-4">Gestión de Ventas</span>
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
                        <x-ui.submit-link :href="route('admin.accounts_receivable.index')" :active-class="Route::is('admin.accounts_receivable.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-user-clock"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Cuentas por Cobrar</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('admin.sales.index')" :active-class="Route::is('admin.sales.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-receipt"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Historial de Ventas</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('admin.payments.index')" :active-class="Route::is('admin.payments.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-money-bill-wave"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Historia de pagos</span>
                        </x-ui.submit-link>
                    </li>
                </ul>
            </li>

            <li class="relative px-6 py-3" x-data="purchaseDropdownMenu()" x-init="initPurchaseDropdown()">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="togglePurchaseDropdown" aria-haspopup="true">
                    <span class="inline-flex items-center">
                        <i class="fas fa-boxes w-5 h-5"></i>
                        <span class="ml-4">Gestión de Compras</span>
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
                        <x-ui.submit-link :href="route('purchases.index')" :active-class="Route::is('purchases.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-shopping-cart"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Compras</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('entities.index')" :active-class="Route::is('entities.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-users"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Clientes & Proveedores</span>
                        </x-ui.submit-link>
                    </li>
                </ul>
            </li>
        </ul>

        <ul>
            <!-- Dropdown Gestión de Inventario -->
            <li class="relative px-6 py-3" x-data="inventoryDropdownMenu()" x-init="initInventoryDropdown()">
                <button
                    class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none"
                    @click="toggleInventoryDropdown" aria-haspopup="true">
                    <span class="inline-flex items-center">
                        <i class="fas fa-boxes w-5 h-5"></i>
                        <span class="ml-4">Gestión de Inventario</span>
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
                        <x-ui.submit-link :href="route('products.index')" :active-class="Route::is('products.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-tags"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Productos</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('inventories.index')" :active-class="Route::is('inventories.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-warehouse"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Inventarios</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('inventory_movements.index')" :active-class="Route::is('inventory_movements.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-exchange-alt"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Movimientos</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('warehouses.index')" :active-class="Route::is('warehouses.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-warehouse"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Almacenes</span>
                        </x-ui.submit-link>
                    </li>

                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('kardex.index')" :active-class="Route::is('kardex.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-book"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Reporte Kardex</span>
                        </x-ui.submit-link>
                    </li>
                </ul>
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
                        <x-ui.submit-link :href="route('categories.index')" :active-class="Route::is('categories.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-th-list"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Categorías</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('brands.index')" :active-class="Route::is('brands.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-tags"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Marcas</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('colors.index')" :active-class="Route::is('colors.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-paint-brush"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Colores</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('sizes.index')" :active-class="Route::is('sizes.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-ruler-combined"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Tallas</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('unit_measures.index')" :active-class="Route::is('unit_measures.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-balance-scale"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Unidades de Medida</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('taxes.index')" :active-class="Route::is('taxes.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-percent"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Impuestos</span>
                        </x-ui.submit-link>
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
                        <x-ui.submit-link :href="route('users.index')" :active-class="Route::is('users.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-users-cog"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Usuarios</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('roles.index')" :active-class="Route::is('roles.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-user-shield"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Roles & Permisos</span>
                        </x-ui.submit-link>
                    </li>
                    <li class="px-6 py-2">
                        <x-ui.submit-link :href="route('audits.index')" :active-class="Route::is('audits.*') ? 'text-gray-800 dark:text-gray-100' : ''" icon="fas fa-clipboard-list"
                            class="inline-flex items-center w-full transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-semibold">
                            <span class="ml-4">Auditoría</span>
                        </x-ui.submit-link>
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
    function salesDropdownMenu() {
        return {
            isOpen: localStorage.getItem('salesDropdownOpen') === 'true',
            toggleSalesDropdown() {
                this.isOpen = !this.isOpen;
                localStorage.setItem('salesDropdownOpen', this.isOpen);
            },
            initSalesDropdown() {
                this.isOpen = localStorage.getItem('salesDropdownOpen') === 'true';
            }
        }
    }

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

    function inventoryDropdownMenu() {
        return {
            isOpen: localStorage.getItem('inventoryDropdownOpen') === 'true',
            toggleInventoryDropdown() {
                this.isOpen = !this.isOpen;
                localStorage.setItem('inventoryDropdownOpen', this.isOpen);
            },
            initInventoryDropdown() {
                this.isOpen = localStorage.getItem('inventoryDropdownOpen') === 'true';
            }
        }
    }

    function purchaseDropdownMenu() {
        return {
            isOpen: localStorage.getItem('purchaseDropdownOpen') === 'true',
            togglePurchaseDropdown() {
                this.isOpen = !this.isOpen;
                localStorage.setItem('purchaseDropdownOpen', this.isOpen);
            },
            initPurchaseDropdown() {
                this.isOpen = localStorage.getItem('purchaseDropdownOpen') === 'true';
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
