<header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
    <div class="container flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
        <!-- Mobile hamburger -->
        <button class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple"
            @click="toggleSideMenu" aria-label="Menu">
            <i class="fas fa-bars w-6 h-6"></i>
        </button>
        <!-- Reloj con fecha y hora en tiempo real, nombre y rol de usuario -->
        <div class="flex justify-center flex-1 lg:mr-32">
            <div x-data="{ now: new Date() }" x-init="setInterval(() => now = new Date(), 1000)">
                <span class="text-lg font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-4 uppercase">
                    <span>
                        <span
                            x-text="now.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }).toUpperCase()"></span>
                        <span class="mx-2">|</span>
                        <span
                            x-text="now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).toUpperCase()"></span>
                    </span>
                    @auth
                        <span class="ml-4 font-bold text-xl text-gray-800 dark:text-gray-100">
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            @if (Auth::user()->formatted_role_name)
                                - {{ Auth::user()->formatted_role_name }}
                            @endif
                        </span>
                    @endauth
                </span>
            </div>
        </div>
        <ul class="flex items-center flex-shrink-0 space-x-6">
            <!-- Theme toggler -->
            <li class="flex">
                <button class="rounded-md focus:outline-none focus:shadow-outline-purple" @click="toggleTheme"
                    aria-label="Toggle color mode">
                    <template x-if="!dark">
                        <i class="fas fa-sun w-5 h-5"></i>
                    </template>
                    <template x-if="dark">
                        <i class="fas fa-moon w-5 h-5"></i>
                    </template>
                </button>
            </li>
            <!-- Notifications menu -->
            <li class="relative">
                <button class="relative align-middle rounded-md focus:outline-none focus:shadow-outline-purple"
                    @click="toggleNotificationsMenu" @keydown.escape="closeNotificationsMenu" aria-label="Notifications"
                    aria-haspopup="true">
                    <i class="fas fa-bell w-5 h-5"></i>
                    <!-- Notification badge -->
                    <span aria-hidden="true"
                        class="absolute top-0 right-0 inline-block w-3 h-3 transform translate-x-1 -translate-y-1 bg-red-600 border-2 border-white rounded-full dark:border-gray-800"></span>
                </button>
                <template x-if="isNotificationsMenuOpen">
                    <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click.away="closeNotificationsMenu"
                        @keydown.escape="closeNotificationsMenu"
                        class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:text-gray-300 dark:border-gray-700 dark:bg-gray-700">
                        <li class="flex">
                            <a class="inline-flex items-center justify-between w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                href="#">
                                <span>Alertas</span>
                                <span
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-600 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-600">
                                    2
                                </span>
                            </a>
                        </li>
                    </ul>
                </template>
            </li>
            <!-- Profile menu -->
            <li class="relative">
                <button class="align-middle rounded-full focus:shadow-outline-purple focus:outline-none"
                    @click="toggleProfileMenu" @keydown.escape="closeProfileMenu" aria-label="Account"
                    aria-haspopup="true">
                    @auth
                        <img class="object-cover w-8 h-8 rounded-full"
                            src="{{ Auth::user()->profile && Auth::user()->profile->avatar_url ? Auth::user()->profile->avatar_url : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) . '&background=6D28D9&color=fff&size=128' }}"
                            alt="Avatar de {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}" />
                    @else
                        <img class="object-cover w-8 h-8 rounded-full"
                            src="https://ui-avatars.com/api/?name=Usuario&background=6D28D9&color=fff&size=128"
                            alt="Avatar" />
                    @endauth
                </button>
                <template x-if="isProfileMenuOpen">
                    <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click.away="closeProfileMenu"
                        @keydown.escape="closeProfileMenu"
                        class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                        aria-label="submenu">
                        <li class="flex">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                    <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </template>
            </li>
        </ul>
    </div>
</header>
